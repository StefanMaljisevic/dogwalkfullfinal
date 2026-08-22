<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/classes/User.php';
$userService = new User();
$success = $userService->activate($_GET['token'] ?? '');
?>
<div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?>">
    <?= $success ? 'Nalog je uspešno aktiviran. Sada možeš da se prijaviš.' : 'Aktivacioni link nije ispravan.' ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
