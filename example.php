<?php
require_once("updater_class.php");

var_dump("New update?:",updater::check_new_update("example.php","https://raw.githubusercontent.com/dmd2222/php-update-class/main/example.php",true));

echo "<br>";
echo "<br>";

if($_GET["update"]=="yes"){
        updater::do_file_update("example.php","https://raw.githubusercontent.com/dmd2222/php-update-class/main/example.php",true,true,true,50);
}


echo "<br>";
echo "<br>";


var_dump("New update?:",updater::check_new_update_if_new_inform_admin("example.php","https://raw.githubusercontent.com/dmd2222/php-update-class/main/example.php",true));

?>
