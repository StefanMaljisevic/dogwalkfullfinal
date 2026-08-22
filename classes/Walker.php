<?php
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

class Walker
{
    private PDO $database;

    public function __construct()
    {
        $this->database = Database::getConnection();
    }

    public function getApprovedWalkers(string $search = ''): array
    {
        $sql = 'SELECT walkers.*, users.first_name, users.last_name, users.email, users.phone, users.address,
                COALESCE(rating_stats.average_rating, 0) AS average_rating,
                COALESCE(rating_stats.rating_count, 0) AS rating_count
                FROM walkers
                JOIN users ON users.id = walkers.user_id
                LEFT JOIN (
                    SELECT walker_id, AVG(rating) AS average_rating, COUNT(id) AS rating_count
                    FROM ratings
                    GROUP BY walker_id
                ) AS rating_stats ON rating_stats.walker_id = walkers.id
                WHERE walkers.is_approved = 1 AND walkers.is_available = 1 AND users.is_active = 1 AND users.is_blocked = 0';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (users.first_name LIKE ? OR users.last_name LIKE ? OR walkers.favorite_breed LIKE ? OR users.address LIKE ?)';
            $value = '%' . $search . '%';
            $params = [$value, $value, $value, $value];
        }

        $sql .= ' ORDER BY average_rating DESC, walkers.contact_count DESC';
        $statement = $this->database->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function getTopRated(): array
    {
        return $this->database->query(
            'SELECT walkers.*, users.first_name, users.last_name,
                    COALESCE(rating_stats.average_rating, 0) AS average_rating,
                    COALESCE(rating_stats.rating_count, 0) AS rating_count
             FROM walkers
             JOIN users ON users.id = walkers.user_id
             LEFT JOIN (
                SELECT walker_id, AVG(rating) AS average_rating, COUNT(id) AS rating_count
                FROM ratings
                GROUP BY walker_id
             ) AS rating_stats ON rating_stats.walker_id = walkers.id
             WHERE walkers.is_approved = 1 AND walkers.is_available = 1 AND users.is_active = 1 AND users.is_blocked = 0
             ORDER BY average_rating DESC, walkers.contact_count DESC LIMIT 5'
        )->fetchAll();
    }

    public function getMostActive(): array
    {
        return $this->database->query(
            'SELECT walkers.*, users.first_name, users.last_name,
                    COALESCE(rating_stats.average_rating, 0) AS average_rating,
                    COALESCE(rating_stats.rating_count, 0) AS rating_count
             FROM walkers
             JOIN users ON users.id = walkers.user_id
             LEFT JOIN (
                SELECT walker_id, AVG(rating) AS average_rating, COUNT(id) AS rating_count
                FROM ratings
                GROUP BY walker_id
             ) AS rating_stats ON rating_stats.walker_id = walkers.id
             WHERE walkers.is_approved = 1 AND walkers.is_available = 1 AND users.is_active = 1 AND users.is_blocked = 0
             ORDER BY walkers.contact_count DESC LIMIT 5'
        )->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $statement = $this->database->prepare(
            'SELECT walkers.*, users.first_name, users.last_name, users.email, users.phone, users.address, users.is_active, users.is_blocked,
                    COALESCE(rating_stats.average_rating, 0) AS average_rating,
                    COALESCE(rating_stats.rating_count, 0) AS rating_count
             FROM walkers
             JOIN users ON users.id = walkers.user_id
             LEFT JOIN (
                SELECT walker_id, AVG(rating) AS average_rating, COUNT(id) AS rating_count
                FROM ratings
                GROUP BY walker_id
             ) AS rating_stats ON rating_stats.walker_id = walkers.id
             WHERE walkers.id = ?'
        );
        $statement->execute([$id]);
        $walker = $statement->fetch();
        return $walker ?: null;
    }

    public function getPublicById(int $id): ?array
    {
        $walker = $this->getById($id);
        if (!$walker) {
            return null;
        }
        if ((int) $walker['is_approved'] !== 1 || (int) $walker['is_available'] !== 1 || (int) $walker['is_active'] !== 1 || (int) $walker['is_blocked'] === 1) {
            return null;
        }
        return $walker;
    }

    public function getByUserId(int $userId): ?array
    {
        $statement = $this->database->prepare('SELECT * FROM walkers WHERE user_id = ?');
        $statement->execute([$userId]);
        $walker = $statement->fetch();
        return $walker ?: null;
    }

    public function ensureWalkerProfile(int $userId): array
    {
        $walker = $this->getByUserId($userId);
        if ($walker) {
            return $walker;
        }
        $statement = $this->database->prepare('INSERT INTO walkers (user_id, description, favorite_breed) VALUES (?, "", "")');
        $statement->execute([$userId]);
        return $this->getByUserId($userId) ?: [];
    }

    public function updateByUserId(int $userId, array $data): array
    {
        if (!validateRequiredText($data['description'] ?? '', 10, 2000)) {
            return ['success' => false, 'message' => 'Opis mora imati bar 10 karaktera.'];
        }
        if (!validateRequiredText($data['favorite_breed'] ?? '', 2, 100)) {
            return ['success' => false, 'message' => 'Omiljena rasa je obavezna.'];
        }
        $statement = $this->database->prepare('UPDATE walkers SET description = ?, favorite_breed = ?, is_available = ? WHERE user_id = ?');
        $success = $statement->execute([
            plainInput($data['description']),
            plainInput($data['favorite_breed']),
            (int) $data['is_available'],
            $userId,
        ]);
        return ['success' => $success, 'message' => $success ? 'Podaci su izmenjeni.' : 'Greška pri izmeni podataka.'];
    }

    public function updatePhotoByUserId(int $userId, array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Slika nije poslata.'];
        }

        $allowedMimeTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mimeType = mime_content_type($file['tmp_name']);
        if (!isset($allowedMimeTypes[$mimeType])) {
            return ['success' => false, 'message' => 'Dozvoljene su samo JPG, PNG i WEBP slike.'];
        }
        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Slika ne sme biti veća od 2MB.'];
        }

        $walker = $this->getByUserId($userId);
        if (!$walker) {
            return ['success' => false, 'message' => 'Profil šetača nije pronađen.'];
        }

        $fileName = 'walker_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $allowedMimeTypes[$mimeType];
        $destination = __DIR__ . '/../uploads/walkers/' . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'message' => 'Greška pri upload-u slike.'];
        }

        $this->deletePhysicalPhoto($walker['photo'] ?? null);
        $statement = $this->database->prepare('UPDATE walkers SET photo = ? WHERE user_id = ?');
        $success = $statement->execute([$fileName, $userId]);
        return ['success' => $success, 'message' => $success ? 'Slika je uspešno sačuvana.' : 'Greška pri čuvanju slike.'];
    }

    public function deletePhotoByUserId(int $userId): array
    {
        $walker = $this->getByUserId($userId);
        if (!$walker) {
            return ['success' => false, 'message' => 'Profil šetača nije pronađen.'];
        }
        $this->deletePhysicalPhoto($walker['photo'] ?? null);
        $statement = $this->database->prepare('UPDATE walkers SET photo = NULL WHERE user_id = ?');
        $success = $statement->execute([$userId]);
        return ['success' => $success, 'message' => $success ? 'Slika je obrisana.' : 'Greška pri brisanju slike.'];
    }

    public function clearDetailsByUserId(int $userId): array
    {
        $walker = $this->getByUserId($userId);
        if (!$walker) {
            return ['success' => false, 'message' => 'Profil šetača nije pronađen.'];
        }

        $this->deletePhysicalPhoto($walker['photo'] ?? null);
        $statement = $this->database->prepare(
            'UPDATE walkers SET photo = NULL, description = "", favorite_breed = "", is_available = 0 WHERE user_id = ?'
        );
        $success = $statement->execute([$userId]);

        return ['success' => $success, 'message' => $success ? 'Podaci oglasa su obrisani.' : 'Greška pri brisanju podataka.'];
    }

    public function getAllForAdmin(): array
    {
        return $this->database->query(
            'SELECT walkers.*, users.email, users.first_name, users.last_name FROM walkers JOIN users ON users.id = walkers.user_id ORDER BY walkers.created_at DESC'
        )->fetchAll();
    }

    public function setApprovalStatus(int $id, int $status): bool
    {
        $statement = $this->database->prepare('UPDATE walkers SET is_approved = ? WHERE id = ?');
        return $statement->execute([$status, $id]);
    }

    public function increaseContactCount(int $id): void
    {
        $statement = $this->database->prepare('UPDATE walkers SET contact_count = contact_count + 1 WHERE id = ?');
        $statement->execute([$id]);
    }

    private function deletePhysicalPhoto(?string $photo): void
    {
        if (!$photo || $photo === 'default-dog.jpg') {
            return;
        }
        $path = __DIR__ . '/../uploads/walkers/' . basename($photo);
        if (is_file($path)) {
            unlink($path);
        }
    }
}
