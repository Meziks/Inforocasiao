<?php
/**
 * Autenticação do gestor (sessão + password_hash).
 */

declare(strict_types=1);

final class Auth
{
    /** Tenta autenticar. Devolve true se as credenciais forem válidas. */
    public static function attempt(string $username, string $password): bool
    {
        $user = Database::one(
            'SELECT * FROM users WHERE username = ? LIMIT 1',
            [$username]
        );

        if ($user && password_verify($password, $user['password_hash'])) {
            self::login($user);
            return true;
        }
        return false;
    }

    public static function login(array $user): void
    {
        // Regenera o ID para prevenir "session fixation"
        session_regenerate_id(true);
        $_SESSION['user_id']  = (int) $user['id'];
        $_SESSION['username'] = $user['username'];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return [
            'id'       => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? '',
        ];
    }

    /** Exige login; caso contrário redireciona para o login. */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            redirect('/admin/login');
        }
    }
}
