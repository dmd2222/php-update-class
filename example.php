<?php
require_once("updater_class.php");

#Version 1.0.0.1

//Check update: true/false
var_dump("New update?:",updater::check_new_update("example.php","https://raw.githubusercontent.com/dmd2222/php-update-class/main/example.php",true));

echo "<br>";
echo "<br>";


//Make update of file
if($_GET["update"]=="yes"){
        updater::do_file_update("example.php","https://raw.githubusercontent.com/dmd2222/php-update-class/main/example.php",true,true,true,0);
}


echo "<br>";
echo "<br>";

//Check update and inform admin over update: true/false
var_dump("New update?:",updater::check_new_update_if_new_inform_admin("example.php","https://raw.githubusercontent.com/dmd2222/php-update-class/main/example.php",true));




echo "<br>";
echo "<br>";


//Make updater object
$a = new updater("example.php","https://raw.githubusercontent.com/dmd2222/php-update-class/main/example.php",true,true,true,50);



?>
