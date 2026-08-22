<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/classes/Walker.php';
$walkerService = new Walker();
$walker = $walkerService->getPublicById((int) ($_GET['id'] ?? 0));
if (!$walker) { echo '<div class="alert alert-danger">Šetač nije pronađen.</div>'; require_once __DIR__ . '/includes/footer.php'; exit; }
$photo = $walker['photo'] ? BASE_URL . '/uploads/walkers/' . cleanInput($walker['photo']) : BASE_URL . '/assets/img/default-dog.jpg';
?>
<div class="row g-4">
    <div class="col-lg-5"><img class="img-fluid rounded-4 shadow-sm walker-detail-image" src="<?= $photo ?>" alt="Dog walker"></div>
    <div class="col-lg-7">
        <h1><?= cleanInput($walker['first_name'] . ' ' . $walker['last_name']) ?></h1>
        <p class="lead">Ocena: <?= number_format((float) $walker['average_rating'], 2) ?>/5 (<?= (int) $walker['rating_count'] ?> ocena)</p>
        <p><strong>Omiljena rasa:</strong> <?= cleanInput($walker['favorite_breed'] ?: 'Nije navedeno') ?></p>
        <p><strong>Telefon:</strong> <?= cleanInput($walker['phone'] ?: 'Nije navedeno') ?></p>
        <p><strong>Adresa:</strong> <?= cleanInput($walker['address'] ?: 'Nije navedeno') ?></p>
        <p><?= nl2br(cleanInput($walker['description'] ?? '')) ?></p>
        <?php if (isLoggedIn() && !isAdmin()): ?>
            <a class="btn btn-primary" href="contact_walker.php?id=<?= (int) $walker['id'] ?>">Kontaktiraj šetača</a>
        <?php endif; ?>
    </div>
</div>
<?php if (isLoggedIn() && !isAdmin()): ?>
<hr class="my-5">
<div class="row"><div class="col-lg-6">
    <h2 class="h4">Oceni šetača</h2>
    <div id="ratingMessage"></div>
    <form id="ratingForm" class="needs-validation" novalidate>
        <input type="hidden" name="walker_id" value="<?= (int) $walker['id'] ?>">
        <label class="form-label">Ocena</label>
        <select class="form-select mb-3" name="rating" required>
            <option value="5">5</option><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option>
        </select>
        <label class="form-label">Komentar</label>
        <textarea class="form-control mb-3" name="comment" maxlength="1000"></textarea>
        <button class="btn btn-success">Sačuvaj ocenu</button>
    </form>
</div></div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
