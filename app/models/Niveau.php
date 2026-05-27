<?php

class Niveau {
    private $conn;
    private $table = 'niveaux';

    public $id;
    public $libelle;
   

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        return $this->conn->query("SELECT * FROM {$this->table} ORDER BY libelle ASC");
    }

    public function getById($id) {
        $id = intval($id);
        $r  = $this->conn->query("SELECT * FROM {$this->table} WHERE id=$id LIMIT 1");
        return $r ? $r->fetch_assoc() : null;
    }

    public function getFiliere($niveau_id) {
        $niveau_id = intval($niveau_id);
        $sql = "SELECT f.* FROM filieres f
                INNER JOIN filiere_niveau fn ON fn.filiere_id=f.id
                WHERE fn.niveau_id=$niveau_id ORDER BY f.libelle ASC";
        return $this->conn->query($sql);
    }

    public function create() {
        $libelle = $this->conn->real_escape_string($this->libelle);
        $sql = "INSERT INTO {$this->table} (libelle)
                VALUES ('$libelle')";
        if ($this->conn->query($sql)) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        return false;
    }

    public function update() {
        $id      = intval($this->id);
        $libelle = $this->conn->real_escape_string($this->libelle);
        $ordre   = intval($this->ordre);
        return $this->conn->query(
            "UPDATE {$this->table} SET libelle='$libelle' WHERE id=$id"
        );
    }

    public function delete($id) {
        $id = intval($id);
        $this->conn->query("DELETE FROM filiere_niveau WHERE niveau_id=$id");
        return $this->conn->query("DELETE FROM {$this->table} WHERE id=$id");
    }

    public function lierFiliere($niveau_id, $filiere_id) {
        $n = intval($niveau_id);
        $f = intval($filiere_id);
        $check = $this->conn->query(
            "SELECT id FROM filiere_niveau WHERE niveau_id=$n AND filiere_id=$f"
        );
        if ($check && $check->num_rows > 0) return true;
        return $this->conn->query(
            "INSERT INTO filiere_niveau (filiere_id, niveau_id) VALUES ($f, $n)"
        );
    }

    public function delierFiliere($niveau_id, $filiere_id) {
        $n = intval($niveau_id);
        $f = intval($filiere_id);
        return $this->conn->query(
            "DELETE FROM filiere_niveau WHERE niveau_id=$n AND filiere_id=$f"
        );
    }
}