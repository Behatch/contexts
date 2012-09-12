<?php

namespace Sanpi\Behatch\Context;

use Behat\Gherkin\Node\TableNode;

class TableContext extends BaseContext
{
    /**
     * Checks that the specified table's columns match the given schema
     *
     * @Then /^the columns schema of the "(?P<table>[^"]*)" table should match:$/
     */
    public function theColumnsSchemaShouldMatch($table, TableNode $text)
    {
        $columnsSelector = sprintf('%s thead tr th', $table);
        $columns = $this->getSession()->getPage()->findAll('css', $columnsSelector);

        $this->iShouldSeeColumnsInTheTable(count($text->getHash()), $table);

        foreach ($text->getHash() as $key => $column) {
            $this->assertEquals($column['columns'], $columns[$key]->getText());
        }
    }

    /**
     * Checks that the specified table contains the given number of columns
     *
     * @Then /^(?:|I )should see (?P<nth>\d+) columns? in the "(?P<table>[^"]*)" table$/
     */
    public function iShouldSeeColumnsInTheTable($nth, $table)
    {
        $columnsSelector = sprintf('%s thead tr th', $table);
        $columns = $this->getSession()->getPage()->findAll('css', $columnsSelector);

        $this->assertEquals($nth, count($columns));
    }

    /**
     * Checks that the specified table contains the specified number of rows in its body
     *
     * @Then /^(?:|I )should see (?P<nth>\d+) rows in the (?P<index>\d+)(?:st|nd|rd|th) "(?P<table>[^"]*)" table$/
     */
    public function iShouldSeeRowsInTheNthTable($nth, $index, $table)
    {
        $tables = $this->getSession()->getPage()->findAll('css', $table);
        if (!isset($tables[$index - 1])) {
            throw new \Exception(sprintf('The %d table "%s" was not found in the page', $index, $table));
        }

        $rows = $tables[$index - 1]->findAll('css', 'tbody tr');
        $this->assertEquals($nth, count($rows));
    }

    /**
     * Checks that the specified table contains the specified number of rows in its body
     *
     * @Then /^(?:|I )should see (?P<nth>\d+) rows? in the "(?P<table>[^"]*)" table$/
     */
    public function iShouldSeeRowsInTheTable($nth, $table)
    {
        $this->iShouldSeeRowsInTheNthTable($nth, 1, $table);
    }

    /**
     * Checks that the data of the specified row matches the given schema
     *
     * @Then /^the data in the (?P<nth>\d+)(?:st|nd|rd|th) row of the "(?P<table>[^"]*)" table should match:$/
     */
    public function theDataOfTheRowShouldMatch($nth, $table, TableNode $text)
    {
        $rowsSelector = sprintf('%s tbody tr', $table);
        $rows = $this->getSession()->getPage()->findAll('css', $rowsSelector);

        if (!isset($rows[$nth - 1])) {
            throw new \Exception(sprintf('The row %d was not found in the "%s" table', $nth, $table));
        }

        $cells = (array)$rows[$nth - 1]->findAll('css', 'td');
        $cells = array_merge((array)$rows[$nth - 1]->findAll('css', 'th'), $cells);

        $hash = current($text->getHash());
        $keys = array_keys($hash);
        $this->assertEquals(count($hash), count($cells));

        for ($i = 0; $i < count($cells); $i++) {
            $this->assertEquals($hash[$keys[$i]], $cells[$i]->getText());
        }
    }

    /**
     * Checks that the specified cell (column/row) of the table's body contains the specified text
     *
     * @Then /^the (?P<colIndex>\d+)(?:st|nd|rd|th) column of the (?P<rowIndex>\d+)(?:st|nd|rd|th) row in the "(?P<table>[^"]*)" table should contain "(?P<text>[^"]*)"$/
     */
    public function theStColumnOfTheStRowInTheTableShouldContain($colIndex, $rowIndex, $table, $text)
    {
        $rowSelector = sprintf('%s tbody tr', $table);
        $rows = $this->getSession()->getPage()->findAll('css', $rowSelector);

        if (!isset($rows[$rowIndex - 1])) {
            throw new \Exception(sprintf("The row %d was not found in the %s table", $rowIndex, $table));
        }

        $row = $rows[$rowIndex - 1];
        $colSelector = sprintf('td', $table);
        $cols = $row->findAll('css', $colSelector);

        if (!isset($cols[$colIndex - 1])) {
            throw new \Exception(sprintf("The column %d was not found in the row %d of the %s table", $colIndex, $rowIndex, $table));
        }

        $actual   = $cols[$colIndex - 1]->getText();
        $e = null;

        $this->assertContains($text, $actual);
    }
}
