/*
====================================================================
        SEEDS — PLATEFORME DES MÉMOIRES
====================================================================

OBJECTIF
--------
Données initiales pour démarrer le projet.

Contient :
- Compte admin
- Compte DE
- Niveaux (L3, M1, M2)
- Centres (les 5 centres officiels de l'école)
- Filières
- Année académique courante
- Un professeur de test
- Deux étudiants de test (simple + diplômé)

====================================================================

MOT DE PASSE PAR DÉFAUT POUR TOUS LES COMPTES DE TEST
------------------------------------------------------
Mot de passe en clair  : Test1234
Hash bcrypt            : généré avec password_hash('Test1234', PASSWORD_DEFAULT)

⚠️  À CHANGER ABSOLUMENT EN PRODUCTION

====================================================================
*/

USE memoires_db;

/*
====================================================================
                        UTILISATEURS
====================================================================
*/

INSERT INTO utilisateur (email, mot_de_passe, role, actif) VALUES

    /*
    Administrateur principal
    Email    : admin@universite.bj
    Password : Test1234
    */
    (
        'admin@universite.bj',
        '$2y$10$TKh8H1.PfXFBzxpgFIe8MuFnHHOEKzn9mkr.sSMGUFGWd3mEwJNi2',
        'admin',
        TRUE
    ),

    /*
    Direction des Études
    Email    : de@universite.bj
    Password : Test1234
    */
    (
        'de@universite.bj',
        '$2y$10$TKh8H1.PfXFBzxpgFIe8MuFnHHOEKzn9mkr.sSMGUFGWd3mEwJNi2',
        'de',
        TRUE
    ),

    /*
    Professeur de test
    Email    : professeur@universite.bj
    Password : Test1234
    */
    (
        'professeur@universite.bj',
        '$2y$10$TKh8H1.PfXFBzxpgFIe8MuFnHHOEKzn9mkr.sSMGUFGWd3mEwJNi2',
        'professeur',
        TRUE
    ),

    /*
    Étudiant simple de test
    Email    : etudiant.simple@universite.bj
    Password : Test1234
    */
    (
        'etudiant.simple@universite.bj',
        '$2y$10$TKh8H1.PfXFBzxpgFIe8MuFnHHOEKzn9mkr.sSMGUFGWd3mEwJNi2',
        'etudiant',
        TRUE
    ),

    /*
    Étudiant diplômé de test (peut soumettre)
    Email    : etudiant.diplome@universite.bj
    Password : Test1234
    */
    (
        'etudiant.diplome@universite.bj',
        '$2y$10$TKh8H1.PfXFBzxpgFIe8MuFnHHOEKzn9mkr.sSMGUFGWd3mEwJNi2',
        'etudiant',
        TRUE
    );



/*
====================================================================
                            NIVEAUX
====================================================================
*/

INSERT INTO niveau (libelle) VALUES
    ('L3'),
    ('M1'),
    ('M2');



/*
====================================================================
                            CENTRES
====================================================================
Les 5 centres officiels de l'école
====================================================================
*/

INSERT INTO centre (nom_centre) VALUES
    ('Gbegamey'),
    ('Porto-Novo'),
    ('Calavi'),
    ('Agla'),
    ('Akpakpa');



/*
====================================================================
                            FILIÈRES
====================================================================
*/

INSERT INTO filiere (nom_filiere) VALUES
    ('Informatique et Systèmes'),
    ('Gestion des Entreprises'),
    ('Droit Privé'),
    ('Droit Public'),
    ('Physique'),
    ('Chimie'),
    ('Économie'),
    ('Sciences de la Santé'),
    ('Mathématiques'),
    ('Biologie');



/*
====================================================================
                    ANNÉES ACADÉMIQUES
====================================================================
*/

INSERT INTO annee_academique (libelle) VALUES
    ('2019-2020'),
    ('2020-2021'),
    ('2021-2022'),
    ('2022-2023'),
    ('2023-2024'),
    ('2024-2025');



/*
====================================================================
                        PROFESSEUR DE TEST
====================================================================
Lié au compte utilisateur id = 3 (professeur@universite.bj)
====================================================================
*/

INSERT INTO professeur (nom, prenom, telephone, specialite, id_utilisateur) VALUES
    (
        'Adéwa',
        'Kossi',
        '+229 97 00 00 01',
        'Informatique et Intelligence Artificielle',
        3
    );



/*
====================================================================
                        ÉTUDIANTS DE TEST
====================================================================
*/

INSERT INTO etudiant (matricule, nom, prenom, telephone, id_utilisateur) VALUES

    /*
    Étudiant simple — lié au compte id = 4
    */
    (
        'GBE-2023-001',
        'Gbéto',
        'Arnaud',
        '+229 97 11 11 11',
        4
    ),

    /*
    Étudiant diplômé — lié au compte id = 5
    */
    (
        'GBE-2021-045',
        'Hounkpatin',
        'Céleste',
        '+229 97 22 22 22',
        5
    );



/*
====================================================================
                        INSCRIPTIONS DE TEST
====================================================================
*/

INSERT INTO inscription
    (id_etudiant, id_filiere, id_niveau, id_centre, id_annee_academique, type_etudiant, niveau_diplome, peut_soumettre)
VALUES

    /*
    Arnaud Gbéto — étudiant simple
    Inscrit en M2 Informatique à Gbegamey en 2024-2025
    type_etudiant  = simple
    niveau_diplome = NULL (pas encore diplômé)
    peut_soumettre = FALSE
    */
    (
        1,  -- id_etudiant  : Gbéto Arnaud
        1,  -- id_filiere   : Informatique et Systèmes
        3,  -- id_niveau    : M2
        1,  -- id_centre    : Gbegamey
        6,  -- id_annee     : 2024-2025
        'simple',
        NULL,
        FALSE
    ),

    /*
    Céleste Hounkpatin — étudiante diplômée
    Inscrite en M2 Informatique à Gbegamey en 2021-2022
    type_etudiant  = diplome
    niveau_diplome = M2 (id = 3)
    peut_soumettre = TRUE (le DE l'a passée diplômée)
    */
    (
        2,  -- id_etudiant  : Hounkpatin Céleste
        1,  -- id_filiere   : Informatique et Systèmes
        3,  -- id_niveau    : M2
        1,  -- id_centre    : Gbegamey
        3,  -- id_annee     : 2021-2022
        'diplome',
        3,  -- niveau_diplome : M2
        TRUE
    );

