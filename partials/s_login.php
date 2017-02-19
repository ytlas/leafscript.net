<?php
$flag=false;
if(isset($_POST['userName'])&&isset($_POST['userPassword'])&&!Misc::li()){
    $userName=$_POST['userName'];
    $userPassword=$_POST['userPassword'];
    $userEmail="";
    if(Misc::validateUsername($userName)&&Misc::validatePassword($userPassword)){
	if(User::exists($userName)){
	    $userName=$con->real_escape_string($userName);
	    $result=$con->query("SELECT id,name,password,groupName,email
				 FROM user
				 WHERE name='$userName'");
	    if($result->num_rows==1){
		$row=$result->fetch_assoc();
		if($row['groupName']!='banned'){
		    if($con->query("SELECT userEmail FROM unactivated WHERE userEmail='".$row['email']."'")->num_rows==0){
			if(password_verify($userPassword,$row['password'])){
			    $session = sha1(uniqid());
			    $token = md5(uniqid(rand(), true));
			    $user=md5($userName);
			    $userId=$row['id'];
			    $value = "$session|$token|$user";
			    $expire=date('Y-m-d H:i:s',time()+4184000);
			    $con->query("INSERT INTO tokens
				 (session,token,user,expire,userId) VALUES
				 ('$session','$token','$user','$expire',$userId)");

			    if(setcookie('remember',$value,time()+4184000,'/','leafscript.net',true,true)){
				$_COOKIE['remember']=$value;
				if(User::authenticate()){
				    $_SESSION['check']=true;
				    $flag=true;
				    echo "<h1>Successfully logged in.</h1>";
				}
			    }
			}
		    }else{
			echo "<p>Your account is not activated yet. Please visit the inbox of the email address you provided and click the activation link.</p>";
		    }
		}else{
		    echo "<p>You are banned.</p>";
		    $flag=true;
		}
	    }
	}
    }
}
if(!$flag)
    echo "<p>Invalid username or password</p>";
?>
