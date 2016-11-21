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
 * 功能：终端指令反馈接口
 * 
 * 	{result:"true或false",errmsg:"操作信息"}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Event.class.php');
	
	/*获取参数*/	
	$zlbh = isset( $_REQUEST['zlbh'] ) ? $_REQUEST['zlbh'] : "";
	$cljg = isset( $_REQUEST['cljg'] ) ? iconv("UTF-8","GBK",$_REQUEST['cljg']) : "";

	
	/*调试*/
	if (isDebug()){ 
		$zlbh = '136';	
		$cljg = '已处理完成';
		$cljg=iconv("UTF-8","GBK",$cljg);
	}
	/*参数校验*/
	if ($zlbh==""||$cljg==""){	
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!');
		die(encodeJson($arr));
	}
	$event = new Event();//创建tpms数据库实例getImageInfo($img)
	$res=$event->updatePeProcessCommand($zlbh,$cljg);
	if($res){
		$arr = array('result' =>'true' , 'errmsg' =>'操作成功!');
	}else{
		$arr = array('result' =>'false' , 'errmsg' =>'操作失败!');
	}
	echo json_encode($arr, JSON_UNESCAPED_UNICODE);
		

?>