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
	
	/*查询*/
	/*传入参数*/
	$bmbm="";
	$bmbm = isset ($_REQUEST['bmbm']) ? $_REQUEST['bmbm'] : "";
	if ($bmbm=="") {
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
	//$sjbm = '_210200000000_210200150000_';
	$sjbm = "";
	$TpmsDB = new TpmsDB();//创建tpms数据库实例
	$res1=$TpmsDB->getParentOrg($bmbm);
	if(isset($res1['parenttreepath']))$sjbm = $res1['parenttreepath'];
	$TpmsDB->clearAllMemcache($bmbm,$sjbm);
	$result = array (
			'head' => array (
						'code' => 1,
						'message' => '操作成功'
						),
			'value' => true,
			'extend' => ''
		);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>