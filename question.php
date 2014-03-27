<!doctype html>

<html>
<title>Question Page</title>
<link rel="stylesheet" type="text/css" href="homepage.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>	
$(document).ready(function() {
    $("#textans").keydown(function() {
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

<script>
$(document).ready(function() {
	$(".add").click(function() {
		var _rate=Math.round($(this).siblings(".rate").text())+1;
		$(this).siblings(".rate").text(_rate);
		var user_name=$(this).siblings(".content").find('.name').text();

		$.post("backend.php",{username:user_name,rate:_rate});


	})

    
    });
</script>


<script>
$(document).ready(function() {
	$(".min").click(function() {
		var _rate=Math.round($(this).siblings(".rate").text())-1;
		$(this).siblings(".rate").text(_rate);
		var user_name=$(this).siblings(".content").find('.name').text();

		$.post("backend.php",{username:user_name,rate:_rate});



	})

    
    });
</script>



<?php 
include_once("sql.php");
include_once("utils.php");


$checked_in_id=check_signedin();
$checkedin_user_name=sql_user_for_id($checked_in_id); 
$has_pic=sql_pic_for_id($checked_in_id);

if(!isset($_GET['id'])){
	die("<h1>Error202, cannot find the question</h1>");
	
}
else{
	


	$question_id=$_GET['id'];

	if(isset($_POST['content'])){
		$answer=$_POST['content'];
		$answer=htmlspecialchars($answer);
		$order   = array("\r\n", "\n", "\r");
		$replace = '<br>';

// Processes \r\n's first so they aren't converted twice.
		$answer = str_replace($order, $replace, $answer);
		sql_insert_answer($checked_in_id,$question_id,$answer);



	}





	$rows=sql_get_questions_id($question_id);
	if($rows){
		$user_id=$rows[0]['user_id'];
		$title=$rows[0]['title'];
		$date=$rows[0]['date'];
		$text=$rows[0]['text'];
		$username=sql_user_for_id($user_id);
    	$has_pic=sql_pic_for_id($user_id);
    	$descrip=sql_description_for_id($user_id);
    	if($has_pic=="Yes")
        	$pro=$username;
   		else
        $pro="default";
		echo "
		<div class='homepage_' id='Questions' style='margin-top:90px'>";

    	echo "<div class=question2> 
    	<a href=homePage.php?who=$username>
        <div class=pro_pic style='width:70px;height:70px;background-image:url(images/{$pro}.jpg)'></div></a>
        <div class=content> 
                <div style='float:right;font-size:0.8em'>posted by $date</div>  
        <div ><a href=homePage.php?who=$username><span class=grey>$username</span></a>, $descrip</div>
        <div class=title >Q: $title</div><br><div class=text >$text</div><br>";
        $tags=sql_get_question_id_tag($question_id);
        foreach ($tags as $key => $tag) {
            echo "<span class=tag><a href='search.php?search=$tag'>#$tag</a></span>";
        }

      echo  "</div></div><hr>";
      $rows=sql_get_answers_id($question_id);
      if($rows){
      	foreach ($rows as $key => $row) {
      		$ans_user_id=$row['user_id'];
      		$username=sql_user_for_id($ans_user_id);
    		$has_pic=sql_pic_for_id($ans_user_id);
      		$answer=$row['answer'];
      		$rate=$row['rate'];
      		if($has_pic=="Yes")
        		$pro=$username;
    		else
        		$pro="default";

			echo "<div class=answer> 
			<div class='add s' style='position:absolute;left:-85px;top:10px'>+</div>
			<div class='min s' style='position:absolute;left:-85px;top:77px'>-</div>
			<div class='rate q' style='position:absolute;left:-85px;top:43px'>$rate</div>
			 <a href=homePage.php?who=$username><div class=pro_pic style='width:70px;height:70px;background-image:url(images/{$pro}.jpg)'></div></a>


        	<div class=content> 
        	<div class=i ><a href=homePage.php?who=$username><span class='name'style=color:grey>$username</span></a></div>
        	<br>
        	<div class=text >$answer</div><br>";
        		

      		echo  "</div>

    		</div><br><hr style=width:650px;color:#F8F8F8>";

      	}



      }else{
      	echo "<h2>No answers yet...</h2>";

      }







      echo "</div>";




      echo "
      <div id='ask' class=homepage>";


    if(!check_unique_answer($question_id,$checked_in_id))
    	echo "<h1>You have already answered this question..sorry</h1><div>";
    else{

	echo 	"<h1>Answer the question: </h1>

		<form id=newform action=question.php?id=$question_id method=post style='vertical-align:top'>
<textarea id='textans' placeholder='Answer the question here...(You can only answer once)'cols=60 rows=10 name=content required></textarea><div id='count'></div>
<br><br>
<br><br>
<input type=submit name=submit value='Submit'>
</form>
</div>";}




	}else
		die("Error203: no such question!");
}



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
