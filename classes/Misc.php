<?php
class Misc{
    static function validateUsername($userName){
	return preg_match('/^\w+$/',$userName)&&strlen($userName)>2||strlen($userName)<17;
    }
    static function validatePassword($userPassword){
	return strlen($userPassword)>7&&strlen($userPassword)<33;
    }
    static function validateEmail($userEmail){
	return filter_var($userEmail,FILTER_VALIDATE_EMAIL);
    }
    static function li(){
	return isset($_SESSION['check'])&&$_SESSION['check'];
    }
}
?>
