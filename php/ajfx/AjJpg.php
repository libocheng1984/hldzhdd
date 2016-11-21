<?php
error_reporting(E_ALL || ~E_NOTICE);
header('Content-Type: image/jpg');

include_once('../GlobalConfig.class.php');
include_once('../class/TpmsDB.class.php');
include_once('../class/getOracleBlob.Class.php');

        $imageid = isset ($_REQUEST['imageid']) ? $_REQUEST['imageid'] : "";
	$getOracleBlob = new getOracleBlob();//创建调度类实例
        $sql = "SELECT FJ FROM FJ_PIC WHERE MD5='$imageid'";
	$res = $getOracleBlob->getBlob($sql);//调用实例方法
?>