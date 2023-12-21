const {Given} = require('@cucumber/cucumber');
const {When, Before} = require('@cucumber/cucumber');
const {Then} = require('@cucumber/cucumber');

When(/^I navigate to "([^"]*)?"$/, function( url) {
  return browser.url(url);
});
