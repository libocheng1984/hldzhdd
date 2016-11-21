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
 * 功能：警员实时查询
 * 
 * 输入参数：
 * bmdm	部门ID
 * lastTime	上次最晚查询时间
 * 返回值：
 * 	{result:"true或false",errmsg:"错误信息",records:"[]"}
 *********************************************************
 * 功能：警员实时新增修改
 *
 * 输入参数：
 * mode: 2
 * method=post	上传参数信息(必输项)
 * xh	警员ID
 * online	是否在线
 * deviceId	定位设备编号
 * location	最新定位经纬度
 * locateTime	定位时间
 * message	消息
 * direction			方向
 * speed		速度
 * status	定位状态
 * road	所处位置
 * recvTime	接收定位时间
 *
 * 返回值：
 *  {result:"true或false",errmsg:"错误信息"}
 *********************************************************
 * 功能：施工删除
 *
 * 输入参数：
 *  mode: 3
 *  eventId: 警情编号
 *
 * 返回值：
 *  {result:"true或false",errmsg:"错误信息"}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/ZHDD.class.php');
	include_once('../class/DynamicPoint.class.php');
	include_once('../class/PolicePoints.class.php');
	
	$method = isset( $_REQUEST['method'] ) ? $_REQUEST['method'] : "";
	
	/*调试*/
	if (isDebug()) 
		$method = 'get';
	
	/*必输参数校验*/
	if ($method=="") {
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!');
		die(encodeJson($arr));
	}
	
	/*查询*/
	if($method=='get'){
		$lastTime = isset( $_REQUEST['lastTime'] ) ? $_REQUEST['lastTime'] : null;
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
		
		$commandDb = new ZHDD();//创建调度类实例
		$res = $commandDb->getDynamicLocation($orgCode,$lastTime);//调用实例方法
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
	}
?>