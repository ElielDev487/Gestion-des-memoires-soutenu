<?php
// File : app/views/memoires/liste_de.php
// Liste de tous les mémoires pour la Direction des Études

ob_start();
?>

<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1>Mémoires</h1>
            <p>Tous les mémoires archivés et soumis sur la plateforme</p>
        </div>
        <a href="<?= BASE_URL ?>/de/memoires/archiver" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i>
            Archiver un mémoire
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>
            <i class="fa-solid fa-book"></i>
            Liste des mémoires
            <span class="count-badge"><?= count($memoires) ?></span>
        </h3>
    </div>

    <div class="card-body">

        <!-- Barre de recherche -->
        <div class="search-bar">
            <input
                type="text"
                data-search="true"
                data-table="memoiresTable"
                placeholder="Rechercher par titre, auteur, filière..."
            >
        </div>

        <?php if (empty($memoires)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-book-open fa-3x"></i>
                <p>Aucun mémoire archivé pour l'instant.</p>
                <a href="<?= BASE_URL ?>/de/memoires/archiver" class="btn btn-primary">
                    Archiver le premier mémoire
                </a>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table id="memoiresTable">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Filière</th>
                            <th>Niveau</th>
                            <th>Centre</th>
                            <th>Année</th>
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($memoires as $m): ?>
                            <tr>
                                <td data-label="Titre">
                                    <a href="<?= BASE_URL ?>/catalogue/memoire/<?= $m['id_memoire'] ?>" class="link-primary">
                                        <?= htmlspecialchars($m['titre']) ?>
                                    </a>
                                </td>
                                <td data-label="Auteur">
                                    <?= htmlspecialchars($m['auteur_prenom'] . ' ' . $m['auteur_nom']) ?>
                                </td>
                                <td data-label="Filière">
                                    <?= htmlspecialchars($m['filiere'] ?? '—') ?>
                                </td>
                                <td data-label="Niveau">
                                    <?= htmlspecialchars($m['niveau'] ?? '—') ?>
                                </td>
                                <td data-label="Centre">
                                    <?= htmlspecialchars($m['centre'] ?? '—') ?>
                                </td>
                                <td data-label="Année">
                                    <?= htmlspecialchars($m['annee'] ?? '—') ?>
                                </td>
                                <td data-label="Statut">
                                    <?php
                                    $statuts = [
                                        'publie'     => ['label' => 'Publié',     'class' => 'badge-green'],
                                        'en_attente' => ['label' => 'En attente', 'class' => 'badge-orange'],
                                        'refuse'     => ['label' => 'Refusé',     'class' => 'badge-red'],
                                    ];
                                    $st = $statuts[$m['statut']] ?? ['label' => $m['statut'], 'class' => 'badge-gray'];
                                    ?>
                                    <span class="badge <?= $st['class'] ?>">
                                        <?= $st['label'] ?>
                                    </span>
                                </td>
                                <td data-label="Date">
                                    <?= date('d/m/Y', strtotime($m['date_soumission'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
$title   = 'Mémoires';
require_once ROOT_PATH . '/app/views/layouts/main.php';
?>