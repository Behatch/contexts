  @accesibility
  Feature: toegankelijkheid verschillende types paginas

  @accesibility
  Scenario Outline: max 1 h1 and min 1 h2 on page
    Given I am on "<url>"
    Then I should see 1 "h1" elements
    And I should see an "h2" element

  Examples:
    | url |
    | / |

  @accesibility
  Scenario Outline: h1 length check
    Given I am on "<url>"
    Then the title should not be longer than 70

  Examples:
    | url |
    | / |

  @accesibility
  Scenario Outline: alt check on images
    Given I am on "<url>"
    Then all images should have an alt attribute

  Examples:
    | url |
    | / |

  @accesibility
  Scenario Outline: check table headers
    Given I am on "<url>"
    Then all tables should have a table header

  Examples:
    | url |
    | / |

  @accesibility
  Scenario Outline: check table data
    Given I am on "<url>"
    Then all tables should have at least one data row

  Examples:
    | url |
    | / |