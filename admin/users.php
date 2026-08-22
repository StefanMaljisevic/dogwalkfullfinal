<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../includes/header.php';
$userService = new User();
if (isPostRequest()) {
    $userService->setBlockedStatus((int) ($_POST['id'] ?? 0), (int) ($_POST['status'] ?? 0));
}
$users = $userService->getAllUsers();
?>
<h1 class="h3 mb-4">Admin - korisnici</h1>
<p><a href="walkers.php" class="btn btn-outline-primary btn-sm">Upravljanje šetačima</a></p>
<div class="table-responsive"><table class="table table-striped align-middle">
<tr><th>Email</th><th>Ime</th><th>Uloga</th><th>Aktivan</th><th>Blokiran</th><th>Akcija</th></tr>
<?php foreach ($users as $user): ?>
<tr>
<td><?= cleanInput($user['email']) ?></td>
<td><?= cleanInput($user['first_name'] . ' ' . $user['last_name']) ?></td>
<td><?= cleanInput($user['role']) ?></td>
<td><?= (int)$user['is_active'] ? 'Da' : 'Ne' ?></td>
<td><?= (int)$user['is_blocked'] ? 'Da' : 'Ne' ?></td>
<td>
<?php if ($user['role'] !== 'admin'): ?>
<form method="post" class="d-inline">
<input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
<input type="hidden" name="status" value="<?= (int)$user['is_blocked'] ? 0 : 1 ?>">
<button class="btn btn-sm <?= (int)$user['is_blocked'] ? 'btn-success' : 'btn-danger' ?>"><?= (int)$user['is_blocked'] ? 'Odblokiraj' : 'Blokiraj' ?></button>
</form>
<?php endif; ?>
</td></tr>
<?php endforeach; ?>
</table></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
