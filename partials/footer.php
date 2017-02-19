<p>Contact: adam@leafscript.net, <a href="https://github.com/ytlas">Github profile</a></p>
<div style="position:absolute;left:50%;top:3.5em">
    <div style="position: relative; left: -50%;">
	<?php
	if(Misc::li())
	    echo '<span id="status"><a href="/profile/'.$_SESSION['userName'].'"><span style="color:'.$_SESSION['color'].'">['.$_SESSION['groupName'].']</span> '.$_SESSION['userName']."</a></span>";
	else
	    echo '<span id="status">Not logged in</span>';
	?>
    </div>
</div>
<style>
 footer {
     clear:both;
 }
</style>
