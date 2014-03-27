<?php

function sql_id_for_user($user)
{
    return sql_col_for_entity("id", "user", "username", $user);
}

function sql_id_for_tag($tag)
{
    return sql_col_for_entity("id", "tag", "tag", $tag);
}

function sql_user_for_id($id)
{
    return sql_col_for_entity("username", "user", "id", $id);
}

function sql_gender_for_id($id)
{
    return sql_col_for_entity("gender", "user", "id", $id);
}
function sql_description_for_id($id)
{
    return sql_col_for_entity("description", "user", "id", $id);
}
function sql_email_for_id($id)
{
    return sql_col_for_entity("email", "user", "id", $id);
}
function sql_tags_for_id($id)
{
    return sql_col_for_entity("tags", "user", "id", $id);
}
function sql_pic_for_id($id)
{
    return sql_col_for_entity("has_pic", "user", "id", $id);
}

//return the answer_id with the user_id

function sql_answers_for_id($id){
    return sql_col_for_entity("answer_id","question_answer","user_id",$id);
}
//return the title of the question of the question(id)
function sql_title_for_id($id){
    return sql_col_for_entity("title","question","id",$id);

}


function sql_col_for_entity($column, $table, $eqcolumn, $value)
{
    $conn = getconn();

    $stmt = $conn->prepare("select $column from $table where $eqcolumn=:value");
    $stmt->bindParam(':value', $value);
    $result = $stmt->execute();
    
    if (!$result)
        pdo_die($stmt);
    
    $result = $stmt->fetchAll();

    assert(count($result) <= 1);
    if (count($result) != 0){
        return $result[0][$column];
    }else
        return null;
}

function sql_insert_question($user_id, $title, $text, $tags)
{
    $conn = getconn();
    
    $stmt = $conn->prepare("insert into question(user_id, title, date, text)
                            values(:user_id, :title, now(), :text)");

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':text', $text);
    $result = $stmt->execute();
    $question_id = $conn->lastInsertId();
    #echo "entry_id: $entry_id\n";

    if (!$result)
        pdo_die($stmt);

    sql_insert_entry_tags( $question_id,$user_id, $tags);
}

function sql_insert_answer($user_id,$question_id,$answer){
    $conn = getconn();

    if(!check_unique_answer($question_id,$user_id))
        die("Error: database has already had an answer for question_id=$question_id, user_id=$user_id!");

    
    $stmt = $conn->prepare("insert into question_answer(user_id, question_id, answer, rate,hide)
                            values(:uid, :qid, :answer, 0,'No')");

    $stmt->bindParam(':uid', $user_id);
    $stmt->bindParam(':qid', $question_id);
    $stmt->bindParam(':answer', $answer);
    $result = $stmt->execute();
    #echo "entry_id: $entry_id\n";

    if (!$result)
        pdo_die($stmt);

}
function check_unique_answer($question_id,$user_id){

    $rows=sql_get_answers($user_id);
    foreach ($rows as $key => $row) {
        if($row['question_id']==$question_id)
            return false;
    }
    return true;
}




function sql_insert_tag($tag)
{
    $conn = getconn();
    
    $stmt = $conn->prepare("insert into tag(tag) values(:tag)");
    $stmt->bindParam(':tag', $tag);
    $result = $stmt->execute();
    $tag_id = $conn->lastInsertId();
    #echo "tag_id: $entry_id\n";

    if (!$result)
        pdo_die($stmt);

    return $tag_id;
}

function sql_insert_entry_tags($question_id, $user_id, $tags)
{
    #echo "insert_entry_tags($entry_id, $owner_id)\n";
    #var_dump($tags);
    
    $conn = getconn();
    
    foreach ($tags as $tag) {
        $tag_id = sql_id_for_tag($tag);
        if ($tag_id === null)
            $tag_id = sql_insert_tag($tag);
        $stmt = $conn->prepare("insert into question_tag(question_id, tag_id) values(:qid, :tid)");
        $stmt->bindParam(":qid", $question_id);
        $stmt->bindParam(":tid", $tag_id);
        $result = $stmt->execute();
        
        if (!$result)
            pdo_die($stmt);
        }
}



function sql_get_questions($user_id){

    $conn = getconn();
    
    $stmt= $conn->prepare(
        "select * from question
         where user_id=:uid order by id DESC");
        $stmt->bindParam(":uid", $user_id);
        $result = $stmt->execute();
        
        if (!$result)
            pdo_die($stmt);
        $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;


}
function sql_get_all_questions(){
      $conn = getconn();
    
    $stmt= $conn->prepare(
        "select * from question order by id DESC");
        $result = $stmt->execute();
        
        if (!$result)
            pdo_die($stmt);
        $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
 
}
function sql_search($search){
    $words=explode(' ', trim($search));
    $words=implode(' ', $words);
    //echo $words;
    $rows=array();
    $index=array();
    $conn = getconn();

    $stmt= $conn->prepare(
    "SELECT id FROM tag
    WHERE MATCH (tag) 
    AGAINST (:words IN BOOLEAN MODE);");
    $stmt->bindParam(":words",$words);
    $result=$stmt->execute();


    if (!$result)
            pdo_die($stmt);
        $out=$stmt->fetchAll();
      //  var_dump($out);
    foreach ($out as $key => $row) {
        $index[] = $row['id'];
    }

    foreach ($index as $key => $id) {
        $stmt=$conn->query("select question_id from question_tag where tag_id = $id;");
        
        foreach ($stmt->fetchAll() as $key => $row) {
            $rows[]=$row['question_id'];
        }
    }
    $conn = getconn();

    $stmt2= $conn->prepare(
    "SELECT * FROM question
 WHERE MATCH (title) AGAINST (:sw);");
    $stmt2->bindParam(":sw",$words);
    $result=$stmt2->execute();
    $out=$stmt2->fetchAll();
   // var_dump($out);
  //  if (!$result)
  //          pdo_die($stmt);
    foreach ($out as $key => $row) {
        $rows[]=$row['id'];
    }

    return $rows;

}


function sql_get_questions_id($question_id){
    $conn = getconn();
    
    $stmt= $conn->prepare(
        "select * from question
         where id=:qid");
        $stmt->bindParam(":qid", $question_id);
        $result = $stmt->execute();
        
        if (!$result)
            pdo_die($stmt);
        $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
}
function sql_get_answers_id($question_id){
    $conn = getconn();
    
    $stmt= $conn->prepare(
        "select * from question_answer
         where question_id=:qid order by rate DESC");
        $stmt->bindParam(":qid", $question_id);
        $result = $stmt->execute();
        
        if (!$result)
            pdo_die($stmt);
        $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;

}

function sql_get_tags($user_id)
{
    $conn = getconn();
    
    $stmt= $conn->prepare(
        "select distinct(tag)
         from tag, question_tag, question
         where tag.id=question_tag.tag_id and question_id=question.id and user_id=:uid
         order by tag");
         
    $stmt->bindParam(":uid", $user_id);
    $result = $stmt->execute();

    if (!$result)
        pdo_die($stmt);

    $tags = array();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
        $tags[] = $row['tag'];
    }
    return $tags;
}
function   sql_get_answers($user_id){

    $conn = getconn();
    $stmt= $conn->prepare(
        "select question_id from question_answer
         where user_id=:uid order by id DESC");
    $stmt->bindParam(":uid", $user_id);
    $result = $stmt->execute();
        
    if (!$result)
        pdo_die($stmt);
    $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;



}




function sql_get_question_id_tag($question_id){
    $conn = getconn();
    $stmt= $conn->prepare(
    "select distinct(tag) from tag,question_tag
         where tag.id=question_tag.tag_id and question_id=:qid order by tag");

    $stmt->bindParam(":qid", $question_id);
    $result = $stmt->execute();

    if (!$result)
        pdo_die($stmt);
    $tags=array();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
        $tags[] = $row['tag'];
    }
    return $tags;

    
}

function sql_add_user($username, $pw,$gender,$email,$tags,$description,$has_pic)
{
    $shuffled = sha1($username) . $pw;
    $hashed = sha1($shuffled);
    if($description=="")
        $description="This guys is lazy...He didn't leave anything here..";

    $conn = getconn();
    $stmt = $conn->prepare("insert into user(username, password,gender,description,email,tags,has_pic) values(:username, :hashed,:gender,:description,:email,:tags,:has_pic)");
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":hashed", $hashed);
        $stmt->bindParam(":gender", $gender);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":email", $email);
        $stmt->bindParam(":tags", $tags);

    $stmt->bindParam(":has_pic", $has_pic);
    
    $result = $stmt->execute();
    $user_id = $conn->lastInsertId();

    if (!$result)
        pdo_die($stmt);

    return $user_id;
}

function sql_check_password($user, $password)
{
    //
    // Note: with PHP 5.5 should use password_hash(...) instead!!
    $salted = sha1($user) . $password;
    $hashed = sha1($salted);
    #echo "hashed = $hashed";

    $conn = getconn();
    $stmt = $conn->prepare("select id from user where username=:user and password=:hashed");
    $stmt->bindParam(":user", $user);
    $stmt->bindParam(":hashed", $hashed);
    
    $result = $stmt->execute();

    if (!$result)
        pdo_die($stmt);

    $rset = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($rset) == 1)
        return $rset[0]["id"];
    else
        return null;
}



function sql_update_rate($username,$rate){
    $id=sql_id_for_user($username);
    $conn = getconn();


    $conn->query("Update question_answer set rate=$rate where user_id=$id;");




}
function pdo_die($stmt)
{
    var_dump($stmt->errorInfo());
    die("PDO error!");
}

/*
 * This is slight change from newconn() in the slides.  Instead of always
 * getting a connection we use a static declaration to cause $conn to
 * retain its value across calls.
 *
 * The first call to getconn() will get a connection, save it in $conn,
 * and return it.
 *
 * Subsequent calls will simply return that already-established connection.
 */
function getconn()
{
    static $conn;

    if ($conn)
        return $conn;
        
    if (gethostname() === "cgi-vm.cs.arizona.edu") {
        $dbname = "whm_cs337f13";
        $user = "cs337f13"; $pw = "tednelson";
        $host = "mysql.cs.arizona.edu";
    } else {
        $dbname = "ask";
        $user = "root"; $pw = "";
        $host = "localhost";
    }
        
    $dsn = "mysql:host=$host;dbname=$dbname"; // Data source name

    $conn = new PDO($dsn, $user, $pw);

    return $conn;
}
