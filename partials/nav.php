<ul id="navList">
    <li><a href="/" id="l"> Home</a></li><li>
	<a href="/chat" id="lchat"> Chat</a></li><li>
	<a href="/info" id="linfo"> Info</a></li><li>
	    <div class="dropdown">
		<span id="dropIcon"></span><a href="/account" class="dropbtn" id="laccount"> Account</a>
		<div class="dropdown-content">
		    <a id="luserlist" href="/userlist"> Userlist</a>
		    <?php if(Misc::li()): ?>
		    <a class="logout" href="/logout">Log out</a>
		    <?php endif; ?>
		</div>
	    </div>
	</li>
</ul>
<style>
 .logout:before{
     font-family:awesome;
     content:"\f08b";
 }
 #navList{
     padding-left:1em;
     padding-right:1em;
 }
 #dropIcon{
     position:absolute;
     z-index:2;
     height:100%;
     padding:0.5em;
     top:-0.3em;
     right:1em;
 }
 #dropIcon:active .dropdown-content{
     display:blocK;
 }
 @media only screen
 and (max-width : 620px) {
     #dropIcon{
	 right:-1em;
     }
 }

 #dropIcon:after{
     font-family:awesome;
     content:"\f0d7";
 }
 .dropdown:hover #dropIcon:after{
     content:"\f0d9";

 }
 .dropbtn {
     background-color:white;
     color: black;
     border:none;
 }

 /* The container <div> - needed to position the dropdown content */
 .dropdown {
     position: relative;
     display: inline-block;
     width:100%;
 }

 /* Dropdown Content (Hidden by Default) */
 .dropdown-content {
     display: none;
     position: absolute;
     background-color:white;
     width:100%;
     box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
     z-index: 1;
 }

 /* Links inside the dropdown */
 .dropdown-content a {
     color: black;
     text-decoration: none;
     display: block;
     border-top:1px solid black;
 }
 /* Change color of dropdown links on hover */
 .dropdown-content a:hover {background-color: #D0DFDF;}

 /* Show the dropdown menu on hover */
 .dropdown:hover .dropdown-content,.dropdown:focus .dropdown-content,.dropdown:active .dropdown-content{
     display: block;
 }


 <?php
 echo '#l'.$req->site.'{background-color:#DFDFDF;}';
 ?>
 nav ul{
     margin:0;
     padding:0;
 }
 nav ul li{
     display:inline-block;
     text-align:center;
 }
 nav ul li a{
     display:block;
     color:black;
     text-decoration:none;
     width:100%;
     height:1.5em;
     line-height:1.5em;
 }
 nav ul li a:hover{
     background-color:#D0DFDF;
 }
 nav ul li:first-child:nth-last-child(1) {
     width: 100%;
 }

 nav ul li:first-child:nth-last-child(2),
 nav ul li:first-child:nth-last-child(2) ~ li {
     width: 50%;
 }

 nav ul li:first-child:nth-last-child(3),
 nav ul li:first-child:nth-last-child(3) ~ li {
     width: 33.3333%;
 }

 nav ul li:first-child:nth-last-child(4),
 nav ul li:first-child:nth-last-child(4) ~ li {
     width: 25%;
 }

 nav ul li:first-child:nth-last-child(5),
 nav ul li:first-child:nth-last-child(5) ~ li {
     width: 20%;
 }

 #l:before{
     font-family: awesome;
     content: "\f015";
 }
 #lchat:before{
     font-family:awesome;
     content:"\f086";
 }
 #linfo:before{
     font-family:awesome;
     content:"\f05a";
 }
 #laccount:before{
     font-family:awesome;
     content:"\f007";
 }
 #laccount{

 }
 #luserlist:before{
     font-family:awesome;
     content:"\f0c0";
 }

</style>
