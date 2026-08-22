<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-lg-5">
    <div class="card shadow-sm"><div class="card-body">
        <h1 class="h3 mb-4">Prijava</h1>
        <div id="loginMessage"></div>
        <form id="loginForm" class="needs-validation" novalidate>
            <label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" required>
            <label class="form-label">Lozinka</label><input class="form-control mb-3" type="password" name="password" required>
            <button class="btn btn-primary w-100" type="submit">Prijavi se</button>
        </form>
        <a href="forgot_password.php" class="d-block mt-3">Zaboravljena lozinka?</a>
    </div></div>
</div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
