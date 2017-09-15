<?php

namespace Mapt\Beejeetest\Interfaces\Database;

interface Result
{
    /**
     * @return integer
     */
    public function numRows();

    /**
     * @return integer
     */
    public function affectedRows();

    /**
     * @return array|false
     */
    public function fetchRow();

    /**
     * @return array|false
     */
    public function fetchAll();

    /**
     * @param string $columnName
     *
     * @return array|false
     */
    public function fetchColumn(string $columnName);

    /**
     * @return array|false
     */
    public function fetchOne();

    /**
     * @param string $keyColumnName
     *
     * @return array|false
     */
    public function fetchAllWithKey(string $keyColumnName);

    /**
     * @param string $key
     * @param $val
     *
     * @return array|false
     */
    public function fetchAllPair(string $key, $val);
}