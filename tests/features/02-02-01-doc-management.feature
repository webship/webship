Feature: The documentation content type management
      As a admin user
      I want to be able to add Web Doc pages
      So that they will show up in the documentation page.

  Scenario: Check if the admin user can add Web Doc page.
    Given I am a logged in user with the "Admin" user
     When I go to "/node/add/webdoc"
     Then I should see "Create Web Doc"

