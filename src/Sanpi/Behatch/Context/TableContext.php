<?php

namespace Sanpi\Behatch\Context;

use Behat\Gherkin\Node\TableNode;

class TableContext extends BaseContext
{
    /**
     * @Then /^the columns schema of the "(?P<table>[^"]*)" table should match:$/
     */
    public function theColumnsSchemaShouldMatch($table, TableNode $text)
    {
        $columnsSelector = sprintf('%s thead tr th', $table);
        $columns = $this->getSession()->getPage()->findAll('css', $columnsSelector);

        $this->iShouldSeeColumnsInTheTable(count($text->getHash()), $table);

        foreach ($text->getHash() as $key => $column) {
            assertEquals($column['columns'], $columns[$key]->getText());
        }
    }

    /**
     * @Then /^(?:|I )should see (?P<nth>\d+) columns? in the "(?P<table>[^"]*)" table$/
     */
    public function iShouldSeeColumnsInTheTable($nth, $table)
    {
        $columnsSelector = sprintf('%s thead tr th', $table);
        $columns = $this->getSession()->getPage()->findAll('css', $columnsSelector);

        assertEquals($nth, count($columns));
    }

    /**
     * @Then /^(?:|I )should see (?P<nth>\d+) rows in the (?P<index>\d+)(?:st|nd|rd|th) "(?P<table>[^"]*)" table$/
     */
    public function iShouldSeeRowsInTheNthTable($nth, $index, $table)
    {
        $tables = $this->getSession()->getPage()->findAll('css', $table);
        if (!isset($tables[$index - 1])) {
            throw new \Exception(sprintf('The %d table "%s" was not found in the page', $index, $table));
        }

        $rows = $tables[$index - 1]->findAll('css', 'tbody tr');
        assertEquals($nth, count($rows));
    }

    /**
     * @Then /^(?:|I )should see (?P<nth>\d+) rows? in the "(?P<table>[^"]*)" table$/
     */
    public function iShouldSeeRowsInTheTable($nth, $table)
    {
        $this->iShouldSeeRowsInTheNthTable($nth, 1, $table);
    }

    /**
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
        assertEquals(count($hash), count($cells));

        for ($i = 0; $i < count($cells); $i++) {
            assertEquals($hash[$keys[$i]], $cells[$i]->getText());
        }
    }

    /**
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

        assertContains($text, $actual);
    }
}
