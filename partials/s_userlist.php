<table>
    <tr>
	<th>Group</th>
	<th>Username</th>
	<th>Date registered</th>
	<th>Date last active</th>
    </tr>
<?php
$result=$con->query("SELECT user.id as userId,user.name as userName,groups.name as groupName,groups.color as color,user.dateRegistered,user.dateActive
		     FROM user
		     INNER JOIN groups ON user.groupName=groups.name ORDER BY groups.power DESC");
while ($row=$result->fetch_assoc()){
    $userId=$row['userId'];
    $userName=$row['userName'];
    $groupName=$row['groupName'];
    $color=$row['color'];
    $dateRegistered=$row['dateRegistered'];
    $dateActive=$row['dateActive'];
    printf("<tr><td><center>[<span style='color:%s'>%s</span>]</center></td><td><center><a href='/profile/%s'>%s</a></center></td><td>%s</td><td>%s</td></tr>",$color,$groupName,$userName,$userName,$dateRegistered,$dateActive);
}
?>
</table>
<style>
 table {
     border-collapse:collapse;
     table-layout:fixed;
     width:100%;
 }
 table tr{
     border-bottom:0;
 }
 table td {
     width:25%;
     word-wrap:break-word;
     border:1px solid black;
     border-bottom:none;
 }
 table tr:last-child{
     border-bottom:1px solid black;
 }
</style>
