<?php
/**
 * Envio de email transacional via API REST da Brevo (antiga Sendinblue).
 *
 * Sem SDK/Composer — só cURL, tal como o resto do projeto (alojamento sem
 * SSH/Composer). Configura-se em config.php, chave "mailer":
 *
 *   'mailer' => [
 *       'brevo_api_key' => 'xkeysib-...',
 *       'from_email'    => 'loja@inforocasiao.pt',
 *       'from_name'     => 'Inforocasião',
 *   ],
 *
 * Enquanto a "brevo_api_key" não estiver preenchida, os emails não são
 * enviados — fica só registado em storage/app-error.log, para não
 * impedir o registo/login de funcionar durante o desenvolvimento.
 */

declare(strict_types=1);

final class Mailer
{
    /** Envia um email HTML. Devolve true se enviado (ou simulado em desenvolvimento), false se falhou. */
    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        $cfg = $GLOBALS['config']['mailer'] ?? [];
        $apiKey = trim((string) ($cfg['brevo_api_key'] ?? ''));
        $fromEmail = (string) ($cfg['from_email'] ?? 'no-reply@inforocasiao.pt');
        $fromName  = (string) ($cfg['from_name'] ?? 'Inforocasião');

        if ($apiKey === '') {
            self::logSkipped($toEmail, $subject);
            return false;
        }

        $payload = json_encode([
            'sender'      => ['name' => $fromName, 'email' => $fromEmail],
            'to'          => [['email' => $toEmail, 'name' => $toName !== '' ? $toName : $toEmail]],
            'subject'     => $subject,
            'htmlContent' => $htmlBody,
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'Content-Type: application/json',
                'api-key: ' . $apiKey,
            ],
            CURLOPT_TIMEOUT => 20,
        ]);
        $response = curl_exec($ch);
        $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($response === false || $code < 200 || $code >= 300) {
            self::logFailure($toEmail, $subject, $code, $err ?: (string) $response);
            return false;
        }
        return true;
    }

    private static function logSkipped(string $toEmail, string $subject): void
    {
        self::log("Email NÃO enviado (mailer.brevo_api_key por preencher) — destinatário: $toEmail | assunto: $subject");
    }

    private static function logFailure(string $toEmail, string $subject, int $code, string $detail): void
    {
        self::log("Falha ao enviar email para $toEmail (assunto: $subject) — HTTP $code — $detail");
    }

    private static function log(string $line): void
    {
        $dir = BASE_PATH . '/storage';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        @file_put_contents(
            $dir . '/app-error.log',
            '[' . date('Y-m-d H:i:s') . "] Mailer: $line\n",
            FILE_APPEND | LOCK_EX
        );
    }
}
