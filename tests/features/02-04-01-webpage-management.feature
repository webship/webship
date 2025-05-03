Feature: Webpage management
      As a admin user
      I want to be able to add general Web Pages
      So that I can map the site map for the main site .sections

  Scenario: Check if the admin user can add web pages.
    Given I am a logged in user with the "Admin" user
     When I go to "/node/add/webpage"
     Then I should see "Create Webpage"
