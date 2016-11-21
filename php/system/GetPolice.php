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
	include_once('../class/Unit.class.php');
	include_once('../class/DynamicPoint.class.php');
	include_once('../class/PolicePoints.class.php');
	
	/*查询*/
	$orgCode = isset( $_REQUEST['orgCode'] ) ? $_REQUEST['orgCode'] : "";
		
	/*调试*/
	if (isDebug()) {
		$orgCode = '210200000000';
		//$lastTime='2015-12-12 16:17:21';
	}
	if ($orgCode=="") {
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!!');
		die(encodeJson($arr));
	}
		
	$unit = new Unit();//创建调度类实例
	$res = $unit->GetMenByBmdm($orgCode);//调用实例方法
	//构建登陆成功返回数据		
	$res = array('result' =>'true', 'errmsg' =>"", 'records' =>$res);
	echo encodeJson($res);
?>