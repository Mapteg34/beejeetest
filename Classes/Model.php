<?php

namespace Mapt\Beejeetest;


abstract class Model
{
    /**
     * @return string
     */
    abstract public static function tableName();

    /**
     * @return array
     */
    public static function references()
    {
        return [];
    }

    /**
     * @param array $params
     * @param bool $references
     *
     * @return static[]
     */
    public static function select(array $params = [], bool $references = false)
    {
        if ($references) {
            $joins = static::references();
            if ($joins) {
                $params["joins"] = $joins;
            }
        }

        $items  = db()->iSelect(static::tableName(), $params)->fetchAll();
        $result = [];
        foreach ($items as $item) {
            $m = new static();
            $m->loadFromArray($item);
            $result[] = $m;
        }

        return $result;
    }

    /**
     * @param array $params
     * @param bool $references
     *
     * @return bool|static
     */
    public static function selectOne(array $params = [], bool $references = false)
    {
        if ($references) {
            $joins = static::references();
            if ($joins) {
                $params["joins"] = $joins;
            }
        }

        $params["limit"] = 1;
        $item            = db()->iSelect(static::tableName(), $params)->fetchRow();
        if ($item) {
            $m = new static();
            $m->loadFromArray($item);

            return $m;
        }

        return false;
    }

    private $fields = [];

    public function __get(string $name)
    {
        return isset($this->fields[$name]) ? $this->fields[$name] : null;
    }

    public function __set(string $name, $value)
    {
        $this->fields[$name] = $value;
    }

    public function __isset(string $name)
    {
        return isset($this->fields[$name]);
    }

    private function loadFromArray(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return bool
     */
    public function save()
    {
        if ($this->id) {
            $res = db()->iUpdate(static::tableName(), $this->fields, [
                "filter" => [
                    "id" => $this->id
                ]
            ]);

            return $res && $res->AffectedRows() > 0;
        } else {
            $this->id = db()->iInsert(static::tableName(), $this->fields);
            if (!$this->id) {
                return false;
            }
            $this->loadFromArray(db()->iSelect(static::tableName(), [
                "filter" => [
                    "id" => $this->id
                ],
                "limit"  => 1
            ])->fetchRow());

            return true;
        }
    }

    /**
     * @param array $params
     * @param bool $references
     *
     * @return int
     */
    public static function selectCnt(array $params = [], bool $references = false)
    {
        if ($references) {
            $joins = static::references();
            if ($joins) {
                $params["joins"] = $joins;
            }
        }

        return db()->iSelectCnt(static::tableName(), $params);
    }
}