<?php

namespace RadHam;

class DataDx extends \PDO
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
            parent::__construct($dsn, $dbUser, $dbPass, $options);
            $this->dbDriver = $dbDriver;
            $this->dbHost   = $dbHost;
            $this->dbUser   = $dbUser;
            $this->dbPass   = $dbPass;
            $this->dbName   = $dbName;
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
     * @todo Call the "get" methods via a magic method and secondary class?
     * 
     * @return mixed
     */
    public function getColNames($table)
    {
        $database = $this->dbName;

        $sql = "SELECT `COLUMN_NAME`
                FROM `information_schema`.`COLUMNS`
                WHERE `TABLE_SCHEMA` = '{$database}' 
                  AND `TABLE_NAME`   = '{$table}'";

        return $this->query($sql)
                    ->fetchAll(self::FETCH_COLUMN);
    }

    /**
     * [getTableNames description]
     * @return array|boolean [description]
     */
    public function getTableNames()
    {
        $sql = sprintf(
            'SELECT `table_name` 
             FROM `information_schema`.`TABLES` 
             WHERE `TABLE_TYPE`   = "BASE TABLE" 
               AND `table_schema` = "%s"',
                 $this->dbName
        );

        return $this->query($sql)
                    ->fetchAll(self::FETCH_COLUMN);
    }

    /**
     * @param array $src
     *
     * @return string
     */
    public static function identifierQuoteArrayToStr(array $src)
    {
        $dest = [];

        // MySQL variant.
        foreach ($src as $item) {
            $dest[] = "`{$item}`";
        }

        return implode(', ', $dest);
    }

    /**
     * @return void
     */
    public function close()
    {
        $this->dbh = null;
    }
}
