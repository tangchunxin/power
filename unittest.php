<?php

require_once('./inc/head.inc.php');

if(!isset($_POST['test_key']) || $_POST['test_key'] != 'ncbdtocar' )
{
	exit('exit');
}



$data_receive = $_POST;

$randkey = encryptMD5($data_receive);
$_REQUEST = array('randkey'=>$randkey, 'c_version'=>'0.0.1', 'user_end'=>1, 'parameter'=>json_encode($data_receive) );

require ("./index.php");

