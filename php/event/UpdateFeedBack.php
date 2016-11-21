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
	include_once('../class/EventMix.class.php');
	include_once('../class/DynamicPoint.class.php');
	include_once('../class/FeedBackEntity.class.php');
	
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
	/*传入参数*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
	
	if(isset($content['condition'])){
		$fkdbh = isset($content['condition']['fkdbh'])?$content['condition']['fkdbh']:"";	//反馈单编号
		$cjdbh = isset($content['condition']['cjdbh'])?$content['condition']['cjdbh']:"";	//处警单编号
		$jqid =	 isset($content['condition']['jqid'])?$content['condition']['jqid']:"";		//警情ID
		$xzqhdm = isset($content['condition']['xzqhdm'])?$content['condition']['xzqhdm']:"";//行政区划代码
		$fkdwdm = isset($content['condition']['fkdwdm'])?$content['condition']['fkdwdm']:"";//反馈单位代码
		$fkybh = isset($content['condition']['fkybh'])?$content['condition']['fkybh']:"";	//反馈员编号
		$fkyxm = isset($content['condition']['fkyxm'])?iconv("UTF-8","GBK",$content['condition']['fkyxm']):"";//反馈员姓名
		$zhrs =	 isset($content['condition']['zhrs'])?iconv("UTF-8","GBK",$content['condition']['zhrs']):"";  //抓获人数
		$sars =	 isset($content['condition']['sars'])?iconv("UTF-8","GBK",$content['condition']['sars']):"";  //死亡人数
		$jzrs =	 isset($content['condition']['jzrs'])?iconv("UTF-8","GBK",$content['condition']['jzrs']):"";  //救助人数
		$jzrssm = isset($content['condition']['jzrssm'])?iconv("UTF-8","GBK",$content['condition']['jzrssm']):"";//救助人数说明
		$jjrs =	 isset($content['condition']['jjrs'])?iconv("UTF-8","GBK",$content['condition']['jjrs']):"";	 //解救人数
		$jjrssm = isset($content['condition']['jjrssm'])?iconv("UTF-8","GBK",$content['condition']['jjrssm']):"";//解救人数说明
		$tprs =	 isset($content['condition']['tprs'])?iconv("UTF-8","GBK",$content['condition']['tprs']):"";	 //逃跑人数
		$ssrs =	 isset($content['condition']['ssrs'])?iconv("UTF-8","GBK",$content['condition']['ssrs']):"";	 //受伤人数
		$ssrssm = isset($content['condition']['ssrssm'])?iconv("UTF-8","GBK",$content['condition']['ssrssm']):"";//受伤人数说明
		$swrs =	 isset($content['condition']['swrs'])?iconv("UTF-8","GBK",$content['condition']['swrs']):"";	 //死亡人数
		$swrssm = isset($content['condition']['swrssm'])?iconv("UTF-8","GBK",$content['condition']['swrssm']):"";//死亡人数说明
		$djyrs = isset($content['condition']['djyrs'])?iconv("UTF-8","GBK",$content['condition']['djyrs']):"";	 //待救援人数
		$cjryssrs = isset($content['condition']['cjryssrs'])?iconv("UTF-8","GBK",$content['condition']['cjryssrs']):"";//出警人员受伤人数
		$cjryswrs = isset($content['condition']['cjryswrs'])?iconv("UTF-8","GBK",$content['condition']['cjryswrs']):"";//出警人员死亡人数
		$jjssqk = isset($content['condition']['jjssqk'])?iconv("UTF-8","GBK",$content['condition']['jjssqk']):"";	   //经济损失
		$whjjssqk =	 isset($content['condition']['whjjssqk'])?iconv("UTF-8","GBK",$content['condition']['whjjssqk']):"";//挽回经济损失
		$sfphxsaj =	 isset($content['condition']['sfphxsaj'])?iconv("UTF-8","GBK",$content['condition']['sfphxsaj']):"";//是否破获刑事案件
		$sfcczaaj =	 isset($content['condition']['sfcczaaj'])?iconv("UTF-8","GBK",$content['condition']['sfcczaaj']):"";//是否查处治安案件
		$sfjjjf =	 isset($content['condition']['sfjjjf'])?iconv("UTF-8","GBK",$content['condition']['sfjjjf']):"";	//是否解决纠纷
		$lzscrs =	 isset($content['condition']['lzscrs'])?iconv("UTF-8","GBK",$content['condition']['lzscrs']):"";	//留置审查人数
		$jqcljg =	 isset($content['condition']['jqcljg'])?iconv("UTF-8","GBK",$content['condition']['jqcljg']):"";	//警情处理结果
		$jjfvrs =	 isset($content['condition']['jjfvrs'])?iconv("UTF-8","GBK",$content['condition']['jjfvrs']):"";	//解救被拐卖妇女人数
		$jjetrs =	 isset($content['condition']['jjetrs'])?iconv("UTF-8","GBK",$content['condition']['jjetrs']):"";	//解救被拐卖儿童人数
		$hzdjdm =	 isset($content['condition']['hzdjdm'])?iconv("UTF-8","GBK",$content['condition']['hzdjdm']):"";	//火灾等级代码
		$tqqkdm =	 isset($content['condition']['tqqkdm'])?iconv("UTF-8","GBK",$content['condition']['tqqkdm']):"";	//天气情况代码
		$ssqkms =	 isset($content['condition']['ssqkms'])?iconv("UTF-8","GBK",$content['condition']['ssqkms']):"";	//损失情况描述
		$hzyydm =	 isset($content['condition']['hzyydm'])?iconv("UTF-8","GBK",$content['condition']['hzyydm']):"";	//火灾原因代码
		$zhsglxdm =	 isset($content['condition']['zhsglxdm'])?iconv("UTF-8","GBK",$content['condition']['zhsglxdm']):"";//灾害事故类型代码
		$qhwdm =	 isset($content['condition']['qhwdm'])?iconv("UTF-8","GBK",$content['condition']['qhwdm']):"";		//起火物代码
		$qhjzjgdm =	 isset($content['condition']['qhjzjgdm'])?iconv("UTF-8","GBK",$content['condition']['qhjzjgdm']):"";//起火建筑结构代码
		$hzcsdm =	 isset($content['condition']['hzcsdm'])?iconv("UTF-8","GBK",$content['condition']['hzcsdm']):"";	//火灾场所代码
		$dycdsj =	 isset($content['condition']['dycdsj'])?iconv("UTF-8","GBK",$content['condition']['dycdsj']):"";	//第一出动时间
		$dydcsj =	 isset($content['condition']['dydcsj'])?iconv("UTF-8","GBK",$content['condition']['dydcsj']):"";	//第一到场时间
		$hcpmsj =	 isset($content['condition']['hcpmsj'])?iconv("UTF-8","GBK",$content['condition']['hcpmsj']):"";	//火场扑灭时间
		$clsj =	 isset($content['condition']['clsj'])?iconv("UTF-8","GBK",$content['condition']['clsj']):"";			//撤离时间
		$xczzh =	 isset($content['condition']['xczzh'])?iconv("UTF-8","GBK",$content['condition']['xczzh']):"";		//现场总指挥
		$cdsqs =	 isset($content['condition']['cdsqs'])?iconv("UTF-8","GBK",$content['condition']['cdsqs']):"";		//出动水枪数
		$sfzddw =	 isset($content['condition']['sfzddw'])?iconv("UTF-8","GBK",$content['condition']['sfzddw']):"";	//是否重点单位
		$zddwbm =	 isset($content['condition']['zddwbm'])?iconv("UTF-8","GBK",$content['condition']['zddwbm']):"";	//重点单位名称
		$xlbmrs =	 isset($content['condition']['xlbmrs'])?iconv("UTF-8","GBK",$content['condition']['xlbmrs']):"";	//下落不明人数
		$jtsgxtdm =	 isset($content['condition']['jtsgxtdm'])?iconv("UTF-8","GBK",$content['condition']['jtsgxtdm']):"";//交通事故形态代码
		$sfwhp =	 isset($content['condition']['sfwhp'])?iconv("UTF-8","GBK",$content['condition']['sfwhp']):"";		//是否装载危险品
		$sgccyydm =	 isset($content['condition']['sgccyydm'])?iconv("UTF-8","GBK",$content['condition']['sgccyydm']):"";//交通事故初查原因代码
		$njddm =	 isset($content['condition']['njddm'])?iconv("UTF-8","GBK",$content['condition']['njddm']):"";		//能见度代码
		$lmzkdm =	 isset($content['condition']['lmzkdm'])?iconv("UTF-8","GBK",$content['condition']['lmzkdm']):"";	//路面状况代码
		$shjdcs =	 isset($content['condition']['shjdcs'])?iconv("UTF-8","GBK",$content['condition']['shjdcs']):"";	//损坏机动车数
		$shfjdcs =	 isset($content['condition']['shfjdcs'])?iconv("UTF-8","GBK",$content['condition']['shfjdcs']):"";	//损坏非机动车数
		$dllxdm =	 isset($content['condition']['dllxdm'])?iconv("UTF-8","GBK",$content['condition']['dllxdm']):"";	//道路类型代码
		$belong =	 isset($content['condition']['belong'])?iconv("UTF-8","GBK",$content['condition']['belong']):"";	//是否属于街面 1是，0不是
		$scene =	 isset($content['condition']['scene'])?iconv("UTF-8","GBK",$content['condition']['scene']):"";	//是否已立，拟立
	}
	
		
	/*调试*/
	if (isDebug()) {
		$fkdbh = "1";
		$cjdbh= "2";
		$jqid= "3";
		$xzqhdm= "4";
		$fkybh= "210203440000";
		$fkyxm="人民会场";
		$fkyxm = iconv("UTF-8","GBK",$fkyxm);
	$fkdwdm=""; 	
	 $zhrs =	 "2";  //抓获人数
	 $sars =	 "1";  //死亡人数
	 $jzrs =	 "1";  //救助人数
	 $jzrssm = "1";//救助人数说明
	 $jjrs =	 "";	 //解救人数
	 $jjrssm = "";//解救人数说明
	 $tprs =	 "";	 //逃跑人数
	 $ssrs =	 "";	 //受伤人数
	 $ssrssm = "";//受伤人数说明
	 $swrs =	 "";	 //死亡人数
	 $swrssm = "";//死亡人数说明
	 $djyrs = "";	 //待救援人数
	 $cjryssrs = "";//出警人员受伤人数
	 $cjryswrs = "";//出警人员死亡人数
	 $jjssqk = "";	   //经济损失
	 $whjjssqk =	"";//挽回经济损失
	 $sfphxsaj =	"";//是否破获刑事案件
	 $sfcczaaj =	"";//是否查处治安案件
	 $sfjjjf = "";	//是否解决纠纷
	 $lzscrs = "";	//留置审查人数
	 $jqcljg = "";	//警情处理结果
	 $jjfvrs = "";	//解救被拐卖妇女人数
	 $jjetrs = "";	//解救被拐卖儿童人数
	 $hzdjdm = "";	//火灾等级代码
	 $tqqkdm = "";	//天气情况代码
	 $ssqkms = "";	//损失情况描述
	 $hzyydm = "";	//火灾原因代码
	 $zhsglxdm = "";//灾害事故类型代码
	 $qhwdm = "";		//起火物代码
	 $qhjzjgdm =	"";//起火建筑结构代码
	 $hzcsdm = "";	//火灾场所代码
	 $dycdsj = "2014-12-12 15:23:45";	//第一出动时间
	 $dydcsj = "2014-12-12 15:23:45";	//第一到场时间
	 $hcpmsj = "2014-12-12 15:23:45";	//火场扑灭时间
	 $clsj = "2014-12-12 15:23:45";			//撤离时间
	 $xczzh = "";		//现场总指挥
	 $cdsqs = "";		//出动水枪数
	 $sfzddw = "";	//是否重点单位
	 $zddwbm = "";	//重点单位名称
	 $xlbmrs = "";	//下落不明人数
	 $jtsgxtdm = "";//交通事故形态代码
	 $sfwhp = "";		//是否装载危险品
	 $sgccyydm = "";//交通事故初查原因代码
	 $njddm = "";		//能见度代码
	 $lmzkdm = "";	//路面状况代码
	 $shjdcs = "";	//损坏机动车数
	 $shfjdcs = "";	//损坏非机动车数
	 $dllxdm = "";	//道路类型代码

//		$orgCode = '210203440000';
//		$jqid = '1';
//		$mhjqzb = 'point(121.61761 38.913387)';
//		$hphm = "辽B22222";
//		$hphm = iconv("UTF-8","GBK",$hphm);
//		$bjnr = "人民广场发生抢劫";
		//$lastTime='2015-12-12 16:17:21';
	}
	if ($cjdbh==""||$jqid=="") {
		//echo "cjdbh:".$cjdbh;
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
	$entity = new FeedBackEntity($fkdbh, $cjdbh, $jqid, $xzqhdm, $fkdwdm, $fkybh, $fkyxm, $zhrs , $sars, $jzrs, $jzrssm, $jjrs, $jjrssm,
 $tprs, $ssrs, $ssrssm, $swrs , $swrssm, $djyrs, $cjryssrs, $cjryswrs, $jjssqk , $whjjssqk, $sfphxsaj, $sfcczaaj, $sfjjjf, $lzscrs, $jqcljg,
 $jjfvrs, $jjetrs, $hzdjdm, $tqqkdm , $ssqkms, $hzyydm, $zhsglxdm, $qhwdm, $qhjzjgdm, $hzcsdm , $dycdsj, $dydcsj, $hcpmsj, $clsj, $xczzh,
 $cdsqs, $sfzddw, $zddwbm, $xlbmrs, $jtsgxtdm, $sfwhp, $sgccyydm, $njddm, $lmzkdm, $shjdcs, $shfjdcs, $dllxdm,$belong,$scene);
	$event = new EventMix();//创建调度类实例
	$res = $event->updateFeedBack($entity);//调用实例方法
	$arr = array('result' =>'false', 'errmsg' =>'程序异常');
	if($res){
		$arr = array('result' =>'true', 'errmsg' =>'修改成功');
	}else{
		$arr = array('result' =>'false', 'errmsg' =>'修改失败');
	}
	$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => $arr,
				'extend' => ''
			);
	//$res = $event->GetMenByBmdm($orgCode,$hphm);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>