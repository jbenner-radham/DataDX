<?php

namespace RadHam;

class DataDx extends \PDO
{
    protected $db_driver,
              $db_host,
              $db_name,
              $db_user,
              $db_pass;

    /**
     * @param string $db_driver
     * @param string $db_host
     * @param string $db_user
     * @param string $db_pass
     * @param bool   $db_name
     *
     * @return DataDx
     */
    public function __construct($db_driver, $db_host, $db_user, $db_pass, $db_name = false)
    {
        $dsn = "{$db_driver}:host={$db_host}";
        if ($db_name) {
            $dsn .= ";dbname={$db_name}";
        }
        $db_options = [
            self::ATTR_ERRMODE => self::ERRMODE_EXCEPTION
        ];
        $this->db_driver = $db_driver;
        $this->db_host   = $db_host;
        $this->db_user   = $db_user;
        $this->db_pass   = $db_pass;
        $this->db_name   = $db_name;

        try {
            parent::__construct($dsn, $db_user, $db_pass, $db_options);
        } catch (\PDOException $e) {
            die($e->getMessage());
        }

        return $this;
    }

    /**
     * @param string|bool $sql
     *
     * @return array
     */
    public function get($sql = false, $fetch_method = 'assoc')
    {
        // The numeric fetch values below are the values of the corresponding
        // PDO::FETCH_[x] constants. 
        // For reference see: http://php.net/manual/en/pdo.constants.php
        switch ($fetch_method) {
            case 'column':
                $fetch_method = 7;
                break;
            
            case 'assoc':
                // no break
            default:
                $fetch_method = 2;
                break;
        }

        $rows = $this->query($sql)
                     ->fetchAll($fetch_method);
        
        return count($rows) > 0 ? $rows : false;
    }

    /**
     * [getColumn description]
     * 
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    public function getColumn($sql)
    {
        return $this->get($sql, 'column');
    }

    /**
     * @param $table
     * @todo Call the "get" methods via a magic method and secondary class?
     *
     * @return mixed
     */
    public function getColumnNames($table)
    {
        $sql = "SELECT `COLUMN_NAME`
                FROM `information_schema`.`COLUMNS`
                WHERE `TABLE_SCHEMA` = '{$this->db_name}'
                  AND `TABLE_NAME`   = '{$table}'";

        return $this->getColumn($sql);
    }

    /**
     * Returns the results of a provided SQL query and returns the result as
     * JSON.
     *
     * @param string $query SQL query string.
     *
     * @return array|bool JSON encoded array or results or Boolean false.
     */
    public function getJson($query)
    {
        return json_encode($this->get($query));
    }

    /**
     * [getTableNames description]
     *
     * @return array|boolean [description]
     */
    public function getTableNames()
    {
        $sql = "SELECT `table_name`
                FROM `information_schema`.`TABLES`
                WHERE `TABLE_TYPE`   = 'BASE TABLE'
                  AND `table_schema` = '{$this->db_name}'";

        return $this->getColumn($sql);
    }

    public function preparedExecute($sql)
    {
        $stmt = self::prepare($sql);
        $stmt->execute();
        
        // You cannot return $stmt->execute(); you must return $stmt seperately.
        return $stmt;
    }

    /**
     * @param array|string $identifiers
     * @todo Make this compatible with other DB types.
     *
     * @return string
     */
    public static function quoteIdentifiers($identifiers)
    {
        /**
         * If the $identifiers argv is a string cast it into an array 
         * for processing.
         */
        if (is_string($identifiers)) {
            $identifiers = [$identifiers];
        }
        // MySQL/MariaDB variant.
        foreach ($identifiers as &$identifier) {
            $identifier = "`{$identifier}`";
        }

        return implode(', ', $identifiers);
    }
}
