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
	
	/*查询*/
	$orgCode = isset( $_REQUEST['orgCode'] ) ? $_REQUEST['orgCode'] : "";

		
	/*调试*/
	if (isDebug()) {
		$orgCode = '210203440000';
	}
	if ($orgCode=="") {
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!!');
		die(encodeJson($arr));
	}
		
	$equip = new Equip();//创建调度类实例
	$res = $equip->getEquip350M($orgCode);//调用实例方法
	$datas = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $res
			);
	echo json_encode($datas, JSON_UNESCAPED_UNICODE);
?>