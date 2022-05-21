Feature: Example test for webship-js 
As a tester
I want to login to webship.co site
And see Dashboards
So that I know it is working

  Scenario: Check the Dashboards
    Given I go to "http://webship.test/user/logout"
    Given I go to "http://webship.test/user/login"
     When I put username as "Admin"
      And I put password as "dD.123123ddd"
      And I click on Login button
     Then I should see "Dashboards"
    Given I go to "http://webship.test/user/logout"
    Given I go to "http://webship.test/user/login"
     When I put username as "Content editor"
      And I put password as "dD.123123ddd"
      And I click on Login button
     Then I should see "Dashboards"
    Given I go to "http://webship.test/user/logout"
    Given I go to "http://webship.test/user/login"
     When I put username as "Authenticated user"
      And I put password as "dD.123123ddd"
      And I click on Login button
     Then I should see "Dashboards"