Feature: The Product content type management
      As a admin user
      I want to be able to add products
      So that they will show up in the products site section.

  Scenario: Check if the admin user can add products.
    Given I am a logged in user with the "Admin" user
     When I go to "/node/add/product"
     Then I should see "Create Product"
