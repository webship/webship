const {Given} = require('@cucumber/cucumber');
const {When, Before} = require('@cucumber/cucumber');
const {Then} = require('@cucumber/cucumber');


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

Given(/^I am a logged in user with (the )*(username )*"([^"]*)?" user$/, function(theCase, usernameCase, username) {
  if (username in browser.globals.users) {
    const password = browser.globals.users[username].password;

    if(password != null) {
      browser.url(browser.launch_url + "/user/login")
            .setValue('#edit-name', username)
            .setValue('#edit-pass', password)
            .click("#edit-submit");
    }
    else{
      return null;
    }
  }
  else{
    console.log(username + " username are not exist");
    return null;
  }
});

Given(/^I login with (the )*(username )*"([^"]*)?" user$/, function(theCase, usernameCase, username) {
  if (username in browser.globals.users) {
    const password = browser.globals.users[username].password;

    if(password != null) {
      console.log(password);
      browser.url(browser.launch_url + "/user/login")
            .setValue('#edit-name', username)
            .setValue('#edit-pass', password)
            .click("#edit-submit");
    }
    else{
      return null;
    }
  }
  else{
    console.log(username + " username are not exist");
    return null;
  }
});

Given(/^I am an anonymous user$/, function() {
  browser.url(browser.launch_url + "/user/logout");
});