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
	 * 添加和修改巡逻路线接口
	 */
	/*查询*/
	$rwid = isset( $_REQUEST['rwid'] ) ? $_REQUEST['rwid'] : "";
	$sfzh = isset( $_REQUEST['sfzh'] ) ? $_REQUEST['sfzh'] : "";
	$kdid = isset( $_REQUEST['kdid'] ) ? $_REQUEST['kdid'] : "";
	$xlid = isset( $_REQUEST['xlid'] ) ? $_REQUEST['xlid'] : "";
	$ydcs = isset( $_REQUEST['ydcs'] ) ? $_REQUEST['ydcs'] : "";
	$jgsj = isset( $_REQUEST['ydcs'] ) ? $_REQUEST['jgsj'] : "";
	$geometry = isset( $_REQUEST['geometry'] ) ? $_REQUEST['geometry'] : "";
	//echo "id:".$id.",type:".$type.";equips:".json_encode($equips,true);

	/*调试*/
	if (isDebug()) {
		$rwid = '6';
		$sfzh ='210211195907111414';
		$kdid ='8';
		$geometry = "POINT (121.50873377167 38.943964182768)";
		$ydcs ="5";
		$jgsj ="5300";
	}
//	$rwid = '9';
//	$sfzh ='210211197112261493';
//	$kdid ='13';
//	$geometry = "POINTS (121.52840946 31.24538348)";
//	$ydcs ="3";
//	$jgsj ="600";
	
	if ($sfzh==""||$rwid==""||$kdid==""||$xlid==""||$ydcs==""||$jgsj=="") {
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
	$res = $equip->insertAutoPoint($rwid,$sfzh,$kdid,$xlid,$geometry,$ydcs,$jgsj);//调用实例方法
	if($res['result']){
		$result = array (
				'result' => 'true',
				'errmsg' => '',
				'ydkcs' => $res['records']?$res['records']['ydkcs']:"",
				'zhdksj' => $res['records']?$res['records']['zhdksj']:""
			);
	}else{
		$result = array (
				'result' => 'false',
				'errmsg' => $res['errmsg'],
				'ydkcs' => $res['records']?$res['records']['ydkcs']:"",
				'zhdksj' => $res['records']?$res['records']['zhdksj']:""
			);
	}
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>