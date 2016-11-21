<?php
error_reporting(E_ALL || ~E_NOTICE);
header('Content-Type: application/octet-stream'); 

include_once('../GlobalConfig.class.php');
include_once('../class/TpmsDB.class.php');
include_once('../class/getOracleBlob.Class.php');

        $yabh = isset ($_REQUEST['yabh']) ? $_REQUEST['yabh'] : "";
        $nrmc = isset ($_REQUEST['nrmc']) ? $_REQUEST['nrmc'] : "";
        $url =  isset ($_REQUEST['url']) ? $_REQUEST['url'] : "";
        //$nrmc = iconv("UTF-8","GBK", $nrmc);
        header('Content-Disposition: attachment; filename="'.$nrmc.'"');  
        header('Content-Transfer-Encoding: binary');
    if($url){
		readfile($url); 
    }else{
    	$getOracleBlob = new getOracleBlob();//创建调度类实例
	    $sql = "select yanr as fj from zdt_plan_event t where t.yabh='$yabh'";
		$res = $getOracleBlob->getBlob($sql);//调用实例方法
    }
	
?>