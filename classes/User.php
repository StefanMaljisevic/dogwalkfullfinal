<?php
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/MailService.php';
require_once __DIR__ . '/../includes/functions.php';

class User
{
    private PDO $database;

    public function __construct()
    {
        $this->database = Database::getConnection();
    }

    public function register(array $data): array
    {
        $validationMessage = $this->validateRegistrationData($data);
        if ($validationMessage !== '') {
            return ['success' => false, 'message' => $validationMessage];
        }

        if ($this->findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email adresa je već registrovana.'];
        }

        $token = bin2hex(random_bytes(32));
        $role = !empty($data['is_walker']) ? 'walker' : 'user';
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

        $this->database->beginTransaction();
        try {
            $statement = $this->database->prepare(
                'INSERT INTO users (email, password, first_name, last_name, phone, address, role, activation_token)
                 VALUES (:email, :password, :first_name, :last_name, :phone, :address, :role, :activation_token)'
            );

            $statement->execute([
                'email' => $data['email'],
                'password' => $passwordHash,
                'first_name' => plainInput($data['first_name']),
                'last_name' => plainInput($data['last_name']),
                'phone' => plainInput($data['phone'] ?? ''),
                'address' => plainInput($data['address'] ?? ''),
                'role' => $role,
                'activation_token' => $token,
            ]);

            $userId = (int) $this->database->lastInsertId();
            if ($role === 'walker') {
                $walkerStatement = $this->database->prepare('INSERT INTO walkers (user_id, description, favorite_breed) VALUES (?, ?, ?)');
                $walkerStatement->execute([$userId, '', '']);
            }

            $this->database->commit();
            MailService::sendActivationEmail($data['email'], $token);
            return ['success' => true, 'message' => 'Registracija uspešna. Proveri email za aktivacioni link. Ako SMTP nije podešen, link je u logs/mail_log.txt.'];
        } catch (Throwable $exception) {
            $this->database->rollBack();
            return ['success' => false, 'message' => 'Greška pri registraciji.'];
        }
    }

    public function activate(string $token): bool
    {
        if ($token === '') {
            return false;
        }
        $statement = $this->database->prepare('UPDATE users SET is_active = 1, activation_token = NULL WHERE activation_token = ?');
        $statement->execute([$token]);
        return $statement->rowCount() > 0;
    }

    public function login(string $email, string $password): array
    {
        $user = $this->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Pogrešan email ili lozinka.'];
        }
        if ((int) $user['is_active'] !== 1) {
            return ['success' => false, 'message' => 'Nalog nije aktiviran.'];
        }
        if ((int) $user['is_blocked'] === 1) {
            return ['success' => false, 'message' => 'Administrator je blokirao nalog.'];
        }

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        return ['success' => true, 'message' => 'Uspešna prijava.'];
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->database->prepare('SELECT * FROM users WHERE email = ?');
        $statement->execute([$email]);
        $user = $statement->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $statement = $this->database->prepare('SELECT * FROM users WHERE id = ?');
        $statement->execute([$id]);
        $user = $statement->fetch();
        return $user ?: null;
    }

    public function updateProfile(int $id, array $data): array
    {
        if (!validateRequiredText($data['first_name'] ?? '') || !validateRequiredText($data['last_name'] ?? '')) {
            return ['success' => false, 'message' => 'Ime i prezime su obavezni.'];
        }
        if (!validatePhone($data['phone'] ?? '')) {
            return ['success' => false, 'message' => 'Broj telefona nije ispravan.'];
        }

        $statement = $this->database->prepare('UPDATE users SET first_name = ?, last_name = ?, phone = ?, address = ? WHERE id = ?');
        $success = $statement->execute([
            plainInput($data['first_name']),
            plainInput($data['last_name']),
            plainInput($data['phone'] ?? ''),
            plainInput($data['address'] ?? ''),
            $id,
        ]);
        return ['success' => $success, 'message' => $success ? 'Profil je izmenjen.' : 'Greška pri izmeni profila.'];
    }

    public function changePassword(int $id, string $currentPassword, string $newPassword, string $confirmPassword): array
    {
        $user = $this->findById($id);
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Trenutna lozinka nije tačna.'];
        }
        if (strlen($newPassword) < 8 || $newPassword !== $confirmPassword) {
            return ['success' => false, 'message' => 'Nova lozinka mora imati bar 8 karaktera i mora se poklapati.'];
        }
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $statement = $this->database->prepare('UPDATE users SET password = ? WHERE id = ?');
        $success = $statement->execute([$hash, $id]);
        return ['success' => $success, 'message' => $success ? 'Lozinka je uspešno promenjena.' : 'Greška pri promeni lozinke.'];
    }

    public function requestPasswordReset(string $email): bool
    {
        $user = $this->findByEmail($email);
        if (!$user) {
            return false;
        }
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $statement = $this->database->prepare('UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE email = ?');
        $statement->execute([$token, $expiresAt, $email]);
        MailService::sendResetEmail($email, $token);
        return true;
    }

    public function resetPassword(string $token, string $password): bool
    {
        if ($token === '' || strlen($password) < 8) {
            return false;
        }
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $statement = $this->database->prepare(
            'UPDATE users SET password = ?, reset_token = NULL, reset_expires_at = NULL WHERE reset_token = ? AND reset_expires_at > NOW()'
        );
        $statement->execute([$hash, $token]);
        return $statement->rowCount() > 0;
    }

    public function getAllUsers(): array
    {
        return $this->database->query('SELECT id, email, first_name, last_name, role, is_active, is_blocked, created_at FROM users ORDER BY created_at DESC')->fetchAll();
    }

    public function setBlockedStatus(int $id, int $status): bool
    {
        $statement = $this->database->prepare('UPDATE users SET is_blocked = ? WHERE id = ? AND role != "admin"');
        return $statement->execute([$status, $id]);
    }

    private function validateRegistrationData(array $data): string
    {
        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            return 'Email adresa nije ispravna.';
        }
        if (strlen($data['password'] ?? '') < 8 || ($data['password'] ?? '') !== ($data['confirm_password'] ?? $data['password'] ?? '')) {
            return 'Lozinka mora imati bar 8 karaktera i mora se poklapati.';
        }
        if (!validateRequiredText($data['first_name'] ?? '') || !validateRequiredText($data['last_name'] ?? '')) {
            return 'Ime i prezime su obavezni.';
        }
        if (!validatePhone($data['phone'] ?? '')) {
            return 'Broj telefona nije ispravan.';
        }
        return '';
    }
}
