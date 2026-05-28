<?php
// File : app/views/memoires/archiver.php
// Formulaire d'archivage d'un ancien mémoire par le DE
// Le professeur directeur est optionnel (anciens mémoires)
// Le mémoire est publié directement sans validation

ob_start();
?>

<div class="page-header">
    <h1>Archiver un mémoire</h1>
    <p>Enregistrement d'un mémoire existant — publié directement dans le catalogue</p>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>
            <i class="fa-solid fa-file-arrow-up"></i>
            Informations du mémoire
        </h3>
    </div>

    <div class="card-body">

        <div class="alert alert-info">
            <p>
                <i class="fa-solid fa-circle-info"></i>
                Ce mémoire sera <strong>publié directement</strong> dans le catalogue sans validation.
            </p>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/de/memoires/archiver" enctype="multipart/form-data">

            <!-- Titre -->
            <div class="form-group">
                <label for="titre">
                    Titre du mémoire <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="titre"
                    name="titre"
                    placeholder="Titre complet du mémoire"
                    value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
                    required
                >
            </div>

            <!-- Thème -->
            <div class="form-group">
                <label for="theme">
                    Thème <span class="required">*</span>
                    <span class="optional">— domaine du mémoire (ex : Intelligence Artificielle, Droit Pénal...)</span>
                </label>
                <input
                    type="text"
                    id="theme"
                    name="theme"
                    placeholder="Ex : Intelligence Artificielle, Blockchain, Droit Pénal..."
                    value="<?= htmlspecialchars($_POST['theme'] ?? '') ?>"
                    required
                >
            </div>

            <!-- Auteur -->
            <div class="form-row">
                <div class="form-group">
                    <label for="auteur_prenom">
                        Prénom de l'auteur <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="auteur_prenom"
                        name="auteur_prenom"
                        placeholder="Prénom"
                        value="<?= htmlspecialchars($_POST['auteur_prenom'] ?? '') ?>"
                        required
                    >
                </div>
                <div class="form-group">
                    <label for="auteur_nom">
                        Nom de l'auteur <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="auteur_nom"
                        name="auteur_nom"
                        placeholder="Nom de famille"
                        value="<?= htmlspecialchars($_POST['auteur_nom'] ?? '') ?>"
                        required
                    >
                </div>
            </div>

            <!-- Filière + Niveau -->
            <div class="form-row">
                <div class="form-group">
                    <label for="id_filiere">
                        Filière <span class="required">*</span>
                    </label>
                    <select id="id_filiere" name="id_filiere" required>
                        <option value="">-- Sélectionner une filière --</option>
                        <?php foreach ($filieres as $f): ?>
                            <option
                                value="<?= $f['id_filiere'] ?>"
                                <?= (($_POST['id_filiere'] ?? '') == $f['id_filiere']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($f['nom_filiere']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_niveau">
                        Niveau <span class="required">*</span>
                    </label>
                    <select id="id_niveau" name="id_niveau" required>
                        <option value="">-- Sélectionner un niveau --</option>
                        <?php foreach ($niveaux as $n): ?>
                            <option
                                value="<?= $n['id_niveau'] ?>"
                                <?= (($_POST['id_niveau'] ?? '') == $n['id_niveau']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($n['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Centre + Année -->
            <div class="form-row">
                <div class="form-group">
                    <label for="id_centre">
                        Centre <span class="required">*</span>
                    </label>
                    <select id="id_centre" name="id_centre" required>
                        <option value="">-- Sélectionner un centre --</option>
                        <?php foreach ($centres as $c): ?>
                            <option
                                value="<?= $c['id_centre'] ?>"
                                <?= (($_POST['id_centre'] ?? '') == $c['id_centre']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($c['nom_centre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_annee">
                        Année académique <span class="required">*</span>
                    </label>
                    <select id="id_annee" name="id_annee" required>
                        <option value="">-- Sélectionner une année --</option>
                        <?php foreach ($annees as $a): ?>
                            <option
                                value="<?= $a['id_annee_academique'] ?>"
                                <?= (($_POST['id_annee'] ?? '') == $a['id_annee_academique']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($a['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Professeur directeur — autocomplete -->
            <div class="form-group">
                <label>
                    Professeur directeur
                    <span class="optional">— optionnel, laissez vide si inconnu</span>
                </label>

                <!-- Champ de recherche visible -->
                <input
                    type="text"
                    id="prof_search"
                    placeholder="Taper le nom du professeur pour rechercher..."
                    autocomplete="off"
                >

                <!-- Dropdown des résultats -->
                <div id="prof_results" class="autocomplete-dropdown" style="display:none"></div>

                <!-- Champ caché soumis avec le formulaire -->
                <input
                    type="hidden"
                    id="id_professeur"
                    name="id_professeur"
                    value="<?= htmlspecialchars($_POST['id_professeur'] ?? '') ?>"
                >

                <!-- Tag affiché après sélection -->
                <div id="prof_selected" class="selected-tag" style="display:none">
                    <span id="prof_selected_label"></span>
                    <button type="button" onclick="clearProf()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

            </div>

            <!-- Résumé -->
            <div class="form-group">
                <label for="resume">
                    Résumé <span class="optional">(facultatif)</span>
                </label>
                <textarea
                    id="resume"
                    name="resume"
                    placeholder="Résumé du mémoire (500 mots maximum)..."
                    rows="4"
                ><?= htmlspecialchars($_POST['resume'] ?? '') ?></textarea>
            </div>

            <!-- Upload PDF -->
            <div class="form-group">
                <label>
                    Fichier PDF <span class="required">*</span>
                    <span class="optional">(10 Mo max.)</span>
                </label>
                <div class="upload-zone" onclick="document.getElementById('fichier_pdf').click()">
                    <i class="fa-solid fa-file-pdf fa-2x" style="color: var(--red)"></i>
                    <p id="upload-label">Glissez votre PDF ici ou cliquez pour parcourir</p>
                </div>
                <input
                    type="file"
                    id="fichier_pdf"
                    name="fichier_pdf"
                    accept="application/pdf"
                    style="display: none"
                    onchange="updateUploadLabel(this)"
                    required
                >
            </div>

            <!-- Boutons -->
            <div class="form-actions">
                <a href="<?= BASE_URL ?>/de/memoires" class="btn btn-outline">
                    <i class="fa-solid fa-xmark"></i>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Archiver et publier
                </button>
            </div>

        </form>
    </div>
</div>

<script>
// Mise à jour du label de la zone upload quand un fichier est sélectionné
function updateUploadLabel(input) {
    const label = document.getElementById('upload-label');
    if (input.files && input.files[0]) {
        label.textContent = '📄 ' + input.files[0].name;
    }
}
</script>

<?php
$content = ob_get_clean();
$title   = 'Archiver un mémoire';
require_once ROOT_PATH . '/app/views/layouts/main.php';
?>