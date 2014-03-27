<?php
function check_signedin()
{
    session_start();
    
    if (!isset($_SESSION["user_id"])) {
    	//echo "<h1 style='text-align:center;position:fixed;top:30%;left:30%'>Really need user login...Going to the index page in 3 seconds...</h1>";
    	//echo "<script> alert('Really need user login...Going to the index page in 3 seconds...')</script>";
    	//sleep(3);
        header("Location: index.php");
        exit();
        }

    return $_SESSION["user_id"];
}

function show_questions($user_id){



}


?>