<?php

class Filiere {
    private $conn;
    private $table = 'filiere';

    public $id;
    public $nom;
    

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY nom ASC";
        return $this->conn->query($sql);
    }

    public function getById($id) {
        $id = intval($id);
        $sql = "SELECT * FROM {$this->table} WHERE id = $id LIMIT 1";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }

    public function getNiveau($filiere_id) {
        $filiere_id = intval($filiere_id);
        $sql = "SELECT n.* FROM niveaux n
                INNER JOIN filiere_niveau fn ON fn.niveau_id = n.id
                WHERE fn.filiere_id = $filiere_id
                ORDER BY n.ordre ASC";
        return $this->conn->query($sql);
    }

    public function create() {
        $nom       = $this->conn->real_escape_string($this->nom);
        $sql = "INSERT INTO {$this->table} (nom)
                VALUES ('$nom')";
        if ($this->conn->query($sql)) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        return false;
    }

    public function update() {
        $id          = intval($this->id);
        $nom        = $this->conn->real_escape_string($this->nom);
        $sql = "UPDATE {$this->table}
                SET code='$nom'
                WHERE id=$id";
        return $this->conn->query($sql);
    }

    public function delete($id) {
        $id = intval($id);
        $this->conn->query("DELETE FROM filiere_niveau WHERE filiere_id=$id");
        return $this->conn->query("DELETE FROM {$this->table} WHERE id=$id");
    }

    public function codeExiste($nom, $exclude_id = null) {
        $code = $this->conn->real_escape_string($nom);
        $sql  = "SELECT id FROM {$this->table} WHERE code='$nom'";
        if ($exclude_id) $sql .= " AND id!=" . intval($exclude_id);
        $r = $this->conn->query($sql);
        return $r && $r->num_rows > 0;
    }
}