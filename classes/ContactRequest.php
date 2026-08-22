<?php
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/MailService.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/Walker.php';

class ContactRequest
{
    private PDO $database;

    public function __construct()
    {
        $this->database = Database::getConnection();
    }

    public function create(int $userId, int $walkerId, array $data): array
    {
        $validationMessage = $this->validateContactData($data);
        if ($validationMessage !== '') {
            return ['success' => false, 'message' => $validationMessage];
        }

        $walkerService = new Walker();
        $walker = $walkerService->getPublicById($walkerId);
        if (!$walker) {
            return ['success' => false, 'message' => 'Šetač nije dostupan za kontakt.'];
        }

        if ((int) $walker['user_id'] === $userId) {
            return ['success' => false, 'message' => 'Ne možeš poslati zahtev sam sebi.'];
        }

        $statement = $this->database->prepare(
            'INSERT INTO contacts (user_id, walker_id, dog_breed, dog_name, dog_gender, dog_age, message)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $success = $statement->execute([
            $userId,
            $walkerId,
            plainInput($data['dog_breed']),
            plainInput($data['dog_name']),
            plainInput($data['dog_gender']),
            (int) $data['dog_age'],
            plainInput($data['message']),
        ]);

        if ($success) {
            $walkerService->increaseContactCount($walkerId);
            $emailBody = "Dog: {$data['dog_name']}\nBreed: {$data['dog_breed']}\nGender: {$data['dog_gender']}\nAge: {$data['dog_age']}\n\nMessage:\n{$data['message']}";
            MailService::sendContactEmail($walker['email'], 'Dog walking request', $emailBody);
        }

        return ['success' => $success, 'message' => $success ? 'Zahtev je poslat.' : 'Greška pri slanju zahteva.'];
    }

    public function getForWalkerUser(int $walkerUserId): array
    {
        $statement = $this->database->prepare(
            'SELECT contacts.*, users.first_name, users.last_name, users.email, users.phone
             FROM contacts
             JOIN walkers ON walkers.id = contacts.walker_id
             JOIN users ON users.id = contacts.user_id
             WHERE walkers.user_id = ?
             ORDER BY contacts.created_at DESC'
        );
        $statement->execute([$walkerUserId]);
        return $statement->fetchAll();
    }

    public function updateStatusForWalkerUser(int $contactId, int $walkerUserId, string $status): array
    {
        if (!in_array($status, ['accepted', 'declined'], true)) {
            return ['success' => false, 'message' => 'Status nije ispravan.'];
        }

        $statement = $this->database->prepare(
            'UPDATE contacts
             JOIN walkers ON walkers.id = contacts.walker_id
             SET contacts.status = ?
             WHERE contacts.id = ? AND walkers.user_id = ?'
        );
        $statement->execute([$status, $contactId, $walkerUserId]);

        return [
            'success' => $statement->rowCount() > 0,
            'message' => $statement->rowCount() > 0 ? 'Status zahteva je promenjen.' : 'Zahtev nije pronađen.',
        ];
    }

    private function validateContactData(array $data): string
    {
        if (!validateRequiredText($data['dog_breed'] ?? '') || !validateRequiredText($data['dog_name'] ?? '')) {
            return 'Rasa i ime psa su obavezni.';
        }
        if (!in_array($data['dog_gender'] ?? '', ['male', 'female'], true)) {
            return 'Pol psa nije ispravan.';
        }
        $dogAge = (int) ($data['dog_age'] ?? -1);
        if ($dogAge < 0 || $dogAge > 30) {
            return 'Godine psa nisu ispravne.';
        }
        if (!validateRequiredText($data['message'] ?? '', 10, 2000)) {
            return 'Opis mora imati bar 10 karaktera.';
        }
        return '';
    }
}
