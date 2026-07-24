<?php
/**
 * SEO — dados estruturados (Schema.org / JSON-LD), URLs absolutos e metadados.
 *
 * Centraliza a informação do negócio (usada em várias páginas) e gera o
 * JSON-LD que motores de busca e IAs leem para perceber o que é a loja.
 */

declare(strict_types=1);

final class Seo
{
    /** Informação base do negócio. */
    public const BIZ = [
        'name'        => 'Inforocasião',
        'street'      => 'Rua do Clube Desportivo de Cucujães, 275',
        'postal'      => '3720-385',
        'city'        => 'Cucujães',
        'region'      => 'Aveiro',
        'country'     => 'PT',
        'phone'       => '+351912138094',
        'priceRange'  => '€€',
        'google'      => 'https://share.google/YXjo7b9ymKWjfTeuh',
        'facebook'    => 'https://www.facebook.com/100017988694141/',
        'instagram'   => 'https://www.instagram.com/inforocasiao.vendas/',
    ];

    /**
     * Condições comerciais declaradas nos produtos (Schema.org).
     * ALTERAR aqui se mudarem os portes ou a política de devolução.
     */
    public const SHIPPING_FREE   = true;   // portes grátis em Portugal
    public const SHIPPING_COUNTRY = 'PT';
    public const RETURN_DAYS      = 14;     // dias para devolução (mínimo legal PT)

    public const DESCRIPTION =
        'A Inforocasião é uma loja de informática em Cucujães (Oliveira de Azeméis) '
        . 'especializada na venda de computadores, portáteis, telemóveis e componentes '
        . 'electrónicos, novos e recondicionados, e na reparação de telemóveis e '
        . 'computadores com garantia.';

    /** Origem absoluta do site (ex.: https://www.inforocasiao.com). */
    public static function origin(): string
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
            || (($_SERVER['SERVER_PORT'] ?? '') === '443');
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'www.inforocasiao.com';
        return $scheme . '://' . $host;
    }

    /** URL absoluto a partir de um caminho do site. */
    public static function abs(string $path = ''): string
    {
        return self::origin() . url($path);
    }

    /** Horário no formato Schema.org. */
    private static function openingHours(): array
    {
        $week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        return [
            ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => $week, 'opens' => '09:30', 'closes' => '13:00'],
            ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => $week, 'opens' => '14:30', 'closes' => '19:00'],
            ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => 'Saturday', 'opens' => '09:30', 'closes' => '13:00'],
        ];
    }

    /** JSON-LD do negócio (loja de eletrónica local). */
    public static function businessJsonLd(): array
    {
        $data = [
            '@context'    => 'https://schema.org',
            '@type'       => 'ElectronicsStore',
            '@id'         => self::origin() . '/#loja',
            'name'        => self::BIZ['name'],
            'description' => self::DESCRIPTION,
            'url'         => self::abs('/'),
            'image'       => self::abs('assets/img/og-image.png'),
            'logo'        => self::abs('assets/img/logo.png'),
            'telephone'   => self::BIZ['phone'],
            'priceRange'  => self::BIZ['priceRange'],
            'currenciesAccepted' => 'EUR',
            'address'     => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => self::BIZ['street'],
                'postalCode'      => self::BIZ['postal'],
                'addressLocality' => self::BIZ['city'],
                'addressRegion'   => self::BIZ['region'],
                'addressCountry'  => self::BIZ['country'],
            ],
            'openingHoursSpecification' => self::openingHours(),
            'hasMap'      => self::BIZ['google'],
            'sameAs'      => [self::BIZ['facebook'], self::BIZ['instagram'], self::BIZ['google']],
            'areaServed'  => ['Cucujães', 'Oliveira de Azeméis', 'São João da Madeira', 'Santa Maria da Feira'],
        ];

        // Avaliação agregada (das avaliações reais do Google)
        $rev = Reviews::data();
        if (!empty($rev['total'])) {
            $data['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => (string) $rev['rating'],
                'reviewCount' => (string) $rev['total'],
                'bestRating'  => '5',
                'worstRating' => '1',
            ];
        }
        return $data;
    }

    /** JSON-LD do site (permite a "caixa de pesquisa" nos resultados). */
    public static function websiteJsonLd(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => self::BIZ['name'],
            'url'      => self::abs('/'),
            'inLanguage' => 'pt-PT',
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => [
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => self::abs('/produtos') . '?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /** JSON-LD de um produto. */
    public static function productJsonLd(array $p): array
    {
        $availability = ((int) ($p['stock'] ?? 0) > 0)
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock';
        $condition = ($p['condition'] ?? 'Novo') === 'Novo'
            ? 'https://schema.org/NewCondition'
            : 'https://schema.org/RefurbishedCondition';

        $image = !empty($p['image']) ? uploadUrl($p['image']) : self::abs('assets/img/og-image.png');
        if (!preg_match('#^https?://#i', $image)) {
            $image = self::origin() . $image;
        }

        $data = [
            '@context' => 'https://schema.org',
            '@type'    => 'Product',
            'name'     => $p['name'],
            'image'    => $image,
            'sku'      => 'IO-' . ($p['id'] ?? ''),
            'category' => $p['category_name'] ?? null,
            'itemCondition' => $condition,
            'offers'   => [
                '@type'         => 'Offer',
                'price'         => number_format((float) $p['price'], 2, '.', ''),
                'priceCurrency' => 'EUR',
                'availability'  => $availability,
                'itemCondition' => $condition,
                'url'           => self::abs('/produto/' . ($p['id'] ?? '')),
                'seller'        => ['@type' => 'Organization', 'name' => self::BIZ['name']],
                'shippingDetails'         => self::shippingDetails(),
                'hasMerchantReturnPolicy' => self::returnPolicy(),
            ],
        ];
        if (!empty($p['brand'])) {
            $data['brand'] = ['@type' => 'Brand', 'name' => $p['brand']];
        }
        if (!empty($p['description'])) {
            $data['description'] = $p['description'];
        }
        return $data;
    }

    /** Portes de envio (Schema.org OfferShippingDetails). */
    private static function shippingDetails(): array
    {
        return [
            '@type'        => 'OfferShippingDetails',
            'shippingRate' => [
                '@type'    => 'MonetaryAmount',
                'value'    => self::SHIPPING_FREE ? '0' : '0',
                'currency' => 'EUR',
            ],
            'shippingDestination' => [
                '@type'          => 'DefinedRegion',
                'addressCountry' => self::SHIPPING_COUNTRY,
            ],
            'deliveryTime' => [
                '@type'        => 'ShippingDeliveryTime',
                'handlingTime' => ['@type' => 'QuantitativeValue', 'minValue' => 0, 'maxValue' => 1, 'unitCode' => 'DAY'],
                'transitTime'  => ['@type' => 'QuantitativeValue', 'minValue' => 1, 'maxValue' => 3, 'unitCode' => 'DAY'],
            ],
        ];
    }

    /** Política de devolução (Schema.org MerchantReturnPolicy). */
    private static function returnPolicy(): array
    {
        return [
            '@type'                => 'MerchantReturnPolicy',
            'applicableCountry'    => self::SHIPPING_COUNTRY,
            'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
            'merchantReturnDays'   => self::RETURN_DAYS,
            'returnMethod'         => 'https://schema.org/ReturnInStore',
            'returnFees'           => 'https://schema.org/FreeReturn',
        ];
    }

    /** JSON-LD de migalhas (breadcrumb). $items = [[nome, caminho], ...] */
    public static function breadcrumbJsonLd(array $items): array
    {
        $list = [];
        foreach ($items as $i => [$name, $path]) {
            $list[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $name,
                'item'     => self::abs($path),
            ];
        }
        return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $list];
    }

    /** Imprime um bloco <script> JSON-LD. */
    public static function jsonLdTag(array $data): string
    {
        return '<script type="application/ld+json">'
            . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            . '</script>';
    }
}
