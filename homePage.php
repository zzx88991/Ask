<!doctype html>
<?php
include_once("sql.php");
include_once("utils.php");



$checked_in_id=check_signedin();
$checkedin_user_name=sql_user_for_id($checked_in_id); 

if (isset($_POST["signout"])) {
    session_destroy();
    header("Location: ."); exit();
    }

if(isset($_GET['who'])){

$username=$_GET['who'];

$user_id = sql_id_for_user($username);

if($user_id=="")
	die("Error:102, no users found!");
$gender = sql_gender_for_id($user_id);
$description=sql_description_for_id($user_id);
$email = sql_email_for_id($user_id);

if($email==""){
	$email="blank..";
}
$tags=sql_tags_for_id($user_id);
if($tags=="a:0:{}"){
	$tags=array("blank..");



}else
	$tags=unserialize($tags);
$has_pic=sql_pic_for_id($user_id);
if($has_pic=="No")
	$has_pic="default";
else
	$has_pic=$username;

//var_dump($_POST);

if(isset($_POST['submit'])){
	echo "sss";
	insert_question($checked_in_id);


}
}else{
	die("Error:101, did not find the user.");


}


function insert_question($checked_in_id){
	$title=$_POST['title'];
	$title=htmlspecialchars($title);
	$content=$_POST['content'];
	$content=htmlspecialchars($content);
	$order  = array("\r\n", "\n", "\r");
	$replace = '<br>';

// Processes \r\n's first so they aren't converted twice.
	$content = str_replace($order, $replace, $content);
	$tags=array();
	if(isset($_POST['tags']))
		$tags=$_POST['tags'];
	if($_POST['newtags']!=""){
		$pre_newtags=explode(",", $_POST['newtags']);
		$newtags=array();
		foreach ($pre_newtags as $tag) {
			$newtags[]=trim($tag);
		}
		$tags=array_merge($tags,$newtags);
	}
	foreach ($tags as  $tag) {
		$tag=htmlspecialchars($tag);
	}
	sql_insert_question($checked_in_id,$title,$content,$tags);

}

?>



<html>
<title>Home Page of <?php echo $username;?></title>
<link rel="stylesheet" type="text/css" href="homepage.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>	
$(document).ready(function() {
    $("#textask").keydown(function() {
        var words = $(this).val().split(" ").length
        if (words > 2000 && event.keyCode != 8) {
            $("#count").addClass("red");
            return false;  // Or, event.preventDefault();
            }
        else {
            $("#count").removeClass("red");
            }
        $("#count").text(words + "/2000");
        })
    
});
</script>





<div id="navigation_bar">


</div>
<div class="homepage" id="information">
<h2>Profiles</h2>

<span id="profile" style="background:url('images/<?php echo $has_pic; ?>.jpg');background-size:100% 100%"></span><br>
<br>
<span>Username: <span class=profile><?php echo $username;?></span></span><br>
<span>Gender: <span class=profile><?php echo $gender;?></span></span><br>
<span>Description: <span class=profile><?php echo $description;?></span></span><br>
<span>Email address: <span class=profile><?php echo $email;?></span></span><br>
<span>Fields interesed in: <span class=profile><?php 
for( $i=0;$i<sizeof($tags);$i++) {
	if($i!=sizeof($tags)-1)
		echo "$tags[$i]",", ";
	else
		echo "$tags[$i]",".";
}

?></span></span><br>




</div>

<div class="homepage" id="Questions">
<h2><?php echo $username,"'s Questions"?></h2>
<?php   
$rows=sql_get_questions($user_id);
if(sizeof($rows)==0){
		echo "<span style='text-align:center;font-size:large'>$username doesn't has any question yet..</span>";
	}else{

for ($i=0; $i < sizeof($rows); $i++) { 
    $id=$rows[$i]['id'];
    //$user_id=$rows[$i]['user_id'];
    $title=$rows[$i]['title'];
    $date=$rows[$i]['date'];
    //$username=sql_user_for_id($user_id);
    //$has_pic=sql_pic_for_id($user_id);
    if($has_pic=="Yes")
        $pro=$username;
    else
        $pro="default";

    echo "<div class=question> 

        <div class=content> 
                <div style='float:right;font-size:0.8em'>posted by $date</div>  
        <div class=title ><a style='text-decoration:none'href=question.php?id=$id >Q: $title</a></div><br>";
        $tags=sql_get_question_id_tag($id);
        foreach ($tags as $key => $tag) {
            echo "<span class=tag><a href='search.php?search={$tag}'>#$tag</a></span>";
        }

      echo  "</div>

    </div>"; if($i!=sizeof($rows)-1) echo "<hr style=width:670px;color:#F8F8F8>";


}}

?>



</div>
<div class="homepage" id="answers">
<h2><?php echo $username,"'s Answers"?></h2>
<?php
	$rows=sql_get_answers($user_id);
	if(sizeof($rows)==0){
		echo "<span style='text-align:center;font-size:large'>$username hasn't answered any question yet..</span>";
	}else{
	foreach ($rows as $row) {
		$question_id=$row['question_id'];
		$col=sql_get_questions_id($question_id);
		//var_dump($col);
		$author_id=$col[0]['user_id'];
		$title=$col[0]['title'];
		$date=$col[0]['date'];
		$text=$col[0]['text'];
		$tags=sql_get_question_id_tag($question_id);
				$username=sql_user_for_id($user_id);
    	$has_pic=sql_pic_for_id($user_id);
    	//$descrip=sql_description_for_id($user_id);
    	if($has_pic=="Yes")
        	$pro=$username;
   		else
        $pro="default";
		
    	 echo "<div class=question> 

        <div class=content> 
                <div style='float:right;font-size:0.8em'>posted by $date</div>  
        <div class=title ><a style='text-decoration:none'href=question.php?id=$question_id >Q: $title</a></div><br>";
        foreach ($tags as $key => $tag) {
            echo "<span class=tag><a href='search.php?search={$tag}'>#$tag</a></span>";
        }

      echo  "</div>

    </div>";  echo "<hr style=width:670px;color:#F8F8F8>";

	}
}


?>



</div>

<!-- Navigation bar-->
<div class=navigate style="vertical-align:top;text-align:left;position: fixed; width: 100%; height: 70px; top:0%; left:0%;background-color: rgba(0,0,0,.8); overflow: hidden
">
<div style="height:45px;margin:15px 15px;font:15px arial;color:white"> 
	<div style="display:inline-block;padding-top:8px;vertical-align:top;float:right"><div style="display:inline-block;vertical-align:top;background:white;color:black;
border: white solid 1px;border-radius:4px;padding:2px;margin:0px 10px"><a href="homePage.php?who=<?=$checkedin_user_name;?>">Home</a></div><form action=<?=$_SERVER['PHP_SELF']?> method=post style='display:inline-block'> 
<input style="display:inline-block;vertical-align:top;background:white;color:black;
border: white solid 1px;border-radius:4px;margin:0px;border-radius:5px;padding:2px;font:15px arial;margin:0px 3px" 	type=submit name=signout value="Sign Out"></form></div>
	<div style="margin-top:0px;display:inline-block;vertical-align:middle;float:right">
		<img width=40px height=40px src="images/<?php 
		if($has_pic=="Yes") echo $checkedin_user_name;else echo "default";?>.jpg"></img><div style="height:35px;display:inline-block;padding-top:8px;vertical-align:top"><?php echo $checkedin_user_name,", logged in. " ?></div>
</div><div style="margin-left:20px;padding-top:5px;display:inline-block"><form action="search.php" method=get><input placeholder="Search known questions" tabindex='1'type=text name=search  size=50 style="margin-left:215px;height:25px;border-radius:5px" style="display:inline-block;">&nbsp;&nbsp;<input type=submit name=submit value="Search"></form>
</div></div>
</div>








<?php 


if($checked_in_id!=$user_id)
	echo "<!-- $checked_in_id";

?>

<div id="ask" class=homepage>
<h1>Ask your question</h1>
<form id=newform action="homePage.php?who=<?php $checkedin_user_name=sql_user_for_id($checked_in_id); echo $checkedin_user_name; ?>" method=post style="vertical-align:top">
<input placeholder="Title"  style="height:20px" type=text name=title size=60 required><br><br>
<textarea id='textask' placeholder="Specific description of your question..."cols=60 rows=10 name=content required></textarea>
<div id='count'><span id='count' style=text-align:right></span></div><br><br>









<?php
$tags=sql_get_tags($checked_in_id);
    foreach ($tags as $tag) {
        echo("<input type=checkbox name=tags[] value='$tag'>#$tag</input>\n");
        }
?>
<br><br>
<input placeholder="New tags here (separate with commas)"type=text name=newtags size=40> 
<br><br>
<input type=submit name=submit value="Submit">
</form>
</div>



</html>