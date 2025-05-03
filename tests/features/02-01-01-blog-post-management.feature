Feature: The blog post content type management
      As an admin user
      I want to be able to add blog posts
      So that they will be in the blog site section.

  Scenario: Check if the admin user can add blog posts.
    Given I am a logged in user with the "Admin" user
     When I go to "/node/add/webblog"
     Then I should see "Create Web Blog"
