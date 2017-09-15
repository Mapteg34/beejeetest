<?php

namespace Mapt\Beejeetest;

use Mapt\Beejeetest\Exceptions\Database\ConnectFail;
use Mapt\Beejeetest\Exceptions\Database\Invalidquery;
use Mapt\Beejeetest\Exceptions\Database\QueryError;

class Database implements
    Interfaces\Database\Transactions,
    Interfaces\Database\Database,
    Interfaces\Database\Intelectual,
    Interfaces\Instanceable
{
    private $host;
    private $port;
    private $user;
    private $pass;
    private $name;

    private $executedquery = null;

    /**
     * @var static
     */
    static private $instance = null;

    /**
     * @inheritdoc
     */
    final static public function instance()
    {
        if (self::$instance === null) {
            self::$instance = new static(
                config()->db_host,
                config()->db_port,
                config()->db_user,
                config()->db_pass,
                config()->db_name
            );
        }

        return self::$instance;
    }

    public function __construct(
        string $host,
        string $port,
        string $user,
        string $pass,
        string $name
    )
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->name = $name;
    }

    /*************************************
     * Database
     *************************************/

    /**
     * @inheritdoc
     */

    public function query(string $sql, array $arParams = [])
    {
        if (!$this->connect()) {
            throw new ConnectFail();
        }
        if (!$sql) {
            throw new Invalidquery();
        }

        foreach ($arParams as &$param) {
            if (is_array($param)) {
                $param = "{".implode(', ', $param)."}";
            } elseif (is_bool($param)) {
                $param = $param ? 1 : 0;
            }
        }
        unset($param);

        $result = null;
        if (count($arParams)) {
            $this->executedquery = $sql." ".print_r($arParams, true);
            $result              = pg_query_params($this->conn, $sql, $arParams);
        } else {
            $this->executedquery = $sql;
            $result              = pg_query($this->conn, $sql);
        }

        if ($result === false) {
            throw new QueryError();
        }

        return new DatabaseResult($result);
    }

    /**
     * @inheritdoc
     */
    public function executedquery()
    {
        return $this->executedquery;
    }

    /**
     * @inheritdoc
     */
    public function errorText()
    {
        if ($this->conn !== null && $this->conn !== false) {
            return pg_last_error($this->conn);
        } else {
            return pg_last_error();
        }
    }

    /*************************************
     * Transactions
     *************************************/

    /**
     * @inheritdoc
     */
    public function transactionStart()
    {
        if (!$this->connect()) {
            throw new ConnectFail();
        }
        pg_query($this->conn, "BEGIN WORK");
    }

    /**
     * @inheritdoc
     */
    public function transactionRollback()
    {
        if (!$this->connect()) {
            throw new ConnectFail();
        }
        pg_query($this->conn, "ROLLBACK");
    }

    /**
     * @inheritdoc
     */
    public function transactionCommit()
    {
        if (!$this->connect()) {
            throw new ConnectFail();
        }
        pg_query($this->conn, "COMMIT");
    }

    /*************************************
     * Intelectual
     *************************************/

    /**
     * Return prepared filter list($whereSql,$arParams)
     *
     * @param array $arFilters
     * @param array $arParams
     *
     * @return array
     */
    public function prepareFilter(array $arFilters, array $arParams = [])
    {
        if (!is_array($arFilters)) {
            if (!$arFilters) {
                return ["", $arParams];
            }

            return ["WHERE ".$arFilters, $arParams];
        }

        $logic   = " AND ";
        $arPairs = [];
        foreach ($arFilters as $column => $value) {
            if ($column == "LOGIC") {
                $logic = $value == "AND" ? " AND " : " OR ";
                continue;
            }
            $isInverted = false;
            if ($column[0] == "!") {
                $column     = substr($column, 1);
                $isInverted = true;
            }
            $column = $column;

            if (is_array($value)) {
                $pair       = $column." = ANY(\$".(count($arParams) + 1).")";
                $arParams[] = $value;
                if ($isInverted) {
                    $pair = "NOT (".$pair.")";
                }
                $arPairs[] = $pair;
            } elseif (is_null($value)) {
                $pair = $column." IS";
                if ($isInverted) {
                    $pair .= " NOT";
                }
                $pair      .= " NULL ";
                $arPairs[] = $pair;
            } else {
                if (substr($column, 0, 1) == "%") {
                    $pair = substr($column, 1);
                    if ($isInverted) {
                        $pair .= " NOT ";
                    }
                    $pair       .= " ILIKE \$".(count($arParams) + 1);
                    $arParams[] = $value;
                    $arPairs[]  = $pair;
                } else {
                    $pair = $column;
                    if ($isInverted) {
                        $pair .= "!";
                    }
                    $pair       .= "=\$".(count($arParams) + 1);
                    $arParams[] = $value;
                    $arPairs[]  = $pair;
                }
            }
        }
        if (!count($arPairs)) {
            return ["", $arParams];
        }

        return ["WHERE ".implode($logic, $arPairs), $arParams];
    }

    /**
     * @inheritdoc
     */
    public function iSelect(string $table, array $params = [])
    {
        if (!$this->connect()) {
            throw new ConnectFail();
        }

        $arFilters = isset($params["filter"]) ? $params["filter"] : [];
        $limit     = isset($params["limit"]) ? $params["limit"] : false;
        $offset    = isset($params["offset"]) ? $params["offset"] : 0;

        $sql = "SELECT ";
        if (!@$params["select"]) {
            $sql .= "*";
        } elseif (!is_array($params["select"])) {
            $sql .= $params["select"];
        } else {
            $sql .= implode(",", $params["select"]);
        }
        $sql .= " FROM ".$table;
        if (@$params["joins"]) {
            foreach (@$params["joins"] as $table => $opts) {
                if (!@$opts["type"]) {
                    $opts["type"] = "INNER JOIN";
                }
                $sql .= " ".$opts["type"]." ".$table;
                if (@$opts["on"]) {
                    $sql .= " ON ".$opts["on"];
                }
            }
        }
        list($whereSql, $arParams) = $this->prepareFilter($arFilters);
        if ($whereSql) {
            $sql .= " ".$whereSql;
        }
        if (@$params["order"]) {
            $pairs = [];
            foreach ($params["order"] as $field => $dir) {
                $pairs[] = $field." ".$dir;
            }
            if (count($pairs) > 0) {
                $sql .= " ORDER BY ".implode(", ", $pairs);
            }
        }
        if ($limit) {
            $sql        .= " LIMIT \$".(count($arParams) + 1);
            $arParams[] = $limit;

            $sql        .= " OFFSET \$".(count($arParams) + 1);
            $arParams[] = $offset;
        }

        return $this->query($sql, $arParams);
    }

    /**
     * @param string $table
     * @param array $params
     *
     * @return int
     * @throws ConnectFail
     */
    public function iSelectCnt(string $table, array $params = [])
    {
        if (!$this->connect()) {
            throw new ConnectFail();
        }

        $arFilters = isset($params["filter"]) ? $params["filter"] : [];

        $sql = "SELECT COUNT(*)";
        $sql .= " FROM ".$table;
        if (@$params["joins"]) {
            foreach (@$params["joins"] as $table => $opts) {
                if (!@$opts["type"]) {
                    $opts["type"] = "INNER JOIN";
                }
                $sql .= " ".$opts["type"]." ".$table;
                if (@$opts["on"]) {
                    $sql .= " ON ".$opts["on"];
                }
            }
        }
        list($whereSql, $arParams) = $this->prepareFilter($arFilters);
        if ($whereSql) {
            $sql .= " ".$whereSql;
        }

        return $this->query($sql, $arParams)->fetchOne();
    }

    /**
     * @inheritdoc
     */
    public function iInsert(string $table, array $arFields, string $pkey = "id")
    {
        if (!$this->connect()) {
            throw new ConnectFail();
        }

        $arParams = [];
        foreach ($arFields as $column => $value) {
            $arParams[] = $value;
        }
        $query = "INSERT INTO ".$table.' ("'.implode('","', array_keys($arFields)).'") VALUES ($'.implode(',$', range(1, count($arFields))).") RETURNING ".$pkey;

        return $this->query($query, $arParams)->fetchOne();
    }

    /**
     * @inheritdoc
     */
    public function iUpdate(string $table, array $arFields, array $params = [])
    {
        if (!$this->connect()) {
            throw new ConnectFail();
        }

        $arFilters = isset($params["filter"]) ? $params["filter"] : [];

        $arPairs  = [];
        $arParams = [];
        foreach ($arFields as $col => $val) {
            $arPairs[]  = '"'.$col.'"=$'.(count($arParams) + 1);
            $arParams[] = $val;
        }
        $sql = "UPDATE ".$table." SET ".implode(",", $arPairs);
        list($whereSql, $arParams) = $this->prepareFilter($arFilters, $arParams);
        if ($whereSql) {
            $sql .= " ".$whereSql;
        }

        return $this->query($sql, $arParams);
    }


    /**
     * @inheritdoc
     */
    public function iDelete(string $table, array $params = [])
    {
        if (!$this->connect()) {
            throw new ConnectFail();
        }

        $arFilters = isset($params["filter"]) ? $params["filter"] : [];

        $sql = "DELETE FROM ".$table;
        list($whereSql, $arParams) = $this->prepareFilter($arFilters);
        if ($whereSql) {
            $sql .= " ".$whereSql;
        }

        return $this->query($sql, $arParams);
    }

    private $conn = null;

    private function connect()
    {
        if ($this->conn === null) {
            $this->conn = pg_connect(sprintf(
                "hostaddr=%s port=%s dbname=%s user=%s password=%s",
                $this->host,
                $this->port,
                $this->name,
                $this->user,
                $this->pass
            ));
        }

        return $this->conn !== false;
    }

    public function meta_data($table_name)
    {
        if (!$this->connect()) {
            throw new ConnectFail();
        }

        return pg_meta_data($this->conn, $table_name);
    }
}