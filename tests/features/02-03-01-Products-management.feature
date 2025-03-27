Feature: Check the products add
  As a nonymouse user
  I want to be able to visit the products add
  So that I know that the site is working

  Scenario: Check products add
    Given I am on the homepage
     When I click "Log in"
      And I fill in "#edit-name" with "admin" by attribute
      And I fill in "#edit-pass" with "admin" by attribute
      And I press "Log in"
     Then I should see "admin"
     When I go to "/node/add/product"
     Then I should see "Create Product"
     When I fill in "#edit-title-0-value" with "What is webship-js" by attribute
      And I press "Add media"
