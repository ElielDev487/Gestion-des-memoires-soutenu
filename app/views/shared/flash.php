<?php
// File : app/views/shared/flash.php

$flash = Session::getFlash();
if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <p><?= htmlspecialchars($flash['message']) ?></p>
    </div>
<?php endif; ?>