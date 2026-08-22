<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-lg-7">
    <div class="card shadow-sm"><div class="card-body">
        <h1 class="h3 mb-4">Registracija</h1>
        <div id="registerMessage"></div>
        <form id="registerForm" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Ime</label><input class="form-control" name="first_name" minlength="2" required></div>
                <div class="col-md-6"><label class="form-label">Prezime</label><input class="form-control" name="last_name" minlength="2" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required></div>
                <div class="col-md-6"><label class="form-label">Telefon</label><input class="form-control" name="phone" pattern="[0-9+\-\s]{6,30}"></div>
                <div class="col-12"><label class="form-label">Adresa</label><input class="form-control" name="address" maxlength="255"></div>
                <div class="col-md-6"><label class="form-label">Lozinka</label><input class="form-control" type="password" name="password" minlength="8" required></div>
                <div class="col-md-6"><label class="form-label">Ponovi lozinku</label><input class="form-control" type="password" name="confirm_password" minlength="8" required></div>
                <div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" name="is_walker" value="1" id="isWalker"><label class="form-check-label" for="isWalker">Želim da se registrujem kao šetač pasa</label></div>
            </div>
            <button class="btn btn-primary mt-4" type="submit">Registruj se</button>
        </form>
    </div></div>
</div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
