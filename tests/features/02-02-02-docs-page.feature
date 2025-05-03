Feature: The documentation site section page
      As an anonymous user
      I want to be able to visit the documentation page
      So that I can see the main Documentation page

  Scenario: Check the Docs page.
    Given I am on "/docs"
     Then I should see "Documentation"
