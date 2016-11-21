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
	$sfzh = isset( $_REQUEST['sfzh'] ) ? $_REQUEST['sfzh'] : "";

		
	/*调试*/
	if (isDebug()) {
		//$orgCode = '210200000000';
		$sfzh ='210225197512280456';
	}
	//$sfzh ='210211197112261493';
	if ($sfzh=="") {
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!!');
		die(encodeJson($arr));
	}
		
	$equip = new Equip();//创建调度类实例
	$res = $equip->getAndroidTask($sfzh);//调用实例方法
	if($res['result']){
		$res['result']='true';
	}else{
		$res['result']='false';
	}
	
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>