<?php
/**
 * Avaliações do Google (Places API), com cache e fallback estático.
 *
 * - Se existir config/google.php com api_key + place_id, vai buscar os dados
 *   ao Google (nota, total e até 5 críticas), guardando em cache (storage/).
 * - Sem configuração (ou em caso de falha), devolve um selo estático com os
 *   valores conhecidos, com link para a página de avaliações.
 */

declare(strict_types=1);

final class Reviews
{
    /** Valores conhecidos, usados como fallback e como selo estático. */
    private const FALLBACK = [
        'rating'  => 4.6,
        'total'   => 26,
        'reviews' => [],
        'url'     => 'https://share.google/YXjo7b9ymKWjfTeuh',
        'source'  => 'static',
    ];

    private const CACHE_TTL = 43200; // 12 horas

    public static function data(): array
    {
        $cfgFile = BASE_PATH . '/config/google.php';
        if (!is_file($cfgFile)) {
            return self::FALLBACK;
        }
        $g       = require $cfgFile;
        $key     = trim((string) ($g['api_key'] ?? ''));
        $placeId = trim((string) ($g['place_id'] ?? ''));
        if ($key === '' || $placeId === '') {
            return self::FALLBACK;
        }

        $cacheFile = BASE_PATH . '/storage/reviews.json';

        // Cache válida?
        if (is_file($cacheFile) && (time() - filemtime($cacheFile) < self::CACHE_TTL)) {
            $cached = json_decode((string) file_get_contents($cacheFile), true);
            if (is_array($cached)) {
                return $cached + ['source' => 'cache'];
            }
        }

        // Ir buscar ao Google
        $fresh = self::fetch($key, $placeId);
        if ($fresh !== null) {
            @file_put_contents($cacheFile, json_encode($fresh, JSON_UNESCAPED_UNICODE));
            return $fresh + ['source' => 'live'];
        }

        // Falhou: usar cache antiga se existir, senão fallback
        if (is_file($cacheFile)) {
            $stale = json_decode((string) file_get_contents($cacheFile), true);
            if (is_array($stale)) {
                return $stale + ['source' => 'stale'];
            }
        }
        return self::FALLBACK;
    }

    /** Chama a Places Details API. Devolve null em caso de erro. */
    private static function fetch(string $key, string $placeId): ?array
    {
        if (!function_exists('curl_init')) {
            return null;
        }
        $url = 'https://maps.googleapis.com/maps/api/place/details/json?'
            . http_build_query([
                'place_id'     => $placeId,
                'fields'       => 'rating,user_ratings_total,reviews,url',
                'reviews_sort' => 'newest',
                'language'     => 'pt',
                'key'          => $key,
            ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 4,
            CURLOPT_CONNECTTIMEOUT => 3,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $code !== 200) {
            return null;
        }
        $json = json_decode((string) $body, true);
        if (($json['status'] ?? '') !== 'OK' || !isset($json['result'])) {
            return null;
        }
        $r = $json['result'];

        $reviews = [];
        foreach (($r['reviews'] ?? []) as $rev) {
            $reviews[] = [
                'author' => (string) ($rev['author_name'] ?? ''),
                'rating' => (int) ($rev['rating'] ?? 0),
                'text'   => trim((string) ($rev['text'] ?? '')),
                'when'   => (string) ($rev['relative_time_description'] ?? ''),
                'photo'  => (string) ($rev['profile_photo_url'] ?? ''),
            ];
        }

        return [
            'rating'  => round((float) ($r['rating'] ?? 0), 1),
            'total'   => (int) ($r['user_ratings_total'] ?? 0),
            'reviews' => $reviews,
            'url'     => (string) ($r['url'] ?? self::FALLBACK['url']),
        ];
    }
}
