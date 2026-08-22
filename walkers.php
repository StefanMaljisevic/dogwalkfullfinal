<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/classes/Walker.php';
$search = cleanInput($_GET['search'] ?? '');
$walkerService = new Walker();
$walkers = $walkerService->getApprovedWalkers($search);
?>
<h1 class="h3 mb-4">Pretraga šetača pasa</h1>
<form class="row g-2 mb-4" method="get">
    <div class="col-md-10"><input class="form-control" name="search" placeholder="Ime, rasa, grad..." value="<?= $search ?>"></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Pretraži</button></div>
</form>
<div class="row g-4">
<?php foreach ($walkers as $walker): ?>
    <?php $photo = $walker['photo'] ? BASE_URL . '/uploads/walkers/' . cleanInput($walker['photo']) : BASE_URL . '/assets/img/default-dog.jpg'; ?>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
            <img src="<?= $photo ?>" class="card-img-top walker-image" alt="Dog walker">
            <div class="card-body">
                <h2 class="h5"><?= cleanInput($walker['first_name'] . ' ' . $walker['last_name']) ?></h2>
                <p class="text-muted mb-1">Omiljena rasa: <?= cleanInput($walker['favorite_breed'] ?: 'Nije navedeno') ?></p>
                <p>Ocena: <?= number_format((float) $walker['average_rating'], 2) ?>/5</p>
                <p><?= cleanInput(textExcerpt($walker['description'] ?? '', 90)) ?>...</p>
                <a class="btn btn-outline-primary" href="walker.php?id=<?= (int) $walker['id'] ?>">Detalji</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php if (!$walkers): ?><div class="alert alert-info">Nema rezultata.</div><?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
