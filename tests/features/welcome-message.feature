Feature: Example test for webship
As a tester
I want to be able to test the webship site
So that I know it is working

  Scenario: Check the webship site
    Given I go to "http://webship.test"
     Then I should see "Welcome to Webship"
