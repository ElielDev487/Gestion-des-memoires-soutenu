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
     * Retourne le nombre total de mémoires publiés
     * Utilisé pour la pagination du catalogue
     * Accepte les mêmes filtres que getAllPublies()
     */
    public function countPublies(array $filtres = []): int {
        $sql = 'SELECT COUNT(*) as total
                FROM memoire m
                LEFT JOIN inscription    ins ON ins.id_inscription      = m.id_inscription
                LEFT JOIN filiere        fi  ON fi.id_filiere           = ins.id_filiere
                LEFT JOIN niveau         ni  ON ni.id_niveau            = ins.id_niveau
                LEFT JOIN centre         ci  ON ci.id_centre            = ins.id_centre
                LEFT JOIN annee_academique ai ON ai.id_annee_academique = ins.id_annee_academique
                WHERE m.statut = "publie"';

        $params = [];

        if (!empty($filtres['id_filiere'])) {
            $sql .= ' AND (m.id_filiere_archive = :id_filiere OR ins.id_filiere = :id_filiere2)';
            $params[':id_filiere']  = $filtres['id_filiere'];
            $params[':id_filiere2'] = $filtres['id_filiere'];
        }

        if (!empty($filtres['id_niveau'])) {
            $sql .= ' AND (m.id_niveau_archive = :id_niveau OR ins.id_niveau = :id_niveau2)';
            $params[':id_niveau']  = $filtres['id_niveau'];
            $params[':id_niveau2'] = $filtres['id_niveau'];
        }

        if (!empty($filtres['id_centre'])) {
            $sql .= ' AND (m.id_centre_archive = :id_centre OR ins.id_centre = :id_centre2)';
            $params[':id_centre']  = $filtres['id_centre'];
            $params[':id_centre2'] = $filtres['id_centre'];
        }

        if (!empty($filtres['id_annee'])) {
            $sql .= ' AND (m.id_annee_archive = :id_annee OR ins.id_annee_academique = :id_annee2)';
            $params[':id_annee']  = $filtres['id_annee'];
            $params[':id_annee2'] = $filtres['id_annee'];
        }

        if (!empty($filtres['id_professeur'])) {
            $sql .= ' AND m.id_professeur = :id_professeur';
            $params[':id_professeur'] = $filtres['id_professeur'];
        }

        if (!empty($filtres['q'])) {
            $sql .= ' AND (
                m.titre         LIKE :q1 OR
                m.theme         LIKE :q2 OR
                m.auteur_nom    LIKE :q3 OR
                m.auteur_prenom LIKE :q4 OR
                m.directeur_nom LIKE :q5
            )';
            $kw = '%' . $filtres['q'] . '%';
            $params[':q1'] = $kw;
            $params[':q2'] = $kw;
            $params[':q3'] = $kw;
            $params[':q4'] = $kw;
            $params[':q5'] = $kw;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['total'];
    }

    /**
     * Retourne les mémoires publiés pour le catalogue
     * Paginés directement en SQL pour les performances
     * Accepte des filtres optionnels et la pagination
     */
    public function getAllPublies(array $filtres = [], int $limit = 12, int $offset = 0): array {
        $sql = 'SELECT
                    m.id_memoire,
                    m.titre,
                    m.theme,
                    m.auteur_nom,
                    m.auteur_prenom,
                    m.date_publication,
                    -- Filière : archive ou via inscription
                    COALESCE(fa.nom_filiere, fi.nom_filiere) AS filiere,
                    -- Niveau : archive ou via inscription
                    COALESCE(na.libelle, ni.libelle)         AS niveau,
                    -- Centre : archive ou via inscription
                    COALESCE(ca.nom_centre, ci.nom_centre)   AS centre,
                    -- Année : archive ou via inscription
                    COALESCE(aa.libelle, ai.libelle)         AS annee,
                    -- Professeur : lié ou nom libre
                    COALESCE(
                        CONCAT(p.prenom, " ", p.nom),
                        m.directeur_nom
                    )                                        AS professeur,
                    -- Statistiques
                    (SELECT COUNT(*) FROM like_memoire      lm WHERE lm.id_memoire = m.id_memoire) AS nb_likes,
                    (SELECT COUNT(*) FROM commentaire_memoire cm WHERE cm.id_memoire = m.id_memoire) AS nb_commentaires
                FROM memoire m
                -- Jointures pour les mémoires archivés (champs *_archive)
                LEFT JOIN filiere          fa  ON fa.id_filiere            = m.id_filiere_archive
                LEFT JOIN niveau           na  ON na.id_niveau             = m.id_niveau_archive
                LEFT JOIN centre           ca  ON ca.id_centre             = m.id_centre_archive
                LEFT JOIN annee_academique aa  ON aa.id_annee_academique   = m.id_annee_archive
                -- Jointures pour les mémoires soumis via inscription
                LEFT JOIN inscription      ins ON ins.id_inscription       = m.id_inscription
                LEFT JOIN filiere          fi  ON fi.id_filiere            = ins.id_filiere
                LEFT JOIN niveau           ni  ON ni.id_niveau             = ins.id_niveau
                LEFT JOIN centre           ci  ON ci.id_centre             = ins.id_centre
                LEFT JOIN annee_academique ai  ON ai.id_annee_academique   = ins.id_annee_academique
                -- Professeur
                LEFT JOIN professeur       p   ON p.id_professeur          = m.id_professeur
                WHERE m.statut = "publie"';

        $params = [];

        if (!empty($filtres['id_filiere'])) {
            $sql .= ' AND (m.id_filiere_archive = :id_filiere OR ins.id_filiere = :id_filiere2)';
            $params[':id_filiere']  = $filtres['id_filiere'];
            $params[':id_filiere2'] = $filtres['id_filiere'];
        }

        if (!empty($filtres['id_niveau'])) {
            $sql .= ' AND (m.id_niveau_archive = :id_niveau OR ins.id_niveau = :id_niveau2)';
            $params[':id_niveau']  = $filtres['id_niveau'];
            $params[':id_niveau2'] = $filtres['id_niveau'];
        }

        if (!empty($filtres['id_centre'])) {
            $sql .= ' AND (m.id_centre_archive = :id_centre OR ins.id_centre = :id_centre2)';
            $params[':id_centre']  = $filtres['id_centre'];
            $params[':id_centre2'] = $filtres['id_centre'];
        }

        if (!empty($filtres['id_annee'])) {
            $sql .= ' AND (m.id_annee_archive = :id_annee OR ins.id_annee_academique = :id_annee2)';
            $params[':id_annee']  = $filtres['id_annee'];
            $params[':id_annee2'] = $filtres['id_annee'];
        }

        if (!empty($filtres['id_professeur'])) {
            $sql .= ' AND m.id_professeur = :id_professeur';
            $params[':id_professeur'] = $filtres['id_professeur'];
        }

        if (!empty($filtres['q'])) {
            $sql .= ' AND (
                m.titre         LIKE :q1 OR
                m.theme         LIKE :q2 OR
                m.auteur_nom    LIKE :q3 OR
                m.auteur_prenom LIKE :q4 OR
                m.directeur_nom LIKE :q5
            )';
            $kw = '%' . $filtres['q'] . '%';
            $params[':q1'] = $kw;
            $params[':q2'] = $kw;
            $params[':q3'] = $kw;
            $params[':q4'] = $kw;
            $params[':q5'] = $kw;
        }

        $sql .= ' ORDER BY m.date_publication DESC
                  LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);

        // LIMIT et OFFSET doivent être bindés en int
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Retourne tous les mémoires pour le DE
     * Avec les infos académiques complètes
     * Pas de pagination — vue d'administration
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
                COALESCE(fa.nom_filiere, fi.nom_filiere) AS filiere,
                COALESCE(na.libelle, ni.libelle)         AS niveau,
                COALESCE(ca.nom_centre, ci.nom_centre)   AS centre,
                COALESCE(aa.libelle, ai.libelle)         AS annee,
                COALESCE(
                    CONCAT(p.prenom, " ", p.nom),
                    m.directeur_nom
                )                                        AS professeur
            FROM memoire m
            LEFT JOIN filiere          fa  ON fa.id_filiere            = m.id_filiere_archive
            LEFT JOIN niveau           na  ON na.id_niveau             = m.id_niveau_archive
            LEFT JOIN centre           ca  ON ca.id_centre             = m.id_centre_archive
            LEFT JOIN annee_academique aa  ON aa.id_annee_academique   = m.id_annee_archive
            LEFT JOIN inscription      ins ON ins.id_inscription       = m.id_inscription
            LEFT JOIN filiere          fi  ON fi.id_filiere            = ins.id_filiere
            LEFT JOIN niveau           ni  ON ni.id_niveau             = ins.id_niveau
            LEFT JOIN centre           ci  ON ci.id_centre             = ins.id_centre
            LEFT JOIN annee_academique ai  ON ai.id_annee_academique   = ins.id_annee_academique
            LEFT JOIN professeur       p   ON p.id_professeur          = m.id_professeur
            ORDER BY m.date_soumission DESC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Retourne un mémoire par son id avec toutes les infos
     * Utilisé dans : page détail catalogue + validation professeur
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT
                m.*,
                COALESCE(fa.nom_filiere, fi.nom_filiere) AS filiere,
                COALESCE(na.libelle, ni.libelle)         AS niveau,
                COALESCE(ca.nom_centre, ci.nom_centre)   AS centre,
                COALESCE(aa.libelle, ai.libelle)         AS annee,
                COALESCE(
                    CONCAT(p.prenom, " ", p.nom),
                    m.directeur_nom
                )                                        AS professeur,
                (SELECT COUNT(*) FROM like_memoire      lm WHERE lm.id_memoire = m.id_memoire) AS nb_likes,
                (SELECT COUNT(*) FROM commentaire_memoire cm WHERE cm.id_memoire = m.id_memoire) AS nb_commentaires
            FROM memoire m
            LEFT JOIN filiere          fa  ON fa.id_filiere            = m.id_filiere_archive
            LEFT JOIN niveau           na  ON na.id_niveau             = m.id_niveau_archive
            LEFT JOIN centre           ca  ON ca.id_centre             = m.id_centre_archive
            LEFT JOIN annee_academique aa  ON aa.id_annee_academique   = m.id_annee_archive
            LEFT JOIN inscription      ins ON ins.id_inscription       = m.id_inscription
            LEFT JOIN filiere          fi  ON fi.id_filiere            = ins.id_filiere
            LEFT JOIN niveau           ni  ON ni.id_niveau             = ins.id_niveau
            LEFT JOIN centre           ci  ON ci.id_centre             = ins.id_centre
            LEFT JOIN annee_academique ai  ON ai.id_annee_academique   = ins.id_annee_academique
            LEFT JOIN professeur       p   ON p.id_professeur          = m.id_professeur
            WHERE m.id_memoire = :id'
        );
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Supprime un mémoire par son id
     * Supprimer aussi le fichier PDF dans storage/ après appel
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            'DELETE FROM memoire WHERE id_memoire = :id'
        );
        return $stmt->execute([':id' => $id]);
    }
}