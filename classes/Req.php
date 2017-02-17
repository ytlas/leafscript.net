<?php
class Req{
    public $site='';
    static $userName;
    static $groupName;
    static $color;
    function __construct($cip,$cmethod,$cagent){
	$this->ip=$cip;
	$this->method=$cmethod;
	$this->agent=$cagent;
    }
    function log($data){
	file_put_contents('../log',$this->ip." >> ".date("Y/m/d G:i:s >>")." ".$data.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
?>
