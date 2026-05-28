<?php
// File : app/models/Utilisateur.php

class Utilisateur {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare(
            'SELECT id_utilisateur, email, mot_de_passe, role, actif
             FROM utilisateur
             WHERE email = ?
             LIMIT 1'
        );
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getAll(): array {
        $stmt = $this->db->query(
            'SELECT id_utilisateur, email, role, actif, date_creation
             FROM utilisateur
             ORDER BY date_creation DESC'
        );
        return $stmt->fetchAll();
    }

    public function getStats(): array {
        $stats = [];

        $stmt = $this->db->query('SELECT COUNT(*) as total FROM utilisateur WHERE actif = 1');
        $stats['total_utilisateurs'] = $stmt->fetch()['total'];

        $stmt = $this->db->query('SELECT COUNT(*) as total FROM memoire WHERE statut = "publie"');
        $stats['total_memoires'] = $stmt->fetch()['total'];

        $stmt = $this->db->query('SELECT COUNT(*) as total FROM memoire WHERE statut = "en_attente"');
        $stats['en_attente'] = $stmt->fetch()['total'];

        $stmt = $this->db->query('SELECT COUNT(*) as total FROM filiere');
        $stats['total_filieres'] = $stmt->fetch()['total'];

        return $stats;
    }
	
	/**
	 * Crée un nouveau compte utilisateur
	 * Retourne l'id inséré
	 * Utilisé lors de la création d'un étudiant ou professeur par le DE
	 */
	public function create(array $data): int {
		$stmt = $this->db->prepare(
			'INSERT INTO utilisateur (email, mot_de_passe, role)
			VALUES (?, ?, ?)'
		);
		$stmt->execute([
			$data['email'],
			$data['mot_de_passe'],
			$data['role'],
		]);
		return (int) $this->db->lastInsertId();
	}
}