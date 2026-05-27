<?php

class Centre {
    private $conn;
    private $table = 'centre';

    public $id;
    public $nom;
    

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        return $this->conn->query("SELECT * FROM {$this->table} ORDER BY nom ASC");
    }

    public function getById($id) {
        $id = intval($id);
        $r  = $this->conn->query("SELECT * FROM {$this->table} WHERE id=$id LIMIT 1");
        return $r ? $r->fetch_assoc() : null;
    }

    public function getMemoires($centre_id) {
        $centre_id = intval($centre_id);
        return $this->conn->query(
            "SELECT * FROM memoires WHERE centre_id=$centre_id ORDER BY date_soumission DESC"
        );
    }

    public function create() {
        $nom        = $this->conn->real_escape_string($this->nom);
        
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
        $nom       = $this->conn->real_escape_string($this->nom);
        
        return $this->conn->query(
            "UPDATE {$this->table}
             SET nom='$nom'
             WHERE id=$id"
        );
    }

    public function delete($id) {
        $id    = intval($id);
        $check = $this->conn->query("SELECT id FROM memoires WHERE centre_id=$id LIMIT 1");
        if ($check && $check->num_rows > 0) return false;
        return $this->conn->query("DELETE FROM {$this->table} WHERE id=$id");
    }
}