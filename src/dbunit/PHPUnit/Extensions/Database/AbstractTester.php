<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2013, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    DbUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2002-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 1.0.0
 */

/**
 * Can be used as a foundation for new DatabaseTesters.
 *
 * @package    DbUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2010-2013 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version    Release: 1.2.2
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.0.0
 */
abstract class PHPUnit_Extensions_Database_AbstractTester implements PHPUnit_Extensions_Database_ITester
{

    /**
     * @var PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    protected $setUpOperation;

    /**
     * @var PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    protected $tearDownOperation;

    /**
     * @var PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected $dataSet;

    /**
     * @var string
     */
    protected $schema;

    /**
     * Creates a new database tester.
     */
    public function __construct()
    {
        $this->setUpOperation    = PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
        $this->tearDownOperation = PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }

    /**
     * Closes the specified connection.
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     */
    public function closeConnection(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection)
    {
        $connection->close();
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->dataSet;
    }

    /**
     * TestCases must call this method inside setUp().
     */
    public function onSetUp()
    {
        $this->getSetUpOperation()->execute($this->getConnection(), $this->getDataSet());
    }

    /**
     * TestCases must call this method inside tearDown().
     */
    public function onTearDown()
    {
        $this->getTearDownOperation()->execute($this->getConnection(), $this->getDataSet());
    }

    /**
     * Sets the test dataset to use.
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet
     */
    public function setDataSet(PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        $this->dataSet = $dataSet;
    }

    /**
     * Sets the schema value.
     *
     * @param string $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * Sets the DatabaseOperation to call when starting the test.
     *
     * @param PHPUnit_Extensions_Database_Operation_DatabaseOperation $setUpOperation
     */
    public function setSetUpOperation(PHPUnit_Extensions_Database_Operation_IDatabaseOperation $setUpOperation)
    {
        $this->setUpOperation = $setUpOperation;
    }

    /**
     * Sets the DatabaseOperation to call when ending the test.
     *
     * @param PHPUnit_Extensions_Database_Operation_DatabaseOperation $tearDownOperation
     */
    public function setTearDownOperation(PHPUnit_Extensions_Database_Operation_IDatabaseOperation $tearDownOperation)
    {
        $this->tearDownOperation = $tearDownOperation;
    }

    /**
     * Returns the schema value
     *
     * @return string
     */
    protected function getSchema()
    {
        return $this->schema;
    }

    /**
     * Returns the database operation that will be called when starting the test.
     *
     * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
     */
    protected function getSetUpOperation()
    {
        return $this->setUpOperation;
    }

    /**
     * Returns the database operation that will be called when ending the test.
     *
     * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
     */
    protected function getTearDownOperation()
    {
        return $this->tearDownOperation;
    }
}
