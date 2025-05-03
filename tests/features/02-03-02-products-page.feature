Feature: The Products site section page
      As a anonymous user
      I want to be able to visit the products page
      So that I can see the list of products.

  Scenario: Check the Products page.
    Given I am an anonymous user
     When I go to "/products"
     Then I should see "Products"
