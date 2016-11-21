<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
	
?>
<?php
/**
 * 功能：发送文件
 * 
 * 输入参数：
 * method=1	 (必输项)
 * bmdm	部门ID
 * lastTime	上次最晚查询时间
 * 返回值：
 * 	{result:"true或false",errmsg:"错误信息",records:"[]"}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Unit.class.php');
	
	/*获取参数*/
	$orgCode = isset( $_REQUEST['orgCode'] ) ? $_REQUEST['orgCode'] : "";	
	$method = isset( $_REQUEST['mode'] ) ? $_REQUEST['mode'] : "";
	$gid = isset( $_REQUEST['gid'] ) ? $_REQUEST['gid'] : "";
	$carId = isset( $_REQUEST['carId'] ) ? $_REQUEST['carId'] : "";
	$userId = isset( $_REQUEST['userId'] ) ? $_REQUEST['userId'] : "";
	$leaderId = isset( $_REQUEST['leaderId'] ) ? $_REQUEST['leaderId'] : "";
	$status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : "";
	$m350Id = isset( $_REQUEST['m350Id'] ) ? $_REQUEST['m350Id'] : "";
	
	/*调试*/
	if (isDebug()){ 
		$method = '2';		
	}
	/*参数校验*/
	if ($method==""){	
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!');
		die(encodeJson($arr));
	}
	if($method=='1'){
		
		/*调试*/
		if (isDebug()){ 
			$gid='66';
			//$userId='210225197512280456,210202197802183211';
			//$carId='5';//$carId='辽B22222';
			//$leaderId='210202197802183211';
			$orgCode = "210203440000";
			$status='3';
			//$jybh='250000';
		//	$id='285';
		}
		if ($gid==""||$status=="") {
			$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!!');
			die(encodeJson($arr));
		}
		$unit = new Unit();//创建tpms数据库实例getImageInfo($img)
		$res=$unit->updateDutyGroups($gid,$status);
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
		
  }else if($method=='2'){
  	
  
  	/*调试*/
		if (isDebug()){ 
			
			$userId='210211195907111414,210204198205024834,210225197512280456';
			$carId='4';//$carId='辽B22222';
			$leaderId='210225197512280456';
			$status='1';
			$m350Id="000005165549";
			$orgCode="210203440000";
			
		}
		
		if ($carId==""||$userId==""||$leaderId==""||$status==""||$m350Id=="") {
			$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!!');
			die(encodeJson($arr));
		}
  	
  		$unit = new Unit();//创建tpms数据库实例getImageInfo($img)
		$res=$unit->createDutyGroups($gid,$carId,$userId,$leaderId,$orgCode,$status,$m350Id);
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
		
  }

?>