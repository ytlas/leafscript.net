<?php
class Sql{
    static function emailExists($userEmail){
	global $con;
	return $con->query("SELECT id FROM user WHERE email='".$con->real_escape_string($userEmail)."'")->num_rows==1;
    }
}
?>
