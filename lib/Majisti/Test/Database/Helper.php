<?php

namespace Majisti\Test\Database;

use \Doctrine\ORM\EntityManager;

/**
 * @desc Interface for database helpers. Provides fluent interface.
 *
 * @author Steven Rosato
 */
interface Helper
{
    /**
     * @desc Creates the database shema.
     * 
     * @return Helper this
     */
    public function createSchema();

    /**
     * @desc Updates the database shema.
     * 
     * @return Helper this
     */
    public function updateSchema();

    /**
     * @desc Recreates the schema by droping
     * and recreating the schema
     * 
     * Proxies to dropSchema() followed by createSchema()
     * 
     * @return Helper this
     */
    public function recreateSchema();

    /**
     * @desc Drops the database shema.
     * 
     * @return Helper this
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
     * @desc Reloads the data fixtures.
     * 
     * @return Helper this
     */
    public function reloadFixtures();

    /**
     * Returns the application
     * 
     * @return \Zend_Application The application
     */
    public function getApplication();
}