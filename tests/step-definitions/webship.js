const {Given} = require('@cucumber/cucumber');
const {When, Before} = require('@cucumber/cucumber');
const {Then} = require('@cucumber/cucumber');

Given(/^I go to "([^"]*)?"$/, function( url) {
  return browser.url(url);
});

Then(/^I should( not)* see "([^"]*)?"$/, function(negativeCase, expectedText) {
  if (negativeCase) {
    return browser.assert.not.textContains('body', expectedText);
  }
  
  return browser.assert.textContains('body', expectedText);
});

When(/^I navigate to "([^"]*)?"$/, function( url) {
  return browser.url(url);
  
});

When("I put username as {string}", function(string) {
  return  browser.setValue('input[type=text]',string);
});

When("I put password as {string}", function(string) {
  return  browser.setValue('input[type=password]',string);
});

When("I click on Login button",function(){
  return  browser.click('input[type=submit]')
});
