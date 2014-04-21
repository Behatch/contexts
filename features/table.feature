Feature: Browser Feature

    Scenario: Testing access to /table/index.html
        Given I am on "/table/index.html"
        Then I should see "You are about to test table."

    Scenario: Testing columns
        Given I am on "/table/index.html"

        Then I should see 2 columns in the "table" table

        And the columns schema of the "table" table should match:
            | columns |
            | Lorem   |
            | Ipsum   |

    Scenario: Testing rows
        Given I am on "/table/index.html"

        Then I should see 2 rows in the "table" table
        And I should see 2 rows in the 1st "table" table

        And the data in the 1st row of the "table" table should match:
            | col1   | col2   |
            | Lorem  | Ipsum  |

        And the data in the 2nd row of the "table" table should match:
            | col1   | col2   |
            | Dolor  | Sit    |

    Scenario: Partial Testing rows
        Given I am on "/table/index.html"

        Then I should see 2 rows in the "table" table
        And I should see 2 rows in the 1st "table" table

        And the data in the 1st row of the "table" table should match:
            | col2   |
            | Ipsum  |

        And the data in the 2nd row of the "table" table should match:
            | col1   |
            | Dolor  |

    Scenario: Testing cell content
        Given I am on "/table/index.html"
        Then the 1st column of the 1st row in the "table" table should contain "Lorem"
        And the 2nd column of the 1st row in the "table" table should contain "Ipsum"
