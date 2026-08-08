<?php
header('Content-Type: application/json');

$file = __DIR__ . '/data/currency.json';

// Default configuration
$config = [
    'enabled' => false,
    'base_rate' => 0.50,
    'current_rate' => 0.50,
    'auto_fetch' => true,
    'last_updated' => ''
];

if (file_exists($file)) {
    $content = json_decode(file_get_contents($file), true);
    if (is_array($content)) {
        $config = array_merge($config, $content);
    }
}

// If auto-fetch is enabled, try to fetch the latest CNY to ILS rate
if ($config['enabled'] && $config['auto_fetch']) {
    $now = time();
    $last_time = !empty($config['last_updated']) ? strtotime($config['last_updated']) : 0;
    
    // Refresh rate if older than 1 hour (3600 seconds)
    if ($now - $last_time > 3600) {
        $apiUrl = 'https://open.er-api.com/v6/latest/CNY';
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 3, // 3 seconds timeout
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
            ]
        ]);
        $response = @file_get_contents($apiUrl, false, $ctx);
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['rates']['ILS'])) {
                $config['current_rate'] = floatval($data['rates']['ILS']);
                $config['last_updated'] = date('Y-m-d H:i:s');
                // Save updated config
                file_put_contents($file, json_encode($config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            }
        }
    }
}

echo json_encode($config);
