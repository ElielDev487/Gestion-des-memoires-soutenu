<?php
// File : core/Auth.php

class Auth {
    public static function isLoggedIn(): bool {
        return isset($_SESSION['id_utilisateur']);
    }

    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public static function requireRole(string ...$roles): void {
        self::requireLogin();
        if (!in_array($_SESSION['role'], $roles)) {
            http_response_code(403);
            require_once ROOT_PATH . '/app/views/shared/403.php';
            exit;
        }
    }

    public static function role(): string {
        return $_SESSION['role'] ?? '';
    }

    public static function id(): ?int {
        return $_SESSION['id_utilisateur'] ?? null;
    }
}
