<?php
/**
 * class FeedBackEntity
 * version: 1.0
 * 反馈实体类
 * author: carl
 * 2014/6/17
 * 
 * 此类定义交通信息中心全部方法
 * 使用前必须先引用TpmsDB.class.php和GlobalConfig.class.php
 */
class FeedBackEntity {
	public $fkdbh  = "";
	public $cjdbh = "";	//处警单编号
	public $jqid =	 "";		//警情ID
	public $xzqhdm = "";//行政区划代码
	public $fkdwdm = "";//反馈单位代码
	public $fkybh = "";	//反馈员编号
	public $fkyxm = "";//反馈员姓名
	public $zhrs =	 "";  //抓获人数
	public $sars =	 "";  //死亡人数
	public $jzrs =	 "";  //救助人数
	public $jzrssm = "";//救助人数说明
	public $jjrs =	 "";	 //解救人数
	public $jjrssm = "";//解救人数说明
	public $tprs =	 "";	 //逃跑人数
	public $ssrs =	 "";	 //受伤人数
	public $ssrssm = "";//受伤人数说明
	public $swrs =	 "";	 //死亡人数
	public $swrssm = "";//死亡人数说明
	public $djyrs = "";	 //待救援人数
	public $cjryssrs = "";//出警人员受伤人数
	public $cjryswrs = "";//出警人员死亡人数
	public $jjssqk = "";	   //经济损失
	public $whjjssqk =	"";//挽回经济损失
	public $sfphxsaj =	"";//是否破获刑事案件
	public $sfcczaaj =	"";//是否查处治安案件
	public $sfjjjf = "";	//是否解决纠纷
	public $lzscrs = "";	//留置审查人数
	public $jqcljg = "";	//警情处理结果
	public $jjfvrs = "";	//解救被拐卖妇女人数
	public $jjetrs = "";	//解救被拐卖儿童人数
	public $hzdjdm = "";	//火灾等级代码
	public $tqqkdm = "";	//天气情况代码
	public $ssqkms = "";	//损失情况描述
	public $hzyydm = "";	//火灾原因代码
	public $zhsglxdm = "";//灾害事故类型代码
	public $qhwdm = "";		//起火物代码
	public $qhjzjgdm =	"";//起火建筑结构代码
	public $hzcsdm = "";	//火灾场所代码
	public $dycdsj = "";	//第一出动时间
	public $dydcsj = "";	//第一到场时间
	public $hcpmsj = "";	//火场扑灭时间
	public $clsj = "";			//撤离时间
	public $xczzh = "";		//现场总指挥
	public $cdsqs = "";		//出动水枪数
	public $sfzddw = "";	//是否重点单位
	public $zddwbm = "";	//重点单位名称
	public $xlbmrs = "";	//下落不明人数
	public $jtsgxtdm = "";//交通事故形态代码
	public $sfwhp = "";		//是否装载危险品
	public $sgccyydm = "";//交通事故初查原因代码
	public $njddm = "";		//能见度代码
	public $lmzkdm = "";	//路面状况代码
	public $shjdcs = "";	//损坏机动车数
	public $shfjdcs = "";	//损坏非机动车数
	public $dllxdm = "";	//道路类型代码
	
	

	function __construct($fkdbh, $cjdbh, $jqid, $xzqhdm, $fkdwdm, $fkybh, $fkyxm, $zhrs , $sars, $jzrs, $jzrssm, $jjrs, $jjrssm,
 $tprs, $ssrs, $ssrssm, $swrs , $swrssm, $djyrs, $cjryssrs, $cjryswrs, $jjssqk , $whjjssqk, $sfphxsaj, $sfcczaaj, $sfjjjf, $lzscrs, $jqcljg,
 $jjfvrs, $jjetrs, $hzdjdm, $tqqkdm , $ssqkms, $hzyydm, $zhsglxdm, $qhwdm, $qhjzjgdm, $hzcsdm , $dycdsj, $dydcsj, $hcpmsj, $clsj, $xczzh,
 $cdsqs, $sfzddw, $zddwbm, $xlbmrs, $jtsgxtdm, $sfwhp, $sgccyydm, $njddm, $lmzkdm, $shjdcs, $shfjdcs, $dllxdm) {
		$this->fkdbh = $fkdbh;
		$this->cjdbh = $cjdbh;
		$this->jqid = $jqid;
		$this->xzqhdm = $xzqhdm;
		$this->fkdwdm = $fkdwdm;
		$this->fkybh = $fkybh;
		$this->fkyxm = $fkyxm;
		$this->zhrs = $zhrs;
		$this->sars = $sars;
		$this->jzrs = $jzrs;
		$this->jzrssm = $jzrssm;
		$this->jjrs = $jjrs;
		$this->jjrssm = $jjrssm;
		$this->tprs = $tprs;
		$this->ssrs = $ssrs;
		$this->ssrssm = $ssrssm;
		$this->swrs = $swrs;
		$this->swrssm = $swrssm;
		$this->djyrs = $djyrs;
		$this->cjryssrs = $cjryssrs;
		$this->cjryswrs = $cjryswrs;
		$this->jjssqk = $jjssqk;
		$this->whjjssqk = $whjjssqk;
		$this->sfphxsaj = $sfphxsaj;
		$this->sfcczaaj = $sfcczaaj;
		$this->sfjjjf = $sfjjjf;
		$this->lzscrs = $lzscrs;
		$this->jqcljg = $jqcljg;
		$this->jjfvrs = $jjfvrs;
		$this->jjetrs = $jjetrs;
		$this->hzdjdm = $hzdjdm;
		$this->tqqkdm = $tqqkdm;
		$this->ssqkms = $ssqkms;
		$this->hzyydm = $hzyydm;
		$this->zhsglxdm = $zhsglxdm;
		$this->qhwdm = $qhwdm;
		$this->qhjzjgdm = $qhjzjgdm;
		$this->hzcsdm = $hzcsdm;
		$this->dycdsj = $dycdsj;
		$this->dydcsj = $dydcsj;
		$this->hcpmsj = $hcpmsj;
		$this->clsj = $clsj;
		$this->xczzh = $xczzh;
	 	$this->cdsqs = $cdsqs;
		$this->sfzddw = $sfzddw;
		$this->zddwbm = $zddwbm;
		$this->xlbmrs = $xlbmrs;
		$this->jtsgxtdm = $jtsgxtdm;
		$this->sfwhp = $sfwhp;
		$this->sgccyydm = $sgccyydm;
		$this->njddm = $njddm;
		$this->lmzkdm = $lmzkdm;
		$this->shjdcs = $shjdcs;
		$this->shfjdcs = $shfjdcs;
		$this->dllxdm = $dllxdm;
	}
	
 	
}
?>
