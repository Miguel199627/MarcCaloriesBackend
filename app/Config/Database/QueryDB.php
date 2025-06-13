<?php

namespace App\Config\Database;

use App\Config\Http\Response;
use PDOException;

class QueryDB
{
    protected $con;
    protected string $table;
    protected string $primaryKey;
    protected string $fcreated;
    protected string $fupdated;
    protected string $status;

    private string $select = '*';
    private array $joins = [];
    private array $wheres = [];
    private array $bindings = [];

    public function __construct()
    {
        $this->con = ConfigDB::connectDB();
    }

    public function select(string $select): self
    {
        $this->select = $select;
        return $this;
    }

    public function join(string $table, string $on): self
    {
        $this->joins[] = "JOIN $table ON $on";
        return $this;
    }

    public function where(string $condition, array $bindings = []): self
    {
        $this->wheres[] = $condition;
        $this->bindings = array_merge($this->bindings, $bindings);
        return $this;
    }

    public function all()
    {
        try {
            $this->validation();

            $stmt = $this->con->prepare($this->getSQL());
            $stmt->execute($this->bindings);
            return $stmt->fetchAll();
        } catch (\TypeError | \PDOException $e) {
            Response::response(500, $e->getMessage());
        }
    }

    public function first()
    {
        try {
            $this->validation();

            $stmt = $this->con->prepare($this->getSQL());
            $stmt->execute($this->bindings);
            return $stmt->fetch();
        } catch (\TypeError | \PDOException $e) {
            Response::response(500, $e->getMessage());
        }
    }

    public function save(array $dataForm)
    {
        try {
            $this->validation();

            if (array_key_exists($this->primaryKey, $dataForm)) $this->update($dataForm);
            else $this->insert($dataForm);
        } catch (\TypeError | \PDOException $e) {
            Response::response(500, $e->getMessage());
        }
    }

    private function getSQL(): string
    {
        $sql = "SELECT {$this->select} FROM `$this->table` ";

        if (!empty($this->joins)) {
            $sql .= implode(' ', $this->joins) . ' ';
        }

        if (!empty($this->wheres)) {
            $sql .= 'WHERE ' . implode(' AND ', $this->wheres);
        }

        return $sql;
    }

    private function validation()
    {
        if (empty($this->table)) throw new \TypeError("Existe un model sin asignación a una tabla en la db");
        else if (empty($this->primaryKey)) throw new \TypeError("El model: $this->table no tiene relación con un Primary Key");
        else if (empty($this->fcreated)) throw new \TypeError("El model: $this->table no tiene relación de auditoria para la fecha de creación");
        else if (empty($this->fupdated)) throw new \TypeError("El model: $this->table no tiene relación de auditoria para la fecha de actualización");
        else if (empty($this->status)) throw new \TypeError("El model: $this->table no tiene relación de auditoria para el estado del registro");
    }

    private function insert(array $dataForm)
    {
        $campos = array_keys($dataForm);
        $wildcards = array_map(fn($campo) => ":$campo", $campos);

        $sql = "INSERT INTO `$this->table` (" . implode(', ', $campos) . ", $this->fcreated, $this->fupdated, $this->status) 
        VALUES (" . implode(', ', $wildcards) . ", :$this->fcreated, :$this->fupdated, :$this->status)";
        $stmt = $this->con->prepare($sql);

        $params = [];
        foreach ($dataForm as $campo => $valor) {
            $params[":$campo"] = $valor;
        }

        $params[":$this->fcreated"] = date("Y-m-d H:i:s");
        $params[":$this->fupdated"] = date("Y-m-d H:i:s");
        $params[":$this->status"] = array_key_exists($this->status, $dataForm) ? $this->status : 1;

        $stmt->execute($params);
    }

    private function update(array $dataForm)
    {
        $campos = array_keys($dataForm);
        $wildcards = array_map(fn($campo) => "$campo = :$campo", $campos);

        array_push($wildcards, "$this->fupdated = :$this->fupdated");

        $sql = "UPDATE `$this->table` SET " . implode(', ', $wildcards) . " WHERE `$this->primaryKey` = " . $dataForm[$this->primaryKey];
        $stmt = $this->con->prepare($sql);

        $params = [];
        foreach ($dataForm as $campo => $valor) {
            $params[":$campo"] = $valor;
        }

        $params[":$this->fupdated"] = date("Y-m-d H:i:s");

        $stmt->execute($params);
    }
}
