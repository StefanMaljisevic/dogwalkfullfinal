<?php
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/Walker.php';

class Rating
{
    private PDO $database;

    public function __construct()
    {
        $this->database = Database::getConnection();
    }

    public function addRating(int $userId, int $walkerId, int $rating, string $comment): array
    {
        if ($rating < 1 || $rating > 5) {
            return ['success' => false, 'message' => 'Ocena mora biti između 1 i 5.'];
        }
        if (textLength($comment) > 1000) {
            return ['success' => false, 'message' => 'Komentar može imati najviše 1000 karaktera.'];
        }

        $walkerService = new Walker();
        $walker = $walkerService->getPublicById($walkerId);
        if (!$walker) {
            return ['success' => false, 'message' => 'Šetač nije pronađen.'];
        }
        if ((int) $walker['user_id'] === $userId) {
            return ['success' => false, 'message' => 'Ne možeš oceniti svoj profil.'];
        }

        try {
            $statement = $this->database->prepare('INSERT INTO ratings (user_id, walker_id, rating, comment) VALUES (?, ?, ?, ?)');
            $statement->execute([$userId, $walkerId, $rating, plainInput($comment)]);
            return ['success' => true, 'message' => 'Ocena je uspešno sačuvana.'];
        } catch (PDOException $exception) {
            return ['success' => false, 'message' => 'Već si ocenio ovog šetača.'];
        }
    }
}
