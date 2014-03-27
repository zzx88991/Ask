<!doctype html>
<title>Register</title>
<link rel="stylesheet" type="text/css" href="theme.css">    
<?php
include_once("sql.php");
include_once("field_tags.php");


if ($_POST) {
	
		//header("Location: index.php");
	if (isset($_POST['submit'])){
		$params=$_POST;
		check_submit_form($params);


	}

}else
	show_form(null,"default");






function check_submit_form($params){

		$username=$params['username'];
		$pw=$params['pw'];
		$repw=$params['repw'];
		$email=$params['email'];
		$gender=$params['gender'];
		if(isset($params['tags'])){
			$tags=$params['tags'];
		}else
			$tags=array();


		$description=$params['description'];
		if($_FILES["image"]["tmp_name"]!=""){
			$has_pic="Yes";
		}else
			$has_pic="No";

		if(!ctype_alnum($username))
			show_form("Unvalid characters!","default");

		elseif (strlen($username)>50||strlen($username)==0) {
			show_form("The name is too long or too short!","default");
		}

		elseif (sql_id_for_user($username)){
        	show_form("Sorry, the username is taken!","default");
       	}
        elseif(strlen($pw)<6){
        	show_form("Sorry, the password should have length at least 6.","default");
        }
        elseif ($pw!=$repw) {
			show_form("Sorry, the passwords doesn't agree with each other.","default");
        }
 		else{

 			if($has_pic=="Yes")
 				move_pic($username);
 			$tags=serialize($tags);
 			$user_id = sql_add_user($username, $pw,$gender,$email,$tags,$description,$has_pic);
 			start_with_id($user_id);
        }
}

function move_pic($username)
{
 if (count($_FILES) != 0) {
            $name = $username.".jpg";
        
        #var_dump("is_uploaded...", is_uploaded_file($_FILES["image"]["tmp_name"]));

        $tmp = $_FILES["image"]["tmp_name"];
        if (is_uploaded_file($tmp) && strstr($name, "/") === false){

            move_uploaded_file($tmp, "images/$name");        }
        }


}
function start_with_id($user_id)
{
	session_start(oid);
    $_SESSION["user_id"] = $user_id;
    $username=sql_user_for_id($user_id);
    header("Location: homePage.php?who=$username");
    exit();
}
//signup function 
function signup($owner, $pw)
{
    if (sql_id_for_owner($owner))
        show_signin("sorry, that's taken!");

    if (strlen($pw) < 3)
        show_signin("password too short!");

    if (strlen($owner) == 0)
        show_signin("no user name!");

    $ents = htmlentities($owner, ENT_QUOTES);
    if ($ents != $owner)
        show_signin("disallowed characters!");

    if (strlen($owner) > 50)
        show_signin("name too long!");

    $owner_id = sql_add_owner($owner, $pw);
    if ($owner_id) {

        assert(mkdir("images/$owner_id"));
        start_with_id($owner_id);
        }
}


function show_form($feedback=null,$user_id){
	echo "<body id=reg><div  id='form'>";
    if (isset($feedback))
        echo "<span id=feedback>$feedback</span><br>";
    $tags=get_field_tags();

?>
<form id=regform method=post enctype=multipart/form-data action='<?=$_SERVER['PHP_SELF']?>'>
<br>
<h1 style="text-align:center">User Register</h1>
<br>
Upload your profile picture
<!-- <div id="profile" style="background:url('images/<?php echo $user_id ?>.jpg');background-size:100% "></div> -->

<input class=in name=image type=file>

<br><br>User Name: 
<input class=in type=text name=username size=60 placeholder="(Only letters and numbers are accepted)"  autofocus required>
<br><br>Password: 
<input type=password class=in name=pw size=30 placeholder="(at least 6 digits) " required>
<br><br>
Re-enter the password:
<input type=password class=in name=repw size=30 placeholder="Enter your password again" required>

<br><br>Gender:
<select class=in name=gender>
<option name=gender value='male'>Male</option>
<option name=gender value='female'>Female</option>
</select>
<br><br>Description: <input class=in size=60 type=text name=description placeholder="Write a sentence about yourself">
<br><br>Email Address: <input class=in type=email name=email size=30 placeholder="Your email address(optional)">
<br><br>Fields interested in:<br> <br>
<?php 
	foreach ($tags as $tag){
		 echo("<input type=checkbox name=tags[] value='$tag'>$tag </input><br>\n");
        }
	}


?>
<br>
<div style="text-align:center">
<input class=sub style="text-align:center" type=submit name=submit value="Submit">
<a href="index.php" style="text-align:center">Cancel</a>
</div>
</form>
</div></body>
<?
	exit();

