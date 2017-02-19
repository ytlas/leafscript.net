<h1 id="title"> Userlist</h1>
<?php
$result=$con->query("SELECT user.id as userId,user.name as userName,groups.name as groupName,groups.color as color,user.dateRegistered,user.dateActive
		     FROM user
		     INNER JOIN groups ON user.groupName=groups.name ORDER BY groups.power DESC LIMIT 50");
while ($row=$result->fetch_assoc()):
	$userId=$row['userId'];
$userName=$row['userName'];
$groupName=$row['groupName'];
$color=$row['color'];
$dateRegistered=$row['dateRegistered'];
$dateActive=$row['dateActive'];
$path=User::avatar($row['userName'],64);
$base64=base64_encode(file_get_contents($path));
?>
    <div class="user"><img src="data:image/jpeg;base64,<?=$base64?>" alt="avatar"><div class="dateRegistered">Registered: <?=$dateRegistered?></div><div class="dateActive">Last active: <?=$dateActive?></div><span class="userName">{<b style="color:<?=$color?>"><?=$groupName?></b>} <a href="/profile/<?=$userName?>"><?=$userName?></a></span></div>
<?php endwhile;?>
</table>
<style>
 #title:before{
     font-family:awesome;
     content:"\f0c0";
 }
 .user{
     width:100%;
     height:4em;
 }
 .user {
     position:relative;
     margin-bottom:1em;
 }
 .dateRegistered{
     position:absolute;
     top:0.5em;
     left:7em;
     font-size:0.6em;
 }
 .dateActive{
     position:absolute;
     bottom:0.5em;
     font-size:0.6em;
     left:7em;
 }
 .userName{
     position:absolute;
     top:1.4em;
     left:4em;
 }
</style>
