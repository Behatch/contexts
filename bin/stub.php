<?php

if (class_exists('Phar')) {
    Phar::mapPhar('default.phar');
    return require 'phar://' . __FILE__ . '/bin/main';
}
__HALT_COMPILER();
