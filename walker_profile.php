<?php
require_once __DIR__ . '/includes/auth.php';
requireWalker();
require_once __DIR__ . '/classes/Walker.php';
require_once __DIR__ . '/includes/header.php';
$walkerService = new Walker();
$walker = $walkerService->ensureWalkerProfile(currentUserId());
$message = '';

if (isPostRequest()) {
    if (!$walker || (int) $walker['is_approved'] !== 1) {
        $result = ['success' => false, 'message' => 'Administrator mora prvo da odobri tvoj nalog šetača.'];
    } else {
        $action = $_POST['action'] ?? 'details';
        if ($action === 'upload_photo') {
            $result = $walkerService->updatePhotoByUserId(currentUserId(), $_FILES['photo'] ?? []);
        } elseif ($action === 'delete_photo') {
            $result = $walkerService->deletePhotoByUserId(currentUserId());
        } elseif ($action === 'delete_details') {
            $result = $walkerService->clearDetailsByUserId(currentUserId());
        } else {
            $result = $walkerService->updateByUserId(currentUserId(), [
                'description' => $_POST['description'] ?? '',
                'favorite_breed' => $_POST['favorite_breed'] ?? '',
                'is_available' => isset($_POST['is_available']) ? 1 : 0,
            ]);
        }
    }
    $message = '<div class="alert ' . ($result['success'] ? 'alert-success' : 'alert-danger') . '">' . cleanInput($result['message']) . '</div>';
    $walker = $walkerService->getByUserId(currentUserId());
}

$photo = $walker['photo'] ? BASE_URL . '/uploads/walkers/' . cleanInput($walker['photo']) : BASE_URL . '/assets/img/default-dog.jpg';
$isApproved = (int) $walker['is_approved'] === 1;
?>
<div class="row g-4 justify-content-center">
    <div class="col-lg-8">
        <h1 class="h3 mb-4">Moj oglas za šetanje pasa</h1>
        <?php if (!$isApproved): ?>
            <div class="alert alert-warning">Administrator još nije odobrio tvoj nalog šetača. Dok ne bude odobren, ne možeš da postavljaš opis, sliku i prihvataš ponude.</div>
        <?php endif; ?>
        <?= $message ?>
        <?php if ($isApproved): ?>
        <form method="post" class="card card-body shadow-sm needs-validation" novalidate>
            <input type="hidden" name="action" value="details">
            <label class="form-label">Opis</label>
            <textarea class="form-control mb-3" name="description" rows="5" minlength="10" required><?= cleanInput($walker['description'] ?? '') ?></textarea>
            <label class="form-label">Omiljena rasa</label>
            <input class="form-control mb-3" name="favorite_breed" value="<?= cleanInput($walker['favorite_breed'] ?? '') ?>" minlength="2" required>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_available" id="available" <?= (int) $walker['is_available'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="available">Dostupan sam</label>
            </div>
            <button class="btn btn-primary">Sačuvaj podatke</button>
        </form>
        <form method="post" class="mt-3" onsubmit="return confirm('Da li sigurno želiš da obrišeš podatke oglasa?');">
            <input type="hidden" name="action" value="delete_details">
            <button class="btn btn-outline-danger">Obriši podatke oglasa</button>
        </form>
        <?php endif; ?>
    </div>
    <div class="col-lg-4">
        <h2 class="h4 mb-4">Fotografija</h2>
        <div class="card card-body shadow-sm">
            <img class="walker-photo-preview mb-3" src="<?= $photo ?>" alt="Walker photo">
            <?php if ($isApproved): ?>
            <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                <input type="hidden" name="action" value="upload_photo">
                <input class="form-control mb-3" type="file" name="photo" accept="image/jpeg,image/png,image/webp" required>
                <button class="btn btn-outline-primary w-100">Upload slike</button>
            </form>
            <?php if (!empty($walker['photo'])): ?>
                <form method="post" class="mt-2">
                    <input type="hidden" name="action" value="delete_photo">
                    <button class="btn btn-outline-danger w-100">Obriši sliku</button>
                </form>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
