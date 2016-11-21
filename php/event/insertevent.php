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
	include_once('../class/OutEvent.class.php');
	
	
	/*获取参数*/
       
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";

	$content = Json_decode($content,true);
	//print_r($content);
	if(isset($content['condition'])){
		
        $jqid = isset($content['condition']['jqid'])? iconv("UTF-8","GBK",$content['condition']['jqid']):"";
		//echo "jqid:".$jqid;
		$jqbh = isset($content['condition']['jqbh'])? iconv("UTF-8","GBK",$content['condition']['jqbh']):"";
		$jjlx = isset($content['condition']['jjlx'])? iconv("UTF-8","GBK",$content['condition']['jjlx']):"";
		$jjfs = isset($content['condition']['jjfs'])? iconv("UTF-8","GBK",$content['condition']['jjfs']):"";
		$bjfs = isset($content['condition']['bjfs'])? iconv("UTF-8","GBK",$content['condition']['bjfs']):"";
		$bjsj = isset($content['condition']['bjsj'])? iconv("UTF-8","GBK",$content['condition']['bjsj']):"";
		$jqdd = isset($content['condition']['jqdd'])? iconv("UTF-8","GBK",$content['condition']['jqdd']):"";
		$bjnr = isset($content['condition']['bjnr'])? iconv("UTF-8","GBK",$content['condition']['bjnr']):"";
		$jqlbdm = isset($content['condition']['jqlbdm'])? iconv("UTF-8","GBK",$content['condition']['jqlbdm']):"";
		$jqlxdm = isset($content['condition']['jqlxdm'])? iconv("UTF-8","GBK",$content['condition']['jqlxdm']):"";
		$jqxldm = isset($content['condition']['jqxldm'])? iconv("UTF-8","GBK",$content['condition']['jqxldm']):"";
		$jqdjdm = isset($content['condition']['jqdjdm'])? iconv("UTF-8","GBK",$content['condition']['jqdjdm']):"";
		$bjrxm = isset($content['condition']['bjrxm'])? iconv("UTF-8","GBK",$content['condition']['bjrxm']):"";
		$bjrsfzh = isset($content['condition']['bjrsfzh'])? iconv("UTF-8","GBK",$content['condition']['bjrsfzh']):"";
		$bjrxbdm = isset($content['condition']['bjrxbdm'])? iconv("UTF-8","GBK",$content['condition']['bjrxbdm']):"";
		$bjdh = isset($content['condition']['bjdh'])? iconv("UTF-8","GBK",$content['condition']['bjdh']):"";
		$lxdh = isset($content['condition']['lxdh'])? iconv("UTF-8","GBK",$content['condition']['lxdh']):"";
		$bjdhyhxm = isset($content['condition']['bjdhyhxm'])? iconv("UTF-8","GBK",$content['condition']['bjdhyhxm']):"";
		$bjdhyhdz = isset($content['condition']['bjdhyhdz'])? iconv("UTF-8","GBK",$content['condition']['bjdhyhdz']):"";
		$jjrbh = isset($content['condition']['jjrbh'])? iconv("UTF-8","GBK",$content['condition']['jjrbh']):"";
		$jjsj = isset($content['condition']['jjsj'])? iconv("UTF-8","GBK",$content['condition']['jjsj']):"";
		$hzsj = isset($content['condition']['hzsj'])? iconv("UTF-8","GBK",$content['condition']['hzsj']):"";
		$cjdbh = isset($content['condition']['cjdbh'])? iconv("UTF-8","GBK",$content['condition']['cjdbh']):"";
		$stationhouseCode = isset($content['condition']['stationhouseCode'])? iconv("UTF-8","GBK",$content['condition']['stationhouseCode']):"";
		$stationhouse = isset($content['condition']['stationhouse'])? iconv("UTF-8","GBK",$content['condition']['stationhouse']):"";
		$bmbh = isset($content['condition']['bmbh'])? iconv("UTF-8","GBK",$content['condition']['bmbh']):"";
	}
	//echo "content=".$content."...............................";
	
	/*
	$jqid = isset( $_REQUEST['jqid'] ) ? iconv("UTF-8","GBK",$_REQUEST['jqid']) : "";
	$jqbh = isset( $_REQUEST['jqbh'] ) ? iconv("UTF-8","GBK",$_REQUEST['jqbh']) : "";
	$jjlx = isset( $_REQUEST['jjlx'] ) ? iconv("UTF-8","GBK",$_REQUEST['jjlx']) : "";
	$jjfs = isset( $_REQUEST['jjfs'] ) ? iconv("UTF-8","GBK",$_REQUEST['jjfs']) : "";
	$bjfs = isset( $_REQUEST['bjfs'] ) ? iconv("UTF-8","GBK",$_REQUEST['bjfs']) : "";
	$bjsj = isset( $_REQUEST['bjsj'] ) ? iconv("UTF-8","GBK",$_REQUEST['bjsj']) : "";
	$jqdd = isset( $_REQUEST['jqdd'] ) ? iconv("UTF-8","GBK",$_REQUEST['jqdd']) : "";
	$bjnr = isset( $_REQUEST['bjnr'] ) ? iconv("UTF-8","GBK",$_REQUEST['bjnr']) : "";
	$jqlbdm = isset( $_REQUEST['jqlbdm'] ) ? iconv("UTF-8","GBK",$_REQUEST['jqlbdm']) : "";
	$jqlxdm = isset( $_REQUEST['jqlxdm'] ) ? iconv("UTF-8","GBK",$_REQUEST['jqlxdm']) : "";
	$jqxldm = isset( $_REQUEST['jqxldm'] ) ? iconv("UTF-8","GBK",$_REQUEST['jqxldm']) : "";
	$jqdjdm = isset( $_REQUEST['jqdjdm'] ) ? iconv("UTF-8","GBK",$_REQUEST['jqdjdm']) : "";
	$bjrxm = isset( $_REQUEST['bjrxm'] ) ? iconv("UTF-8","GBK",$_REQUEST['bjrxm']) : "";
	$bjrsfzh = isset( $_REQUEST['bjrsfzh'] ) ? iconv("UTF-8","GBK",$_REQUEST['bjrsfzh']) : "";
	$bjrxbdm = isset( $_REQUEST['bjrxbdm'] ) ? iconv("UTF-8","GBK",$_REQUEST['bjrxbdm']) : "";
	$bjdh = isset( $_REQUEST['bjdh'] ) ? iconv("UTF-8","GBK",$_REQUEST['bjdh']) : "";
	$lxdh = isset( $_REQUEST['lxdh'] ) ? iconv("UTF-8","GBK",$_REQUEST['lxdh']) : "";
	$bjdhyhxm = isset( $_REQUEST['bjdhyhxm'] ) ? iconv("UTF-8","GBK",$_REQUEST['bjdhyhxm']) : "";	
	$bjdhyhdz = isset( $_REQUEST['bjdhyhdz'] ) ? iconv("UTF-8","GBK",$_REQUEST['bjdhyhdz']) : "";
	$jjrbh = isset( $_REQUEST['jjrbh'] ) ? iconv("UTF-8","GBK",$_REQUEST['jjrbh']) : "";
	$jjsj = isset( $_REQUEST['jjsj'] ) ? iconv("UTF-8","GBK",$_REQUEST['jjsj']) : "";
	$hzsj = isset( $_REQUEST['hzsj'] ) ? iconv("UTF-8","GBK",$_REQUEST['hzsj']) : "";
	*/
	/*调试*/
	if (isDebug()){ 
		$jqid = 'out3';	
		$jqbh = 'dswersser';
		$jjlx ='1';
		$jjfs = '7';
		$bjfs = '7';
		$bjsj = '2015-03-18 23:12:45';
		$jqdd="在析要";
		$bjnr = '仍佣兵枯';
		$jqlbdm = '7';
		$jqlxdm = '7';
		$jqxldm = '7';
		$jqdjdm = '7';
		$bjrxm = '要械';
		$bjrsfzh = '123213123123';
		$bjrxbdm = '1';
		$bjdh="021-1254878";
		$lxdh="135467899";
		$bjdhyhxm="夺要";
		$bjdhyhdz="我工地我";
		$jjrbh="11212";
		$jjsj="2015-03-18 12:25:36";
		$hzsj="";
		
	$jqid = isset($jqid) ?iconv("UTF-8","GBK",$jqid) : "";
	$jqbh = isset($jqbh) ?iconv("UTF-8","GBK",$jqbh) : "";
	$jjlx =  isset($jjlx) ?iconv("UTF-8","GBK",$jjlx) : "";
	$jjfs =  isset($jjfs) ?iconv("UTF-8","GBK",$jjfs) : "";
	$bjfs = isset($bjfs) ? iconv("UTF-8","GBK",$bjfs) : "";
	$bjsj = isset($bjsj) ? iconv("UTF-8","GBK",$bjsj) : "";
	$jqdd = isset($jqdd) ? iconv("UTF-8","GBK",$jqdd) : "";
	$bjnr = isset($bjnr) ? iconv("UTF-8","GBK",$bjnr) : "";
	$jqlbdm =isset($jqlbdm) ? iconv("UTF-8","GBK",$jqlbdm) : "";
	$jqlxdm =isset($jqlxdm) ? iconv("UTF-8","GBK",$jqlxdm) : "";
	$jqxldm =isset($jqxldm) ? iconv("UTF-8","GBK",$jqxldm) : "";
	$jqdjdm =isset($jqdjdm) ? iconv("UTF-8","GBK",$jqdjdm) : "";
	$bjrxm =isset($bjrxm) ? iconv("UTF-8","GBK",$bjrxm) : "";
	$bjrsfzh =isset($bjrsfzh) ? iconv("UTF-8","GBK",$bjrsfzh) : "";
	$bjrxbdm =isset($bjrxbdm) ? iconv("UTF-8","GBK",$bjrxbdm) : "";
	$bjdh =isset($bjdh) ? iconv("UTF-8","GBK",$bjdh) : "";
	$lxdh =isset($lxdh) ? iconv("UTF-8","GBK",$lxdh) : "";
	$bjdhyhxm =isset($bjdhyhxm) ? iconv("UTF-8","GBK",$bjdhyhxm) : "";	
	$bjdhyhdz =isset($bjdhyhdz) ? iconv("UTF-8","GBK",$bjdhyhdz) : "";
	$jjrbh =isset($jjrbh) ? iconv("UTF-8","GBK",$jjrbh) : "";
	$jjsj =isset($jjsj) ? iconv("UTF-8","GBK",$jjsj) : "";
	$hzsj =isset($hzsj) ? iconv("UTF-8","GBK",$hzsj) : "";
		
	}
	/*参数校验*/
	if ($jqid==""){	
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
	$event = new OutEvent();//创建调度类实例
	$res=$event->insertEvent($jqid,$jqbh,$jjlx,$jjfs,$bjfs,$bjsj,$jqdd,$bjnr,$jqlbdm,$jqlxdm,$jqxldm,$jqdjdm,$bjrxm,$bjrsfzh,$bjrxbdm,$bjdh,$lxdh,$bjdhyhxm,$bjdhyhdz,$jjrbh,$jjsj,$hzsj,$cjdbh,$stationhouseCode,$stationhouse,$bmbh);
	$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => $res,
				'extend' => ''
			);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
        
		

?>