<?php

namespace Majisti\Test\Database;

/**
 * @desc Interface for database helpers.
 *
 * @author Steven Rosato
 */
interface Helper
{
    /**
     * @desc Creates the database shema.
     */
    public function createSchema();

    /**
     * @desc Updates the database shema.
     */
    public function updateSchema();

    /**
     * @desc Drops the database shema.
     */
    public function dropSchema();

    /**
     * @desc Truncates the provided database tables.
     *
     * @param array $tables Array of mixed tables, could be either
     * table name, repositories, depending on the concrete helper used.
     *
     * @return Helper this
     */
    public function truncateTables(array $tables);

    /**
     * @desc Returns Majisti's Test helper
     *
     * @returns \Majisti\Test\Helper The helper
     */
    public function getHelper();
}