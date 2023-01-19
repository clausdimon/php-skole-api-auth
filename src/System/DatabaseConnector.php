<?php
    namespace Src\System;

    use \mysqli;


    class DatabaseConnector
    {
        private $dbConnection = null;
        public function __construct()
        {
            $host = getenv('DB_HOST');
            $port = getenv('DB_PORT');
            $db = getenv('DB_DATABASE');
            $user = getenv('DB_USERNAME');
            $pass = getenv('DB_PASSWORD');

            try {
                $this->dbConnection = new mysqli($host, $user, $pass, $db, $port);

            }catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }
        public function getConnection()
        {
            return $this->dbConnection;
        }
    }
