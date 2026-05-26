/*
====================================================================
        BASE DE DONNÉES - PLATEFORME DES MEMOIRES
====================================================================

OBJECTIF
--------
Cette plateforme permet :

- la gestion des étudiants
- la gestion des professeurs
- la gestion académique
- l'archivage des anciens mémoires (par le DE)
- la soumission des mémoires (par les étudiants diplômés)
- la validation des mémoires (par les professeurs)
- la publication des mémoires
- la consultation sécurisée des mémoires
- les interactions sociales (likes/commentaires)

====================================================================
                        CHOIX DE CONCEPTION
====================================================================

1. TABLE UTILISATEUR
--------------------
Tous les utilisateurs utilisent une seule table
d'authentification.

Rôles :
- admin
- de
- professeur
- etudiant

Cela centralise :
- login
- email
- mot de passe
- permissions

====================================================================

2. TABLE ETUDIANT
-----------------
Contient uniquement les informations permanentes.

Le niveau n'est PAS stocké ici.

Pourquoi ?
-----------
Le niveau appartient à l'inscription, pas à l'étudiant.
Un étudiant reste dans le système même après ses études.
Seule son inscription garde la trace de son parcours.

====================================================================

3. TABLE INSCRIPTION
--------------------
Représente le parcours académique de l'étudiant.

Contient :
- niveau au moment de l'inscription
- filière
- centre
- année académique
- type (simple ou diplome)
- droit de soumission
- niveau du diplôme obtenu (renseigné par le DE lors du
  passage au statut diplômé — peut différer du niveau
  d'inscription initial)

Pourquoi niveau_diplome séparé de id_niveau ?
---------------------------------------------
id_niveau    = niveau au moment de l'inscription
niveau_diplome = niveau effectivement obtenu au diplôme

Le DE choisit explicitement L3, M1 ou M2 lors du passage
au statut diplômé. Ces deux valeurs peuvent différer.

====================================================================

4. TABLE MEMOIRE
----------------
Le mémoire est lié à UNE inscription pour les soumissions
étudiants.

Pour les anciens mémoires archivés par le DE :
- id_inscription est NULL
- les informations académiques sont portées par
  id_filiere_archive, id_niveau_archive, id_centre_archive,
  id_annee_archive directement sur le mémoire

Cela permet au DE d'archiver des mémoires existants sans
avoir à créer une inscription fictive.

====================================================================

5. SYSTEME DE LIKE/COMMENTAIRE
------------------------------
Tous les utilisateurs peuvent :
- liker
- commenter

Donc :
- les interactions pointent vers utilisateur

et NON vers etudiant.

====================================================================

6. SECURITE
-----------
- PDO obligatoire
- password_hash obligatoire
- password_verify obligatoire
- stockage PDF hors public
- accès PDF via proxy PHP uniquement

====================================================================

7. PRIORITE DE DEVELOPPEMENT
-----------------------------
Phase 1 : Archivage des anciens mémoires (DE)
           + Catalogue + Lecteur PDF sécurisé

Phase 2 : Soumission étudiant + Validation professeur

====================================================================


====================================================================
                CREATION DE LA BASE DE DONNEES
====================================================================
*/

CREATE DATABASE memoires_db;
USE memoires_db;


/*
====================================================================
                        UTILISATEUR
====================================================================
Authentification centralisée pour tous les rôles
====================================================================
*/

CREATE TABLE utilisateur (

    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,

    email VARCHAR(150)
    UNIQUE NOT NULL,

    /*
    Mot de passe hashé avec :
    password_hash($password, PASSWORD_DEFAULT)

    Vérification avec :
    password_verify($password, $hash)

    INTERDIT : md5, sha1
    */
    mot_de_passe VARCHAR(255)
    NOT NULL,

    role ENUM(
        'admin',
        'de',
        'professeur',
        'etudiant'
    ) NOT NULL,

    actif BOOLEAN DEFAULT TRUE,

    date_creation DATETIME
    DEFAULT CURRENT_TIMESTAMP
);



/*
====================================================================
                            CENTRE
====================================================================
Géré par la Direction des Études
====================================================================
*/

CREATE TABLE centre (

    id_centre INT PRIMARY KEY AUTO_INCREMENT,

    nom_centre VARCHAR(100)
    UNIQUE NOT NULL
);



/*
====================================================================
                            FILIERE
====================================================================
Géré par la Direction des Études
====================================================================
*/

CREATE TABLE filiere (

    id_filiere INT PRIMARY KEY AUTO_INCREMENT,

    nom_filiere VARCHAR(150)
    UNIQUE NOT NULL
);



/*
====================================================================
                            NIVEAU
====================================================================
Géré par la Direction des Études.

Exemples : L3, M1, M2
====================================================================
*/

CREATE TABLE niveau (

    id_niveau INT PRIMARY KEY AUTO_INCREMENT,

    libelle VARCHAR(50)
    UNIQUE NOT NULL
);



/*
====================================================================
                    ANNEE ACADEMIQUE
====================================================================
Géré par la Direction des Études.

Exemple : 2023-2024
====================================================================
*/

CREATE TABLE annee_academique (

    id_annee_academique INT PRIMARY KEY AUTO_INCREMENT,

    libelle VARCHAR(20)
    UNIQUE NOT NULL
);



/*
====================================================================
                        PROFESSEUR
====================================================================
Chaque professeur possède un compte utilisateur.
Le compte est créé par la Direction des Études.
====================================================================
*/

CREATE TABLE professeur (

    id_professeur INT PRIMARY KEY AUTO_INCREMENT,

    nom VARCHAR(100) NOT NULL,

    prenom VARCHAR(100) NOT NULL,

    telephone VARCHAR(30),

    specialite VARCHAR(255),

    id_utilisateur INT UNIQUE NOT NULL,

    FOREIGN KEY (id_utilisateur)
    REFERENCES utilisateur(id_utilisateur)
);



/*
====================================================================
                            ETUDIANT
====================================================================
Informations permanentes uniquement.

Le niveau et la filière ne sont PAS ici :
ils appartiennent à l'inscription.

Le compte utilisateur est créé par la Direction des Études.
====================================================================
*/

CREATE TABLE etudiant (

    id_etudiant INT PRIMARY KEY AUTO_INCREMENT,

    matricule VARCHAR(100)
    UNIQUE NOT NULL,

    nom VARCHAR(100) NOT NULL,

    prenom VARCHAR(100) NOT NULL,

    telephone VARCHAR(30),

    id_utilisateur INT UNIQUE NOT NULL,

    FOREIGN KEY (id_utilisateur)
    REFERENCES utilisateur(id_utilisateur)
);



/*
====================================================================
                        INSCRIPTION
====================================================================
Parcours académique de l'étudiant.

Une inscription unique par étudiant dans la plateforme.
Créée par le DE en même temps que le compte étudiant.

Passage au statut diplômé :
- Le DE change type_etudiant à 'diplome'
- Le DE choisit le niveau_diplome obtenu (L3, M1, M2...)
- peut_soumettre passe à TRUE automatiquement
====================================================================
*/

CREATE TABLE inscription (

    id_inscription INT PRIMARY KEY AUTO_INCREMENT,

    id_etudiant INT NOT NULL,

    id_filiere INT NOT NULL,

    /*
    Niveau au moment de l'inscription initiale.
    Peut différer du niveau_diplome.
    */
    id_niveau INT NOT NULL,

    id_centre INT NOT NULL,

    id_annee_academique INT NOT NULL,

    /*
    simple  = étudiant classique, consultation uniquement
    diplome = peut soumettre son mémoire
    */
    type_etudiant ENUM(
        'simple',
        'diplome'
    ) NOT NULL DEFAULT 'simple',

    /*
    Niveau du diplôme obtenu.
    Renseigné par le DE lors du passage au statut diplômé.
    Peut être différent du id_niveau d'inscription.

    NULL tant que l'étudiant est de type 'simple'.

    Exemple :
    - id_niveau     = M2 (inscrit en M2)
    - niveau_diplome = M2 (diplômé en M2)
    Ou :
    - id_niveau     = M1 (inscrit en M1)
    - niveau_diplome = L3 (a finalement obtenu une L3)
    */
    niveau_diplome INT NULL,

    /*
    TRUE  = peut déposer son mémoire
    FALSE = mémoire déjà soumis et publié
    */
    peut_soumettre BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (id_etudiant)
    REFERENCES etudiant(id_etudiant),

    FOREIGN KEY (id_filiere)
    REFERENCES filiere(id_filiere),

    FOREIGN KEY (id_niveau)
    REFERENCES niveau(id_niveau),

    FOREIGN KEY (niveau_diplome)
    REFERENCES niveau(id_niveau),

    FOREIGN KEY (id_centre)
    REFERENCES centre(id_centre),

    FOREIGN KEY (id_annee_academique)
    REFERENCES annee_academique(id_annee_academique)
);



/*
====================================================================
                            MEMOIRE
====================================================================
Table centrale de la plateforme.

DEUX CAS D'USAGE :

1. Archivage par le DE (anciens mémoires existants)
   - id_inscription = NULL
   - statut         = 'publie' directement
   - date_publication renseignée
   - les champs *_archive portent les infos académiques

2. Soumission par l'étudiant (nouveaux mémoires)
   - id_inscription renseigné (lié au parcours étudiant)
   - statut         = 'en_attente' puis 'publie' ou 'refuse'
   - les champs *_archive sont NULL (on lit via id_inscription)

La dénormalisation auteur_nom / auteur_prenom garantit
la conservation des données même si le compte est supprimé.
====================================================================
*/

CREATE TABLE memoire (

    id_memoire INT PRIMARY KEY AUTO_INCREMENT,

    titre VARCHAR(255)
    NOT NULL,

    theme VARCHAR(255)
    NOT NULL,

    resume TEXT,

    fichier_pdf VARCHAR(255)
    NOT NULL,

    date_soumission DATETIME
    DEFAULT CURRENT_TIMESTAMP,

    date_publication DATETIME NULL,

    /*
    en_attente = soumis par étudiant, en attente de validation
    refuse     = refusé par le professeur
    publie     = validé et accessible dans le catalogue
    */
    statut ENUM(
        'en_attente',
        'refuse',
        'publie'
    ) DEFAULT 'en_attente',

    /*
    Dénormalisé pour conserver l'identité de l'auteur
    même si son compte venait à être supprimé
    */
    auteur_nom VARCHAR(100)
    NOT NULL,

    auteur_prenom VARCHAR(100)
    NOT NULL,

    /*
    NULL pour les anciens mémoires archivés par le DE.
    Renseigné pour les soumissions étudiants.
    */
    id_inscription INT NULL,

    /*
    Champs utilisés UNIQUEMENT pour les anciens mémoires
    archivés par le DE (id_inscription = NULL).
    Permettent de renseigner filière, niveau, centre, année
    sans créer d'inscription fictive.
    */
    id_filiere_archive INT NULL,

    id_niveau_archive INT NULL,

    id_centre_archive INT NULL,

    id_annee_archive INT NULL,

    /*
    Le professeur directeur du mémoire.
    Obligatoire dans tous les cas.
    */
    id_professeur INT NOT NULL,

    FOREIGN KEY (id_inscription)
    REFERENCES inscription(id_inscription),

    FOREIGN KEY (id_filiere_archive)
    REFERENCES filiere(id_filiere),

    FOREIGN KEY (id_niveau_archive)
    REFERENCES niveau(id_niveau),

    FOREIGN KEY (id_centre_archive)
    REFERENCES centre(id_centre),

    FOREIGN KEY (id_annee_archive)
    REFERENCES annee_academique(id_annee_academique),

    FOREIGN KEY (id_professeur)
    REFERENCES professeur(id_professeur)
);



/*
====================================================================
                    COMMENTAIRE MEMOIRE
====================================================================
Tous les utilisateurs connectés peuvent commenter.
Les commentaires pointent vers utilisateur et non etudiant.
====================================================================
*/

CREATE TABLE commentaire_memoire (

    id_commentaire INT PRIMARY KEY AUTO_INCREMENT,

    contenu TEXT NOT NULL,

    date_commentaire DATETIME
    DEFAULT CURRENT_TIMESTAMP,

    id_memoire INT NOT NULL,

    id_utilisateur INT NOT NULL,

    FOREIGN KEY (id_memoire)
    REFERENCES memoire(id_memoire),

    FOREIGN KEY (id_utilisateur)
    REFERENCES utilisateur(id_utilisateur)
);



/*
====================================================================
                        LIKE MEMOIRE
====================================================================
Tous les utilisateurs connectés peuvent liker.
Un utilisateur ne peut liker un mémoire qu'une seule fois.
====================================================================
*/

CREATE TABLE like_memoire (

    id_like INT PRIMARY KEY AUTO_INCREMENT,

    id_memoire INT NOT NULL,

    id_utilisateur INT NOT NULL,

    FOREIGN KEY (id_memoire)
    REFERENCES memoire(id_memoire),

    FOREIGN KEY (id_utilisateur)
    REFERENCES utilisateur(id_utilisateur),

    /*
    Contrainte d'unicité : un seul like par utilisateur
    et par mémoire
    */
    UNIQUE(id_memoire, id_utilisateur)
);



/*
====================================================================
                            INDEX
====================================================================
Optimisation des recherches dans le catalogue
====================================================================
*/

/*
Recherche par thème
*/
CREATE INDEX idx_theme
ON memoire(theme);

/*
Filtrage par statut (catalogue = publie uniquement)
*/
CREATE INDEX idx_statut
ON memoire(statut);

/*
Filtrage par professeur directeur
*/
CREATE INDEX idx_professeur
ON memoire(id_professeur);

/*
Filtrage par inscription (soumissions étudiants)
*/
CREATE INDEX idx_inscription
ON memoire(id_inscription);

/*
Filtrage par filière (anciens mémoires archivés)
*/
CREATE INDEX idx_filiere_archive
ON memoire(id_filiere_archive);

/*
Filtrage par niveau (anciens mémoires archivés)
*/
CREATE INDEX idx_niveau_archive
ON memoire(id_niveau_archive);

/*
Filtrage par centre (anciens mémoires archivés)
*/
CREATE INDEX idx_centre_archive
ON memoire(id_centre_archive);

/*
Filtrage par année (anciens mémoires archivés)
*/
CREATE INDEX idx_annee_archive
ON memoire(id_annee_archive);
