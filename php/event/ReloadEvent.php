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
	include_once('../class/Event.class.php');
	
	/*获取参数*/	
	$jqid = isset( $_REQUEST['jqid'] ) ? $_REQUEST['jqid'] : "";
	$zlbh = isset( $_REQUEST['zlbh'] ) ? $_REQUEST['zlbh'] : "";

	
	/*调试*/
	if (isDebug()){ 
		$jqid = '200';
	}
	/*参数校验*/
	if ($jqid==""||$zlbh==""){	
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!');
		die(encodeJson($arr));
	}
	$event = new Event();//创建tpms数据库实例getImageInfo($img)
	$res=$event->reLoadEventStatus($jqid,$zlbh);
	echo json_encode($res, JSON_UNESCAPED_UNICODE);
		

?>