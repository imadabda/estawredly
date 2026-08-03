<?php
$productsData = [
    ['id' => 1, 'stock' => 10]
];
$items = [
    ['id' => 1, 'qty' => 2]
];

$stockChanged = false;
foreach ($items as $item) {
    if (isset($item['id']) && isset($item['qty'])) {
        foreach ($productsData as &$p) {
            if ((string)$p['id'] === (string)$item['id']) {
                if (isset($p['stock']) && $p['stock'] !== null && $p['stock'] > 0) {
                    $p['stock'] = max(0, intval($p['stock']) - intval($item['qty']));
                    $stockChanged = true;
                }
                break;
            }
        }
    }
}
var_dump($productsData);
