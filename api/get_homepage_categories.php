<?php
header('Content-Type: application/json; charset=utf-8');
$file = 'data/homepage_categories.json';
if(file_exists($file)){
    echo file_get_contents($file);
} else {
    echo '[]';
}
?>
