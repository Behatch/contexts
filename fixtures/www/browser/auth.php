<?php

session_start();

$username = 'gabriel';
$password = '30091984';

if (isset($_GET['logout'])) {
    unset($_SESSION['login']);
    echo "Logged out<br /><a href='" . $_SERVER['PHP_SELF'] . "'>Login</a>";
}
elseif (
    !isset($_SERVER['PHP_AUTH_USER'])
    || !isset($_SERVER['PHP_AUTH_PW'])
    || !isset($_SESSION['login'])
) {
    header('WWW-Authenticate: Basic realm="Test"');
    header('HTTP/1.0 401 Unauthorized');
    $_SESSION['login'] = true;
    echo 'NONE SHALL PASS !';
}
else {
    if(
        $_SERVER['PHP_AUTH_USER'] == $username
        && $_SERVER['PHP_AUTH_PW'] == $password
    ) {
        echo 'Successfuly logged in';
    }
    else {
        unset($_SESSION['login']);
        header('Location: ' . $_SERVER['PHP_SELF']);
    }
}
