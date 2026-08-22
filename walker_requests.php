<?php
require_once __DIR__ . '/includes/auth.php';
requireWalker();
require_once __DIR__ . '/classes/Walker.php';
require_once __DIR__ . '/classes/ContactRequest.php';
require_once __DIR__ . '/includes/header.php';

$walkerService = new Walker();
$walker = $walkerService->ensureWalkerProfile(currentUserId());
$message = '';

if (!$walker || (int) $walker['is_approved'] !== 1) {
    echo '<div class="alert alert-warning">Administrator mora prvo da odobri tvoj nalog šetača.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$contactService = new ContactRequest();

if (isPostRequest()) {
    $result = $contactService->updateStatusForWalkerUser(
        (int) ($_POST['contact_id'] ?? 0),
        currentUserId(),
        $_POST['status'] ?? ''
    );
    $message = '<div class="alert ' . ($result['success'] ? 'alert-success' : 'alert-danger') . '">' . cleanInput($result['message']) . '</div>';
}

$requests = $contactService->getForWalkerUser(currentUserId());
?>
<h1 class="h3 mb-4">Zahtevi za šetnju</h1>
<?= $message ?>

<?php if (!$requests): ?>
    <div class="alert alert-info">Još nema zahteva za šetnju.</div>
<?php endif; ?>

<div class="row g-4">
<?php foreach ($requests as $request): ?>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between gap-3">
                    <h2 class="h5"><?= cleanInput($request['dog_name']) ?> / <?= cleanInput($request['dog_breed']) ?></h2>
                    <span class="badge text-bg-<?= $request['status'] === 'accepted' ? 'success' : ($request['status'] === 'declined' ? 'danger' : 'secondary') ?>">
                        <?= cleanInput($request['status']) ?>
                    </span>
                </div>
                <p class="mb-1"><strong>Korisnik:</strong> <?= cleanInput($request['first_name'] . ' ' . $request['last_name']) ?></p>
                <p class="mb-1"><strong>Email:</strong> <?= cleanInput($request['email']) ?></p>
                <p class="mb-1"><strong>Telefon:</strong> <?= cleanInput($request['phone'] ?: 'Nije naveden') ?></p>
                <p class="mb-1"><strong>Pol:</strong> <?= $request['dog_gender'] === 'male' ? 'Mužjak' : 'Ženka' ?></p>
                <p class="mb-3"><strong>Starost:</strong> <?= (int) $request['dog_age'] ?> godina</p>
                <p><?= nl2br(cleanInput($request['message'])) ?></p>
                <?php if ($request['status'] === 'pending'): ?>
                    <form method="post" class="d-flex gap-2">
                        <input type="hidden" name="contact_id" value="<?= (int) $request['id'] ?>">
                        <button class="btn btn-success btn-sm" name="status" value="accepted">Prihvati</button>
                        <button class="btn btn-outline-danger btn-sm" name="status" value="declined">Odbij</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
