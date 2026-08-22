<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/classes/User.php';
$message = '';
if (isPostRequest()) {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    if ($email) {
        $userService = new User();
        $userService->requestPasswordReset($email);
    }
    $message = '<div class="alert alert-info">Ako email postoji, reset link je poslat. Ako SMTP nije podešen, link je u logs/mail_log.txt.</div>';
}
?>
<div class="row justify-content-center"><div class="col-lg-5">
    <h1 class="h3 mb-3">Zaboravljena lozinka</h1>
    <?= $message ?>
    <form method="post" class="card card-body shadow-sm needs-validation" novalidate>
        <label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" required>
        <button class="btn btn-primary">Pošalji reset link</button>
    </form>
</div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
