<?php
if(Misc::li()&&
   isset($_POST['message'])&&
   User::priv("user.chat")){
    $userId=User::id($_SESSION['userName']);
    $message=trim($_POST['message']);
    if(!(strlen($message)<256&&strlen($message)>2)){
	echo "Your message is too long.";
    }
    elseif($message[0]=='!'&&User::priv("user.chatCommand")){
	echo Misc::parseCommand($message);
    }
    else{
	$message=$con->real_escape_string(htmlspecialchars($message));
	$con->query("INSERT INTO chat (userId,message,dateSent) VALUES ($userId,'$message',now())");
    }
}
?>
<h1 id="title"> Chat</h1>
<div id="chatContainer">
    <div id="chatBox">
	<?php
	if(Misc::li())
	    $userId=User::id($_SESSION['userName']);
	else $userId=0;
	$result=$con->query("SELECT user.id as userId,groups.color as groupColor,user.groupName as groupName,
				    user.name as userName,chat.message as chatMessage,chat.dateSent as chatDateSent,
				    chat.id as chatId
			     FROM chat
			     INNER JOIN user   ON chat.userId=user.id
			     INNER JOIN groups ON user.groupName=groups.name
			     WHERE alert=0 OR user.id=$userId
			     ORDER BY chat.id ASC
			     ");
	if($result->num_rows>0):
	?>
	    <?php
	    while($row=$result->fetch_assoc()):
	    if(!isset($row[$row['userId']])){
		$path=User::avatar($row['userName'],32);
		$base64=base64_encode(file_get_contents($path));
		$row[$row['userId']]=$base64;
	    }
	    ?>
		<div class="message"><img src="data:image/jpeg;base64,<?=$row[$row['userId']]?>" alt="avatar"><div class="chatId">No. <?=$row['chatId']?></div><div class="messageDate"><?=$row['chatDateSent']?></div><span class="content">[<span style='color:<?=$row['groupColor']?>'><b><?=$row['groupName']?></span></b>] <a style="text-decoration:none;color:black;" href="/profile/<?=$row['userName']?>"><?=$row['userName']?></a>: <?=$row['chatMessage']?></span></div>
	    <?php endwhile; ?>
	<?php else: ?>
	    <p>No chat messages.</p>
	<?php endif; ?>
    </div>
    <?php if(Misc::li()&&User::priv("user.chat")): ?>
    <form method="post" action="/chat#autoreload">
	<input type="text" autocomplete="off" name="message" pattern=".{3,255}" required title="3 to 255 characters" id="message" autofocus>
	<input type="submit" autocomplete="off" value="Send">
    </form>
    <?php else: ?>
    <form>
	<input type="text" autocomplete="off" name="message" placeholder="You have to be logged in to chat, and have permission to." disabled>
	<input type="submit" autocomplete="off" value="Send" disabled>
    </form>
    <?php endif; ?>
    <span id="reloadCB"><input type="checkbox" onclick="toggleAutoRefresh(this);" id="reloadCBin"> Auto Refresh</span>
</div>
<style>
 #title:before{
     font-family:awesome;
     content:"\f086";
 }
 #chatContainer{
     height:20em;
     border:1px solid black;
     position:relative;
     padding-bottom:1.5em;
 }
 #chatBox{
     height:100%;
     width:100%;
     overflow-y:scroll;
 }
 #chatContainer input[type="text"]{
     position:absolute;
     width:calc(100% - 5px);
     bottom:0;
     left:0;
     width:80%;
 }
 #chatContainer input[type="submit"]{
     position:absolute;
     width:calc(100% - 5px);
     bottom:0;
     right:0;
     width:20%;
 }
 .message{
     width:100%;
     border-bottom:1px solid black;
     padding-top:0.6em;
     padding-bottom:0.5em;
     position:relative;
 }
 .message img{
     position:absolute;
     top:1.5px;
 }
 .messageDate{
     font-size:0.6em;
     top:0;
     position:absolute;
     left:4em;
    }
    .chatId{
    font-size:0.6em;
    bottom:0;
    position:absolute;
    left:4em;
    }
 .content{
     margin-left:2em;
 }

    @media (max-height: 600px) {
    #chatContainer{
    height:10em;
    }
    }
 #reloadCB{
     position:absolute;
     z-index:2;
     top:-1.5em;
 }

</style>
<script
    src="https://code.jquery.com/jquery-3.1.1.min.js"
    integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
    crossorigin="anonymous"></script>
<script>
 var chatBox=document.getElementById("chatBox");
 chatBox.scrollTop=chatBox.scrollHeight;
 var reloading;
 if(window.location.hash=="#autoreload")
     document.getElementById("reloadCBin").checked=true;
 function checkReloading(){
     if (window.location.hash=="#autoreload"){
	 reloading=setTimeout(function(){
	     console.log(document.getElementById("message").value)
	     if(document.getElementById("message").value.length==0)
		 window.location.reload();
	     else
		 checkReloading();
	 },10000);
     }
 }
 function toggleAutoRefresh(cb) {
     if (cb.checked) {
	 window.location.replace("#autoreload");
	 reloading=setTimeout("window.location.reload();",10000);
     } else {
	 window.location.replace("#");
	 clearTimeout(reloading);
     }
 }
 window.onload=checkReloading();
</script>
