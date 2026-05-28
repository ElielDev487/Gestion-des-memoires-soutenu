<?php
// File : config/routes.php

// Auth
$router->add('GET',  'login',                       'AuthController',        'showLogin');
$router->add('POST', 'login',                       'AuthController',        'login');
$router->add('GET',  'logout',                      'AuthController',        'logout');

// Admin
$router->add('GET',  'admin/dashboard',             'AdminController',       'dashboard');
$router->add('GET',  'admin/utilisateurs',          'AdminController',       'utilisateurs');

// DE
$router->add('GET',  'de/dashboard',                'DEController',          'dashboard');
$router->add('GET',  'de/etudiants',                'EtudiantController',    'liste');
$router->add('POST', 'de/etudiants/creer',          'EtudiantController',    'creer');
$router->add('POST', 'de/etudiants/diplomer/{id}',  'EtudiantController',    'diplomer');
$router->add('GET',  'de/professeurs',              'ProfesseurController',  'liste');
$router->add('POST', 'de/professeurs/creer',        'ProfesseurController',  'creer');
$router->add('GET',  'de/referentiel',              'ReferentielController', 'index');
$router->add('POST', 'de/filieres/creer',           'ReferentielController', 'creerFiliere');
$router->add('POST', 'de/niveaux/creer',            'ReferentielController', 'creerNiveau');
$router->add('POST', 'de/centres/creer',            'ReferentielController', 'creerCentre');
$router->add('POST', 'de/annees/creer',             'ReferentielController', 'creerAnnee');
$router->add('POST', 'de/filieres/supprimer/{id}',  'ReferentielController', 'supprimerFiliere');
$router->add('POST', 'de/niveaux/supprimer/{id}',   'ReferentielController', 'supprimerNiveau');
$router->add('POST', 'de/centres/supprimer/{id}',   'ReferentielController', 'supprimerCentre');
$router->add('GET',  'de/memoires',                 'MemoireController',     'listeDe');
$router->add('GET',  'de/memoires/archiver',        'MemoireController',     'showArchiver');
$router->add('POST', 'de/memoires/archiver',        'MemoireController',     'archiver');

// Professeur
$router->add('GET',  'professeur/dashboard',        'ProfesseurController',  'dashboard');
$router->add('GET',  'professeur/memoires',         'ProfesseurController',  'listeMemoiresProf');
$router->add('POST', 'professeur/valider/{id}',     'ProfesseurController',  'valider');
$router->add('POST', 'professeur/refuser/{id}',     'ProfesseurController',  'refuser');
// API — Autocomplete professeur (utilisé par le formulaire d'archivage)
$router->add('GET', 'api/professeurs', 'ProfesseurController', 'search');

// Etudiant
$router->add('GET',  'etudiant/dashboard',          'MemoireController',     'dashboardEtudiant');
$router->add('GET',  'etudiant/mon-memoire',        'MemoireController',     'monMemoire');
$router->add('GET',  'etudiant/soumettre',          'MemoireController',     'showSoumettre');
$router->add('POST', 'etudiant/soumettre',          'MemoireController',     'soumettre');

// Catalogue
$router->add('GET',  'catalogue',                   'CatalogueController',   'index');
$router->add('GET',  'catalogue/recherche',         'CatalogueController',   'recherche');
$router->add('GET',  'catalogue/memoire/{id}',      'CatalogueController',   'detail');

// PDF proxy
$router->add('GET',  'pdf/{id}',                    'PdfController',         'servir');

// Interactions AJAX
$router->add('POST', 'like/{id}',                   'LikeController',        'toggle');
$router->add('POST', 'commentaire/{id}',            'CommentaireController', 'ajouter');
