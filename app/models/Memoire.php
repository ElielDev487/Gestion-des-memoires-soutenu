<?php
// File : app/models/Memoire.php
// Gestion des mémoires en base de données
// Couvre les deux cas : archivage DE et soumission étudiant

class Memoire {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Archive un ancien mémoire directement par le DE
     * Le statut est 'publie' directement — pas de validation requise
     * Les champs *_archive portent les infos académiques
     * Retourne l'id du mémoire créé
     */
	public function archiver(array $data): int {
		$stmt = $this->db->prepare(
			'INSERT INTO memoire (
				titre,
				theme,
				resume,
				fichier_pdf,
				statut,
				date_publication,
				auteur_nom,
				auteur_prenom,
				id_inscription,
				id_filiere_archive,
				id_niveau_archive,
				id_centre_archive,
				id_annee_archive,
				id_professeur,
				directeur_nom
			) VALUES (
				:titre,
				:theme,
				:resume,
				:fichier_pdf,
				"publie",
				NOW(),
				:auteur_nom,
				:auteur_prenom,
				NULL,
				:id_filiere,
				:id_niveau,
				:id_centre,
				:id_annee,
				:id_professeur,
				:directeur_nom
			)'
		);

		$stmt->execute([
			':titre'         => $data['titre'],
			':theme'         => $data['theme'],
			':resume'        => $data['resume']        ?? null,
			':fichier_pdf'   => $data['fichier_pdf'],
			':auteur_nom'    => $data['auteur_nom'],
			':auteur_prenom' => $data['auteur_prenom'],
			':id_filiere'    => $data['id_filiere'],
			':id_niveau'     => $data['id_niveau'],
			':id_centre'     => $data['id_centre'],
			':id_annee'      => $data['id_annee'],
			':id_professeur' => $data['id_professeur'] ?? null,
			':directeur_nom' => $data['directeur_nom'] ?? null,
		]);

		return (int) $this->db->lastInsertId();
	}

    /**
     * Retourne tous les mémoires pour le DE
     * Avec les infos académiques (filière, niveau, centre, année)
     * en tenant compte des deux cas (archive et soumission)
     */
    public function getAllForDE(): array {
        $stmt = $this->db->query(
            'SELECT
                m.id_memoire,
                m.titre,
                m.theme,
                m.auteur_nom,
                m.auteur_prenom,
                m.statut,
                m.date_soumission,
                m.date_publication,
                -- Filière : archive ou via inscription
                COALESCE(fa.nom_filiere, fi.nom_filiere) AS filiere,
                -- Niveau : archive ou via inscription
                COALESCE(na.libelle, ni.libelle)         AS niveau,
                -- Centre : archive ou via inscription
                COALESCE(ca.nom_centre, ci.nom_centre)   AS centre,
                -- Année : archive ou via inscription
                COALESCE(aa.libelle, ai.libelle)         AS annee,
                -- Professeur
                CONCAT(p.prenom, " ", p.nom)             AS professeur
            FROM memoire m
            -- Jointures pour les mémoires archivés (champs *_archive)
            LEFT JOIN filiere        fa ON fa.id_filiere           = m.id_filiere_archive
            LEFT JOIN niveau         na ON na.id_niveau            = m.id_niveau_archive
            LEFT JOIN centre         ca ON ca.id_centre            = m.id_centre_archive
            LEFT JOIN annee_academique aa ON aa.id_annee_academique = m.id_annee_archive
            -- Jointures pour les mémoires soumis par étudiant (via inscription)
            LEFT JOIN inscription    ins ON ins.id_inscription      = m.id_inscription
            LEFT JOIN filiere        fi  ON fi.id_filiere           = ins.id_filiere
            LEFT JOIN niveau         ni  ON ni.id_niveau            = ins.id_niveau
            LEFT JOIN centre         ci  ON ci.id_centre            = ins.id_centre
            LEFT JOIN annee_academique ai ON ai.id_annee_academique = ins.id_annee_academique
            -- Professeur
            LEFT JOIN professeur     p  ON p.id_professeur         = m.id_professeur
            ORDER BY m.date_soumission DESC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Retourne tous les mémoires publiés pour le catalogue
     * Avec filtres optionnels
     */
    public function getAllPublies(array $filtres = []): array {
        $sql = 'SELECT
                m.id_memoire,
                m.titre,
                m.theme,
                m.auteur_nom,
                m.auteur_prenom,
                m.date_publication,
                COALESCE(fa.nom_filiere, fi.nom_filiere) AS filiere,
                COALESCE(na.libelle, ni.libelle)         AS niveau,
                COALESCE(ca.nom_centre, ci.nom_centre)   AS centre,
                COALESCE(aa.libelle, ai.libelle)         AS annee,
                CONCAT(p.prenom, " ", p.nom)             AS professeur,
                (SELECT COUNT(*) FROM like_memoire lm WHERE lm.id_memoire = m.id_memoire)       AS nb_likes,
                (SELECT COUNT(*) FROM commentaire_memoire cm WHERE cm.id_memoire = m.id_memoire) AS nb_commentaires
            FROM memoire m
            LEFT JOIN filiere        fa  ON fa.id_filiere            = m.id_filiere_archive
            LEFT JOIN niveau         na  ON na.id_niveau             = m.id_niveau_archive
            LEFT JOIN centre         ca  ON ca.id_centre             = m.id_centre_archive
            LEFT JOIN annee_academique aa ON aa.id_annee_academique  = m.id_annee_archive
            LEFT JOIN inscription    ins ON ins.id_inscription       = m.id_inscription
            LEFT JOIN filiere        fi  ON fi.id_filiere            = ins.id_filiere
            LEFT JOIN niveau         ni  ON ni.id_niveau             = ins.id_niveau
            LEFT JOIN centre         ci  ON ci.id_centre             = ins.id_centre
            LEFT JOIN annee_academique ai ON ai.id_annee_academique  = ins.id_annee_academique
            LEFT JOIN professeur     p   ON p.id_professeur          = m.id_professeur
            WHERE m.statut = "publie"';

        $params = [];

        // Filtre par filière
        if (!empty($filtres['id_filiere'])) {
            $sql .= ' AND (m.id_filiere_archive = ? OR ins.id_filiere = ?)';
            $params[] = $filtres['id_filiere'];
            $params[] = $filtres['id_filiere'];
        }

        // Filtre par niveau
        if (!empty($filtres['id_niveau'])) {
            $sql .= ' AND (m.id_niveau_archive = ? OR ins.id_niveau = ?)';
            $params[] = $filtres['id_niveau'];
            $params[] = $filtres['id_niveau'];
        }

        // Filtre par centre
        if (!empty($filtres['id_centre'])) {
            $sql .= ' AND (m.id_centre_archive = ? OR ins.id_centre = ?)';
            $params[] = $filtres['id_centre'];
            $params[] = $filtres['id_centre'];
        }

        // Filtre par année
        if (!empty($filtres['id_annee'])) {
            $sql .= ' AND (m.id_annee_archive = ? OR ins.id_annee_academique = ?)';
            $params[] = $filtres['id_annee'];
            $params[] = $filtres['id_annee'];
        }

        // Filtre par professeur
        if (!empty($filtres['id_professeur'])) {
            $sql .= ' AND m.id_professeur = ?';
            $params[] = $filtres['id_professeur'];
        }

        // Recherche par mot-clé
        if (!empty($filtres['q'])) {
            $sql .= ' AND (m.titre LIKE ? OR m.theme LIKE ? OR m.auteur_nom LIKE ? OR m.auteur_prenom LIKE ?)';
            $kw = '%' . $filtres['q'] . '%';
            $params = array_merge($params, [$kw, $kw, $kw, $kw]);
        }

        $sql .= ' ORDER BY m.date_publication DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Retourne un mémoire par son id avec toutes les infos
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT
                m.*,
                COALESCE(fa.nom_filiere, fi.nom_filiere) AS filiere,
                COALESCE(na.libelle, ni.libelle)         AS niveau,
                COALESCE(ca.nom_centre, ci.nom_centre)   AS centre,
                COALESCE(aa.libelle, ai.libelle)         AS annee,
                CONCAT(p.prenom, " ", p.nom)             AS professeur,
                (SELECT COUNT(*) FROM like_memoire lm WHERE lm.id_memoire = m.id_memoire)       AS nb_likes,
                (SELECT COUNT(*) FROM commentaire_memoire cm WHERE cm.id_memoire = m.id_memoire) AS nb_commentaires
            FROM memoire m
            LEFT JOIN filiere        fa  ON fa.id_filiere            = m.id_filiere_archive
            LEFT JOIN niveau         na  ON na.id_niveau             = m.id_niveau_archive
            LEFT JOIN centre         ca  ON ca.id_centre             = m.id_centre_archive
            LEFT JOIN annee_academique aa ON aa.id_annee_academique  = m.id_annee_archive
            LEFT JOIN inscription    ins ON ins.id_inscription       = m.id_inscription
            LEFT JOIN filiere        fi  ON fi.id_filiere            = ins.id_filiere
            LEFT JOIN niveau         ni  ON ni.id_niveau             = ins.id_niveau
            LEFT JOIN centre         ci  ON ci.id_centre             = ins.id_centre
            LEFT JOIN annee_academique ai ON ai.id_annee_academique  = ins.id_annee_academique
            LEFT JOIN professeur     p   ON p.id_professeur          = m.id_professeur
            WHERE m.id_memoire = ?'
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Supprime un mémoire et son fichier PDF
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            'DELETE FROM memoire WHERE id_memoire = ?'
        );
        return $stmt->execute([$id]);
    }
}