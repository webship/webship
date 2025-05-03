Feature: No public term page.
      As an anonymous user
      I will not be able to visit the terms page
      So that the terms are only private.

  Scenario: Check terms page
    Given I am an anonymous user
     When I go to "/terms"
     Then I should see "Terms"
