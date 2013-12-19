<?php
$hostip=isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:gethostbyname($_SERVER['SERVER_NAME']);
echo $hostip;
?>