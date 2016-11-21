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
	date_default_timezone_set('Asia/Shanghai'); //设置时区
	/**
	 * 添加和修改巡逻路线接口
	 */
	/*查询*/
	$rwid = isset( $_REQUEST['rwid'] ) ? $_REQUEST['rwid'] : "";
	$sfzh = isset( $_REQUEST['sfzh'] ) ? $_REQUEST['sfzh'] : "";
	$kdid = isset( $_REQUEST['kdid'] ) ? $_REQUEST['kdid'] : "";
	$xlid = isset( $_REQUEST['xlid'] ) ? $_REQUEST['xlid'] : "";
	$ydcs = isset( $_REQUEST['ydcs'] ) ? $_REQUEST['ydcs'] : "";
	$jgsj = isset( $_REQUEST['jgsj'] ) ? $_REQUEST['jgsj'] : "";
	$geometry = isset( $_REQUEST['geometry'] ) ? $_REQUEST['geometry'] : "";
	$phote = $_FILES['photo']['name']?$_FILES['photo']['name']:"";
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
	
	$year = date('Y');
	$month = date('m');
	$day = date('d');
	$base_path = GlobalConfig :: getInstance()->upload_src . 'equip/'; //接收文件目录  	
	$base_path_year = $base_path . $year . '/';
	$base_path_month = $base_path_year . $month . "/";
	$base_path_day = $base_path_month . $day . "/";
	$target_path = $base_path_day . basename($phote);
	
	if ($sfzh==""||$rwid==""||$kdid==""||$xlid==""||$ydcs==""||$jgsj==""||$phote=="") {
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
	
	$gid = "";
	$equip = new Equip();//创建调度类实例
//	$groupEntity = $equip->getGroupByUserId($sfzh);
//	if(!($groupEntity&&$groupEntity['gid'])){
//		$errMsg="无巡逻组数据";
//		$result = array(
//						'result' =>'false',
//						'errmsg' =>$errMsg,
//						'ydkcs' => "",
//						'zhdksj' => ""
//						);
//		die(encodeJson($result));
//	}
//	$gid = $groupEntity['gid'];
	$clockParam = $equip->getClockParam($rwid,$sfzh,$xlid,$kdid);
	if($clockParam['ydkcs']>=$ydcs){
		$errMsg="已完成打卡";
		$result = array(
						'result' =>'false',
						'errmsg' =>$errMsg,
						'ydkcs' => $clockParam['ydkcs'],
						'zhdksj' => $clockParam['zhdksj']
						);
		die(encodeJson($result));
	}
	$nowDate = strtotime(date("Y-m-d H:i:s"));
	$lastDate = strtotime($clockParam['zhdksj']);
	$diffDate = $nowDate-$lastDate;
	if($diffDate<$jgsj){
		$errMsg="打卡次数过频";
		$result = array(
						'result' =>'false',
						'errmsg' =>$errMsg,
						'ydkcs' => $clockParam['ydkcs'],
						'zhdksj' => $clockParam['zhdksj']
						);
		die(encodeJson($result));
	}
	
	
	if (!file_exists($base_path)) {
		if (!mkdir($base_path, 0777, true)) {
			die('无法建立路径');
		}
	}
	if (!file_exists($base_path_year)) {
		if (!mkdir($base_path_year, 0777, true)) {
			die('无法建立路径');
		}
	}
	if (!file_exists($base_path_month)) {
		if (!mkdir($base_path_month, 0777, true)) {
			die('无法建立路径');
		}
	}

	if (!file_exists($base_path_day)) {
		if (!mkdir($base_path_day, 0777, true)) {
			die('无法建立路径');
		}
	}
	
	//开始上传文件
	if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
		$ret = "操作成功";
		$res = $equip->insertHandPoint($rwid,$sfzh,$kdid,$xlid,$target_path);//调用实例方法
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

	}else{
		$result = array(
						'result' =>'false',
						'errmsg' =>'上传失败',
						'ydkcs' => $clockParam['ydkcs'],
						'zhdksj' => $clockParam['zhdksj']
						);
	}
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>