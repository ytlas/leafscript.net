<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/classes/File.php';
if(!Misc::li())
    exit;
if(User::priv("user.uploadFiles")&&
   isset($_POST['upload'])){
    $file=$con->real_escape_string($_FILES['file']['name']);
    $fileTmp=$_FILES['file']['tmp_name'];
    $fileSize=$_FILES['file']['size'];
    $fileType=$_FILES['file']['type'];
    $userName=$_SESSION['userName'];
    $userId=$_SESSION['userId'];
    if (!file_exists("../storage/".$userName)) {
	mkdir("../storage/".$userName,0700);
    }
    if(isset($_POST['fileName'])&&$_POST['fileName']!='')
	$file=$con->real_escape_string($_POST['fileName']);
    $filePath="../storage/".$userName."/".$file;
    if($file!=''&&
       $fileSize!=0&&
       !($con->query("SELECT id FROM files WHERE name='$file' AND userId='$userId' LIMIT 1")->num_rows==1)){
	$isPublic=0;
	if(isset($_POST['public'])){
	    $isPublic=1;
	}
	if($con->query("INSERT INTO files (name,userId,size,type,isPublic) VALUES ('$file',$userId,$fileSize,'$fileType',$isPublic)")){
	    move_uploaded_file($fileTmp,$filePath);
	}
    }
    else{
	if($con->query("UPDATE files SET size=$fileSize,type='$fileType' WHERE name='$file'"))
	    move_uploaded_file($fileTmp,$filePath);
    }
}
elseif((User::priv("user.deleteFiles")||User::priv("user.shareFiles"))&&isset($_POST['options'])){
    foreach($_POST['options'] as $fileId => $option){
	$fileId=$con->real_escape_string($fileId);
	if(File::owns($fileId)){
	    if($option=='d'){
		File::delete($fileId);
	    }elseif($option=='s'){
		if(isset($_POST['shareUserName'])){
		    $sUserName=$con->real_escape_string($_POST['shareUserName']);
		    if(strcasecmp($_SESSION['userName'],$sUserName)!=0&&User::exists($sUserName)){
			$userId=User::id($sUserName);
			File::share($userId,$fileId);
		    }
		}
	    }elseif($option=='e'){
		if(isset($_POST['shareUserName'])){
		    $sUserName=$con->real_escape_string($_POST['shareUserName']);
		    if(strcasecmp($_SESSION['userName'],$sUserName)!=0&&User::exists($sUserName)){
			$userId=User::id($sUserName);
			File::deShare($userId,$fileId);
		    }
		}
	    }elseif($option=='p'){
		File::makePublic($fileId);
	    }elseif($option=='u'){
		File::deMakePublic($fileId);
	    }
	}
    }
}
?>
<h1> Files</h1>
<center><span id="path"><a href="/account">account</a> > <a href="/files">files</a></span></center>
<?php if(User::priv("user.viewFiles")): $result=File::get(); ?>
    <form method="post" action="/files">
	<table border='1' style="float:right" id="filesTable">
	    <tr>
		<th>Name</th><th>Size</th><th>Type</th><th>Opts</th><td>Shared with</td>
	    </tr>
	    <?php while($row=$result->fetch_assoc()):
	    $name=$row['name'];
	    $size=$row['size']/1000;
	    $type=$row['type'];
	    $id=$row['id'];
	    ?>
		<tr><td><a href='/d/<?=$id?>'><?=$name?></a></td>
		    <td><?=$size?> kb</td>
		    <td><?=$type?></td>
		    <td><center>
			<select name="options[<?=$id?>]">
			    <option disabled selected value>----</option>
			    <option value="d">Delete</option>
			    <option value="s">Share</option>
			    <option value="e">DeShare</option>
			    <option value="p">Make public</option>
			    <option value="u">DeMake public</option>
			</select>
		    </center></td>
		    <td><?php
			$sresult=File::sharedWith($id);
			if($sresult->num_rows>0){
			    if(File::isPublic($id)){
				print("It is public, ");
			    }
			    while($srow=$sresult->fetch_assoc()){
				print($srow['userName'].', ');
			    }
			}
			else{
			    print("No one");
			}
			?>
		    </td>
		</tr>
	    <?php endwhile; ?>
	</table>
	<input type="reset" value="Reset options">
	<?php if(User::priv("user.shareFiles")): ?>
	    <p>If you have selected some (de?)Shares, type the user you want to share it to here:
		<input type="text" name="shareUserName" placeholder="Users name"></p>
	<?php endif;if(User::priv("user.deleteFiles")||User::priv("user.shareFiles")): ?>
	    <p>To perform your desired deletions and modifications, press the button:<br>
		<input type="submit" name="saveFileChanges" value="Save changes"></p>
	<?php endif; ?>
    </form>
    <?php
    $result=File::getShared();
    if($result->num_rows>0): ?>
	<h1> Shared files</h1>
	<table border='1' style="float:right" id="sharedTable">
	    <tr>
		<th>Name</th><th>Size</th><th>Type</th><th>Shared by</th>
	    </tr>
	    <?php for($i=0;$row=$result->fetch_assoc();$i++):
	    $name=$row['fileName'];
	    $size=$row['fileSize']/1000;
	    $type=$row['fileType'];
	    $userName=$row['userName'];
	    $id=$row['fileId'];
	    ?>
		<tr><td><a href='/d/<?=$id?>'><?=$name?></a></td>
		    <td><?=$size?> kb</td>
		    <td><?=$type?></td>
		    <td><?=$userName?></td>
		</tr>
	    <?php endfor; ?>
	</table>
    <?php endif; ?>
<?php else: ?>
    <p>You don't have permission to view any files. You would have to at least be in the group 'member' to do this.</p>
<?php endif;if(User::priv("user.uploadFiles")): ?>
    <p>-</p>
    <h1> Upload File</h1>
    <form action="/files" method="post" enctype="multipart/form-data">
	<input type="text" placeholder="OPTIONAL file name" name="fileName"><br>
	<input type="file" name="file"><br>
	<input type="submit" name="upload" value="Upload selected file">
    </form>
<?php endif; ?>
<style>
 section h1:before{
     font-family:awesome;
     content:"\f0c5";
 }
 table {
     border-collapse:collapse;
     table-layout:fixed;
     width:100%;
 }
 #filesTable td,#sharedTable td{
     word-wrap:break-word;
 }
 /* #filesTable td input[type="radio"]{
    float:left;
    } */
</style>
