<?php
// File : app/views/shared/sidebar.php

$role    = Session::get('role');
$email   = Session::get('email');
$initial = strtoupper(substr($email, 0, 1));

$menus = [
    'admin' => [
        ['url' => '/admin/dashboard',    'label' => 'Dashboard',    'icon' => 'fa-solid fa-gauge'],
        ['url' => '/admin/utilisateurs', 'label' => 'Utilisateurs', 'icon' => 'fa-solid fa-users'],
    ],
    'de' => [
        ['url' => '/de/dashboard',    'label' => 'Dashboard',                  'icon' => 'fa-solid fa-gauge'],
        ['url' => '/de/etudiants',    'label' => 'Étudiants',                  'icon' => 'fa-solid fa-user-graduate'],
        ['url' => '/de/professeurs',  'label' => 'Professeurs',                'icon' => 'fa-solid fa-chalkboard-user'],
        ['url' => '/de/memoires',     'label' => 'Mémoires',                   'icon' => 'fa-solid fa-book'],
        ['url' => '/de/referentiel',  'label' => 'Filières / Niveaux / Centres','icon' => 'fa-solid fa-building-columns'],
    ],
    'professeur' => [
        ['url' => '/professeur/dashboard', 'label' => 'Dashboard',   'icon' => 'fa-solid fa-gauge'],
        ['url' => '/professeur/memoires',  'label' => 'Mes mémoires','icon' => 'fa-solid fa-inbox'],
    ],
    'etudiant' => [
        ['url' => '/etudiant/dashboard',   'label' => 'Accueil',    'icon' => 'fa-solid fa-house'],
        ['url' => '/catalogue',            'label' => 'Catalogue',  'icon' => 'fa-solid fa-book-open'],
        ['url' => '/etudiant/soumettre',   'label' => 'Soumettre',  'icon' => 'fa-solid fa-upload'],
        ['url' => '/etudiant/mon-memoire', 'label' => 'Mon mémoire','icon' => 'fa-solid fa-folder'],
    ],
];

$currentMenu = $menus[$role] ?? [];
$currentUrl  = '/' . trim(str_replace(BASE_URL, '', strtok($_SERVER['REQUEST_URI'], '?')), '/');
?>

<aside class="sidebar" id="sidebar">

    <div class="sidebar-logo">
        <span class="logo-circle">M</span>
        <div>
            <div class="logo-text">Mémoires</div>
            <div class="logo-sub">Plateforme académique</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($currentMenu as $item): ?>
            <a href="<?= BASE_URL . $item['url'] ?>"
               class="<?= str_contains($currentUrl, $item['url']) ? 'active' : '' ?>">
                <i class="<?= $item['icon'] ?>"></i>
                <?= $item['label'] ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-bottom">
        <a href="<?= BASE_URL ?>/logout" class="nav-logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            Déconnexion
        </a>
        <div class="sidebar-user">
            <div class="user-avatar"><?= $initial ?></div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($email) ?></div>
                <div class="user-role"><?= $role ?></div>
            </div>
        </div>
    </div>

</aside>