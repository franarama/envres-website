<?php
session_start();
session_destroy();
$aOutput = array();
$aOutput['status']=   "OK";
echo json_encode($aOutput);
exit(0);
?>
