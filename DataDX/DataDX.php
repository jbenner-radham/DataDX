<?php

namespace RadHam;

class DataDX extends \PDO
{
  
    protected $dbDriver,
              $dbHost,
              $dbName,
              $dbUser,
              $dbPass,
              $dbh;

    /**
     * @param string $dbDriver
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPass
     * @param bool $dbName
     *
     * @return DataDX
     */
    public function __construct($dbDriver, $dbHost, $dbUser, $dbPass, $dbName = false)
    {
        try {
            $dsn = "{$dbDriver}:host={$dbHost}";
            if ($dbName) {
                $dsn .= ";dbname={$dbName}";
            }
            $options = array(
                self::ATTR_ERRMODE => self::ERRMODE_EXCEPTION
            );
            $this->dbh = parent::__construct($dsn, $dbUser, $dbPass, $options);
            foreach (['Driver', 'Host', 'User', 'Pass', 'Name'] as $param) {
                $this->db{$param} = ${"db{$param}"};
            }
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
    public function get($sql = false)
    {
        return $this->query($sql)->fetchAll(self::FETCH_ASSOC);
    }

    /**
     * @param $table
     *
     * @return mixed
     */
    public function getColNames($table)
    {
        $database = $this->dbName;
        $sql = "SELECT `COLUMN_NAME`
                FROM `information_schema`.`COLUMNS`
                WHERE `TABLE_SCHEMA` = '{$database}'
                    AND `TABLE_NAME` = '{$table}'";

        return $this->query($sql)->fetchAll(self::FETCH_COLUMN);
    }

    /**
     * @return void
     */
    public function close()
    {
        $this->dbh = null;
    }
} 