Feature: Admin login.
      As a anonymous user
      I want to be able to as an admin user
      So that that I will be able to administer the site

  Scenario: Check if an anonymous user can login as admin
    Given I am an anonymous user
     When I login with the "Admin" user
     Then I should see "Admin"
