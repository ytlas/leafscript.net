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
    static function parseCommand($command){
	global $con;
	$i=explode(" ",substr($command,1));
	$userName=$_SESSION['userName'];
	$userId=User::id($userName);
	switch ($i[0]){
	    case "purge":
		if(count($i)==2){
		    $tuserName=$con->real_escape_string($i[1]);
		    if(!User::exists($tuserName)){
			$message="That user does not exist.";
		    }
		    elseif(User::priv("admin.userPurge")&&
		       User::power($userName)>User::power($tuserName)){
			$storagePath="/var/www/storage/$tuserName";
			if(file_exists($storagePath)){
			    $files = glob("$storagePath/*");
			    foreach($files as $file){ // iterate files
				if(is_file($file))
				    unlink($file);
			    }
			}
			$files = glob('/var/www/avatars/'.User::id($tuserName).'_*.jpg');
			foreach($files as $file){ // iterate files
			    if(is_file($file))
				unlink($file);
			}
			$con->query("DELETE FROM user WHERE name='$tuserName'");
			$message=$con->real_escape_string("[BROADCAST] <span style='color:red'>$tuserName was purged.</span>");
			$con->query("INSERT INTO chat (userId,message,dateSent,alert) VALUES ($userId,'$message',now(),0)");
			$message="You purged $tuserName.";
		    }else{
			$message="You do not have permission to do that";
		    }
		}
		break;
	    case "group":
		if(count($i)==3){
		    $tuserName=$con->real_escape_string($i[1]);
		    $groupName=$con->real_escape_string($i[2]);
		    $tuserPower=User::power($tuserName);
		    if($tuserPower>=User::power($tuserName)){
			User::alterGroup($tuserName,$groupName);
			$message=$con->real_escape_string("<span style='color:green'>[BROADCAST] $tuserName was moved to group $groupName.</span>");
			$con->query("INSERT INTO chat (userId,message,dateSent,alert) VALUES ($userId,'$message',now(),0)");
			$message="You altered the group of $tuserName to $groupName.";
		    }else{
			$message="You do not have permission to do that.";
		    }
		}
		break;
	    case "delete":
		if(count($i)==2){
		    $chatId=$i[1];
		}
		break;
	    case "clear":
		$con->query("DELETE FROM chat WHERE userId=$userId AND alert=1");
		$message="Cleared your alerts.";
		break;
	    default:
		$message="That command was not found.";
	}
	$con->query("INSERT INTO chat (userId,message,dateSent,alert) VALUES ($userId,'"."[ALERT] ".$message."',now(),1)");
    }
}
?>
