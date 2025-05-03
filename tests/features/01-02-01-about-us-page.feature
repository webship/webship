Feature: Check the about us page
      As an anonymous user
      I want to be able to visit the about us page
      So that I will be able to see the more info about the organization.

  Scenario: Check about us
    Given I am an anonymous user
     When I go to "/about-us"
     Then I should see "About us"
      

  
     
