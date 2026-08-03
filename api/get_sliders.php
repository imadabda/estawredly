<?php
header('Content-Type: application/json');
$file = 'data/sliders.json';
if(file_exists($file)){
    echo file_get_contents($file);
} else {
    echo '[]';
}
