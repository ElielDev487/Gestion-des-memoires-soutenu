<?php
// File : app/models/Professeur.php
// Gestion des professeurs en base de données

class Professeur {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Retourne tous les professeurs triés par nom
     * Utilisé dans : selects des formulaires d'archivage et soumission
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            'SELECT id_professeur, nom, prenom, specialite
             FROM professeur
             ORDER BY nom ASC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Retourne un professeur par son id
     * Utilisé dans : vérification avant update/delete
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT id_professeur, nom, prenom, specialite, id_utilisateur
             FROM professeur
             WHERE id_professeur = ?'
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Crée un nouveau professeur
     * Retourne l'id inséré
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO professeur (nom, prenom, telephone, specialite, id_utilisateur)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['telephone'] ?? null,
            $data['specialite'] ?? null,
            $data['id_utilisateur'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Retourne tous les professeurs avec leur email
     * Utilisé dans : liste DE
     */
    public function getAllWithEmail(): array {
        $stmt = $this->db->query(
            'SELECT p.id_professeur, p.nom, p.prenom, p.telephone,
                    p.specialite, u.email, u.actif
             FROM professeur p
             JOIN utilisateur u ON u.id_utilisateur = p.id_utilisateur
             ORDER BY p.nom ASC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Recherche des professeurs par nom ou prénom
     * Retourne max 10 résultats — utilisé par l'autocomplete AJAX
     * Paramètre $q : chaîne de recherche (min 2 caractères recommandé)
     */
    public function search(string $q): array {
        $stmt = $this->db->prepare(
            'SELECT id_professeur, nom, prenom, specialite
             FROM professeur
             WHERE nom LIKE ? OR prenom LIKE ?
             ORDER BY nom ASC
             LIMIT 10'
        );
        $kw = '%' . $q . '%';
        $stmt->execute([$kw, $kw]);
        return $stmt->fetchAll();
    }

    /**
     * Vérifie si un professeur est utilisé dans des mémoires
     * Utilisé avant delete pour éviter les erreurs de contrainte FK
     */
    public function isUsed(int $id): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM memoire
             WHERE id_professeur = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch()['total'] > 0;
    }
}