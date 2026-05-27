<?php
// File : app/views/admin/utilisateurs.php
// Vue liste de tous les utilisateurs de la plateforme
// Accessible uniquement par le rôle admin

ob_start();
?>

<div class="page-header">
    <h1>Utilisateurs</h1>
    <p>Liste de tous les comptes de la plateforme</p>
</div>

<div class="card">

    <div class="card-header">
        <h3>
            <i class="fa-solid fa-users"></i>
            Tous les utilisateurs
        </h3>
    </div>

    <div class="card-body">

        <!-- Barre de recherche — liée au tableau via data-table -->
        <div class="search-bar">
            <input
                type="text"
                data-search="true"
                data-table="usersTable"
                placeholder="Rechercher par email ou rôle..."
            >
        </div>

        <div class="table-wrap">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Date création</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $u): ?>
                        <tr>
                            <!-- ID utilisateur -->
                            <td data-label="#">
                                <?= $u['id_utilisateur'] ?>
                            </td>

                            <!-- Email — échappé pour éviter XSS -->
                            <td data-label="Email">
                                <?= htmlspecialchars($u['email']) ?>
                            </td>

                            <!-- Rôle avec badge coloré -->
                            <td data-label="Rôle">
                                <?php
                                // Correspondance rôle → label + classe CSS
                                $roleLabels = [
                                    'admin'      => ['label' => 'Admin',      'class' => 'badge-navy'],
                                    'de'         => ['label' => 'DE',         'class' => 'badge-blue'],
                                    'professeur' => ['label' => 'Professeur', 'class' => 'badge-green'],
                                    'etudiant'   => ['label' => 'Étudiant',   'class' => 'badge-gray'],
                                ];
                                $r = $roleLabels[$u['role']] ?? ['label' => $u['role'], 'class' => 'badge-gray'];
                                ?>
                                <span class="badge <?= $r['class'] ?>">
                                    <?= $r['label'] ?>
                                </span>
                            </td>

                            <!-- Statut actif / inactif -->
                            <td data-label="Statut">
                                <?php if ($u['actif']): ?>
                                    <span class="badge badge-green">Actif</span>
                                <?php else: ?>
                                    <span class="badge badge-red">Inactif</span>
                                <?php endif; ?>
                            </td>

                            <!-- Date formatée en français -->
                            <td data-label="Date création">
                                <?= date('d/m/Y', strtotime($u['date_creation'])) ?>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php
// Capture le contenu et injecte dans le layout principal
$content = ob_get_clean();
$title   = 'Utilisateurs';
require_once ROOT_PATH . '/app/views/layouts/main.php';
?>