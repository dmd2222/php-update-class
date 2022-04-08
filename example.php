<?php
require_once("updater_class.php");

var_dump("New update?:",updater::check_new_update("example.php","https://raw.githubusercontent.com/dmd2222/php-update-class/main/example.php",true));


?>
