<?php

class PdfController {
    public function servir(int $id): void {
        Auth::requireLogin();

        $memoire = new Memoire();
        $memo     = $memoire->getById($id);

        if (!$memo || $memo['statut'] !== 'publie' || empty($memo['fichier_pdf'])) {
            http_response_code(404);
            require_once ROOT_PATH . '/app/views/shared/404.php';
            return;
        }

        $filePath = STORAGE_PATH . $memo['fichier_pdf'];

        if (!is_file($filePath) || !is_readable($filePath)) {
            http_response_code(404);
            require_once ROOT_PATH . '/app/views/shared/404.php';
            return;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        $stream = fopen($filePath, 'rb');
        if ($stream) {
            while (!feof($stream)) {
                echo fread($stream, 8192);
                flush();
            }
            fclose($stream);
        }
        exit;
    }
}
