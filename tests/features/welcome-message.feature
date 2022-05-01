Feature: Example test for webship
As a tester
I want to be able to test the webship site
So that I know it is working

  Scenario: Check the webship site
    Given I go to "http://localhost:8080"
     Then I should see "Welcome to Webship"
