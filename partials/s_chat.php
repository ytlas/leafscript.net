<?php
if(Misc::li()&&
   isset($_POST['message'])&&
   User::priv("user.chat")){
    $userId=User::id($_SESSION['userName']);
    $message=trim($_POST['message']);
    if(!(strlen($message)<256&&strlen($message)>2)){
	echo "Your message is too long.";
    }else{
	$message=$con->real_escape_string(htmlspecialchars($message));
	$con->query("INSERT INTO chat (userId,message,dateSent) VALUES ($userId,'$message',now())");
    }
}
?>
<h1 id="title"> Chat</h1>
<div id="chatContainer">
    <div id="chatBox">
	<?php
	$result=$con->query("SELECT user.id as userId,groups.color as groupColor,user.groupName as groupName,
				    user.name as userName,chat.message as chatMessage,chat.dateSent as chatDateSent
			     FROM chat
			     INNER JOIN user   ON chat.userId=user.id
			     INNER JOIN groups ON user.groupName=groups.name
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
		<div class="message"><img src="data:image/png;base64,<?=$row[$row['userId']]?>" alt="avatar"><div class="messageDate"><?=$row['chatDateSent']?></div><span class="content">[<span style='color:<?=$row['groupColor']?>'><b><?=$row['groupName']?></span></b>] <?=$row['userName']?>: <?=$row['chatMessage']?></span></div>
	    <?php endwhile; ?>
	<?php else: ?>
	    <p>No chat messages.</p>
	<?php endif; ?>
    </div>
    <?php if(Misc::li()): ?>
    <form method="post" action="/chat">
	<input type="text" autocomplete="off" name="message" pattern=".{3,255}" required title="3 to 255 characters" autofocus>
	<input type="submit" autocomplete="off" value="Send">
    </form>
    <?php else: ?>
    <form>
	<input type="text" autocomplete="off" name="message" placeholder="You have to be logged in to chat" disabled>
	<input type="submit" autocomplete="off" value="Send" disabled>
    </form>
    <?php endif; ?>
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
 .content{
     margin-left:2em;
 }

    @media (max-height: 600px) {
    #chatContainer{
    height:10em;
    }
    }

</style>
<script
    src="https://code.jquery.com/jquery-3.1.1.min.js"
    integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
    crossorigin="anonymous"></script>
<script>
 var chatBox=document.getElementById("chatBox");
 chatBox.scrollTop=chatBox.scrollHeight;

</script>
