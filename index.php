<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/classes/Walker.php';
$walkerService = new Walker();
$topRated = $walkerService->getTopRated();
$mostActive = $walkerService->getMostActive();
?>
<section class="hero rounded-4 p-5 mb-5 text-white">
    <h1>Pronađi pouzdanog šetača pasa</h1>
    <p class="lead">Jednostavna web aplikacija za korisnike, šetače pasa i administratore.</p>
    <a href="walkers.php" class="btn btn-light btn-lg">Pogledaj šetače</a>
</section>

<div class="row g-4">
    <div class="col-lg-6">
        <h2 class="h4 mb-3">5 najbolje ocenjenih šetača</h2>
        <?php foreach ($topRated as $walker): ?>
            <div class="card mb-3 shadow-sm"><div class="card-body">
                <h3 class="h5"><?= cleanInput($walker['first_name'] . ' ' . $walker['last_name']) ?></h3>
                <p>Ocena: <?= number_format((float) $walker['average_rating'], 2) ?>/5</p>
                <a href="walker.php?id=<?= (int) $walker['id'] ?>" class="btn btn-outline-primary btn-sm">Detalji</a>
            </div></div>
        <?php endforeach; ?>
        <?php if (!$topRated): ?><div class="alert alert-info">Još nema odobrenih šetača.</div><?php endif; ?>
    </div>
    <div class="col-lg-6">
        <h2 class="h4 mb-3">5 najaktivnijih šetača</h2>
        <?php foreach ($mostActive as $walker): ?>
            <div class="card mb-3 shadow-sm"><div class="card-body">
                <h3 class="h5"><?= cleanInput($walker['first_name'] . ' ' . $walker['last_name']) ?></h3>
                <p>Broj kontakata: <?= (int) $walker['contact_count'] ?></p>
                <a href="walker.php?id=<?= (int) $walker['id'] ?>" class="btn btn-outline-primary btn-sm">Detalji</a>
            </div></div>
        <?php endforeach; ?>
        <?php if (!$mostActive): ?><div class="alert alert-info">Još nema aktivnosti.</div><?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
