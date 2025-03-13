Feature: Check the home page
  As a nonymouse user
  I want to be able to visit the home page
  So that I know that the site is working

  Scenario: Check homepage
    Given I am on the homepage
     When I click "Log in"
      And I fill in "#edit-name" with "admin" by attribute
      And I fill in "#edit-pass" with "admin" by attribute
      And I press "Log in"
     Then I should see "admin"
     When I go to "/node/add/webblog"
     Then I should see "Create Web Blog"
     When I fill in "#edit-title-0-value" with "What is webship-js" by attribute
      And I press "Add media"
    
