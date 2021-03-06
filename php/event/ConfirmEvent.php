<?php
    error_reporting(E_ALL || ~E_NOTICE);
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
	session_start();
	session_commit();
?>
<?php
/**
 * 功能：终端处警更新状态接口
 * 
 * 	{result:"true或false",errmsg:"操作信息"}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Event.class.php');
	
	/*获取参数*/	
	$status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : "";
	$jqid = isset( $_REQUEST['jqid'] ) ? $_REQUEST['jqid'] : "";
	$cjdbh = isset( $_REQUEST['cjdbh'] ) ? $_REQUEST['cjdbh'] : "";
	$jqjqzb = isset( $_REQUEST['jqjqzb'] ) ? $_REQUEST['jqjqzb'] : "";
	$userId = isset( $_REQUEST['userId'] ) ? $_REQUEST['userId'] : "";
	$userName = isset( $_REQUEST['userName'] ) ? $_REQUEST['userName'] : "";
        $zlbh=isset( $_REQUEST['zlbh'] ) ? $_REQUEST['zlbh'] : "";

	
	/*调试*/
	if (isDebug()){ 
		$status = '5';	
		$jqid = '2';
		$cjdbh='558';
		$jqjqzb ='point(121.1213 38.222)';
		$userId = '210211197112261493';
		$userName='王宏宇';
	}
	/*参数校验*/
	if ($status==""||$jqid==""||$cjdbh==""){	
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!');
		die(encodeJson($arr));
	}else if($status=="4"&&$jqjqzb==""||$cjdbh==""){
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!111');
		die(encodeJson($arr));
	}
	$event = new Event();//创建tpms数据库实例getImageInfo($img)
	//$res = $event->getEventFeedDetailByid($jqid);
	$res=$event->updateEventStatus($jqid,$cjdbh,$status,$jqjqzb,$userId,iconv("UTF-8","GBK",$userName),$zlbh);
	//if($res){
		//$arr = array('result' =>'true' , 'errmsg' =>'操作成功!');
	//}else{
		//$arr = array('result' =>'false' , 'errmsg' =>'操作失败!');
	//}
	//echo $res;
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
		

?>