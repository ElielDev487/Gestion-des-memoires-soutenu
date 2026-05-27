<?php

class AnneeAcademique {
    private $conn;
    private $table = 'annees_academiques';

    public $id;
    public $libelle;
    

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        return $this->conn->query(
            "SELECT * FROM {$this->table} ORDER BY date_debut DESC"
        );
    }

    public function getById($id) {
        $id = intval($id);
        $r  = $this->conn->query("SELECT * FROM {$this->table} WHERE id=$id LIMIT 1");
        return $r ? $r->fetch_assoc() : null;
    }

    public function getActive() {
        $r = $this->conn->query("SELECT * FROM {$this->table} WHERE est_active=1 LIMIT 1");
        return $r ? $r->fetch_assoc() : null;
    }

    public function getMemoires($annee_id) {
        $annee_id = intval($annee_id);
        return $this->conn->query(
            "SELECT * FROM memoires WHERE annee_academique_id=$annee_id ORDER BY date_soumission DESC"
        );
    }

    public function create() {
        $libelle    = $this->conn->real_escape_string($this->libelle);
        
        if ($est_active) {
            $this->conn->query("UPDATE {$this->table} SET est_active=0");
        }
        $sql = "INSERT INTO {$this->table} (libelle)
                VALUES ('$libelle')";
        if ($this->conn->query($sql)) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        return false;
    }

    public function update() {
        $id         = intval($this->id);
        $libelle    = $this->conn->real_escape_string($this->libelle);
        if ($est_active) {
            $this->conn->query("UPDATE {$this->table} SET est_active=0 WHERE id!=$id");
        }
        return $this->conn->query(
            "UPDATE {$this->table}
             SET libelle='$libelle',
              
             WHERE id=$id"
        );
    }

    public function activer($id) {
        $id = intval($id);
        $this->conn->query("UPDATE {$this->table} SET est_active=0");
        return $this->conn->query("UPDATE {$this->table} SET est_active=1 WHERE id=$id");
    }

    public function delete($id) {
        $id  = intval($id);
        $row = $this->getById($id);
        if ($row && $row['est_active']) return false;
        $check = $this->conn->query("SELECT id FROM memoires WHERE annee_academique_id=$id LIMIT 1");
        if ($check && $check->num_rows > 0) return false;
        return $this->conn->query("DELETE FROM {$this->table} WHERE id=$id");
    }
}