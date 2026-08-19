<?php
/**
 * Carrinho de compras, guardado na sessão (sem precisar de login).
 * O login só é exigido no checkout (ver rota /checkout).
 */

declare(strict_types=1);

final class Cart
{
    private static function ensure(): void
    {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public static function add(int $productId, int $qty = 1): void
    {
        self::ensure();
        $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + max(1, $qty);
    }

    public static function setQty(int $productId, int $qty): void
    {
        self::ensure();
        if ($qty <= 0) {
            unset($_SESSION['cart'][$productId]);
            return;
        }
        $_SESSION['cart'][$productId] = $qty;
    }

    public static function remove(int $productId): void
    {
        self::ensure();
        unset($_SESSION['cart'][$productId]);
    }

    public static function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    public static function count(): int
    {
        self::ensure();
        return (int) array_sum($_SESSION['cart']);
    }

    /**
     * Linhas do carrinho com os dados atuais do produto (nome, preço, stock,
     * imagem) — nunca os dados no momento em que foi adicionado, para o
     * carrinho refletir sempre o preço/stock reais.
     */
    public static function items(): array
    {
        self::ensure();
        $cart = $_SESSION['cart'];
        if (!$cart) {
            return [];
        }

        $ids = array_map('intval', array_keys($cart));
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $products = Database::all("SELECT * FROM products WHERE id IN ($placeholders)", $ids);

        $byId = [];
        foreach ($products as $p) {
            $byId[(int) $p['id']] = $p;
        }

        $items = [];
        foreach ($cart as $productId => $qty) {
            $productId = (int) $productId;
            if (!isset($byId[$productId]) || !$byId[$productId]['is_active']) {
                continue; // artigo removido/ocultado entretanto
            }
            $p = $byId[$productId];
            $items[] = [
                'product'  => $p,
                'qty'      => (int) $qty,
                'subtotal' => (float) $p['price'] * (int) $qty,
            ];
        }
        return $items;
    }

    public static function total(): float
    {
        $total = 0.0;
        foreach (self::items() as $item) {
            $total += $item['subtotal'];
        }
        return $total;
    }
}
