<?php
// File : app/views/catalogue/index.php
// Liste des mémoires publiés avec recherche avancée et filtres
// Accessible à tous les utilisateurs connectés

ob_start();
?>

<!-- En-tête avec barre de recherche -->
<div class="catalogue-hero">
    <div class="catalogue-hero-content">
        <h1>Catalogue des mémoires</h1>
        <p><?= number_format($totalMemoires, 0, ',', ' ') ?> mémoire<?= $totalMemoires > 1 ? 's' : '' ?> publié<?= $totalMemoires > 1 ? 's' : '' ?></p>

        <!-- Barre de recherche principale -->
        <form method="GET" action="<?= BASE_URL ?>/catalogue" class="catalogue-search-form">
            <div class="catalogue-search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    type="text"
                    name="q"
                    placeholder="Rechercher par titre, thème, auteur..."
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                    autocomplete="off"
                >
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </form>
    </div>
</div>

<!-- Filtres avancés -->
<div class="catalogue-filters">
    <form method="GET" action="<?= BASE_URL ?>/catalogue" id="filtresForm">

        <!-- Conserver la recherche textuelle -->
        <?php if (!empty($_GET['q'])): ?>
            <input type="hidden" name="q" value="<?= htmlspecialchars($_GET['q']) ?>">
        <?php endif; ?>

        <div class="filters-row">

            <!-- Filière -->
            <select name="id_filiere" onchange="document.getElementById('filtresForm').submit()">
                <option value="">Toutes les filières</option>
                <?php foreach ($filieres as $f): ?>
                    <option
                        value="<?= $f['id_filiere'] ?>"
                        <?= (($_GET['id_filiere'] ?? '') == $f['id_filiere']) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($f['nom_filiere']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Niveau -->
            <select name="id_niveau" onchange="document.getElementById('filtresForm').submit()">
                <option value="">Tous les niveaux</option>
                <?php foreach ($niveaux as $n): ?>
                    <option
                        value="<?= $n['id_niveau'] ?>"
                        <?= (($_GET['id_niveau'] ?? '') == $n['id_niveau']) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($n['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Centre -->
            <select name="id_centre" onchange="document.getElementById('filtresForm').submit()">
                <option value="">Tous les centres</option>
                <?php foreach ($centres as $c): ?>
                    <option
                        value="<?= $c['id_centre'] ?>"
                        <?= (($_GET['id_centre'] ?? '') == $c['id_centre']) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($c['nom_centre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Année -->
            <select name="id_annee" onchange="document.getElementById('filtresForm').submit()">
                <option value="">Toutes les années</option>
                <?php foreach ($annees as $a): ?>
                    <option
                        value="<?= $a['id_annee_academique'] ?>"
                        <?= (($_GET['id_annee'] ?? '') == $a['id_annee_academique']) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($a['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Réinitialiser les filtres -->
            <?php if ($isRecherche): ?>
                <a href="<?= BASE_URL ?>/catalogue" class="btn btn-outline">
                    <i class="fa-solid fa-xmark"></i>
                    Effacer
                </a>
            <?php endif; ?>

        </div>
    </form>
</div>

<!-- Résultats -->
<div class="catalogue-results">

    <?php if ($isRecherche): ?>
        <p class="results-count">
            <strong><?= $totalMemoires ?></strong> résultat<?= $totalMemoires > 1 ? 's' : '' ?> trouvé<?= $totalMemoires > 1 ? 's' : '' ?>
        </p>
    <?php endif; ?>

    <?php if (empty($memoires)): ?>
        <!-- État vide -->
        <div class="empty-state">
            <i class="fa-solid fa-book-open fa-3x"></i>
            <p>Aucun mémoire trouvé.</p>
            <?php if ($isRecherche): ?>
                <a href="<?= BASE_URL ?>/catalogue" class="btn btn-outline">
                    Voir tous les mémoires
                </a>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- Grille des cards -->
        <div class="memoires-grid">
            <?php foreach ($memoires as $m): ?>
                <div class="memoire-card">
                    <div class="memoire-card-top"></div>
                    <div class="memoire-card-body">
                        <h3><?= htmlspecialchars($m['titre']) ?></h3>
                        <p class="author">
                            <i class="fa-solid fa-user"></i>
                            <?= htmlspecialchars($m['auteur_prenom'] . ' ' . $m['auteur_nom']) ?>
                        </p>
                        <p class="meta">
                            <?= htmlspecialchars($m['filiere'] ?? '—') ?>
                            · <?= htmlspecialchars($m['niveau'] ?? '—') ?>
                            · <?= htmlspecialchars($m['centre'] ?? '—') ?>
                        </p>
                        <p class="meta">
                            <?= htmlspecialchars($m['annee'] ?? '—') ?>
                            <?php if (!empty($m['professeur'])): ?>
                                · <?= htmlspecialchars($m['professeur']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="memoire-card-footer">
                        <span class="interactions">
                            <i class="fa-solid fa-heart"></i> <?= $m['nb_likes'] ?>
                            &nbsp;
                            <i class="fa-solid fa-comment"></i> <?= $m['nb_commentaires'] ?>
                        </span>
                        <a href="<?= BASE_URL ?>/catalogue/memoire/<?= $m['id_memoire'] ?>" class="btn btn-primary btn-sm">
                            Consulter
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?= $i ?></span>
                    <?php elseif ($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    <?php elseif (abs($i - $page) == 3): ?>
                        <span>…</span>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$title   = 'Catalogue des mémoires';
require_once ROOT_PATH . '/app/views/layouts/main.php';
?>