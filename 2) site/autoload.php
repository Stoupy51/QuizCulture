<?php declare(strict_types=1);
spl_autoload_register(function (string $className) {
    $class_file = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . "$className.php";
    if (file_exists($class_file)) {
        require_once $class_file;
        return true;
    } else {
        return false;
    }
});