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
	
	$gid = isset ($_REQUEST['gid']) ? $_REQUEST['gid'] : ""; //群组名称
	
	/*调试*/
	if (isDebug()) {
		$gid = '349';
	}
	
	/*参数校验*/
	if ($gid=="") {
		$arr = array (
				'head' => array (
					'code' => 0,
					'message' => '缺少参数!!'
				),
				'value' => '',
				'extend' => ''
			);
		die(encodeJson($arr));
	}
		
	$communication = new Communication();//创建tpms数据库实例getImageInfo($img)
	$res=$communication->getEventBygroupId($gid);
	if($res['records']){
		$res = array (
			'result' => 'true',
			'errmsg' => '',
			'records' => $res['records']
		);
	}
	else{
		$res = array (
				'result' => 'false',
				'errmsg' => '群组不存在',
				'records' => $res['records']
			);
	}
		
	
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
		
  

?>