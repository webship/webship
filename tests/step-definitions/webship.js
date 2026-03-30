const { Given, When, Then } = require('@cucumber/cucumber');
const playwright = require('playwright');
const playwrightConfig = require(require('path').join(process.cwd(), 'playwright.config'));



/**
 * Authenticate a user with password from varbase configuration.
 *
 * Varbase Context #varbase. If you want to see the list of users or add yours you can go and edit the nightwatch.conf.js file under the users list.
 *
 * Example: I am a logged in user with the username "Content admin"
 *
 * @Given /^I am a logged in user with (the )*"([^"]*)?" user$/
 * @Then /^I login with (the )*"([^"]*)?" user$/
 */

Given(
  /^I am a logged in user with (the )*(username )*"([^"]*)?" user$/,
  async function (theCase, usernameCase, username) {

    const users = this.parameters.users;

    if (username in users) {
      const password = users[username].password;

      if (password) {
        const page = this.page; // assuming you stored Playwright page in World

        await page.goto(`${this.parameters.launchUrl}/user/login`);

        await page.fill('#edit-name', username);
        await page.fill('#edit-pass', password);
        await page.click('#edit-submit');
      } else {
        return null;
      }

    } else {
      console.log(`${username} username does not exist`);
      return null;
    }
  }
);

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

    const page = this.page;

    console.log(user.password);

    await page.goto(`${this.parameters.launchUrl}/user/login`);

    await page.fill('#edit-name', user.email);
    await page.fill('#edit-pass', user.password);
    await page.click('#edit-submit');
  }
);


Given(/^I am an anonymous user$/, async function () {
  const page = this.page;

  await page.goto(`${this.parameters.launchUrl}/user/logout`);
});