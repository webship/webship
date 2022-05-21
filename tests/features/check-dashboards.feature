Feature: Example test for webship-js 
As a tester
I want to login to webship.co site
And see Dashboards
So that I know it is working

  Scenario: Check that an Authenticated user can not see the Dashboard
    Given I go to "http://webship.test/user/login"
     When I put username as "Authenticated user"
      And I put password as "dD.123123ddd"
      And I click on Login button
     Then I should not see "Dashboards"

  Scenario: Check that a Content editor can see the Dashboard
    Given I go to "http://webship.test/user/login"
     When I put username as "Content editor"
      And I put password as "dD.123123ddd"
      And I click on Login button
     Then I should see "Dashboards"

  Scenario: Check that the Webmaster user can see the Dashboard
    Given I go to "http://webship.test/user/login"
     When I put username as "Webmaster"
      And I put password as "dD.123123ddd"
      And I click on Login button
     Then I should see "Dashboards"
