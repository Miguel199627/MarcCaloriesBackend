<?php

namespace App\Config\Database;

use App\Config\Http\Response;
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
            Response::response(500, $e->getMessage());
        }
    }
}
