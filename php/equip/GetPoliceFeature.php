<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
	session_start();
	session_commit();
?>
<?php
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Equip.class.php');
	/**
	 * 查询巡逻路线接口
	 */
	/*查询*/
	$gid = isset ($_REQUEST['gid']) ? $_REQUEST['gid'] : "";

	/*调试*/
	if (isDebug()) {
		$gid ='102';
	}
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
		
	$equip = new Equip();//创建调度类实例
	$res = $equip->getPoliceFeatureByGid($gid);//调用实例方法
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>