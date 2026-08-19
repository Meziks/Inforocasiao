<?php
/**
 * Autenticação de clientes da loja (sessão + password_hash).
 *
 * Usa chaves de sessão próprias (customer_*), distintas das do gestor
 * (Auth::), para que uma conta de cliente e uma sessão de gestão possam
 * coexistir no mesmo browser sem se pisarem.
 */

declare(strict_types=1);

final class CustomerAuth
{
    public static function attempt(string $email, string $password): bool
    {
        $customer = Database::one(
            'SELECT * FROM customers WHERE email = ? LIMIT 1',
            [$email]
        );

        if ($customer && password_verify($password, $customer['password_hash'])) {
            self::login($customer);
            return true;
        }
        return false;
    }

    /**
     * Regista um novo cliente. Devolve o id criado, ou null se o email já
     * estiver em uso.
     */
    public static function register(string $name, string $email, string $password, ?string $phone = null): ?int
    {
        $existing = Database::one('SELECT id FROM customers WHERE email = ? LIMIT 1', [$email]);
        if ($existing) {
            return null;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        Database::pdo()->prepare(
            'INSERT INTO customers (name, email, password_hash, phone) VALUES (?, ?, ?, ?)'
        )->execute([$name, $email, $hash, $phone]);

        $id = (int) Database::pdo()->lastInsertId();
        self::login(['id' => $id, 'name' => $name, 'email' => $email]);
        return $id;
    }

    public static function login(array $customer): void
    {
        // Regenera o ID para prevenir "session fixation"
        session_regenerate_id(true);
        $_SESSION['customer_id']    = (int) $customer['id'];
        $_SESSION['customer_name']  = $customer['name'];
        $_SESSION['customer_email'] = $customer['email'];
    }

    /** Termina só a sessão do cliente, sem afetar uma eventual sessão de gestão em paralelo. */
    public static function logout(): void
    {
        unset($_SESSION['customer_id'], $_SESSION['customer_name'], $_SESSION['customer_email']);
        session_regenerate_id(true);
    }

    public static function check(): bool
    {
        return isset($_SESSION['customer_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return [
            'id'    => $_SESSION['customer_id'],
            'name'  => $_SESSION['customer_name'] ?? '',
            'email' => $_SESSION['customer_email'] ?? '',
        ];
    }

    /** Exige login; caso contrário redireciona para o login. */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            redirect('/login');
        }
    }

    // ------------------------------------------------------- Recuperação de password

    /**
     * Gera um token de recuperação para o email indicado (se existir uma
     * conta com esse email) e devolve o token em claro para enviar por
     * email. Devolve null se não houver conta com esse email — quem chama
     * decide mostrar sempre a mesma mensagem genérica, para não revelar
     * quais emails têm conta.
     */
    public static function createPasswordReset(string $email): ?string
    {
        $customer = Database::one('SELECT id FROM customers WHERE email = ? LIMIT 1', [$email]);
        if (!$customer) {
            return null;
        }

        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hora

        Database::pdo()->prepare(
            'INSERT INTO password_resets (customer_id, token_hash, expires_at) VALUES (?, ?, ?)'
        )->execute([$customer['id'], $tokenHash, $expiresAt]);

        return $token;
    }

    /** Valida um token de recuperação. Devolve o id do cliente, ou null se inválido/expirado. */
    public static function validatePasswordResetToken(string $token): ?int
    {
        $tokenHash = hash('sha256', $token);
        $reset = Database::one(
            'SELECT * FROM password_resets WHERE token_hash = ? AND expires_at > NOW() LIMIT 1',
            [$tokenHash]
        );
        return $reset ? (int) $reset['customer_id'] : null;
    }

    /** Define uma nova password e invalida todos os tokens de recuperação desse cliente. */
    public static function resetPassword(int $customerId, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $pdo  = Database::pdo();
        $pdo->prepare('UPDATE customers SET password_hash = ? WHERE id = ?')->execute([$hash, $customerId]);
        $pdo->prepare('DELETE FROM password_resets WHERE customer_id = ?')->execute([$customerId]);
    }
}
