Feature: Check the blog page
  As an anonymous user
  I want to be able to visit the blog page
  So that I know that the webship.co site is working

  Scenario: Check blog
    Given I am on "/blog"
     Then I should see "Blog"
