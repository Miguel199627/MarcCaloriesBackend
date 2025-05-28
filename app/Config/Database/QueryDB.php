<?php

namespace Config\Database;

use Config\Database\ConfigDB;
use Config\ResponseHttp;
use PDOException;

class QueryDB
{
    protected $con;
    protected string $table;

    public function __construct()
    {
        $this->con = ConfigDB::connectDB();
    }

    public function all()
    {
        try {
            $stmt = $this->con->query("SELECT * FROM `$this->table`");
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            ResponseHttp::jsonResponse(500, $e->getMessage());
        }
    }
}
