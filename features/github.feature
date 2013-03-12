# This Feature is intended to be the proof of concept of BehatCH
# It looks directly at the github website so it will fail if you don't have any Internet connection

Feature: Github Feature

    Scenario: Is the github page still up ?
        Given I am on "http://github.com/sanpii/behatch-skeleton"
        Then I should see "Behat Custom Helper"

    Scenario: I'm gonna check myself a bit
        Given I am on "http://github.com/sanpii/behatch-skeleton"
        When I follow "features"
        And I follow "github.feature"
        Then I should see "WE NEED TO GO DEEP !!"
