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
	/*登陆超时校验*/
	if (!isset ($_SESSION["userId"])) {
		$arr = array (
			'head' => array (
						'code' => 9,
						'message' => '登录超时'
						),
			'value' => '',
			'extend' => ''
		);
		die(encodeJson($arr));
	}
	
	/*查询*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
	$id="";$type="";$orgCode="";$equips= array();$type="";
	if(isset($content['condition'])){
		$id = isset($content['condition']['id'])?$content['condition']['id']:"";
		$type = isset($content['condition']['type'])?iconv("UTF-8","GBK",$content['condition']['type']):"";
		$orgCode = isset($content['condition']['orgCode'])?$content['condition']['orgCode']:"";
		$equips = isset($content['condition']['equips'])?$content['condition']['equips']:null;
		
	}
	//echo "id:".$id.",type:".$type.";equips:".json_encode($equips,true);

	/*调试*/
	if (isDebug()) {

		$orgCode = '210202000000';
		//$featureId="6";
		$id ='210203194910175270';
		$type ='2';
		$equips = array();
		$eq = array('zblb'=>'01000005','zbbm'=>'01000005','bdsl'=>'60');
		$eq1 = array('zblb'=>'01T01001','zbbm'=>'01T01001001','bdsl'=>'1');
		array_push($equips, $eq);
		array_push($equips, $eq1);
		echo json_encode($equips,true);
		
	}
	if ($id==""||$type==""||$orgCode=="") {
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
	$res = $equip->updateOrAddModelEquip($id,$type,$equips,$orgCode,$_SESSION["userName"]);//调用实例方法
	$code =1;
	$result = array (
				'head' => array (
							'code' => $code,
							'message' => $res['errmsg']
							),
				'value' => $res['result'],
				'extend' => '',
                                'records' =>$res['records']
			);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>