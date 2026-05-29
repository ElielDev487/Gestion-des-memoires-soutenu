<?php
// File : app/models/Commentaire.php
// Gestion des commentaires sur les mémoires

class Commentaire {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Ajoute un commentaire sur un mémoire
     * Retourne l'id du commentaire créé
     */
    public function ajouter(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO commentaire_memoire (
                contenu,
                id_memoire,
                id_utilisateur,
                date_commentaire
            ) VALUES (
                :contenu,
                :id_memoire,
                :id_utilisateur,
                NOW()
            )'
        );

        $stmt->execute([
            ':contenu'        => $data['contenu'],
            ':id_memoire'     => $data['id_memoire'],
            ':id_utilisateur' => $data['id_utilisateur'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Retourne tous les commentaires pour un mémoire
     * Avec les infos de l'utilisateur qui a commenté
     */
    public function getByMemoire(int $id_memoire): array {
        $stmt = $this->db->prepare(
            'SELECT
                cm.id_commentaire,
                cm.contenu,
                cm.date_commentaire,
                cm.id_utilisateur,
                u.email,
                u.role,
                -- Récupérer le prénom/nom si c\'est un étudiant
                COALESCE(e.prenom, p.prenom, u.email) AS prenom,
                COALESCE(e.nom, p.nom, u.email)       AS nom
            FROM commentaire_memoire cm
            LEFT JOIN utilisateur u ON u.id_utilisateur = cm.id_utilisateur
            LEFT JOIN etudiant e    ON e.id_utilisateur = u.id_utilisateur
            LEFT JOIN professeur p  ON p.id_utilisateur = u.id_utilisateur
            WHERE cm.id_memoire = :id_memoire
            ORDER BY cm.date_commentaire DESC'
        );

        $stmt->execute([':id_memoire' => $id_memoire]);
        return $stmt->fetchAll();
    }

    /**
     * Retourne un commentaire par son id
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT *
             FROM commentaire_memoire
             WHERE id_commentaire = :id'
        );
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Supprime un commentaire (par le propriétaire ou un admin)
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            'DELETE FROM commentaire_memoire
             WHERE id_commentaire = :id'
        );
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Retourne le nombre total de commentaires pour un mémoire
     */
    public function countByMemoire(int $id_memoire): int {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total
             FROM commentaire_memoire
             WHERE id_memoire = :id_memoire'
        );
        $stmt->execute([':id_memoire' => $id_memoire]);
        return (int) $stmt->fetch()['total'];
    }
}
