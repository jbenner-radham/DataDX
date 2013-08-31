<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 8/29/13
 * Time: 9:42 PM
 */

namespace RadHam;

class DataDX extends \PDO
{
  
    protected $dbDriver,
              $dbHost,
              $dbName,
              $dbUser,
              $dbPass,
              $dbh;

    public function __construct($dbDriver, $dbHost, $dbName, $dbUser, $dbPass)
    {
        try {
            $dsn = "{$dbDriver}:host={$dbHost};dbname={$dbName}";
            $options = array(
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            );
            parent::__construct($dsn, $dbUser, $dbPass, $options);
        } catch (\PDOException $e) {
            die($e->getMessage());
        }

        return $this;
    }

} 