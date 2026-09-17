<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Check admin auth
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data format']);
    exit;
}

$assetsDir = __DIR__ . '/../assets';
if (!is_dir($assetsDir)) {
    @mkdir($assetsDir, 0755, true);
}

// Function to save base64 image to file
function saveBase64Image($base64String, $targetFile) {
    if (strpos($base64String, 'data:image') === 0) {
        $parts = explode(',', $base64String, 2);
        if (count($parts) === 2) {
            $raw = base64_decode($parts[1]);
            if ($raw !== false && strlen($raw) > 0) {
                return file_put_contents($targetFile, $raw) !== false;
            }
        }
    }
    return false;
}

$version = time();

// Handle desktop image
if (!empty($data['desktop_image'])) {
    if (strpos($data['desktop_image'], 'data:image') === 0) {
        $desktopFile = $assetsDir . '/hero_banner_import.webp';
        if (saveBase64Image($data['desktop_image'], $desktopFile)) {
            $data['desktop_image'] = 'assets/hero_banner_import.webp?v=' . $version;
        }
    }
}

// Handle mobile image
if (!empty($data['mobile_image'])) {
    if (strpos($data['mobile_image'], 'data:image') === 0) {
        $mobileFile = $assetsDir . '/hero_banner_import_mobile.webp';
        if (saveBase64Image($data['mobile_image'], $mobileFile)) {
            $data['mobile_image'] = 'assets/hero_banner_import_mobile.webp?v=' . $version;
        }
    }
} else if (!empty($data['desktop_image'])) {
    // If mobile is empty, use desktop image for mobile too
    @copy($assetsDir . '/hero_banner_import.webp', $assetsDir . '/hero_banner_import_mobile.webp');
    $data['mobile_image'] = 'assets/hero_banner_import_mobile.webp?v=' . $version;
}

$file = __DIR__ . '/data/hero_banner.json';
if (file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
    // Automatically keep index.html static markup synchronized with the new banner version
    $indexPath = __DIR__ . '/../index.html';
    if (file_exists($indexPath)) {
        $html = @file_get_contents($indexPath);
        if ($html !== false) {
            $desktopImg = $data['desktop_image'] ?? ('assets/hero_banner_import.webp?v=' . $version);
            $mobileImg = $data['mobile_image'] ?? ('assets/hero_banner_import_mobile.webp?v=' . $version);

            // Update preload links
            $html = preg_replace('/href="assets\/hero_banner_import_mobile\.webp[^"]*"/', 'href="' . $mobileImg . '"', $html);
            $html = preg_replace('/href="assets\/hero_banner_import\.webp[^"]*"/', 'href="' . $desktopImg . '"', $html);

            // Update picture sources and img
            $html = preg_replace('/srcset="assets\/hero_banner_import_mobile\.webp[^"]*"/', 'srcset="' . $mobileImg . '"', $html);
            $html = preg_replace('/src="assets\/hero_banner_import\.webp[^"]*"/', 'src="' . $desktopImg . '"', $html);

            if (!empty($data['link'])) {
                $html = preg_replace('/id="hero-banner-link"[^>]*href="[^"]*"/', 'id="hero-banner-link" href="' . htmlspecialchars($data['link']) . '"', $html);
            }
            if (!empty($data['alt_text'])) {
                $html = preg_replace('/id="hero-banner-img-desktop"[^>]*alt="[^"]*"/', 'id="hero-banner-img-desktop" alt="' . htmlspecialchars($data['alt_text']) . '"', $html);
            }

            @file_put_contents($indexPath, $html);
        }
    }
    echo json_encode(['success' => true, 'message' => 'تم حفظ غلاف الرئيسية بنجاح', 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'فشل في حفظ الغلاف']);
}
