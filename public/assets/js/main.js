// File : public/assets/js/main.js
// JS global de la plateforme
// Contient : sidebar mobile, recherche générique, autocomplete professeur

document.addEventListener('DOMContentLoaded', function () {

    // ── Sidebar mobile ─────────────────────────────────────────
    // Toggle ouverture/fermeture via le bouton burger
    const burger  = document.querySelector('.burger-btn');
    const sidebar = document.getElementById('sidebar');

    if (burger && sidebar) {
        burger.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });

        // Fermer la sidebar en cliquant en dehors sur mobile
        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !burger.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });
    }

    // ── Recherche générique pour tous les tableaux ─────────────
    // Usage dans les vues :
    //   <input type="text" data-search="true" data-table="idTableau">
    // Pas besoin d'écrire du JS dans les vues, ça marche automatiquement
    const searchInputs = document.querySelectorAll('input[data-search="true"]');

    searchInputs.forEach(function (input) {
        input.addEventListener('keyup', function () {

            // Récupère l'id du tableau ciblé
            const tableId = input.getAttribute('data-table');
            const table   = document.getElementById(tableId);
            if (!table) return;

            const filter = input.value.toLowerCase();
            const rows   = table.querySelectorAll('tbody tr');

            // Affiche ou masque chaque ligne selon le filtre
            rows.forEach(function (row) {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    });

    // ── Autocomplete professeur ────────────────────────────────
    // Utilisé dans le formulaire d'archivage de mémoire
    // Appelle GET /api/professeurs?q=... et affiche les résultats
    const profSearch   = document.getElementById('prof_search');
    const profResults  = document.getElementById('prof_results');
    const profHidden   = document.getElementById('id_professeur');
    const profSelected = document.getElementById('prof_selected');
    const profLabel    = document.getElementById('prof_selected_label');

    if (profSearch) {

        // Lancer la recherche après 2 caractères saisis
        profSearch.addEventListener('input', function () {
            const q = this.value.trim();

            if (q.length < 2) {
                profResults.style.display = 'none';
                return;
            }

            // Appel AJAX vers l'API
            fetch(BASE_URL + '/api/professeurs?q=' + encodeURIComponent(q))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    profResults.innerHTML = '';

                    if (data.length === 0) {
                        profResults.innerHTML =
                            '<div class="autocomplete-item autocomplete-empty">Aucun résultat</div>';
                    } else {
                        // Construire les items du dropdown
                        data.forEach(function (prof) {
                            const item = document.createElement('div');
                            item.className = 'autocomplete-item';
                            item.textContent = prof.prenom + ' ' + prof.nom +
                                (prof.specialite ? ' — ' + prof.specialite : '');

                            // Sélectionner ce professeur au clic
                            item.addEventListener('click', function () {
                                selectProf(prof);
                            });

                            profResults.appendChild(item);
                        });
                    }

                    profResults.style.display = 'block';
                });
        });

        // Fermer le dropdown en cliquant ailleurs
        document.addEventListener('click', function (e) {
            if (profSearch && !profSearch.contains(e.target) &&
                profResults && !profResults.contains(e.target)) {
                profResults.style.display = 'none';
            }
        });
    }

    /**
     * Sélectionne un professeur depuis le dropdown
     * Remplace le champ de recherche par un tag de confirmation
     */
    window.selectProf = function (prof) {
        profHidden.value           = prof.id_professeur;
        profLabel.textContent      = prof.prenom + ' ' + prof.nom;
        profSelected.style.display = 'flex';
        profSearch.style.display   = 'none';
        profResults.style.display  = 'none';
    };

    /**
     * Efface la sélection du professeur
     * Remet le champ de recherche visible
     */
    window.clearProf = function () {
        profHidden.value           = '';
        profLabel.textContent      = '';
        profSelected.style.display = 'none';
        profSearch.style.display   = 'block';
        profSearch.value           = '';
    };

});