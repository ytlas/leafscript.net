<h1> Info</h1>
<h3>Some random useful information about your request:</h3>
<p><?php echo "Ip: <span style='color:darkcyan'>".$req->ip;?></span></p>
<p><?php echo "Method: <span style='color:red'>".$req->method;?></span></p>
<p><?php echo "Agent: <span style='color:blue'>".$req->agent;?></span></p>
<p><a href="/userlist">Check out the userlist</a></p>
<style>
 section h1:before{
     font-family:awesome;
     content:"\f05a";
 }
</style>
