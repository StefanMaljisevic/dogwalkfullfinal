<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/includes/header.php';
$userService = new User();
$user = $userService->findById(currentUserId());
$message = '';

if (isPostRequest()) {
    $action = $_POST['action'] ?? 'profile';
    if ($action === 'password') {
        $result = $userService->changePassword(
            currentUserId(),
            $_POST['current_password'] ?? '',
            $_POST['new_password'] ?? '',
            $_POST['confirm_new_password'] ?? ''
        );
    } else {
        $result = $userService->updateProfile(currentUserId(), [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'address' => $_POST['address'] ?? '',
        ]);
        $user = $userService->findById(currentUserId());
        $_SESSION['first_name'] = $user['first_name'] ?? $_SESSION['first_name'];
    }
    $message = '<div class="alert ' . ($result['success'] ? 'alert-success' : 'alert-danger') . '">' . cleanInput($result['message']) . '</div>';
}
?>
<div class="row justify-content-center g-4">
    <div class="col-lg-7">
        <h1 class="h3 mb-4">Moj profil</h1>
        <?= $message ?>
        <form method="post" class="card card-body shadow-sm needs-validation" novalidate>
            <input type="hidden" name="action" value="profile">
            <label class="form-label">Ime</label>
            <input class="form-control mb-3" name="first_name" value="<?= cleanInput($user['first_name']) ?>" minlength="2" required>
            <label class="form-label">Prezime</label>
            <input class="form-control mb-3" name="last_name" value="<?= cleanInput($user['last_name']) ?>" minlength="2" required>
            <label class="form-label">Telefon</label>
            <input class="form-control mb-3" name="phone" value="<?= cleanInput($user['phone'] ?? '') ?>">
            <label class="form-label">Adresa</label>
            <input class="form-control mb-3" name="address" value="<?= cleanInput($user['address'] ?? '') ?>">
            <button class="btn btn-primary">Sačuvaj profil</button>
        </form>
    </div>
    <div class="col-lg-5">
        <h2 class="h4 mb-4">Promena lozinke</h2>
        <form method="post" class="card card-body shadow-sm needs-validation" novalidate>
            <input type="hidden" name="action" value="password">
            <label class="form-label">Trenutna lozinka</label>
            <input class="form-control mb-3" type="password" name="current_password" required>
            <label class="form-label">Nova lozinka</label>
            <input class="form-control mb-3" type="password" name="new_password" minlength="8" required>
            <label class="form-label">Ponovi novu lozinku</label>
            <input class="form-control mb-3" type="password" name="confirm_new_password" minlength="8" required>
            <button class="btn btn-outline-primary">Promeni lozinku</button>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
