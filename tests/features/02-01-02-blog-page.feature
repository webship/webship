Feature: The Blog post site section page.
      As a anonymous user
      I want to be able to visit the home page
      So that I can see the list of blog posts

  Scenario: Check blog page.
    Given I am on "/blog"
     Then I should see "Blog"
