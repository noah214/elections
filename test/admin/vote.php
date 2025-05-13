<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<div class="container p-5 bg-light">
<form action="" method="post">
    <div class="row g-5">
        <div class="col-auto">
        <input type="search" name="searchinput" id="" placeholder="Search" class="form-control">
        </div>
        <div class="col-auto">
        <input type="submit" name="search" value="Search" class="btn-primary btn">
        </div>
    </div>
</form>


<?php
//Start database connection
require_once "dbaseconnection.php";

//check connection -> if not fail -> is connected
// if (!$lpconn->connect_error){
//     echo("Connected");
// }

//Display Query


//button function
if(isset($_POST['search'])){
    $usersearch = $_POST['searchinput'];
    //%r r% r......

    $selectsql = "Select * from tbl_accountdetails where lname like '%".$usersearch."%'
    OR fname like '%".$usersearch."%'
    OR gender like '%".$usersearch."%'
    OR account_id like '%".$usersearch."%'
    
    ";
}else{
    //display all records
    $selectsql = "Select * from user_table";
}

//Convert query to sql syntax and return array of records
$result = $conn->query($selectsql);

//check if the table is empty or not
if($result->num_rows > 0){
?>

<table class="table table-success">
    <tr>
        <th>Image</th>
        <th>User Id</th>
        <th>Full Name</th>
        <th>Role</th>
        <th>Username</th>
        <th>Password</th>
    </tr>
    <tr>
        <?php
            foreach($result as $fieldname){
                echo "<tr>";
                echo "<td><img src='".$fieldname['img_path']."' width='80' height='80'></td>";
                echo "<td>".$fieldname['user_id']."</td>";
                echo "<td>".$fieldname['full_name']."</td>";
                echo "<td>".$fieldname['role']."</td>";
                echo "<td>".$fieldname['username']."</td>";
                echo "<td>".$fieldname['password']."</td>";
                echo "</tr>";
            }
    }

        ?>
    </tr>
</table>
</div>
</body>
