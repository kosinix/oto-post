<?php
// Autoload classes in OtoPost dir
spl_autoload_register(function ($class) {
    if ( 0 === strpos( $class, 'OtoPost' ) ) { // Autoload our packages only

        $base_dir = __DIR__ . '/';
        $file = str_replace('\\', '/', $base_dir . $class . '.php'); // Change \ to /

        require_once $file;
    }
});

require_once __DIR__.'/../vendor/autoload.php'; // Load composer packages
require_once __DIR__.'/plugin.php';