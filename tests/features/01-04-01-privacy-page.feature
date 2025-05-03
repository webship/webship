Feature: Check the privacy page
      As an anonymous user
      I want to be able to visit the privacy page
      So that I will able to know the privacy policy.

  Scenario: Check privacy page
    Given I am an anonymous user
     When I go to "/privacy"
     Then I should see "Privacy"
