<?php
// File : app/controllers/AuthController.php

class AuthController {

    public function showLogin(): void {
        // Si déjà connecté, rediriger vers le bon dashboard
        if (Auth::isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        require_once ROOT_PATH . '/app/views/auth/login.php';
    }

    public function login(): void {
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $errors   = [];

        if (empty($email))    $errors[] = 'L\'email est obligatoire.';
        if (empty($password)) $errors[] = 'Le mot de passe est obligatoire.';

        if (empty($errors)) {
            $utilisateur = new Utilisateur();
            $user = $utilisateur->findByEmail($email);

            if ($user && password_verify($password, $user['mot_de_passe'])) {
                if (!$user['actif']) {
                    $errors[] = 'Votre compte est désactivé. Contactez l\'administration.';
                } else {
                    // Créer la session
                    Session::set('id_utilisateur', $user['id_utilisateur']);
                    Session::set('role',           $user['role']);
                    Session::set('email',          $user['email']);
                    $this->redirectToDashboard();
                    return;
                }
            } else {
                $errors[] = 'Email ou mot de passe incorrect.';
            }
        }

        // Retourner à la vue avec les erreurs
        require_once ROOT_PATH . '/app/views/auth/login.php';
    }

    public function logout(): void {
        Session::destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    private function redirectToDashboard(): void {
        $role = Session::get('role');
        $routes = [
            'admin'      => BASE_URL . '/admin/dashboard',
            'de'         => BASE_URL . '/de/dashboard',
            'professeur' => BASE_URL . '/professeur/dashboard',
            'etudiant'   => BASE_URL . '/etudiant/dashboard',
        ];
        header('Location: ' . ($routes[$role] ?? BASE_URL . '/login'));
        exit;
    }
}