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
	$cjdbh =isset($cjdbh) ? iconv("UTF-8","GBK",$cjdbh) : "";
	$stationhouseCode =isset($stationhouseCode) ? iconv("UTF-8","GBK",$stationhouseCode) : "";
	$stationhouse =isset($stationhouse) ? iconv("UTF-8","GBK",$stationhouse) : "";
		
	}
	/*参数校验*/
	$event = new OutEvent();//创建调度类实例
	/*
	 $post_data['jqlbdm'] =	isset($jqlbdm)	?	$jqlbdm:""	;
		$post_data['jqdjdm'] =	isset($jqdjdm)	?	$jqdjdm:""	;
		$post_data['jqfs_sj'] =	isset($jqfs_sj)	?	$jqfs_sj:""	;
		$post_data['jqjs_sj'] =	isset($jqjs_sj)	?	$jqjs_sj:""	;
		$post_data['ddxc_sj'] =	isset($ddxc_sj)	?	$ddxc_sj:""	;
		$post_data['clwb_sj'] =	isset($clwb_sj)	?	$clwb_sj:""	;
		$post_data['cjqk'] =	isset($cjqk)	?	$cjqk:""	;
		$post_data['cljgdm'] =	isset($cljgdm)	?	$cljgdm:""	;
		$post_data['jqztdm'] =	isset($jqztdm)	?	$jqztdm:""	;
		$post_data['cljg'] =	isset($cljg)	?	$cljg:""	;
		$post_data['cdclqk'] =	isset($cdclqk)	?	$cdclqk:""	;
		$post_data['cdryqk'] =	isset($cdryqk)	?	$cdryqk:""	;
		$post_data['zh_rs'] =	isset($zh_rs)	?	$zh_rs:""	;
		$post_data['sa_rs'] =	isset($sa_rs)	?	$sa_rs:""	;
		$post_data['jz_rs'] =	isset($jz_rs)	?	$jz_rs:""	;
		$post_data['jzrssm'] =	isset($jzrssm)	?	$jzrssm:""	;
		$post_data['jj_rf'] =	isset($jj_rf)	?	$jj_rf:""	;
		$post_data['jj_rs'] =	isset($jj_rs)	?	$jj_rs:""	;
		$post_data['jjrssm'] =	isset($jjrssm)	?	$jjrssm:""	;
		$post_data['sw_rs'] =	isset($sw_rs)	?	$sw_rs:""	;
		$post_data['ss_rs'] =	isset($ss_rs)	?	$ss_rs:""	;
		$post_data['ssrssm'] =	isset($ssrssm)	?	$ssrssm:""	;
		$post_data['swrssm'] =	isset($swrssm)	?	$swrssm:""	;
		$post_data['djy_rs'] =	isset($djy_rs)	?	$djy_rs:""	;
		$post_data['cjryss_rs'] =	isset($cjryss_rs)	?	$cjryss_rs:""	;
		$post_data['cjrysw_rs'] =	isset($cjrysw_rs)	?	$cjrysw_rs:""	;
		$post_data['jjssqk'] =	isset($jjssqk)	?	$jjssqk:""	;
		$post_data['whjjssqk'] =	isset($whjjssqk)	?	$whjjssqk:""	;
		$post_data['sfphxsaj'] =	isset($sfphxsaj)	?	$sfphxsaj:""	;
		$post_data['sfcczaaj'] =	isset($sfcczaaj)	?	$sfcczaaj:""	;
		$post_data['sfjjjf'] =	isset($sfjjjf)	?	$sfjjjf:""	;
		$post_data['lzsc_rs'] =	isset($lzsc_rs)	?	$lzsc_rs:""	;
		$post_data['jqcl_jg'] =	isset($jqcl_jg)	?	$jqcl_jg:""	;
		$post_data['ssqkms'] =	isset($ssqkms)	?	$ssqkms:""	;
		$post_data['tqdm'] =	isset($tqdm)	?	$tqdm:""	;
		$post_data['hzyydm'] =	isset($hzyydm)	?	$hzyydm:""	;
		$post_data['zhsglxdm'] =	isset($zhsglxdm)	?	$zhsglxdm:""	;
		$post_data['hzcsdm'] =	isset($hzcsdm)	?	$hzcsdm:""	;
		$post_data['qhwdm'] =	isset($qhwdm)	?	$qhwdm:""	;
		$post_data['qhjzjgdm'] =	isset($qhjzjgdm)	?	$qhjzjgdm:""	;
		$post_data['hzdjdm'] =	isset($hzdjdm)	?	$hzdjdm:""	;
		$post_data['dycd_sj'] =	isset($dycd_sj)	?	$dycd_sj:""	;
		$post_data['dydc_sj'] =	isset($dydc_sj)	?	$dydc_sj:""	;
		$post_data['hcpm_sj'] =	isset($hcpm_sj)	?	$hcpm_sj:""	;
		$post_data['cl_sj'] =	isset($cl_sj)	?	$cl_sj:""	;
		$post_data['xczzh'] =	isset($xczzh)	?	$xczzh:""	;
		$post_data['cdsqs'] =	isset($cdsqs)	?	$cdsqs:""	;
		$post_data['sfzddw'] =	isset($sfzddw)	?	$sfzddw:""	;
		$post_data['zd_dwmc'] =	isset($zd_dwmc)	?	$zd_dwmc:""	;
		$post_data['xlbm_rs'] =	isset($xlbm_rs)	?	$xlbm_rs:""	;
		$post_data['dljtsgxtdm'] =	isset($dljtsgxtdm)	?	$dljtsgxtdm:""	;
		$post_data['sfwhp'] =	isset($sfwhp)	?	$sfwhp:""	;
		$post_data['sgccyydm'] =	isset($sgccyydm)	?	$sgccyydm:""	;
		$post_data['njddm'] =	isset($njddm)	?	$njddm:""	;
		$post_data['dllmzkdm'] =	isset($dllmzkdm)	?	$dllmzkdm:""	;
		$post_data['shjdcs'] =	isset($shjdcs)	?	$shjdcs:""	;
		$post_data['shfjdcs'] =	isset($shfjdcs)	?	$shfjdcs:""	;
		$post_data['jjbh'] =	isset($jjbh)	?	$jjbh:""	;
		$post_data['xzqhdm'] =	isset($xzqhdm)	?	$xzqhdm:""	;
		$post_data['cjdbh'] =	isset($cjdbh)	?	$cjdbh:""	;
		$post_data['dllxdm'] =	isset($dllxdm)	?   $dllxdm:"";	
		$post_data['userId'] =	isset($userid)	?   $userid:"";	
	 */
	$res=$event->outEventFeedBack("010100","02","2015-03-10 20:30:10","2015-03-10 20:30:10","2015-03-10 20:30:10","2015-03-10 20:30:10","123","","","","","","","","","","","","","","","","","","","","","","","","","","123","","","","","","","","","","","","","","","","","","","","","","","","","20150303210281100015","","20150303210281100015301","","201219195002116413");
	//echo json_encode($res, JSON_UNESCAPED_UNICODE);
	echo $res
		

?>