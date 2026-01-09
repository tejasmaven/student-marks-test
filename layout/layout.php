<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_auth();

$page = $_GET['page'] ?? 'dashboard';
?>
<div class="container-fluid">
    <div class="row">
        <aside class="col-lg-2 bg-light sidebar">
            <?php include __DIR__ . '/menu.php'; ?>
        </aside>
        <main class="col-lg-10 p-4">
            <?php include __DIR__ . '/breadcrumbs.php'; ?>
            <?php include __DIR__ . '/alerts.php'; ?>
            <?php include $content_view; ?>
        </main>
    </div>
</div>
<?php
require_once __DIR__ . '/../includes/footer.php';
?>
