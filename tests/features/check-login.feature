Feature: Example test for webship-js 
As a tester
I want to be able to test the webship site
So that I know it is working

  Scenario: Check the login webship site
    Given I go to "/user/login"
     When I click "Log in"
      And I fill in "#edit-name" with "admin" by attribute
      And I fill in "#edit-pass" with "dD.123123ddd" by attribute
      And I press "Log in"
     Then I should see "admin"