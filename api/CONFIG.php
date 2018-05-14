<?php
$_CONFIG = array();
$_CONFIG['COMMON'] ['DEBUG']    = false;
$_CONFIG['COMMON'] ['BASE_URL'] = $_SERVER['HTTP_HOST'] . "/the-advest/api/";
$_CONFIG['URL']    ['M_OFFSET'] = 2;

$_CONFIG['DB']['HOST']     = "localhost";
$_CONFIG['DB']['USERNAME'] = "root";
$_CONFIG['DB']['PASSWORD'] = "givemesomecoffee";
$_CONFIG['DB']['NAME']     = "the_advest";

$_CONFIG['TOKEN']['ACTIVE'] = false;
$_CONFIG['TOKEN']['KEY']    = "jwt_key";
$_CONFIG['TOKEN']['TIME']   = 1440; // minutes

$_CONFIG['CRYPTO']['ALGORITHM'] = "AES-256-CBC";
$_CONFIG['CRYPTO']['KEY']		= "secert_key";

?>
