<?php

Phar::mapPhar('extension.phar');

return require 'phar://extension.phar/bin/init.php';

__HALT_COMPILER();
