<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/classes/Walker.php';
require_once __DIR__ . '/classes/ContactRequest.php';
require_once __DIR__ . '/includes/header.php';
$walkerService = new Walker();
$walker = $walkerService->getPublicById((int) ($_GET['id'] ?? 0));
$message = '';
if (!$walker) { echo '<div class="alert alert-danger">Šetač nije pronađen.</div>'; require_once __DIR__ . '/includes/footer.php'; exit; }
if (isPostRequest()) {
    $contactService = new ContactRequest();
    $result = $contactService->create(currentUserId(), (int) $walker['id'], [
        'dog_breed' => $_POST['dog_breed'] ?? '',
        'dog_name' => $_POST['dog_name'] ?? '',
        'dog_gender' => $_POST['dog_gender'] ?? 'male',
        'dog_age' => (int) ($_POST['dog_age'] ?? 0),
        'message' => $_POST['message'] ?? '',
    ]);
    $message = '<div class="alert ' . ($result['success'] ? 'alert-success' : 'alert-danger') . '">' . cleanInput($result['message']) . '</div>';
}
?>
<div class="row justify-content-center"><div class="col-lg-7">
    <h1 class="h3 mb-3">Kontaktiraj: <?= cleanInput($walker['first_name'] . ' ' . $walker['last_name']) ?></h1>
    <?= $message ?>
    <form method="post" class="card card-body shadow-sm needs-validation" novalidate>
        <label class="form-label">Rasa psa</label><input class="form-control mb-3" name="dog_breed" minlength="2" required>
        <label class="form-label">Ime psa</label><input class="form-control mb-3" name="dog_name" minlength="2" required>
        <label class="form-label">Pol</label><select class="form-select mb-3" name="dog_gender"><option value="male">Mužjak</option><option value="female">Ženka</option></select>
        <label class="form-label">Godine starosti</label><input class="form-control mb-3" type="number" name="dog_age" min="0" max="30" required>
        <label class="form-label">Opis i specifičnosti</label><textarea class="form-control mb-3" name="message" minlength="10" maxlength="2000" required></textarea>
        <button class="btn btn-primary">Pošalji zahtev</button>
    </form>
</div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
