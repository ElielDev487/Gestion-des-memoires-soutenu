<?php
// File : app/views/admin/dashboard.php

ob_start();
?>

<div class="page-header">
    <h1>Dashboard Administrateur</h1>
    <p>Vue d'ensemble de la plateforme</p>
</div>

<div class="stats-grid">
    <div class="stat-card stat-blue">
        <span class="stat-value"><?= $stats['total_utilisateurs'] ?></span>
        <span class="stat-label">Utilisateurs actifs</span>
    </div>
    <div class="stat-card stat-green">
        <span class="stat-value"><?= $stats['total_memoires'] ?></span>
        <span class="stat-label">Mémoires publiés</span>
    </div>
    <div class="stat-card stat-orange">
        <span class="stat-value"><?= $stats['en_attente'] ?></span>
        <span class="stat-label">En attente</span>
    </div>
    <div class="stat-card stat-navy">
        <span class="stat-value"><?= $stats['total_filieres'] ?></span>
        <span class="stat-label">Filières</span>
    </div>
</div>

<?php
$content = ob_get_clean();
$title   = 'Dashboard Admin';
require_once ROOT_PATH . '/app/views/layouts/main.php';
?>