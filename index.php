<?php
// Display errors if there are any.
ini_set('display_errors', 'on'); error_reporting(E_ALL);
// Initiate a session
session_start();

// This file declares a variable $con with a connection to my mariadb database.
include 'con.php';

// If there are errors with the mysql connection, kill the connection but display the error.
if($con->connect_error) die("Connection failed: " . $con->connect_error);

// For each php file in classes/, include it.
include 'classes/Sql.php';
include 'classes/Req.php';
include 'classes/Misc.php';
include 'classes/User.php';
if(isset($_GET['site'])&&file_exists('partials/s_'.$_GET['site'].'_headers.php')){
    include 'partials/s_'.$_GET['site'].'_headers.php';
}

$req=new Req($_SERVER['REMOTE_ADDR'],$_SERVER['REQUEST_METHOD'],$_SERVER['HTTP_USER_AGENT']);

// Authenticate user
User::authenticate();

// If get variable site is set, which is should always be, set req's site variable to it.
if(isset($_GET['site'])){
    $req->site=$_GET['site'];
}

// Log the request uri.
$req->log($_SERVER['REQUEST_URI']);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
	<?php include 'partials/head.php';?>
    </head>
    <body>
	<header><?php include 'partials/header.php';?></header>
	<hr><nav><?php include 'partials/nav.php';?></nav><hr>
	<section>
	    <?php
	    if(isset($_GET['site'])&&file_exists('partials/s_'.$_GET['site'].'.php')){
		include 'partials/s_'.$_GET['site'].'.php';
	    }
	    else echo "<h1>404 error</h1><p>The page you were looking for was not found</p>";
	    ?>
	</section>
	<footer><hr><?php include 'partials/footer.php';?><hr></footer>
    </body>
</html>
