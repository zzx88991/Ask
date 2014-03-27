<?php

include_once("sql.php");



if(isset($_POST['username'])&&isset($_POST['rate']))
	sql_update_rate($_POST['username'],$_POST['rate']);