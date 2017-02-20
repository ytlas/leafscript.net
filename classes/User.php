<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/con.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/classes/Misc.php';
class User{
    static function exists($userName){
	global $con;
	return $result=$con->query("SELECT id FROM user WHERE name='$userName'")->num_rows==1;
    }
    static function register($userName,$userPassword,$userEmail){
	global $con,$req;
	include_once $_SERVER['DOCUMENT_ROOT'] . '/securimage/securimage.php';
	$securimage = new Securimage();
	if ($securimage->check($_POST['registerCaptcha']) == false) {
	    echo "The security code entered was incorrect.<br /><br />";
	    echo "Please go <a href='javascript:history.go(-1)'>back</a> and try again.";
	}
	else{
	    $userName=$con->real_escape_string($userName);
	    $userEmail=$con->real_escape_string($userEmail);
	    // Users password gets hashed with a unique salt.
	    $userPassword=password_hash($userPassword,PASSWORD_BCRYPT);
	    // Users activation code is generated and stored and sent to the email address the user provided
	    $activationCode=md5(uniqid(rand(),true));
	    if($con->query("
	   INSERT INTO unactivated (userEmail,activationCode) VALUES ('$userEmail','$activationCode')
	   ")&&mail($userEmail,"Your activation code for leafscript.net","Hello ".$userName.". Please visit https://leafscript.net/activation?code=".$activationCode." to activate your account.")){
		if($con->query("INSERT INTO user
		     (name,password,email,dateRegistered,dateActive,ipLast) VALUES
		     ('$userName','$userPassword','$userEmail',now(),now(),'".$req->ip."')")){
		    return true;
		}else return false;
	    }
	}
    }
    static function alterGroup($userName,$groupName){
	global $con;
	$con->query("UPDATE user SET groupName='$groupName' WHERE name='$userName'");
    }
    static function alterPassword($userId,$userPassword){
	global $con;
	$userPassword=password_hash($userPassword,PASSWORD_BCRYPT);
	if($con->query("UPDATE user SET password='$userPassword' WHERE id=$userId"))
	    return true;else return false;
    }
    static function authenticate(){
	global $con,$req;
	if(isset($_SESSION['check'])&&$_SESSION['check']){
	    $result=$con->query("SELECT user.id as userId,user.name as userName,groups.name as groupName,groups.color
				 FROM user
				 INNER JOIN tokens ON user.id=tokens.userId
				 INNER JOIN groups ON user.groupName=groups.name
				 WHERE user.name='".$_SESSION["userName"]."' LIMIT 1");
	    if($result->num_rows==1){
		$row=$result->fetch_assoc();
		foreach($row as $key => $value) {
		    $_SESSION[$key]=$value;
		}
		$con->query("UPDATE user SET dateActive=now(),ipLast='".$req->ip."' WHERE name='".$row['userName']."'");
	    }
	    else{
		session_destroy();
	    }
	    return true;
	}
	else if(isset($_COOKIE['remember'])&&strlen($_COOKIE['remember'])==106){
	    /* There is a cookie, which is the right length 40session+32token+32user+2'|'
	       Now lets check it. ?*/
	    $plode = explode('|',$_COOKIE['remember']);
	    $session = $con->real_escape_string($plode[0]);
	    $token = $con->real_escape_string($plode[1]);
	    $user = $con->real_escape_string($plode[2]);
	    $result = $con->query("SELECT user,userId
				   FROM tokens
				   WHERE session='$session'
				   AND token='$token'
				   AND user='$user'");
	    if($result->num_rows==1){
		/* Cookie is completely valid!
		   Make a new cookie with the same session and another token. */
		$row = $result->fetch_assoc();
		$newusername=$row['user'];
		$newsession = $session;
		$newtoken = md5(uniqid(rand(), true));
		$newuser=md5($user);
		$userId=$row['userId'];
		$value = "$newsession|$newtoken|$newuser";
		$expire = date('Y-m-d H:i:s',time()+4184000);
		setcookie('remember',$value,time()+4184000,'/','leafscript.net',true,true);
		$con->query("UPDATE tokens
			     SET token='$newtoken',expire='$expire',user='$newuser'
			     WHERE session='$session'
			     AND token='$token'
			     AND user='$user'");
		$result=$con->query("SELECT user.id as userId, user.name as userName,groups.name as groupName,groups.color as color
				     FROM user
				     INNER JOIN tokens ON user.id=tokens.userId
				     INNER JOIN groups ON user.groupName=groups.name
				     WHERE user.id='$userId'");
		$row=$result->fetch_assoc();
		foreach($row as $key => $value) {
		    $_SESSION[$key]=$value;
		}
		$_SESSION['check']=true;
		return true;
	    }
	    else if($con->query("SELECT user FROM tokens WHERE session='$session' AND md5(user)='$user'")->num_rows==1){
		//TOKEN is different, session is valid
		//This user is probably under attack
		//Put up a warning, and let the user re-validate (login)
		//Remove the whole session (also the other sessions from this user?)
	    } else {
		//Cookie expired in database? Unlikely...
		//Invalid in what way?
	    }
	} else {
	    //No cookie, rest of the script
	}
	return false;
    }
    static function logout(){
	global $con;
	if(Misc::li()){
	    $plode = explode('|',$_COOKIE['remember']);
	    $session = $con->real_escape_string($plode[0]);
	    $result = $con->query("DELETE
				   FROM tokens
				   WHERE session='$session'");
	    setcookie("remember", "",time()-3600);
	    session_unset();
	    session_destroy();
	    return true;
	}
	return false;
    }
    static function isAllowed($userName,$permsName){
	global $con;
	return $con->query("SELECT permissions.name FROM permissions INNER JOIN groupsPerms ON permissions.name=groupsPerms.permsName INNER JOIN user ON groupsPerms.groupName=user.groupName WHERE user.name='$userName' AND (permissions.name='$permsName' OR permissions.name='god')")->fetch_assoc()>0;
    }
    static function id($userName){
	global $con;
	return $con->query("SELECT id FROM user WHERE name='$userName'")->fetch_assoc()['id'];
    }
    static function priv($permsName){
	return self::isAllowed($_SESSION['userName'],$permsName);
    }
    static function avatar($userName,$size){
	$prefix='../avatars/';
	$id=self::id($userName);
	if(file_exists($prefix.$id."_".$size.".jpg"))
	    return $prefix.$id."_".$size.".jpg";
	else
	    return $prefix."default_".$size.".png";
    }
    static function credentials($userName){
	global $con;
	$row=$con->query("SELECT user.name AS userName,user.dateRegistered,user.email AS userEmail,user.groupName,groups.color as groupColor
			  FROM user
			  INNER JOIN groups ON user.groupName=groups.name
			  WHERE user.name='$userName'")->fetch_assoc();

	$row['numFiles']=$con->query("SELECT files.id FROM files
				      INNER JOIN user ON files.userId=user.id
				      WHERE user.name='$userName'")->num_rows;
	return $row;
    }
    static function purge($userName){
	global $con;
	$userId=$con->query("SELECT id as userId FROM user WHERE name='$userName'")->fetch_assoc()['userId'];

    }
    static function power($userName){
	global $con;
	return $con->query("SELECT groups.power as power FROM user INNER JOIN groups ON user.groupName=groups.name WHERE user.name='$userName'")->fetch_assoc()['power'];
    }
}
?>
