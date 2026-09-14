<?php

namespace Drupal\webship\Form;

use Composer\InstalledVersions;
use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\DependencyInjection\AutowireTrait;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Htmx\Htmx;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Drupal\webship\ComposerExecutor;
use Drupal\webship\RecipeHandler;
use Drupal\webship\SiteTemplate;
use GuzzleHttp\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Defines a form to choose a site template.
 *
 * @internal
 *   Everything in the Webship installer is internal and may be changed or
 *   removed at any time without warning. External code should not interact
 *   with this class.
 */
final class SiteTemplateForm extends FormBase {

  use AutowireTrait;

  /**
   * An identifier for this task, to mark it as completed.
   */
  public const string TASK_ID = 'template';

  public function __construct(
    protected ClientInterface $http,
    protected RecipeHandler $recipeHandler,
    #[Autowire(service: 'cache.default')]
    protected CacheBackendInterface $cache,
    #[Autowire(param: 'site.path')]
    protected string $sitePath,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function __wakeup(): void {
    parent::__wakeup();
    // This form comes back from the form cache after HTMX updates the license
    // key panel. The messenger restored with it does not change the messages of
    // the session, so use the current messenger again.
    $this->messenger = NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'installer_site_template_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?array $install_state = NULL): array {
    // @see webship_choose_template()
    $all_choices = $install_state['recipes'] ?? [];

    // Load additional choices. If any of them are already in the code base, the
    // ones that are physically present will "win".
    $all_choices += array_map(
      fn (array $values): SiteTemplate => new SiteTemplate(...$values),
      $this->getCuratedList(),
    );

    $starter = 'webship_starter';
    // Must be called `add_ons` to agree with the theme. In the interactive
    // installer no site template is selected: the user chooses one.
    $form['add_ons'] = [
      '#options' => [],
      '#type' => 'radios',
      '#required' => TRUE,
      '#required_error' => $this->t('Choose a site template.'),
    ];
    // If installing non-interactively (e.g., via Drush), choose Starter by
    // default, or the first site template when Starter is not available.
    if (empty($install_state['interactive'])) {
      $form['add_ons']['#default_value'] = array_key_exists($starter, $all_choices) ? $starter : array_key_first($all_choices);
    }

    // The site template chosen so far: the submitted one, or the default one.
    $input = $form_state->getUserInput();
    $chosen = $input['add_ons'] ?? $form['add_ons']['#default_value'] ?? NULL;
    if (!is_string($chosen) || !isset($all_choices[$chosen])) {
      $chosen = NULL;
    }

    // Choosing a site template updates the license key panel on the server.
    // HTMX posts the form to the installer and swaps only the panel, so the
    // installer needs no custom JavaScript. Without HTMX, the panel is updated
    // when the form is shown again, for example after pressing Next.
    // @see \Drupal\Core\Form\FormBuilder::elementTriggeredScriptedSubmission()
    $htmx = NULL;
    if (!empty($install_state['interactive'])) {
      $htmx = (new Htmx())
        ->post(Url::fromUri('base:install.php', [
          'query' => $install_state['parameters'] ?? [],
          'script' => '',
        ]))
        ->trigger('change')
        ->vals(['_triggering_element_name' => 'add_ons'])
        ->select('#site-template-license')
        ->target('#site-template-license')
        ->swap('outerHTML');
    }

    foreach ($all_choices as $key => $choice) {
      assert($choice instanceof SiteTemplate);

      $form['add_ons'][$key] = [
        '#theme_wrappers' => [
          'form_element__site_template' => ['item' => $choice],
        ],
        '#description' => $choice->description,
        '#locator' => $choice->locator,
        '#repository' => $choice->repository,
        '#price' => $choice->price,
      ];
      $htmx?->applyTo($form['add_ons'][$key]);
      $form['add_ons']['#options'][$key] = $choice->name;
    }
    $form['add_ons'][$starter]['#weight'] = -100;

    // Premium site templates need an access (license) key. Only the chosen
    // site template's key field is shown.
    $form['license'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'site-template-license',
        'class' => ['site-template-license'],
        'aria-live' => 'polite',
      ],
      'access_key' => ['#tree' => TRUE],
    ];
    if ($chosen && $all_choices[$chosen]->price > 0) {
      $form['license']['#attributes']['class'][] = 'site-template-license--required';
      $form['license']['access_key'][$chosen] = [
        '#type' => 'textfield',
        '#title' => $this->t('License key for %name', ['%name' => $all_choices[$chosen]->name]),
        '#description' => $this->t('This premium site template needs a license key, which you can get from the seller.'),
        '#maxlength' => 36,
        '#attributes' => [
          'autocomplete' => 'off',
          'placeholder' => 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX',
        ],
      ];
    }

    $form['actions'] = [
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Next'),
        '#button_type' => 'primary',
        '#op' => 'submit',
      ],
      '#type' => 'actions',
    ];
    $form['#title'] = $this->t('Choose a site template');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    // Choosing a site template only updates the license key panel. Validate the
    // choice when a button (Next) is pressed.
    if (empty($form_state->getTriggeringElement()['#is_button'])) {
      // The installer saves the session of this request after the response is
      // sent, which can be after Next is pressed. Remove the errors of this
      // step, already shown, so they do not show again on the next pages.
      $this->messenger()->deleteByType(MessengerInterface::TYPE_ERROR);
      return;
    }
    $choice = $form_state->getValue('add_ons');

    // Ask for a site template when none is chosen. The error of the required
    // radios comes first, and this one covers any other empty or unknown value.
    if (!is_string($choice) || $choice === '' || !isset($form['add_ons'][$choice])) {
      $form_state->setErrorByName('add_ons', $this->t('Choose a site template.'));
      return;
    }

    // A premium site template needs a license key.
    $access_key = trim((string) $form_state->getValue(['access_key', $choice], ''));
    if (($form['add_ons'][$choice]['#price'] ?? 0) > 0 && $access_key === '') {
      $form_state->setErrorByName("access_key][$choice", $this->t('Enter the license key for %name.', [
        '%name' => $form['add_ons']['#options'][$choice],
      ]));
      return;
    }

    // If the package is provided by an alternate repository (i.e., not
    // Packagist or packages.drupal.org), make Composer aware of it.
    $repository = $form['add_ons'][$choice]['#repository'];
    if (empty($repository)) {
      return;
    }
    $url = parse_url($repository);
    $url = rtrim($url['host'] . ':' . ($url['port'] ?? ''), ':');
    // Only Composer repositories are supported.
    ComposerExecutor::execute('repository', 'add', hash('xxh3', $url), 'composer', $repository);

    // Alternate repositories might require an access key. If one was entered,
    // configure Composer to use it for this repository.
    if ($access_key === '') {
      return;
    }

    // Confirm that the package is available on the repository. If it's not, the
    // access key might be invalid.
    $auth_key = 'bearer.' . $url;
    // @todo $access_key is user input, should we sanitize or validate it?
    //   Or just rely on Symfony's Process constructor handle that for us?
    ComposerExecutor::execute('config', $auth_key, $access_key);
    try {
      ComposerExecutor::execute('show', '--all', $form['add_ons'][$choice]['#locator']);
    }
    catch (ProcessFailedException) {
      $form_state->setErrorByName("access_key[$choice]", $this->t('The access key you entered did not grant access to the package. Contact the seller for support.'));
      ComposerExecutor::execute('config', '--unset', $auth_key);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $choice = $form_state->getValue('add_ons');
    $locator = $form['add_ons'][$choice]['#locator'];

    // The installer does not save the session after it shows this form again
    // with errors, so the errors of this step, already shown, would show again
    // on the next pages. Remove them.
    $this->messenger()->deleteByType(MessengerInterface::TYPE_ERROR);

    $this->recipeHandler->enqueue($locator);
    // Mark the task as finished.
    $GLOBALS['install_state']['parameters'][self::TASK_ID] = INSTALL_TASK_SKIP;
  }

  /**
   * Returns a curated list of site template information.
   *
   * @return array<string, array>
   *   An iterable of information about site templates, keyed by machine name.
   */
  private function getCuratedList(): iterable {
    $messenger = $this->messenger();

    // Allow the list of site templates to be defined per-site. This is helpful
    // for testing, or for hosts which want to limit the available choices. This
    // is an official extension point and can be relied upon. It comes first,
    // because its site templates can be local directories, which do not need
    // Composer.
    // @api
    $list = @include $this->sitePath . '/site-templates.php';
    if (is_iterable($list)) {
      return $list;
    }

    // Ensure the file system is writable. If it's not, then there's no point in
    // showing the curated site templates because you probably won't be able to
    // install any of them anyway.
    ['install_path' => $project_root] = InstalledVersions::getRootPackage();
    if (!is_writable($project_root)) {
      $messenger->addWarning(
        $this->t('Only showing site templates that are already downloaded, because %dir is not writable.', [
          '%dir' => realpath($project_root),
        ]),
      );
      return [];
    }

    // If the original file exists, read it directly. It should not be included
    // in releases of the installer.
    // @see .gitattributes
    $file = dirname(__DIR__, 2) . '/site-templates.yml';
    if (file_exists($file)) {
      return Yaml::decode(file_get_contents($file));
    }

    // @see site-templates.yml
    $url = 'https://git.drupalcode.org/api/v4/projects/project%2Fwebship/repository/files/site-templates.yml/raw?ref=12.0.x';
    $cid = hash('xxh32', $url);

    $cached = $this->cache->get($cid);
    if ($cached) {
      return $cached->data;
    }

    $list = [];
    try {
      $list = Yaml::decode(
        (string) $this->http->request('GET', $url)->getBody(),
      );
    }
    catch (ParseException | ClientExceptionInterface $e) {
      $messenger->addWarning($e->getMessage());
    }
    $this->cache->set($cid, $list);
    return $list;
  }

}
