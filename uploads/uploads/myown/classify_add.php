<?php 
include '../AI9ME/Lib/Action/User/ClassifyAction.class.php';
$name = $_POST['name'];
echo sizeof($name)." content:".$name[0];
$status = $_POST['status'];
echo "<br>".$status;

$classifyAction = new ClassifyAction();
echo $classifyAction;
?>