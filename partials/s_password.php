<center><span id="path"><a href="/account">account</a> > <a href="/password">password</a></span></center>
<?php
if(Misc::li()){
    if(isset($_POST['currentUserPassword'])&&
       isset($_POST['newUserPassword'])){
	$currentUserPassword=$_POST['currentUserPassword'];
	$newUserPassword=$_POST['newUserPassword'];
	if(Misc::validatePassword($currentUserPassword)&&
	   Misc::validatePassword($newUserPassword)){
	    $row=$con->query("SELECT id,password FROM user WHERE name='".$_SESSION['userName']."'")->fetch_assoc();
	    if(password_verify($currentUserPassword,$row['password'])){
		$userId=$row['id'];
		$userPassword=password_hash($newUserPassword,PASSWORD_BCRYPT);
		if($con->query("UPDATE user SET password='$userPassword' WHERE id=$userId"))
		    echo "<p>You successfully changed your password. Your current sessions do however remain valid.</p>";
		else
		    echo "<p>Unknown error occured.</p>";
	    }
	    else{
		echo "<p>The current password you entered was wrong. You could try recovering it with your email by logging out and clicking on account and going to recover password.</p>";
	    }
	}
	else{
	    echo "<p>Something went wrong. Either or both of the passwords you entered are invalid.</p>";
	}
    }else{
	echo '
<p>You are logged in, rendering password form.</p>
<form method="post" action="/password">
<input type="password" placeholder="Current password" name="currentUserPassword" pattern="[^.*$]{4,32}"><br>
<input type="password" placeholder="New password" name="newUserPassword" pattern="[^.*$]{4,32}">
<input type="submit" value="Change password">
</form>
	 ';
    }
}else{
    if(isset($_GET['code'])){
	$activationCode=$con->real_escape_string($_GET['code']);
	if($con->query("SELECT userEmail
			FROM unactivated
			WHERE activationCode='$activationCode'")->num_rows==1){
	    printf('
<p>Success! You may now type in your new desired password to change it:</p>
<form method="post" action="/password">
<input type="password" placeholder="Password" name="userPassword" pattern="[^.*$]{4,32}">
<input type="hidden" name="code" value="%s">
<input type="submit" value="Change password">
</form>
		 ',$activationCode);
	}
    }
    elseif(isset($_POST['userPassword'])&&
	   isset($_POST['code'])){
	$userPassword=$_POST['userPassword'];
	$activationCode=$con->real_escape_string($_POST['code']);
	if(Misc::validatePassword($userPassword)){
	    $result=$con->query("SELECT user.Id as userId,user.email as userEmail
				 FROM unactivated
				 INNER JOIN user
				 ON unactivated.userEmail=user.email
				 WHERE activationCode='$activationCode'");
	    if($result->num_rows==1){
		$row=$result->fetch_assoc();
		$userId=$row['userId'];
		$userEmail=$row['userEmail'];
		if(User::alterPassword($userId,$userPassword)&&
		   $con->query("DELETE FROM unactivated
				WHERE userEmail='$userEmail'")){
		    echo "<p>Successfully changed your password! You may now try and log in with it.</p>";
		    return true;
		}
	    }
	}
	echo "<p>Error</p>";
    }
    elseif(isset($_POST['userEmail'])
	   &&isset($_POST['passwordCaptcha'])){
	include_once $_SERVER['DOCUMENT_ROOT'].'/securimage/securimage.php';
	$securimage = new Securimage();
	if ($securimage->check($_POST['passwordCaptcha']) == false) {
	    echo "The security code entered was incorrect.<br /><br />";
	    echo "Please go <a href='/password'>back</a> and try again.";
	    return false;
	}else{
	    $activationCode=md5(uniqid(rand(),true));
	    $userEmail=$con->real_escape_string($_POST['userEmail']);
	    if(Misc::validateEmail($userEmail)&&
	       Sql::emailExists($userEmail)&&
	       mail($userEmail,"Your lost password","Here is your activation link to change your password: https://leafscript.net/password?code=".$activationCode)&&
	       $con->query("INSERT INTO unactivated (userEmail,activationCode) VALUES ('$userEmail','$activationCode')")){
		echo "<p>An email has been sent to ".$userEmail." with the activation link to change your password.</p>";
	    }
	}
    }
    else{
	echo '
<p>You can recover your lost password here. Please enter the email you registered with, and you will have a link sent to you with a link from which you can change your password.</p>
<form method="post" action="/password">
Email:
<input type="email" placeholder="Registered email" name="userEmail"><br>
Captcha: <input type="text" placeholder="Captcha" name="passwordCaptcha" size="10" maxlength="6" />
<input type="submit" value="Submit">
</form>
<img id="captcha" src="https://leafscript.net/securimage/securimage_show.php" alt="CAPTCHA Image">
    <a href="#" onclick="document.getElementById(\'captcha\').src = \'/securimage/securimage_show.php?\' + Math.random(); return false">[ Different Image ]</a>
<p>DISCLAIMER: If you register and log in, the website will use cookies to authenticate your sessions. If you are against this, do not log in.</p>
	';
    }
}
?>
