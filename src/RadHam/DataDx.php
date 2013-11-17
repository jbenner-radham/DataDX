<?php

namespace RadHam;

class DataDx extends \PDO
{
    protected $db_driver,
              $db_host,
              $db_name,
              $db_user,
              $db_pass,
              $db_handle;

    /**
     * @param string $db_driver
     * @param string $db_host
     * @param string $db_user
     * @param string $db_pass
     * @param bool $db_name
     *
     * @return DataDX
     */
    public function __construct($db_driver, $db_host, $db_user, $db_pass, $db_name = false)
    {
        try {
            $dsn = "{$db_driver}:host={$db_host}";
            if ($db_name) {
                $dsn .= ";db_name={$db_name}";
            }
            $options = array(
                self::ATTR_ERRMODE => self::ERRMODE_EXCEPTION
            );
            parent::__construct($dsn, $db_user, $db_pass, $options);
            $this->db_driver = $db_driver;
            $this->db_host   = $db_host;
            $this->db_user   = $db_user;
            $this->db_pass   = $db_pass;
            $this->db_name   = $db_name;
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
        $rows = $this->query($sql)->fetchAll(self::FETCH_ASSOC);
        if (count($rows) === 0) {
            return false;
        }

        return $rows;
    }

    /**
     * @param $table
     * @todo Call the "get" methods via a magic method and secondary class?
     *
     * @return mixed
     */
    public function getColNames($table)
    {
        $sql = "SELECT `COLUMN_NAME`
                FROM `information_schema`.`COLUMNS`
                WHERE `TABLE_SCHEMA` = '{$database}'
                  AND `TABLE_NAME`   = '{$this->db_name}'";

        return $this->query($sql)
                    ->fetchAll(self::FETCH_COLUMN);
    }

    /**
     * Returns the results of a provided SQL query and returns the result as
     * JSON.
     *
     * @param  string     $query SQL query string.
     *
     * @return array|bool        JSON encoded array or results or Boolean false.
     */
    public function getJson($query)
    {
        return $this->get($query);
    }

    /**
     * [getTableNames description]
     *
     * @return array|boolean [description]
     */
    public function getTableNames()
    {
        $sql = sprintf(
            'SELECT `table_name` 
             FROM `information_schema`.`TABLES` 
             WHERE `TABLE_TYPE`   = "BASE TABLE" 
               AND `table_schema` = "%s"',
                 $this->db_name
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
     * Closes the database handle.
     *
     * @return void
     */
    public function close()
    {
        $this->db_handle = null;
    }
}
