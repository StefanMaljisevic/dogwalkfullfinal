<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/classes/User.php';
$message = '';
$token = $_GET['token'] ?? $_POST['token'] ?? '';
if (isPostRequest()) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    if (strlen($password) < 8 || $password !== $confirmPassword) {
        $message = '<div class="alert alert-danger">Lozinka mora imati bar 8 karaktera i mora se poklapati.</div>';
    } else {
        $userService = new User();
        $success = $userService->resetPassword($token, $password);
        $message = $success ? '<div class="alert alert-success">Lozinka je promenjena.</div>' : '<div class="alert alert-danger">Link nije ispravan ili je istekao.</div>';
    }
}
?>
<div class="row justify-content-center"><div class="col-lg-5">
    <h1 class="h3 mb-3">Nova lozinka</h1>
    <?= $message ?>
    <form method="post" class="card card-body shadow-sm needs-validation" novalidate>
        <input type="hidden" name="token" value="<?= cleanInput($token) ?>">
        <label class="form-label">Nova lozinka</label><input class="form-control mb-3" type="password" name="password" minlength="8" required>
        <label class="form-label">Ponovi novu lozinku</label><input class="form-control mb-3" type="password" name="confirm_password" minlength="8" required>
        <button class="btn btn-primary">Promeni lozinku</button>
    </form>
</div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
