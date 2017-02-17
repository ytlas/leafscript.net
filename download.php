<?php
session_start();
include 'classes/Sql.php';
include 'classes/Misc.php';
include 'classes/File.php';
include 'classes/User.php';
$con=new mysqli('localhost','root','Qw!kmdo<','ls');
if((Misc::li()&&
   User::priv("user.downloadFiles")&&
    isset($_GET['fileId']))||
   File::isPublic($con->real_escape_string($_GET['fileId']))){
    $fileId=$con->real_escape_string($_GET['fileId']);
    if(File::exists($fileId)&&File::access($fileId)){
	$userName=File::owner($fileId);
	$result=$con->query("SELECT files.name as fileName,files.size as fileSize,files.type as fileType
			 FROM files
			 INNER JOIN user ON files.userId=user.id
			 WHERE files.id='$fileId' AND user.name='$userName'");
	$row=$result->fetch_assoc();
	$file="../storage/".$userName.'/'.$row['fileName'];
	$fileType=$row['fileType'];
	if($fileType=="text/html"){
	    echo file_get_contents("../storage/".$userName.'/'.$row['fileName']);;
	    exit;
	}
	elseif($fileType=='image/jpeg'){
	    $file=imagecreatefromjpeg($file);
	    header('Content-Type: image/jpeg');
	    imagejpeg($file);
	    imagedestroy($file);
	}
	elseif($fileType=='image/png'){
	    $file=imagecreatefrompng($file);
	    header('Content-Type: image/png');
	    imagepng($file);
	    imagedestroy($file);
	}
	elseif (file_exists($file)) {
	    header('Content-Description: File Transfer');
	    header("Content-Type: $fileType");
	    header('Content-Disposition: attachment; filename="'.basename($file).'"');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($file));
	    readfile($file);
	    exit;
	}
	else{
	    exit;
	}
    }
}else{
    echo "You probably don't have access to this file, if you think there is something wrong with the site please contact me @ adam@leafscript.net";
}
?>
