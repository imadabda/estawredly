<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$file = __DIR__ . '/data/factory_codes.json';
$prodFile = __DIR__ . '/../products_data.json';

if (file_exists($file)) {
    $content = file_get_contents($file);
    $codes = json_decode($content, true);
    if (is_array($codes)) {
        // Check if there are any new factory codes in products_data.json to merge
        if (file_exists($prodFile)) {
            $prodJson = @file_get_contents($prodFile);
            $products = @json_decode($prodJson, true);
            if (is_array($products)) {
                $existing = [];
                foreach ($codes as $c) {
                    $val = is_string($c) ? $c : ($c['code'] ?? '');
                    if (!empty($val)) $existing[strtolower(trim($val))] = true;
                }
                $merged = false;
                foreach ($products as $p) {
                    $fc = trim($p['factory_code'] ?? '');
                    if (!empty($fc) && !isset($existing[strtolower($fc)])) {
                        $existing[strtolower($fc)] = true;
                        $codes[] = [
                            'code' => $fc,
                            'name' => '',
                            'active' => ($p['active'] ?? true) !== false
                        ];
                        $merged = true;
                    }
                }
                if ($merged) {
                    @file_put_contents($file, json_encode($codes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }
            }
        }
        echo json_encode($codes, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Fallback if file doesn't exist yet
$fallback = [
    ["code" => "E30", "name" => "", "active" => true],
    ["code" => "E16", "name" => "", "active" => true],
    ["code" => "E6",  "name" => "", "active" => true],
    ["code" => "E01", "name" => "", "active" => true],
    ["code" => "E7",  "name" => "", "active" => true],
    ["code" => "E21", "name" => "", "active" => true],
    ["code" => "E20", "name" => "", "active" => true],
    ["code" => "E22", "name" => "", "active" => true],
    ["code" => "E4",  "name" => "", "active" => true],
    ["code" => "E28", "name" => "", "active" => true],
    ["code" => "E1",  "name" => "", "active" => true],
    ["code" => "E24", "name" => "", "active" => true],
    ["code" => "4715","name" => "", "active" => true]
];
@file_put_contents($file, json_encode($fallback, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo json_encode($fallback, JSON_UNESCAPED_UNICODE);
?>
