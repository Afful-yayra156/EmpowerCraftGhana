<!-- 
// $servername = 'empowercraft.mysql.database.azure.com';
// $username = 'empowercraft1234';
// $password = 'passWord@123';
// $dbname = 'empowerhub';

// $conn = mysqli_connect($servername, $username, $password, $dbname) 
//     or die('Unable to connect');

// if ($conn->connect_error) {
//     die('Connection failed');
// }





 



 <?php
$servername = 'empowercraft.mysql.database.azure.com';
$username = 'empowercraft1234@empowercraft'; // Add '@server-name' to username
$password = 'passWord@123';
$dbname = 'empowerhub';

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}
?>
