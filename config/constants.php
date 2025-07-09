<?php
define('BASE_PATH', realpath(__DIR__ . '/..'));

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . '/web_asm');

define('APP_NAME', 'Sistema de Mentoría Académica');
define('DEBUG', true);
