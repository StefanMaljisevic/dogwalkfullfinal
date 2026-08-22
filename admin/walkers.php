<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../classes/Walker.php';
require_once __DIR__ . '/../includes/header.php';
$walkerService = new Walker();
if (isPostRequest()) {
    $walkerService->setApprovalStatus((int) ($_POST['id'] ?? 0), (int) ($_POST['status'] ?? 0));
}
$walkers = $walkerService->getAllForAdmin();
?>
<h1 class="h3 mb-4">Admin - šetači pasa</h1>
<p><a href="users.php" class="btn btn-outline-primary btn-sm">Upravljanje korisnicima</a></p>
<div class="table-responsive"><table class="table table-striped align-middle">
<tr><th>Ime</th><th>Email</th><th>Omiljena rasa</th><th>Odobren</th><th>Akcija</th></tr>
<?php foreach ($walkers as $walker): ?>
<tr>
<td><?= cleanInput($walker['first_name'] . ' ' . $walker['last_name']) ?></td>
<td><?= cleanInput($walker['email']) ?></td>
<td><?= cleanInput($walker['favorite_breed'] ?: 'Nije navedeno') ?></td>
<td><?= (int)$walker['is_approved'] ? 'Da' : 'Ne' ?></td>
<td><form method="post"><input type="hidden" name="id" value="<?= (int)$walker['id'] ?>"><input type="hidden" name="status" value="<?= (int)$walker['is_approved'] ? 0 : 1 ?>"><button class="btn btn-sm <?= (int)$walker['is_approved'] ? 'btn-warning' : 'btn-success' ?>"><?= (int)$walker['is_approved'] ? 'Deaktiviraj' : 'Aktiviraj' ?></button></form></td>
</tr>
<?php endforeach; ?>
</table></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
