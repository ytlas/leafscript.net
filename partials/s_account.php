<h1> Account</h1>
<?php if(!Misc::li()) : ?>
<h3>Log in:</h3>
<p>You may register an account, but I have not yet implemented the code to log in your account.</p>
<form method="post" action="/login">
    <input type="text" placeholder="Username" name="userName" pattern="[a-zA-Z0-9_]{3,16}" required title="3 to 16 characters and only alphanumeric characters."><br>
    <input type="password" placeholder="Password" name="userPassword" pattern="[^.*$]{4,32}" required title="8 to 32 characters.">
    <input type="submit" value="Log in">
</form>
<p><a href="/password">Have you lost your password?</a></p>
<h3>Register an account:</h3>
<form method="post" action="/register">
    <input type="text" placeholder="Username" name="userName" pattern="[a-zA-Z0-9_]{3,16}" required title="3 to 16 characters and only alphanumeric characters."><br>
    <input type="email" placeholder="Email address" name="userEmail"><br>
    <input type="password" placeholder="Password" name="userPassword" pattern="[^.*$]{8,32}" required title="8 to 32 characters."><br>
    <input type="text" placeholder="Captcha" name="registerCaptcha" size="10" maxlength="6">
    <input type="submit" value="Sign up">

</form>
<img id="captcha" src="https://leafscript.net/securimage/securimage_show.php" alt="CAPTCHA Image">
    <a href="#" onclick="document.getElementById(\'captcha\').src = \'/securimage/securimage_show.php?\' + Math.random(); return false">[ Different Image ]</a>
<p>DISCLAIMER: If you register and log in, the website will use cookies to authenticate your sessions. If you are against this, do not log in.</p>
<?php else : ?>
<p id="profile"><a href="/profile/<?=$_SESSION['userName']?>"> Profile</a></p>
<p id="files"><a href="/files"> Files</a></p>
<p id="password"><a href="/password"> Change your password</a></p>
<h2 class="logout"><a href="/logout"> Log out</a></h2>
<?php endif; ?>
<style>
 section h1:before{
     font-family:awesome;
     content:"\f007";
 }
 #password:before{
     font-family:awesome;
     content:"\f084";
 }
 #files:before{
     font-family:awesome;
     content:"\f0c5";
 }
 #profile:before{
     font-family:awesome;
     content:"\f0f0";
 }
</style>
