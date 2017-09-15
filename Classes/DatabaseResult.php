<?php

namespace Mapt\Beejeetest;

use Mapt\Beejeetest\Interfaces\Database\Result;

class DatabaseResult
    implements Result
{
    /**
     * @var mixed
     */
    private $result;

    /**
     * @var bool
     */
    private $arBooleanFields = false;

    /**
     * DatabaseResult constructor.
     *
     * @param mixed $res
     */
    public function __construct(mixed $res)
    {
        $this->result = $res;
    }

    public function __destruct()
    {
        if (!$this->result) {
            return;
        }
        pg_free_result($this->result);
        unset($this->result);
    }

    public function numRows()
    {
        if (!$this->result) {
            return false;
        }

        return pg_num_rows($this->result);
    }

    public function affectedRows()
    {
        if (!$this->result) {
            return false;
        }

        return pg_affected_rows($this->result);
    }

    public function fetchRow()
    {
        if (!$this->result) {
            return false;
        }
        $arLine = pg_fetch_assoc($this->result);
        if ($this->arBooleanFields === false) {
            $this->arBooleanFields = [];
            if ($arLine) {
                foreach ($arLine as $key => $val) {
                    $fieldNum = pg_field_num($this->result, $key);
                    if ($fieldNum >= 0) {
                        $type = pg_field_type($this->result, $fieldNum);
                        if ($type == "boolean" || $type == "bool") {
                            $this->arBooleanFields[] = $key;
                        }
                    }
                }
            }
        }
        if ($arLine) {
            foreach ($this->arBooleanFields as $field) {
                if (!is_null($arLine[$field])) {
                    $arLine[$field] = $arLine[$field] == "t";
                }
            }
        }

        return $arLine;
    }

    public function fetchAll()
    {
        if (!$this->result) {
            return false;
        }

        $arResult = [];
        while ($arLine = $this->fetchRow()) {
            $arResult[] = $arLine;
        }

        return $arResult;
    }

    public function fetchColumn(string $columnName)
    {
        if (!$this->result) {
            return false;
        }

        $arResult = [];
        while ($arLine = $this->fetchRow()) {
            $arResult[] = $arLine[$columnName];
        }

        return $arResult;
    }

    public function fetchOne()
    {
        if (!$this->result) {
            return false;
        }

        $arResult = $this->fetchRow();

        return array_shift($arResult);
    }

    public function fetchAllWithKey(string $keyColumnName)
    {
        if (!$this->result) {
            return false;
        }

        $arResult = [];
        while ($arLine = $this->fetchRow()) {
            $arResult[$arLine[$keyColumnName]] = $arLine;
        }

        return $arResult;
    }

    public function fetchAllPair(string $key, $val)
    {
        if (!$this->result) {
            return false;
        }

        $arResult = [];
        while ($arLine = $this->fetchRow()) {
            $arResult[$arLine[$key]] = $arLine[$val];
        }

        return $arResult;
    }
}