<?php
if(isset($_GET['code'])&&strlen($_GET['code'])==32){
    $activationCode=$con->real_escape_string($_GET['code']);
    $result=$con->query("SELECT unactivated.userEmail,user.name as userName
			 FROM unactivated
			 INNER JOIN user ON unactivated.userEmail=user.email
			 WHERE activationCode='$activationCode'");
    if($result->num_rows==1){
	$row=$result->fetch_assoc();
	$userEmail=$row['userEmail'];
	$userName=$row['userName'];
	User::alterGroup($userName,'noob');
	$con->query("DELETE FROM unactivated WHERE userEmail='$userEmail'");
	echo "Your account is now activated and you are able to log in!";
    }else{
	echo "What are you doing?";
    }
}
?>
