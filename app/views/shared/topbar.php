<!-- File : app/views/shared/topbar.php -->

<div class="topbar">
    <div class="topbar-title">
        <h2><?= $title ?? APP_NAME ?></h2>
    </div>
    <div class="topbar-actions">
        <button class="burger-btn" onclick="toggleSidebar()">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</div>