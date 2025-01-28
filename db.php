<?php 
    //Database connection file
    //database connectiojn variables
    $servername = "localhost";
    $username = "root";
    $password="";
    $dbname = "productmanage";
    //Create a connection for the database
    $con = mysqli_connect($servername,$username,$password,$dbname);
    
    //If the connection is not established it shows the error
    if(mysqli_connect_errno())
    echo"Internal Server Error " .mysqli_connect_error();

    ?>