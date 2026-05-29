<?php
// File : app/models/Like.php
// Gestion des likes sur les mémoires

class Like {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Toggle like : like si pas encore liké, unlike si déjà liké
     * Retourne true si c'est un like (nouveau), false si c'est un unlike
     */
    public function toggle(int $id_memoire, int $id_utilisateur): bool {
        // Vérifier si l'utilisateur a déjà liké ce mémoire
        if ($this->hasLiked($id_memoire, $id_utilisateur)) {
            // Supprimer le like
            $stmt = $this->db->prepare(
                'DELETE FROM like_memoire
                 WHERE id_memoire = :id_memoire AND id_utilisateur = :id_utilisateur'
            );
            $stmt->execute([
                ':id_memoire'     => $id_memoire,
                ':id_utilisateur' => $id_utilisateur,
            ]);
            return false; // C'était un unlike
        } else {
            // Ajouter le like
            $stmt = $this->db->prepare(
                'INSERT INTO like_memoire (id_memoire, id_utilisateur)
                 VALUES (:id_memoire, :id_utilisateur)'
            );
            $stmt->execute([
                ':id_memoire'     => $id_memoire,
                ':id_utilisateur' => $id_utilisateur,
            ]);
            return true; // C'était un like
        }
    }

    /**
     * Retourne le nombre de likes pour un mémoire
     */
    public function countByMemoire(int $id_memoire): int {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM like_memoire
             WHERE id_memoire = :id_memoire'
        );
        $stmt->execute([':id_memoire' => $id_memoire]);
        return (int) $stmt->fetch()['total'];
    }

    /**
     * Vérifie si un utilisateur a liké un mémoire
     * Retourne true si oui, false si non
     */
    public function hasLiked(int $id_memoire, int $id_utilisateur): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM like_memoire
             WHERE id_memoire = :id_memoire AND id_utilisateur = :id_utilisateur'
        );
        $stmt->execute([
            ':id_memoire'     => $id_memoire,
            ':id_utilisateur' => $id_utilisateur,
        ]);
        return $stmt->fetch()['total'] > 0;
    }

    /**
     * Retourne tous les likes pour un mémoire (pour les animations, stats, etc.)
     */
    public function getByMemoire(int $id_memoire): array {
        $stmt = $this->db->prepare(
            'SELECT lm.id_like, lm.id_utilisateur, u.email
             FROM like_memoire lm
             LEFT JOIN utilisateur u ON u.id_utilisateur = lm.id_utilisateur
             WHERE lm.id_memoire = :id_memoire
             ORDER BY lm.id_like DESC'
        );
        $stmt->execute([':id_memoire' => $id_memoire]);
        return $stmt->fetchAll();
    }
}
