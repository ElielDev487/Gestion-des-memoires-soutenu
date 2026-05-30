<?php
// File : app/views/catalogue/detail.php
// Page détail d'un mémoire — infos complètes + likes + commentaires + lien PDF

ob_start();
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="<?= BASE_URL ?>/catalogue">
        <i class="fa-solid fa-book-open"></i> Catalogue
    </a>
    <i class="fa-solid fa-chevron-right"></i>
    <span><?= htmlspecialchars($memo['titre']) ?></span>
</div>

<div class="detail-layout">

    <!-- Colonne principale -->
    <div class="detail-main">

        <!-- En-tête mémoire -->
        <div class="card detail-header-card">
            <div class="detail-header">
                <div>
                    <span class="badge badge-green">
                        <i class="fa-solid fa-circle-check"></i> Publié
                    </span>
                    <?php if (!empty($memo['niveau'])): ?>
                        <span class="badge badge-blue"><?= htmlspecialchars($memo['niveau']) ?></span>
                    <?php endif; ?>
                </div>
                <h1><?= htmlspecialchars($memo['titre']) ?></h1>
                <p class="detail-author">
                    <i class="fa-solid fa-user-graduate"></i>
                    <?= htmlspecialchars($memo['auteur_prenom'] . ' ' . $memo['auteur_nom']) ?>
                </p>
            </div>
        </div>

        <!-- Résumé -->
        <?php if (!empty($memo['resume'])): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fa-solid fa-align-left"></i> Résumé</h3>
                </div>
                <div class="card-body">
                    <p class="detail-resume"><?= nl2br(htmlspecialchars($memo['resume'])) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Bouton lecture PDF -->
        <div class="card detail-read-card">
            <div class="card-body detail-read-body">
                <div>
                    <h3><i class="fa-solid fa-file-pdf"></i> Lire le mémoire</h3>
                    <p>Consultation sécurisée — téléchargement désactivé</p>
                </div>
                <a href="<?= BASE_URL ?>/pdf/<?= $memo['id_memoire'] ?>" class="btn btn-primary" target="_blank">
                    <i class="fa-solid fa-book-open-reader"></i>
                    Ouvrir le lecteur
                </a>
            </div>
        </div>

        <!-- Commentaires -->
        <div class="card" id="commentaires">
            <div class="card-header">
                <h3>
                    <i class="fa-solid fa-comments"></i>
                    Commentaires
                    <span class="count-badge" id="comment-count"><?= $memo['nb_commentaires'] ?></span>
                </h3>
            </div>
            <div class="card-body">

                <!-- Formulaire ajout commentaire -->
                <div class="comment-form">
                    <textarea
                        id="comment-input"
                        placeholder="Laissez un commentaire..."
                        rows="3"
                        maxlength="1000"
                    ></textarea>
                    <div class="comment-form-actions">
                        <span class="char-count" id="char-count">0 / 1000</span>
                        <button
                            class="btn btn-primary btn-sm"
                            onclick="submitComment(<?= $memo['id_memoire'] ?>)"
                        >
                            <i class="fa-solid fa-paper-plane"></i>
                            Commenter
                        </button>
                    </div>
                </div>

                <!-- Liste commentaires -->
                <div id="comments-list">
                    <?php if (empty($commentaires)): ?>
                        <p class="no-comments" id="no-comments-msg">
                            Aucun commentaire pour l'instant. Soyez le premier !
                        </p>
                    <?php else: ?>
                        <?php foreach ($commentaires as $c): ?>
                            <div class="comment-item">
                                <div class="comment-avatar">
                                    <?= strtoupper(substr($c['email'], 0, 1)) ?>
                                </div>
                                <div class="comment-content">
                                    <div class="comment-meta">
                                        <strong><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></strong>
                                        <span class="badge badge-gray"><?= $c['role'] ?></span>
                                        <span class="comment-date">
                                            <?= date('d/m/Y à H:i', strtotime($c['date_commentaire'])) ?>
                                        </span>
                                    </div>
                                    <p><?= nl2br(htmlspecialchars($c['contenu'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>

    <!-- Sidebar infos -->
    <div class="detail-sidebar">

        <!-- Like -->
        <div class="card like-card">
            <div class="card-body like-body">
                <button
                    class="like-btn <?= $hasLiked ? 'liked' : '' ?>"
                    id="like-btn"
                    onclick="toggleLike(<?= $memo['id_memoire'] ?>)"
                >
                    <i class="fa-<?= $hasLiked ? 'solid' : 'regular' ?> fa-heart"></i>
                    <span id="like-count"><?= $memo['nb_likes'] ?></span>
                    J'aime
                </button>
            </div>
        </div>

        <!-- Infos académiques -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-graduation-cap"></i> Informations</h3>
            </div>
            <div class="card-body info-list">
                <?php
                $infos = [
                    ['label' => 'Filière',    'icon' => 'fa-book',            'value' => $memo['filiere']    ?? null],
                    ['label' => 'Niveau',     'icon' => 'fa-layer-group',     'value' => $memo['niveau']     ?? null],
                    ['label' => 'Centre',     'icon' => 'fa-building',        'value' => $memo['centre']     ?? null],
                    ['label' => 'Année',      'icon' => 'fa-calendar',        'value' => $memo['annee']      ?? null],
                    ['label' => 'Thème',      'icon' => 'fa-tag',             'value' => $memo['theme']      ?? null],
                    ['label' => 'Directeur',  'icon' => 'fa-chalkboard-user', 'value' => $memo['professeur'] ?? null],
                ];
                foreach ($infos as $info):
                    if (empty($info['value'])) continue;
                ?>
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fa-solid <?= $info['icon'] ?>"></i>
                            <?= $info['label'] ?>
                        </span>
                        <span class="info-value"><?= htmlspecialchars($info['value']) ?></span>
                    </div>
                <?php endforeach; ?>

                <?php if (!empty($memo['date_publication'])): ?>
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fa-solid fa-clock"></i>
                            Publié le
                        </span>
                        <span class="info-value">
                            <?= date('d/m/Y', strtotime($memo['date_publication'])) ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<?php
$content = ob_get_clean();
$title   = htmlspecialchars($memo['titre']);
require_once ROOT_PATH . '/app/views/layouts/main.php';
?>