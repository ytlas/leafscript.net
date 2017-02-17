<?php
if(isset($_POST['userName'])&&isset($_POST['userPassword'])&&isset($_POST['userEmail'])){
    $userName=$_POST['userName'];
    $userPassword=$_POST['userPassword'];
    $userEmail=$_POST['userEmail'];
    if(Misc::validateUsername($userName)&&Misc::validatePassword($userPassword)&&Misc::validateEmail($userEmail)){
	if(!User::exists($userName)&&User::register($userName,$userPassword,$userEmail)){
	    echo "<p>Successfully registered as ".$userName."! You must now click the activation link sent to ".$userEmail." to be able to log in.";
	    return true;
	}
	else{
	    echo "<p>A user with that name already exists.</p>";
	    return false;
	}
    }else{
	echo "<p>Something went wrong. Here are some things that might help you:</p>
	      <ul>
		 <li>Your username may only contain alphanumeric characters as well as underscores.</li>
		 <li>Your username may only be between 2 to 17 characters long. Your password may only be between 7 and 33 characters long.</li>
		 <li>Your e-mail must be valid.</li>
	      </ul>
	     ";
    }
}

?>
