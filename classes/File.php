<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/con.php';
class File{
    // Functions to confirm that user owns file
    static function owns($fileId){
	return self::userOwns($_SESSION['userName'],$fileId);
    }
    static function userOwns($userName,$fileId){
	global $con;
	return $con->query("SELECT user.id FROM files INNER JOIN user ON files.userId=user.id WHERE user.name='$userName' AND files.id=$fileId LIMIT 1")->num_rows==1;
    }
    static function access($fileId){
	return self::userAccess($_SESSION['userName'],$fileId);
    }
    static function userAccess($userName,$fileId){
	global $con;
	return $con->query("SELECT user.name as userName,files.id as fileId,files.name as fileName,files.userId as userId,files.size as fileSize,files.type as fileType FROM sharedFiles INNER JOIN files ON sharedFiles.fileId=files.id INNER JOIN user ON sharedFiles.userId=user.id WHERE (user.name='$userName' OR files.isPublic=1) AND files.id=$fileId")->num_rows==1 ||
	       self::userOwns($userName,$fileId) ||
	       self::isPublic($fileId);
    }
    static function owner($fileId){
	global $con;
	return $con->query("SELECT user.name AS userName FROM files INNER JOIN user ON files.userId=user.id WHERE files.id=$fileId")->fetch_assoc()['userName'];
    }
    // Get users files
    static function get(){
	return self::userGet($_SESSION['userName']);
    }
    static function userGet($userName){
	global $con;
	return $con->query("SELECT files.id,files.name,files.userId,files.size,files.type FROM files
			    INNER JOIN user ON files.userId=user.id
			    WHERE user.name='$userName'");
    }
    static function getShared(){
	return self::userGetShared($_SESSION['userName']);
    }
    static function userGetShared($userName){
	global $con;
	return $con->query("SELECT user.name as userName,files.id as fileId,files.name as fileName,files.userId as userId,files.size as fileSize,files.type as fileType FROM sharedFiles INNER JOIN files ON sharedFiles.fileId=files.id INNER JOIN user ON files.userId=user.id INNER JOIN user as ruser ON sharedFiles.userId=ruser.id WHERE ruser.name='$userName'");
    }
    static function sharedWith($fileId){
	global $con;
	return $con->query("SELECT ruser.name as userName FROM sharedFiles INNER JOIN files ON sharedFiles.fileId=files.id INNER JOIN user ON files.userId=user.id INNER JOIN user as ruser ON sharedFiles.userId=ruser.id WHERE sharedFiles.fileId=$fileId");
    }
    static function share($userId,$fileId){
	global $con;
	return $con->query("INSERT INTO sharedFiles (userId,fileId) VALUES ($userId,$fileId)");
    }
    static function deShare($userId,$fileId){
	global $con;
	return $con->query("DELETE FROM sharedFiles WHERE userId=$userId AND fileId=$fileId");
    }
    // Delete a file
    static function delete($fileId){
	return self::userDelete($_SESSION['userName'],$fileId);
    }
    static function userDelete($userName,$fileId){
	global $con;
	$row=$con->query("SELECT name FROM files WHERE id=$fileId")->fetch_assoc();
	unlink('../storage/'.$userName.'/'.$row['name']);
	$con->query("DELETE file FROM files AS file INNER JOIN user ON file.userId=user.id WHERE user.name='$userName' AND file.id='$fileId'");
	return $con->affected_rows;
    }

    // Confirms that file exists
    static function exists($fileId){
	global $con;
	return $con->query("SELECT id FROM files WHERE id='$fileId'")->num_rows==1;
    }
    static function makePublic($fileId){
	global $con;
	return $con->query("UPDATE files SET isPublic=1 WHERE id=$fileId");
    }
    static function deMakePublic($fileId){
	global $con;
	return $con->query("UPDATE files SET isPublic=0 WHERE id=$fileId");
    }
    static function isPublic($fileId){
	global $con;
	return $con->query("SELECT id FROM files WHERE id=$fileId AND isPublic=1")->num_rows==1;
    }
}
?>
