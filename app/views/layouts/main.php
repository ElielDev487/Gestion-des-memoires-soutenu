<!-- File : app/views/layouts/main.php -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="app-layout">

    <?php require_once ROOT_PATH . '/app/views/shared/sidebar.php'; ?>

    <div class="main-content">
        <?php require_once ROOT_PATH . '/app/views/shared/topbar.php'; ?>

        <div class="page-content">
            <?php require_once ROOT_PATH . '/app/views/shared/flash.php'; ?>

            <?= $content ?>

        </div>
    </div>
</div>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>