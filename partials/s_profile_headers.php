<?php
if(isset($_POST['upload'])&&isset($_GET['userName']))
    header("location:/profile/".$_GET['userName']);
?>
