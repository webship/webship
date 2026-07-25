const { Given, When, Then } = require('@cucumber/cucumber');
const playwright = require('playwright');
const playwrightConfig = require(require('path').join(process.cwd(), 'playwright.config'));

/**
 * Webship ships Honeypot protection on the user login form
 * (honeypot.settings: form_settings.user_login_form = true) with a
 * `time_limit` of 2 seconds. A submission that arrives sooner than that after
 * the form was rendered is rejected with "There was a problem with your form
 * submission. Please wait N seconds and try again." and, worse, registers a
 * Honeypot flood event that then blocks every further login for the `expire`
 * window (300 seconds) from the same client — which is why a single fast
 * automated login used to cascade into every later scenario failing.
 *
 * Playwright fills and submits the form in well under a second, so we must
 * deliberately wait past the Honeypot time limit before submitting, exactly
 * as a real human visitor naturally would. Do NOT remove this wait to "speed
 * up" the suite and do NOT disable the Honeypot login protection to work
 * around it — the protection is intended distribution behaviour.
 */
const HONEYPOT_LOGIN_TIME_LIMIT_MS = 3000;

/**
 * Submit the Drupal login form for the given name/password, honouring the
 * Honeypot time limit and waiting for the post-login navigation.
 */
async function loginWith(page, launchUrl, name, password) {
  await page.goto(`${launchUrl}/user/login`);

  // Drupal's default login form authenticates by username, not e-mail.
  await page.fill('#edit-name', name);
  await page.fill('#edit-pass', password);

  // Respect Honeypot's time restriction on the login form.
  await page.waitForTimeout(HONEYPOT_LOGIN_TIME_LIMIT_MS);

  await Promise.all([
    page.waitForURL((url) => !url.pathname.includes('/user/login')),
    page.click('#edit-submit'),
  ]);
}

/**
 * Authenticate a user with a password from the world parameters.
 *
 * Example: I am a logged in user with the "Admin" user
 *
 * @Given /^I am a logged in user with (the )*"([^"]*)?" user$/
 */
Given(
  /^I am a logged in user with (the )*(username )*"([^"]*)?" user$/,
  async function (theCase, usernameCase, username) {

    const users = this.parameters.users;

    if (username in users) {
      const user = users[username];

      if (user.password) {
        await loginWith(this.page, this.parameters.launchUrl, user.name || username, user.password);
      } else {
        return null;
      }

    } else {
      console.log(`${username} username does not exist`);
      return null;
    }
  }
);

/**
 * Authenticate a user with a password from the world parameters.
 *
 * Example: I login with the "Admin" user
 *
 * @When /^I login with (the )*"([^"]*)?" user$/
 */
Given(
  /^I login with (the )*(username )*"([^"]*)?" user$/,
  async function (_, __, username) {

    const user = this.parameters.users[username];

    if (!user) {
      throw new Error(`User "${username}" does not exist`);
    }

    if (!user.password) {
      throw new Error(`User "${username}" has no password`);
    }

    await loginWith(this.page, this.parameters.launchUrl, user.name || username, user.password);
  }
);

// "I am an anonymous user" (and its "we are …" variant) is already provided
// by webship-js's own navigation.steps.js
// (`/^(I am |we are )?an anonymous user$/`); a duplicate definition here made
// every scenario using that step ambiguous and fail. Do not redefine it.
