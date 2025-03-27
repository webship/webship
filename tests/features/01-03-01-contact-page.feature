Feature: Check the contact page
  As an anonymous user
  I want to be able to visit the contact page
  So that I know that the webship.co site is working

  Scenario: Check contact
    Given I am on "/contact"
     Then I should see "Contact"
      And I should see "Your Name"
      And I should see "Company name"
      And I should see "Business email"
      And I should see "Phone number"
      And I should see "Team Size"
      And I should see "Job title"
      And I should see "Your message"
      And I fill in "Your Name" with "Jon Smith"
      And I fill in "Company name" with "Webship.co"
      And I fill in "Business email" with "tast@webship.co"
      And I fill in "Phone number" with "+212 (367) 487-7851"
      And I select "11-50 employees" from "Team Size"
      And I fill in "Job title" with "Administrative Assistant II"
      And I fill in "Your message" with "This is just an automated testing for webship.co site"
      And I press "Submit"