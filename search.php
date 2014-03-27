<!doctype html>

<html>
<title>Search result</title>
<link rel="stylesheet" type="text/css" href="homepage.css">

<?php 
include_once("sql.php");
include_once("utils.php");
$checked_in_id=check_signedin();
$checkedin_user_name=sql_user_for_id($checked_in_id); 
$has_pic=sql_pic_for_id($checked_in_id);

if(!isset($_GET['search'])){
	die("<h1>Error301: search page cannot be found!</h1>");
}
$search = $_GET['search'];


echo "<div class=search >Search Result for <b>$search</b>:<br><hr>
";
//if($search=="")
//	die();
$ids=sql_search($search);
if(sizeof($ids)==0)
	echo "<h3>sorry, no thing found, please try another search.</h3>";
else{
$ids=array_unique($ids);
foreach ($ids as $key => $id) {
	$rows=sql_get_questions_id($id);
		if($rows){
			$user_id=$rows[0]['user_id'];
			$title=$rows[0]['title'];
			$date=$rows[0]['date'];
			$username=sql_user_for_id($user_id);
	    	$has_pic=sql_pic_for_id($user_id);
	    	$descrip=sql_description_for_id($user_id);
	    	if($has_pic=="Yes")
	        	$pro=$username;
	   		else
	        $pro="default";


		}


		echo "<div class=question3> 
    	<a href=homePage.php?who=$username>
        <div class=pro_pic style='width:70px;height:70px;background-image:url(images/{$pro}.jpg)'></div></a>
        <div class=content> 
                <div style='float:right;font-size:0.8em'>posted by $date</div>  
        <div ><a href=homePage.php?who=$username><span class=grey>$username</span></a></div>
        <div class=title ><a href='question.php?id=$id'>Q: $title</a></div><br>";
        $tags=sql_get_question_id_tag($id);
        foreach ($tags as $key => $tag) {
            echo "<span class=tag><a href='search.php?search=$tag'>#$tag</a></span>";
        }
        echo "</div></div><br><br>";
}


}

//var_dump($rows);
echo "</div>";



?>

<!-- Navigation bar-->

<div class=navigate style="vertical-align:top;text-align:left;position: fixed; width: 100%; height: 70px; top:0%; left:0%;background-color: rgba(0,0,0,.8); overflow: hidden
">
<div style="height:45px;margin:15px 15px;font:15px arial;color:white"> 
	<div style="display:inline-block;padding-top:8px;vertical-align:top;float:right"><div style="display:inline-block;vertical-align:top;background:white;color:black;
border: white solid 1px;border-radius:4px;padding:2px;margin:0px 10px"><a href="homePage.php?who=<?=$checkedin_user_name;?>">Home</a></div><form action=homePage.php method=post style='display:inline-block'> 
<input style="display:inline-block;vertical-align:top;background:white;color:black;
border: white solid 1px;border-radius:4px;margin:0px;border-radius:5px;padding:2px;font:15px arial;margin:0px 3px" 	type=submit name=signout value="Sign Out"></form></div>
	<div style="margin-top:0px;display:inline-block;vertical-align:middle;float:right">
		<img width=40px height=40px src="images/<?php $has_pic=sql_pic_for_id($checked_in_id);

		if($has_pic=="Yes") echo $checkedin_user_name;else echo "default";?>.jpg"></img><div style="height:35px;display:inline-block;padding-top:8px;vertical-align:top"><?php echo $checkedin_user_name,", logged in. " ?></div>
</div><div style="margin-left:20px;padding-top:5px;display:inline-block"><form action="search.php" method=get><input placeholder="Search known questions" tabindex='1'type=text name=search  size=50 style="margin-left:215px;height:25px;border-radius:5px" style="display:inline-block;">&nbsp;&nbsp;<input type=submit name=submit value="Search"></form>
</div></div>
</div>

</html>