<?php
require __DIR__.'/vendor/autoload.php';
if(class_exists('Google\Cloud\Storage\StorageClient')) {
    echo "YES";
} else {
    echo "NO";
}
