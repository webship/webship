Feature: Example test for webship-js 
As a tester
I want to be able to test the webship.co site
So that I know it is working

  Scenario: Check the webship.co site
    Given I go to "http://webship.test/user/login"
     Then I should see "Log in"