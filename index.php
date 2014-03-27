<!doctype html>
<title>Ask</title>
<link rel="stylesheet" type="text/css" href="theme.css">    
<link href="http://fonts.googleapis.com/css?family=Ubuntu:bold" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Vollkorn" rel="stylesheet" type="text/css">
<?php
include_once("sql.php");

session_start();

if ($user = @$_SESSION["user_id"]) {
    $username=sql_user_for_id($user);
   // session_destroy();

    header("Location: homePage.php?who=$username");
    }
elseif (isset($_POST["user"]) && isset($_POST["pw"])) {
    $user = $_POST["user"]; $pw = $_POST["pw"];
    if (isset($_POST["signin"]))
        signin($user, $pw);
    
    else
        show_signin();
    }
else
    show_signin();

/*
 * If the owner's password matches that in the database, sign them in.
 */
function signin($user, $pw)
{
    $user_id = sql_check_password($_POST["user"], $_POST["pw"]);
    if ($user_id)
        start_with_id($user_id);
    else
        show_signin("login incorrect");
}



function start_with_id($user_id)
{
    $_SESSION["user_id"] = $user_id;
    $username=sql_user_for_id($user_id);
    header("Location:homePage.php?who={$username}");
    exit();
}

function check_credentials($username, $password)
{
    // select password from users where $username=:name password=:pw
    if ($password != "abc")
        return 0;

    return sql_id_for_owner($username);
}
    
function show_signin($feedback = null)
{
?><div >
<header style="font-family: 'Open sans', arial, sans-serif"><span style="font-size:60px;">Q&A</span><br>a place to learn and teach!</header>
<form id=main method=post action='<?=$_SERVER['PHP_SELF']?>'>
<br>
<span style="font:1em 'Lucida Grande', Tahoma, Verdana, sans-serif;">Login to your Account!</span>
<br><br>
<input type=text name=user size=30 placeholder="User Name" autofocus required>
<br>
<input type=password name=pw size=30 placeholder=Password required>
<br>
<?php
    if (isset($feedback))
        echo "<span id=feedback>$feedback</span><br>";
?>
<span style='font-size:0.3em' ></span></input>
<input type=submit class=button name=signin value="Log in" >
<br>
<a href="register.php" style='font-size:0.5em'>Don't have a account? Go sign up one!</a>
<br>
<br>
</form>
<div class=body>
    <div class=question style=";font-size:15px;color:grey;">Recently Update</div>
    <hr>
<?php   
$rows=sql_get_all_questions();
$size=0;
if(sizeof($rows)>=10)
    $size=10;
else
    $size=sizeof($rows);
for ($i=0; $i < $size; $i++) { 
    $id=$rows[$i]['id'];
    $user_id=$rows[$i]['user_id'];
    $title=$rows[$i]['title'];
    $date=$rows[$i]['date'];
    $username=sql_user_for_id($user_id);
    $has_pic=sql_pic_for_id($user_id);
    if($has_pic=="Yes")
        $pro=$username;
    else
        $pro="default";

    echo "<div class=question> 

        <div class=pro_pic style='background-image:url(images/{$pro}.jpg)'></div>
        <div class=content> 
                <div style='float:right;font-size:0.8em'>posted by $date</div>  
        <div >$username asked a question</div>
        <div class=title >Q: $title</div>";
        $tags=sql_get_question_id_tag($id);
        foreach ($tags as $key => $tag) {
            echo "<span class=tag>#$tag</span>";
        }

      echo  "</div>

    </div>"; if($i!=$size-1) echo "<hr style=width:670px;color:#F8F8F8>";


}

?>

 </div>



</div>

<?
    exit();
}