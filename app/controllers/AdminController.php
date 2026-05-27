<?php
// File : app/controllers/AdminController.php

class AdminController {

    public function dashboard(): void {
        Auth::requireRole('admin');

        $utilisateur = new Utilisateur();
        $stats = $utilisateur->getStats();

        require_once ROOT_PATH . '/app/views/admin/dashboard.php';
    }

    public function utilisateurs(): void {
        Auth::requireRole('admin');

        $utilisateur = new Utilisateur();
        $utilisateurs = $utilisateur->getAll();

        require_once ROOT_PATH . '/app/views/admin/utilisateurs.php';
    }
}