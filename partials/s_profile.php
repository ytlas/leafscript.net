<?php if(isset($_GET['userName'])): ?>
    <?php
    $userName=$con->real_escape_string($_GET['userName']);
    if(Misc::li()&&
       User::priv("user.avatar")&&
       isset($_POST['upload'])&&
       $userName==$_SESSION['userName']){
	$fileName=$_FILES['file']['name'];
	$fileTmp= $_FILES['file']['tmp_name'];
	$fileSize=$_FILES['file']['size'];
	$fileType=$_FILES['file']['type'];
	if(substr($fileType,0,5)=='image'){
	    $im=new Imagick();
	    $im->readImage($fileTmp);
	    $im->setImageCompression(Imagick::COMPRESSION_JPEG);
	    $im->setImageCompressionQuality(100);
	    $im->stripImage();
	    //$im->setImageFormat("jpeg");
	    $userId=$con->query("SELECT id as userId FROM user WHERE name='".$_SESSION['userName']."'")->fetch_assoc()['userId'];
	    for($i=5;$i>0;$i--){
		$dim=16*($i*2);
		$im->thumbnailImage($dim,$dim,0);
		$im->writeImage("../avatars/".$userId."_".$dim.".jpg");
	    }
	    $im->destroy();
	}else{
	    echo "This file is not an image.";
	}
    }
    if(User::exists($userName)):
    ?>
	<h3>Profile for <?=$userName?>:</h3>
	<?php
	$path=User::avatar($userName,128);
	$base64=base64_encode(file_get_contents($path));
	$row=User::credentials($userName);
	?>
	<div id="avatar">
	    <img src="data:image/png;base64,<?=$base64?>">
	    <?php if(Misc::li()&&$_SESSION['userName']==$_GET['userName']): ?>
	    <form action="/profile/<?=$_SESSION['userName']?>" method="post" enctype="multipart/form-data">
		<input type="file" name="file"><br>
		<input type="submit" name="upload" value="Change avatar">
	    </form>
	    <?php endif; ?>
	</div>
	<div id="credentials">
	    <p>Username: <?=$row['userName']?><br>
		Group: {<span style="color:<?=$row['groupColor']?>"><?=$row['groupName']?></span>}<br>
		Files uploaded: <?=$row['numFiles']?><br>
		Registered since: <?=$row['dateRegistered']?>
	    </p>
	</div>
    <?php else: ?>
	    <p>The user <?=$userName?> does not exist.</p>
    <?php endif; ?>
<?php else: ?>
    <p>There is no valid specified profile name in the url.</p>
<?php endif; ?>
<style>
 #avatar{
     width:8em;
     margin:1em;
 }
 #avatar img{
     max-height:8em;
     max-height:8em;
 }
 #credentials{
     width:50%;
 }
 #credentials,#avatar{
     float:left;
 }
</style>
