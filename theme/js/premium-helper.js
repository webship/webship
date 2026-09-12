/**
 * @file
 * Manages the license key dialog for premium site templates.
 *
 * Provides UUID input masking, validation, and UI feedback for license keys
 * during site template selection.
 */

(function (Drupal, once) {

  /**
   * The <form> element.
   */
  let form;

  /**
   * The <dialog> element.
   */
  let licenseDialog;

  /**
   * Checks if the current selection is a premium template.
   *
   * @returns {boolean}
   */
  function isPremiumTemplateChecked() {
    const selectedTemplate = form.querySelector(':checked')?.closest('.site-template');

    return selectedTemplate?.classList.contains('site-template--premium');
  }

  /**
   * Checks if the currently entered license key appears to be valid.
   *
   * @param {HTMLInputElement} licenseKeyInput - the license key input to check.
   *
   * @returns {boolean}
   */
  function licenseKeyAppearsGood(licenseKeyInput) {
    return licenseKeyInput.checkValidity() && licenseKeyInput.value.trim() !== '';
  }

  /**
   * Checks if the license key input has been validated as invalid.
   *
   * @param {HTMLInputElement} licenseKeyInput - the license key input to check.
   *
   * @returns {boolean}
   */
  function isInvalid(licenseKeyInput) {
    return licenseKeyInput.classList.contains('license-key--invalid');
  }

  /**
   * Sets the validation state on the input via CSS class.
   *
   * @param {HTMLInputElement} licenseKeyInput - the license key input.
   * @param {boolean|null} isValid - true for valid, false for invalid, null to clear.
   */
  function setValidationState(licenseKeyInput, isValid) {
    licenseKeyInput.classList.remove('license-key--valid', 'license-key--invalid');
    if (isValid === true) {
      licenseKeyInput.classList.add('license-key--valid');
    } else if (isValid === false) {
      licenseKeyInput.classList.add('license-key--invalid');
    }
  }

  /**
   * Returns the currently selected template's license key input element.
   *
   * @returns {HTMLElement}
   */
  function getCurrentSelectionLicenseTextInput() {
    const checkedRadioElement = form.querySelector(':checked');

    return licenseDialog.querySelector(`input[data-for="${checkedRadioElement.value}"]`);
  }

  /**
   * Validates a license key against the server.
   *
   * @param {HTMLInputElement} licenseKeyInput - The license key input element.
   * @returns {Promise<boolean>} True if valid, false if invalid (401).
   * @throws {Error} If network error or non-401 server error.
   */
  async function validateLicenseKeyOnServer(licenseKeyInput) {
    const validationUrl = licenseKeyInput.dataset.validationUrl;
    const key = licenseKeyInput.value;

    const url = new URL(validationUrl);
    url.searchParams.set('key', key);

    const response = await fetch(url, { method: 'GET' });

    if (response.ok) {
      return response.status === 200;
    }
    return false;
  }

  /**
   * Updates the UI based on validation state.
   *
   * @param {HTMLInputElement} licenseKeyInput - The license key input element.
   * @param {string} state - The validation state: 'loading', 'valid', 'invalid', or 'clear'.
   */
  function updateValidationUI(licenseKeyInput, state) {
    const inputKey = licenseKeyInput.dataset.for;
    const statusElement = licenseDialog.querySelector('.license-dialog__status');
    const templateItem = form.querySelector(`[value="${inputKey}"]`)?.closest('.site-template');
    const licenseKeyIndicator = templateItem?.querySelector('.site-template__license-key-matches');

    // Remove existing state classes from status element.
    statusElement.classList.remove(
      'license-dialog__status--valid',
      'license-dialog__status--invalid',
      'license-dialog__status--thinking'
    );

    // Clear status message.
    statusElement.textContent = '';

    // Remove existing state classes from indicator.
    licenseKeyIndicator?.classList.remove(
      'site-template__license-key-matches--valid',
      'site-template__license-key-matches--invalid'
    );

    if (state === 'loading') {
      statusElement.classList.add('license-dialog__status--thinking');
      statusElement.textContent = Drupal.t('Validating...');
    } else if (state === 'valid') {
      statusElement.classList.add('license-dialog__status--valid');
      statusElement.textContent = Drupal.t('License key is valid');
      licenseKeyIndicator?.classList.add('site-template__license-key-matches--valid');
    } else if (state === 'invalid') {
      statusElement.classList.add('license-dialog__status--invalid');
      statusElement.textContent = Drupal.t('License key is invalid');
      licenseKeyIndicator?.classList.add('site-template__license-key-matches--invalid');
    }
  }

  /**
   * Debounced validation function.
   */
  const debouncedValidate = Drupal.debounce(async (input) => {
    if (licenseKeyAppearsGood(input)) {
      // Show loading state.
      updateValidationUI(input, 'loading');

      try {
        const isValid = await validateLicenseKeyOnServer(input);
        setValidationState(input, isValid);
        updateValidationUI(input, isValid ? 'valid' : 'invalid');
      } catch (e) {
        // Network or server error - clear state and UI (fail open).
        setValidationState(input, null);
        updateValidationUI(input, 'clear');
      }
    }
  }, 500);

  /**
   * Handle license key input changes.
   * Clears state immediately, then debounces validation.
   *
   * @param {HTMLInputElement} input - The license key input element.
   */
  function handleLicenseKeyInput(input) {
    // Immediately clear validation state when user types.
    setValidationState(input, null);
    updateValidationUI(input, 'clear');

    // Debounce the actual validation.
    debouncedValidate(input);
  }

  /**
   * Apply UUID text mask to an input field.
   *
   * Formats input as: XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
   *
   * @param {HTMLInputElement} input - a license key input
   */
  function applyLicenseKeyMask(input) {
    input.addEventListener('input', (e) => {
      // Strip non-hex characters and convert to uppercase.
      let value = e.target.value.replace(/[^0-9a-fA-F]/g, '').toUpperCase();

      // Limit to 32 hex characters.
      value = value.substring(0, 32);

      // Insert dashes at positions 8, 12, 16, 20.
      const parts = [
        value.substring(0, 8),
        value.substring(8, 12),
        value.substring(12, 16),
        value.substring(16, 20),
        value.substring(20, 32),
      ].filter(part => part.length > 0);

      e.target.value = parts.join('-');

      // Trigger server validation.
      handleLicenseKeyInput(input);
    });
  }

  /**
   * Initialize everything.
   *
   * @param {HTMLFormElement} el - the form element around the templates.
   */
  function init(el) {
    form = el;

    licenseDialog = form.querySelector('#license-key-dialog');
    const licenseSaveButton = licenseDialog.querySelector('.license-dialog__save');

    // Apply text mask to every license key input.
    licenseDialog.querySelectorAll('[type="text"][name*="access_key"]').forEach(input => {
      applyLicenseKeyMask(input);
    });

    // Open license modal when any "Enter license key" button is clicked.
    form.querySelectorAll('.site-template__enter-key-button').forEach(button => {
      button.addEventListener('click', () => {
        // This automatically sets up a focus trap for free, so better to use
        // this than the popover API.
        licenseDialog.showModal();

        // Clear validation state when opening dialog (may be different template).
        // Defer to after radio selection completes via event bubbling.
        setTimeout(() => {
          const currentInput = getCurrentSelectionLicenseTextInput();
          setValidationState(currentInput, null);
          updateValidationUI(currentInput, 'clear');
        }, 0);
      });
    });

    // If user clicks on a site template (e.g. the "license key" button, or
    //  anything within a site template's grid item), then select that template.
    form.querySelectorAll('.site-template').forEach(template => {
      template.addEventListener('click', e => {
        const currentlySelectedTemplate = form.querySelector(':checked')?.closest('.site-template');
        if (e.currentTarget !== currentlySelectedTemplate) {
          template.querySelector('[type="radio"]').click();
        }
      });
    });

    // Close modal when save button is clicked, but only if valid.
    licenseSaveButton.addEventListener('click', () => {
      const currentInput = getCurrentSelectionLicenseTextInput();

      // Allow closing if format is good AND not explicitly invalid (fail-open).
      if (licenseKeyAppearsGood(currentInput) && !isInvalid(currentInput)) {
        licenseDialog.close();
      }
    });

    // When form is submitted, check if a premium theme is selected and the
    // license key is invalid. If so, re-open the modal instead of submitting.
    form.addEventListener('submit', (e) => {
      const currentInput = getCurrentSelectionLicenseTextInput();

      // Block submission if premium template selected and either:
      // - License key format is bad, OR
      // - License key is explicitly invalid (401 response).
      if (isPremiumTemplateChecked() &&
          (!licenseKeyAppearsGood(currentInput) || isInvalid(currentInput))) {
        licenseDialog.showModal();
        // Update status UI to reflect the current template's validation state.
        updateValidationUI(currentInput, isInvalid(currentInput) ? 'invalid' : 'clear');
        e.preventDefault();
      }
    });

    // Close the modal when its "close" button is clicked.
    form.querySelector('.license-dialog__close').addEventListener('click', () => {
      licenseDialog.close();
    });

    // When the dialog closes, check the selected template and whether the
    // license key appears valid. If valid, show the green check mark and update
    // the button text. If not, change back to default.
    licenseDialog.addEventListener('close', () => {
      const templateItem = form.querySelector(':checked')?.closest('.site-template');
      const enterLicenseButton = templateItem?.querySelector('.site-template__enter-key-button');
      const licenseKeyIndicator = templateItem?.querySelector('.site-template__license-key-matches');
      const currentInput = getCurrentSelectionLicenseTextInput();

      if (isPremiumTemplateChecked() && licenseKeyAppearsGood(currentInput) && !isInvalid(currentInput)) {
        licenseKeyIndicator?.removeAttribute('hidden');
        enterLicenseButton.textContent = Drupal.t('Edit license key');
      }
      else {
        licenseKeyIndicator?.setAttribute('hidden', 'true');
        enterLicenseButton.textContent = Drupal.t('Already purchased? Enter license key');
      }
    });
  }

  Drupal.behaviors.premiumHelper = {
    attach: function (context) {
      once('premium-helper', '[data-drupal-selector="installer-site-template-form"]', context).forEach(init);
    },
  };

})(Drupal, once);
