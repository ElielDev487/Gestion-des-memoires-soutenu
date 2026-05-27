// File : public/assets/js/main.js

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

});