Feature: Check the home page
  As a nonymouse user
  I want to be able to visit the home page
  So that I know that the site is working

  Scenario: Check blog
    Given I am on "/blog"
     Then I should see "Blog"
    
