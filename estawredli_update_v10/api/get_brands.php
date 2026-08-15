<?php
header('Content-Type: application/json; charset=utf-8');
$file = 'data/brands.json';
if(file_exists($file)){
    echo file_get_contents($file);
} else {
    echo '["NIKE", "SAMSUNG", "APPLE", "ZARA", "ADIDAS", "H&M", "SONY", "LG", "IKEA", "GUCCI"]';
}
?>
