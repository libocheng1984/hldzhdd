<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
	session_start();
	session_commit();
?>
<?php
/**
 * 功能：获取巡逻组
 * 
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Communication.class.php');
	
	
	
	/*调试*/
	if (isDebug()) {
		$orgCode = '210200000000';
		$id = '210203440000';
	}
		
		$communication = new Communication();//创建tpms数据库实例getImageInfo($img)
		$res=$communication->getGroups();
		
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
		
  

?>