<?php


/**
 * class EventMix
 * version: 1.0
 * 指挥调度类
 * author: carl
 * 2014/6/17
 * 
 * 此类定义交通信息中心全部方法
 * 使用前必须先引用TpmsDB.class.php和GlobalConfig.class.php
 */
class EventMix extends TpmsDB {

	
	
	
	/**
	 * getNameByUserid
	 * 根据用户ID查询用户对象
	 * @param $id
	 * @return 警员对象
	 */
	public function getNameByUserid($id) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select * from zdb_user where userid='$id'";
		$stmt = oci_parse($this->dbconn, $sql);
		$res = "";
		if (!@ oci_execute($stmt)) {
			oci_close($this->dbconn);
			exit;
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$res = iconv("GBK", "UTF-8", $row["USERNAME"]);
			}
		}
		oci_free_statement($stmt);

		return $res;
	}
	
	/**
	 * getMoreEventById
	 * 根据jqid查询警情详细信息（web端调用）
	 * @param $jqid
	 * @return 警情详细列表
	 */
	public function getMoreEventById($jqid) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.jqid,t.jqbh,t.jjlx,t.jjfs,t.bjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdz, t.jqdd, t.bjnr, t.jqlbdm,t.jqlxdm,t.jqxldm,t.jqdjdm,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as jqjqzb,to_char(t.rksj,'yyyy-MM-dd hh24:mi:ss') as rksj,to_char(t.gxsj,'yyyy-MM-dd hh24:mi:ss') as gxsj,t.cfjqbs,t.jqclzt,t1.bjrxm,t1.bjrsfzh,t1.bjrxbdm,t1.bjdh,t1.lxdh,t1.bjdhyhxm,t1.bjdhyhdz,t2.jjrbh,t2.jjrxm,to_char(t2.jjsj,'yyyy-MM-dd hh24:mi:ss') as jjsj,to_char(t2.hzsj,'yyyy-MM-dd hh24:mi:ss') as hzsj,t3.gxdwdm,t3.hphm,t3.xzqh,t4.ywbzxl,t4.bjchpzldm,t4.bjcph,t4.bkrs,fb_case.ssrs,fb_case.swrs,t4.sfsw,t4.sfswybj,t4.jqztdm,t4.zagjdm,t4.hzdjdm,t4.qhjzjgdm,t4.hzcsdm,t4.qhjzqkms,t4.plqk,t4.qhwdm,t4.ywty,t4.sfswhcl,MDSYS.Sdo_Util.to_wktgeometry_varchar(t5.mhjqzb) as mhjqzb,t6.cjdbh,t7.zlbh,t7.rybh,t7.xm,to_char(t7.zljssj, 'yyyy-MM-dd hh24:mi:ss') as zljssj,to_char(t7.ddxcsj, 'yyyy-MM-dd hh24:mi:ss') as ddxcsj,fb_case.jqcljg,org.orgname,fb_case.zhrs,fb_case.jjssqk,fb_case.jzrs,fb_case.jjfvrs,fb_case.jjetrs,to_char(t6.jqjssj, 'yyyy-MM-dd hh24:mi:ss') as jqjssj,to_char(t7.clwbsj, 'yyyy-MM-dd hh24:mi:ss') as clwbsj,t7.cjqk,t7.cljgdm,t7.cdclqk,t7.cdryqk,t7.jqztdm from zdt_policeevent t
		left join ZDT_PoliceEvent_Reporter t1 on t.jqid=t1.jqid left join ZDT_PoliceEvent_Receiver t2 on t.jqid=t2.jqid left join ZDT_PoliceEvent_Admin t3 on t.jqid=t3.jqid left join ZDB_Organization org on t3.gxdwdm=org.orgcode left join ZDT_PoliceEvent_AddInfo t4 on t.jqid=t4.jqid left join ZDT_PoliceEvent_Location t5 on t.jqid=t5.jqid left join ZDT_PeProcess t6 on t.jqid=t6.jqid left join ZDT_PeProcess_Command t7 on t6.cjdbh =t7.cjdbh left join ZDT_PeFeedback_Case fb_case on t6.cjdbh = fb_case.cjdbh where t.jqid='$jqid' order by t7.zlxdsj desc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			$mens = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$cjdbh = iconv("GBK", "UTF-8", $row["CJDBH"]);
				$array = $this->getDispatchMenByCjdbh($cjdbh,"");
				//$gp_userids = iconv("GBK", "UTF-8", $row["USERID"]);
				//$array = explode(",", $gp_userids);
				$names = "";
				for ($i=0;$i<count($array);$i++) {
					$name = $array[$i]['xm'];
					$p = $i==0 ? "" : ",";
					$names .= $p.$name;
				}
				$men = array (
					'jqid' => iconv("GBK", "UTF-8", $row["JQID"]),
					'jqbh' => iconv("GBK", "UTF-8", $row["JQBH"]),
					'jjlx' => iconv("GBK", "UTF-8", $row["JJLX"]),
					'jjfs' => iconv("GBK", "UTF-8", $row["JJFS"]),
					'bjfs' => iconv("GBK", "UTF-8", $row["BJFS"]),
					'bjsj' => iconv("GBK", "UTF-8", $row["BJSJ"]),
					'jqdz' => iconv("GBK", "UTF-8", $row["JQDZ"]),
					'jqdd' => iconv("GBK", "UTF-8", $row["JQDD"]),
					'bjnr' => iconv("GBK", "UTF-8", $row["BJNR"]),
					'jqlbdm' => iconv("GBK", "UTF-8", $row["JQLBDM"]),
					'jqlxdm' => iconv("GBK", "UTF-8", $row["JQLXDM"]),	
					'jqxldm' => iconv("GBK", "UTF-8", $row["JQXLDM"]),
					'jqdjdm' => iconv("GBK", "UTF-8", $row["JQDJDM"]),	
					'jqjqzb' => iconv("GBK", "UTF-8", $row["JQJQZB"]),
					'rksj' => iconv("GBK", "UTF-8", $row["RKSJ"]),
					'gxsj' => iconv("GBK", "UTF-8", $row["GXSJ"]),
					'cfjqbs' => iconv("GBK", "UTF-8", $row["CFJQBS"]),
					'jqclzt' => iconv("GBK", "UTF-8", $row["JQCLZT"]),
					'bjrxm' => iconv("GBK", "UTF-8", $row["BJRXM"]),
					'bjrsfzh' => iconv("GBK", "UTF-8", $row["BJRSFZH"]),
					'bjrxbdm' => iconv("GBK", "UTF-8", $row["BJRXBDM"]),
					'bjdh' => iconv("GBK", "UTF-8", $row["BJDH"]),
					'lxdh' => iconv("GBK", "UTF-8", $row["LXDH"]),
					'bjdhyhxm' => iconv("GBK", "UTF-8", $row["BJDHYHXM"]),
					'bjdhyhdz' => iconv("GBK", "UTF-8", $row["BJDHYHDZ"]),
					'jjrbh' => iconv("GBK", "UTF-8", $row["JJRBH"]),
					'jjrxm' => iconv("GBK", "UTF-8", $row["JJRXM"]),
					'jjsj' => iconv("GBK", "UTF-8", $row["JJSJ"]),
					'hzsj' => iconv("GBK", "UTF-8", $row["HZSJ"]),
					'gxdwdm' => iconv("GBK", "UTF-8", $row["GXDWDM"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'xzqh' => iconv("GBK", "UTF-8", $row["XZQH"]),
					'ywbzxl' => iconv("GBK", "UTF-8", $row["YWBZXL"]),
					'bjchpzldm' => iconv("GBK", "UTF-8", $row["BJCHPZLDM"]),
					'bjcph' => iconv("GBK", "UTF-8", $row["BJCPH"]),
					'bkrs' => iconv("GBK", "UTF-8", $row["BKRS"]),
					'ssrs' => iconv("GBK", "UTF-8", $row["SSRS"]),
					'swrs' => iconv("GBK", "UTF-8", $row["SWRS"]),
					'sfsw' => iconv("GBK", "UTF-8", $row["SFSW"]),
					'sfswybj' => iconv("GBK", "UTF-8", $row["SFSWYBJ"]),
					'jqztdm' => iconv("GBK", "UTF-8", $row["JQZTDM"]),
					'zagjdm' => iconv("GBK", "UTF-8", $row["ZAGJDM"]),
					'hzdjdm' => iconv("GBK", "UTF-8", $row["HZDJDM"]),
					'qhjzjgdm' => iconv("GBK", "UTF-8", $row["QHJZJGDM"]),
					'hzcsdm' => iconv("GBK", "UTF-8", $row["HZCSDM"]),
					'qhjzqkms' => iconv("GBK", "UTF-8", $row["QHJZQKMS"]),
					'plqk' => iconv("GBK", "UTF-8", $row["PLQK"]),
					'qhwdm' => iconv("GBK", "UTF-8", $row["QHWDM"]),
					'ywty' => iconv("GBK", "UTF-8", $row["YWTY"]),
					'sfswhcl' => iconv("GBK", "UTF-8", $row["SFSWHCL"]),
					'mhjqzb' => iconv("GBK", "UTF-8", $row["MHJQZB"]),
					'cjdbh' => iconv("GBK", "UTF-8", $row["CJDBH"]),
					'zlbh' => iconv("GBK", "UTF-8", $row["ZLBH"]),
					'rybh' => iconv("GBK", "UTF-8", $row["RYBH"]),
					'xm' => iconv("GBK", "UTF-8", $row["XM"]),
					'zljssj' => iconv("GBK", "UTF-8", $row["ZLJSSJ"]),
					'orgname' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'cjr' => $names,
					'cdjl' => count($array)==0?"":count($array),
					'ddxcsj' => iconv("GBK", "UTF-8", $row["DDXCSJ"]),
					'cljg' => iconv("GBK", "UTF-8", $row["JQCLJG"]),
					'zhrs' => iconv("GBK", "UTF-8", $row["ZHRS"]),
					'jjssqk' => iconv("GBK", "UTF-8", $row["JJSSQK"]),
					'jzrs' => iconv("GBK", "UTF-8", $row["JZRS"]),
					'jjfvrs' => iconv("GBK", "UTF-8", $row["JJFVRS"]),
					'jjetrs' => iconv("GBK", "UTF-8", $row["JJETRS"]),
					'jqjssj' => iconv("GBK", "UTF-8", $row["JQJSSJ"]),
					'clwbsj' => iconv("GBK", "UTF-8", $row["CLWBSJ"]),
					'cjqk' => iconv("GBK", "UTF-8", $row["CJQK"]),
					'cljgdm' => iconv("GBK", "UTF-8", $row["CLJGDM"]),
					'cdclqk' => iconv("GBK", "UTF-8", $row["CDCLQK"]),
					'cdryqk' => iconv("GBK", "UTF-8", $row["CDRYQK"]),
					'jqztdm' => iconv("GBK", "UTF-8", $row["JQZTDM"])
				);
				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		else
			$arr = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $mens
			);

		return $arr;
	}
	
	public function updateCommByreLoad($jqid,$cljg){
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
		$this->dbconn = $this->LogonDB();
			$cljg = iconv("UTF-8", "GBK", $cljg);
			$sql = "update zdt_peprocess_command t set t.CLWBSJ=sysdate,t.JQZTDM='5',t.CLJG='$cljg' where t.zlbh in (select zlbh from zdt_policeevent e,zdt_peprocess p,zdt_peprocess_command c where e.jqid=p.jqid and p.cjdbh=c.cjdbh and p.cjdzt='1' and e.jqid='$jqid')";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $bRet;
	}
	
	/**
	 * updateEventCar
	 * 警情派警入口
	 * @param $userId, $jqid, $mhjqzb, $hphm, $orgCode, $bjnr,$jqdz
	 * @return true or false
	 */
	public function updateEventCar($userId, $jqid, $mhjqzb, $hphm, $orgCode, $bjnr,$jqdz,$jqdjdm,$jqdd,$jqzk,$zrqCode,$zrqName,$xlqyCode,$xlqyName) {
		$bRet = true;
		$errMsg = '';
		$gid = "";
		$userIds = "";
		$leaderId="";
		$res = array (
			'result' => 'false',
			'errmsg' => '派发消息失败'
		);
		$result = $this->updateCommByreLoad($jqid,"重新派警");
		if(!$result){
			$arr = array('result' =>'false', 'errmsg' =>'数据库操作失败');
			return $arr;
		}
		
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			
		$sql = "select t.gid,t.userid,t.carid,t.leaderId from  zdt_duty_group t,zdb_equip_car t1 where t.carid=t1.id and t1.hphm='$hphm'   and t.status !='3' and rownum=1  order by  t.createtime desc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		$r = @ oci_execute($stmt);
		if ($r) {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$gid = $row["GID"];
				$userIds = $row["USERID"];
				$carid = $row["CARID"];
				$leaderId = $row["LEADERID"];
			}
		} else {
			$res = array (
				'result' => 'false',
				'errmsg' => '获取单个巡逻组信息失败'
			);
		}
		oci_free_statement($stmt);
		
		if ($gid != "") {
			$res = $this->sendEventToTerminal($userId, $jqdd, $jqid, $bjnr, $leaderId, $gid,"1","");
		}
		
		//if(1==1){
		if($res["result"]=="true"){
			if ($gid != "") {
				$sql = "update zdt_duty_group t set t.commander='$userId',t.status='2' where t.gid='$gid'";
				$stmt = oci_parse($this->dbconn, $sql);
				if (!@ oci_execute($stmt)) {
					$bRet = false;
					$errMsg = '修改失败';
				}
				oci_free_statement($stmt);
			}
		
			$pjfs = $res["msg"]=="08"?"2":"1";
			//$sql = "update  ZDT_PoliceEvent set jqclzt='2',JQDZ='$jqdz',gxsj=sysdate,jqdjdm='$jqdjdm',jqzk='$jqzk',ZRQBM='$zrqCode',ZRQMC='$zrqName',PJFS='$pjfs',XJBMMC='$xlqyName',XJBMBM='$xlqyCode'  where jqid='$jqid'";
			$sql = "update  ZDT_PoliceEvent set jqclzt='2',JQDZ='$jqdz',gxsj=sysdate,jqdjdm='$jqdjdm',jqzk='$jqzk',ZRQBM='$zrqCode',ZRQMC='$zrqName',PJFS='$pjfs',XJBMMC='$xlqyName',XJBMBM='$xlqyCode',FK_ZRQBM='$zrqCode',FK_ZRQMC='$zrqName',FK_XJBMMC='$xlqyName',FK_XJBMBM='$xlqyCode'  where jqid='$jqid'";
                        $stmt = oci_parse($this->dbconn, $sql);
			if (!@ oci_execute($stmt)) {
				$bRet = false;
				$errMsg = '修改失败';
			}
			oci_free_statement($stmt);
			$locationCount = $this->getPoliceEventLocationByJqid($jqid);
			if ($locationCount) {
				$bRet = $this->updatePoliceEventLocationByJqid($jqid, $mhjqzb);
			} else {
				$bRet = $this->insertPoliceEventLocationByJqid($jqid, $mhjqzb);
			}
			$adminCount = $this->getPoliceEventAdmin($jqid);
			if ($adminCount) {
				$bRet = $this->updatePoliceEventAdmin($jqid, $hphm, $orgCode);
			} else {
				$bRet = $this->insertPoliceEventAdmin($jqid, $hphm, $orgCode);
			}
			//$processCount = $this->getZDTPeProcess($jqid);
			//if($processCount){
			//	$datas = $this->updateZDTPeProcess($jqid);
			//}else{
				$this->updateZDTPeProcess_CJDZT($jqid);
				$datas = $this->insertZDTPeProcess($jqid);
			//}
			if ($datas['cjdbh']!="") {
				//$processCommandCount = $this->getZDTPeProcessCommand($datas['cjdbh']);
				
				//if($processCommandCount){
				//	$data_command = $this->updateZDTPeProcessCommand($datas['cjdbh'],$orgCode,$leaderId);
				//}else{
				$data_command = $this->insertZDTPeProcessCommand($datas['cjdbh'],$orgCode,$leaderId,"1","");
				//}
				if($data_command['zlbh']!=""){
					//echo $userIds;
					$array = explode(",", $userIds);
					for ($i=0;$i<count($array);$i++) {
						$useData = $this->getUserInfoById($array[$i]);
							$dispatchCount = $this->getDispatchMen($datas['cjdbh'],$data_command['zlbh'],$array[$i]);
						if($dispatchCount){
							$bRet = $this->updateDispatchMen($datas['cjdbh'],$data_command['zlbh'],$useData['orgCode'],$array[$i]);
						}else{
							$bRet = $this->insertDispatchMen($datas['cjdbh'],$data_command['zlbh'],$useData['orgCode'],$array[$i]);
						}
					}
					/*
					$dispatchCount = $this->getDispatchMen($datas['cjdbh'],$data_command['zlbh']);
					if($dispatchCount){
						$bRet = $this->updateDispatchMen($datas['cjdbh'],$data_command['zlbh'],$orgCode,$leaderId);
					}else{
						$bRet = $this->insertDispatchMen($datas['cjdbh'],$data_command['zlbh'],$orgCode,$leaderId);
					}
					*/
				}
			}
		}
		
		oci_close($this->dbconn);
		$event = $this->getEventReceiveDisposalByid($jqid);
		/*
		if($event['jqid']){
			$url = 'http://192.168.20.215:9999/lbs';
			$params = "operation=FeedbackInfo_AddPoliceEvent_v001&license=a756244eb0236bdc26061cb6b6bdb481&content=";
			$this->outEventReceiveDisposal($url,$params,$event['jqid'],$event['jqbh'],$event['jjlx'],$event['jjfs'],$event['bjfs'],$event['bjsj'],$event['jqdd'],$event['bjnr'],$event['jqlbdm'],$event['jqlxdm'],$event['jqxldm'],$event['jqdjdm'],$event['pjsj'],$event['zljssj'],$event['stationhousecode'],$event['stationhouse'],$event['cjdwdm'],$event['jqclzt'],$event['jqzt'],$event['sfyj']);
		}
		*/
		
		return $res;
		//return $res;
	}
	
	/**
	 * addCommandEvent
	 * 补充指令入口
	 * @param $userId, $jqid, $jqjqzb, $hphm, $orgCode, $bjnr,$jqdz
	 * @return true or false
	 */
	public function addCommandEvent($userId, $jqid, $jqdd, $hphm, $orgCode, $bjnr,$zlnr) {
		$bRet = true;
		$errMsg = '';
		$gid = "";
		$userIds = "";
		$leaderId="";
		$res = array (
			'result' => 'false',
			'errmsg' => '派发消息失败'
		);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$cjdbh = $this->getCjdbhByJqid($jqid);
		if($cjdbh==""){
			return $res;
		}	
		$sql = "select t.gid,t.userid,t.carid,t.leaderId from  zdt_duty_group t,zdb_equip_car t1 where t.carid=t1.id and t1.hphm='$hphm'   and t.status !='3' and rownum=1  order by  t.createtime desc";
		$stmt = oci_parse($this->dbconn, $sql);
		$r = @ oci_execute($stmt);
		if ($r) {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$gid = $row["GID"];
				$userIds = $row["USERID"];
				$carid = $row["CARID"];
				$leaderId = $row["LEADERID"];
			}
		} else {
			$res = array (
				'result' => 'false',
				'errmsg' => '获取单个巡逻组信息失败'
			);
		}
		oci_free_statement($stmt);
		//echo "leaderid".$leaderId;
		if ($gid != "") {
			$res = $this->sendEventToTerminal($userId, $jqdd, $jqid, $bjnr, $leaderId, $gid,"2",$zlnr);
		}
		//if(1==1){
		if($res["result"]=="true"){
			if ($gid != "") {
				$sql = "update zdt_duty_group t set t.commander='$userId',t.status='2' where t.gid='$gid'";
				$stmt = oci_parse($this->dbconn, $sql);
				if (!@ oci_execute($stmt)) {
					$bRet = false;
					$errMsg = '修改失败';
				}
				oci_free_statement($stmt);
			}
			
			$data_command = $this->insertZDTPeProcessCommand($cjdbh,$orgCode,$leaderId,"2",$zlnr);
			if($data_command['zlbh']!=""){
				//echo $userIds;
				$array = explode(",", $userIds);
				for ($i=0;$i<count($array);$i++) {
					$useData = $this->getUserInfoById($array[$i]);
						$dispatchCount = $this->getDispatchMen($cjdbh,$data_command['zlbh'],$array[$i]);
					if($dispatchCount){
						$bRet = $this->updateDispatchMen($cjdbh,$data_command['zlbh'],$useData['orgCode'],$array[$i]);
					}else{
						$bRet = $this->insertDispatchMen($cjdbh,$data_command['zlbh'],$useData['orgCode'],$array[$i]);
					}
				}
			}
		}
		
		oci_close($this->dbconn);
		return $res;
		//return $res;
	}

	/**
	 * getPoliceEventLocationByJqid
	 * 根据jqid查询警情位置信息表count
	 * @param $jqid
	 * @return true or false
	 */
	public function getPoliceEventLocationByJqid($jqid) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from ZDT_PoliceEvent_Location where jqid='$jqid'";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		if ($count > 0) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * insertPoliceEventLocationByJqid
	 * 警情派警时新增警情位置信息表中对应的模糊警情坐标
	 * @param $jqid, $mhjqzb
	 * @return true or false
	 */
	public function insertPoliceEventLocationByJqid($jqid, $mhjqzb) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "insert into ZDT_PoliceEvent_Location values('$jqid',sdo_geometry('$mhjqzb',4326),'')";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '新增失败';
		}
		oci_free_statement($stmt);

		return $bRet;

	}

	/**
	 * updatePoliceEventLocationByJqid
	 * 警情派警时修改警情位置信息表中对应的模糊警情坐标
	 * @param $jqid, $mhjqzb
	 * @return true or false
	 */
	public function updatePoliceEventLocationByJqid($jqid, $mhjqzb) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update  ZDT_PoliceEvent_Location set mhjqzb=sdo_geometry('$mhjqzb',4326)  where jqid='$jqid'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);

		return $bRet;

	}

	/**
	 * getPoliceEventAdmin
	 * 根据jqid查询警情管辖表count
	 * @param $jqid
	 * @return true or false
	 */
	public function getPoliceEventAdmin($jqid) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from ZDT_PoliceEvent_Admin where jqid='$jqid'";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		if ($count > 0) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * insertPoliceEventAdmin
	 * 警情派警时新增警情管辖表中对应的号牌号码，所属部门以及行政区划
	 * @param $jqid, $hphm, $orgCode
	 * @return true or false
	 */
	public function insertPoliceEventAdmin($jqid, $hphm, $orgCode) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$xzqh = substr($orgCode, 0, 6);
		//$hphm = iconv("UTF-8","GBK",$hphm);
		$sql = "insert into ZDT_PoliceEvent_Admin(jqid,gxdwdm,hphm,xzqh) values('$jqid','$orgCode','$hphm','$xzqh')";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '新增失败';
		}
		oci_free_statement($stmt);

		return $bRet;

	}

	/**
	 * updatePoliceEventAdmin
	 * 警情派警时修改警情管辖表中对应的号牌号码，所属部门以及行政区划
	 * @param $jqid, $hphm, $orgCode
	 * @return true or false
	 */
	public function updatePoliceEventAdmin($jqid, $hphm, $orgCode) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$xzqh = substr($orgCode, 0, 6);
		//$hphm = iconv("UTF-8","GBK",$hphm);
		$sql = "update  ZDT_PoliceEvent_Admin set GXDWDM='$orgCode',hphm='$hphm',xzqh='$xzqh'  where jqid='$jqid'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);

		return $bRet;

	}
	
	
	/**
	 * getZDTPeProcess
	 * 根据jqid查询警情处警表count
	 * @param $jqid
	 * @return true or false
	 */
	public function getZDTPeProcess($jqid) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from ZDT_PeProcess where jqid='$jqid'";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		if ($count > 0) {
			return true;
		} else {
			return false;
		}

	}
	
	/**
	 * insertZDTPeProcess
	 * 警情派警时新增指令表中对应的数据项
	 * @param $jqid
	 * @return 处理结果字符串
	 */
	public function insertZDTPeProcess($jqid) {
		$bRet = true;
		$errMsg = '';
		$event = '';
		$event = $this->getEventSimpleByid($jqid);
		$seq = $this->getSequenceByTable("ZDT_PeProcess");
		
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$xzqh = $event['xzqh'];
		$jqdbh = $event['jqbh'];
		$jqlbdm = $event['jqlbdm'];
		$jqlxdm = $event['jqlxdm'];
		$jqxldm= $event['jqxldm'];
		$jqfssj= $event['bjsj'];
		$jqdjdm= $event['jqdjdm'];
		$jqlbdm =iconv("UTF-8","GBK",$jqlbdm);
		$jqdjdm = iconv("UTF-8","GBK",$jqdjdm);
		$jqlxdm = iconv("UTF-8","GBK",$jqlxdm);
		$jqxldm = iconv("UTF-8","GBK",$jqxldm);
		$sql = "insert into ZDT_PeProcess(JQID,XZQH,CJDBH,JQDBH,JQLBDM,JQLXDM,JQXLDM,JQFSSJ,JQDJDM,PJSJ) values('$jqid','$xzqh','$seq','$jqdbh','$jqlbdm','$jqlxdm','$jqxldm',to_date('$jqfssj','yyyy-MM-dd hh24:mi:ss'),'$jqdjdm',sysdate)";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!oci_execute($stmt)) {
			echo $sql;
			$bRet = false;
			$errMsg = '新增失败';
		}
		oci_free_statement($stmt);
		if($bRet){
			$datas = array (
				'result' => true,
				'cjdbh' => $seq
			);
		}else{
			$datas = array (
				'result' => false,
				'cjdbh' => ""
			);
		}
		return $datas;

	}
	
	/**
	 * updateZDTPeProcess
	 * 警情派警时修改处警信息表中对应的数据项
	 * @param $jqid
	 * @return true or false
	 */
	public function updateZDTPeProcess($jqid) {
		$bRet = true;
		$errMsg = '';
		$event = '';
		$event = $this->getEventSimpleByid($jqid);
		
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$xzqh = $event['xzqh'];
		$jqdbh = $event['jqbh'];
		$jqlbdm = $event['jqlbdm'];
		$jqlxdm = $event['jqlxdm'];
		$jqxldm= $event['jqxldm'];
		$jqfssj= $event['rksj'];
		$jqdjdm= $event['jqdjdm'];
		$sql = "update  ZDT_PeProcess set XZQH='$xzqh',JQDBH='$jqdbh',JQLBDM='$jqlbdm',JQLXDM='$jqlxdm',JQXLDM='$jqxldm',JQFSSJ=to_date('$jqfssj','yyyy-MM-dd hh24:mi:ss'),JQDJDM='$jqdjdm') where jqid='$jqid'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '新增失败';
		}
		oci_free_statement($stmt);
		return $bRet;

	}
	
	public function updateZDTPeProcess_CJDZT($jqid) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update  ZDT_PeProcess set CJDZT='2' where jqid='$jqid'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '新增失败';
		}
		oci_free_statement($stmt);
		return $bRet;

	}
	
	/**
	 * getZDTPeProcessCommand
	 * 根据cjdbh查询警情指令表count
	 * @param $cjdbh
	 * @return true or false
	 */
	public function getZDTPeProcessCommand($cjdbh) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from ZDT_PeProcess_Command where CJDBH='$cjdbh'";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		if ($count > 0) {
			return true;
		} else {
			return false;
		}

	}
	
	/**
	 * insertZDTPeProcessCommand
	 * 警情派警时新增指令表中对应的派警单位,处警人以及处警姓名
	 * @param $cjdbh,$orgCode,$leaderId
	 * @return 处理结果字符串
	 */
	public function insertZDTPeProcessCommand($cjdbh,$orgCode,$leaderId,$zlbs,$zlnr) {
		//echo 'zlbs:'.$zlbs;
		$bRet = true;
		$errMsg = '';
		$seq = $this->getSequenceByTable("ZDT_PeProcess_Command");
		$userName = $this->getNameByUserid($leaderId);
		$userName = iconv("UTF-8","GBK",$userName);
		$zlnr = iconv("UTF-8","GBK",$zlnr);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "insert into ZDT_PeProcess_Command(ZLBH,CJDBH,CJDWDM,RYBH,XM,ZLXDSJ,JQZTDM,ZLBS,ZLNR) values('$seq','$cjdbh','$orgCode','$leaderId','$userName',sysdate,'2','$zlbs','$zlnr')";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '新增失败';
		}
		oci_free_statement($stmt);
		if($bRet){
			$datas = array (
				'result' => true,
				'zlbh' => $seq
			);
		}else{
			$datas = array (
				'result' => false,
				'zlbh' => ""
			);
		}
		return $datas;

	}
	
		
	/**
	 * updateZDTPeProcessCommand
	 * 警情派警时修改指令表中对应的派警单位,处警人以及处警姓名
	 * @param $cjdbh,$orgCode,$leaderId
	 * @return true or false
	 */
	public function updateZDTPeProcessCommand($cjdbh,$orgCode,$leaderId) {
		$bRet = true;
		$errMsg = '';
		$userName = $this->getNameByUserid($leaderId);
		$userName = iconv("UTF-8","GBK",$userName);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update ZDT_PeProcess_Command set CJDWDM='$orgCode',RYBH='$leaderId',XM='$userName',ZLXDSJ=sysdate,JQZTDM='2' where CJDBH='$cjdbh'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);
		return $bRet;

	}
	
	/**
	 * getDispatchMen
	 * 根据传递参数查询出动警力库中是否存在
	 * @param $cjdbh,$zlbh,$rybh
	 * @return true or false
	 */
	public function getDispatchMen($cjdbh,$zlbh,$rybh) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from ZDT_PeProcess_DispatchMen where CJDBH='$cjdbh' and ZLBH='$zlbh' and RYBH='$rybh'";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		if ($count > 0) {
			return true;
		} else {
			return false;
		}

	}
	
	/**
	 * insertDispatchMen
	 * 添加出动警力数据到数据库
	 * @param $cjdbh,$zlbh,$orgCode,$leaderId
	 * @return 结果字符串
	 */
	public function insertDispatchMen($cjdbh,$zlbh,$orgCode,$leaderId) {
		$bRet = true;
		$errMsg = '';
		$userName = $this->getNameByUserid($leaderId);
		$userName = iconv("UTF-8","GBK",$userName);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "insert into ZDT_PeProcess_DispatchMen(ZLBH,CJDBH,DWDM,RYBH,XM) values('$zlbh','$cjdbh','$orgCode','$leaderId','$userName')";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '新增失败';
		}
		oci_free_statement($stmt);
		if($bRet){
			$datas = array (
				'result' => true
			);
		}else{
			$datas = array (
				'result' => false
			);
		}
		return $datas;

	}
	
		
	/**
	 * updateDispatchMen
	 * 根据cjdbh修改出动警力数据
	 * @param $cjdbh,$zlbh,$orgCode,$leaderId
	 * @return true or false
	 */
	public function updateDispatchMen($cjdbh,$zlbh,$orgCode,$leaderId) {
		$bRet = true;
		$errMsg = '';
		$userName = $this->getNameByUserid($leaderId);
		$userName = iconv("UTF-8","GBK",$userName);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update ZDT_PeProcess_DispatchMen set DWDM='$orgCode',RYBH='$leaderId',XM='$userName' where CJDBH='$cjdbh' an ZLBH='$zlbh'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);
		return $bRet;

	}
	
	
	/**
	 * getEventSimpleByid
	 * 根据警情ID查询警情信息
	 * @param $jqid
	 * @return 警情对象
	 */
	public function getEventSimpleByid($jqid) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.zrqbm,t.zrqmc,t.xjbmmc,t.xjbmbm,t.pjfs,t.jqzbly,t.jsjqly, t1.xzqh, t.jqid,t.jqbh,t.cjdbh,t.jjlx,t.jjfs,t.bjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdz, t.jqdd, t.bjnr, t.jqlbdm,t.jqlxdm,t.jqxldm,t.jqdjdm,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as jqjqzb,to_char(t.rksj,'yyyy-MM-dd hh24:mi:ss') as rksj,to_char(t.gxsj,'yyyy-MM-dd hh24:mi:ss') as gxsj,t.cfjqbs,t.jqclzt from zdt_policeevent t left join ZDT_PoliceEvent_Admin t1 on t1.jqid=t.jqid 
 where 1=1 and t.jqid='$jqid'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			$mens = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'xzqh' => iconv("GBK", "UTF-8", $row["XZQH"]),
					'jqid' => iconv("GBK", "UTF-8", $row["JQID"]),
					'jqbh' => iconv("GBK", "UTF-8", $row["JQBH"]),
					'jjlx' => iconv("GBK", "UTF-8", $row["JJLX"]),
					'jjfs' => iconv("GBK", "UTF-8", $row["JJFS"]),
					'bjfs' => iconv("GBK", "UTF-8", $row["BJFS"]),
					'bjsj' => iconv("GBK", "UTF-8", $row["BJSJ"]),
					'jqdz' => iconv("GBK", "UTF-8", $row["JQDZ"]),
					'jqdd' => iconv("GBK", "UTF-8", $row["JQDD"]),
					'bjnr' => iconv("GBK", "UTF-8", $row["BJNR"]),
					'jqlbdm' => iconv("GBK", "UTF-8", $row["JQLBDM"]),
					'jqlxdm' => iconv("GBK", "UTF-8", $row["JQLXDM"]),	
					'jqxldm' => iconv("GBK", "UTF-8", $row["JQXLDM"]),
					'jqdjdm' => iconv("GBK", "UTF-8", $row["JQDJDM"]),	
					'jqjqzb' => iconv("GBK", "UTF-8", $row["JQJQZB"]),
					'rksj' => iconv("GBK", "UTF-8", $row["RKSJ"]),
					'gxsj' => iconv("GBK", "UTF-8", $row["GXSJ"]),
					'cfjqbs' => iconv("GBK", "UTF-8", $row["CFJQBS"]),
					'jqclzt' => iconv("GBK", "UTF-8", $row["JQCLZT"]),
					'cjdbh' => iconv("GBK", "UTF-8", $row["CJDBH"]),
					'zrqCode' => iconv("GBK", "UTF-8", $row["ZRQBM"]),
					'zrqName' => iconv("GBK", "UTF-8", $row["ZRQMC"]),
					'xjbmbm' =>	iconv("GBK", "UTF-8", $row["XJBMBM"]),
					'xjbmmc' =>	iconv("GBK", "UTF-8", $row["XJBMMC"]),
					'pjfs' => iconv("GBK", "UTF-8", $row["PJFS"]),
					'jqzbly' => iconv("GBK", "UTF-8", $row["JQZBLY"]),
					'jsjqly' => iconv("GBK", "UTF-8", $row["JSJQLY"])
				);
				//array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		return $men;
	}
	

	/** 
	* sendEventToTerminal 
	* 向终端发送信息 
	*/
	public function sendEventToTerminal($sendPid, $location, $jqid, $sendMsg, $receivePids, $gid,$zlbs,$zlnr) {

		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		$resArr = array ();
		$resArrTrue = array ();
		$strPoliceIdMessage1 = '';

		//判断是不是系统消息 
		if ($sendPid == "指挥中心") {
			$sendPid = "000000";
		}

		//解析群发警员编号	
		$sendMsg = str_replace("\n", "", $sendMsg);

		$str = '{"message":{"comCode":"18","codeId":"' . $date . '"},"result":{"sendPid":"' . $sendPid . '","location":"' . $location . '","jqid":"' . $jqid . '","sendMsg":"' . $sendMsg . '","receivePids":"' . $receivePids . '","gid":"' . $gid . '","zlbs":"' . $zlbs . '","zlnr":"' . $zlnr . '"}}';
		//echo $str;
		//return;
		$length = mb_strlen($str, 'UTF8');
		//$length=100;
		switch (strlen($length)) {
			case 0 :
				$len = '00000000';
				break;
			case 1 :
				$len = '0000000' . $length;
				break;
			case 2 :
				$len = '000000' . $length;
				break;
			case 3 :
				$len = '00000' . $length;
				break;
			case 4 :
				$len = '0000' . $length;
				break;
			case 5 :
				$len = '000' . $length;
				break;
			case 6 :
				$len = '00' . $length;
				break;
			case 7 :
				$len = '0' . $length;
				break;
			default :
				echo '超出上限';
				exit;
		}
		$word = $len . $str;
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('could  not create socket');

		$connect = @ socket_connect($socket, GlobalConfig :: getInstance()->socket_ip, GlobalConfig :: getInstance()->socket_port) or die('could not connect socket	server');

		//向服务端发送数据
		socket_write($socket, $word . "\n");

		//接受服务端返回数据
		$str = @ socket_read($socket, 1024, PHP_NORMAL_READ);
		//判断接收返回数据长度是否一致

		$lastStr = trim($str);

		$lenfan = mb_strlen($lastStr, 'UTF8');

		$str3 = substr($lastStr, 8);
		$str1 = substr($lastStr, 0, 8);

		$s1 = '';

		for ($i = 0; $i < 8; $i++) {
			$s = substr($str1, $i, 1);

			if ($s != '0') {

				$s1 = substr($str1, $i, 8);
				break;
			}

		}

		$obj = json_decode($str3);
		//echo encodeJson($obj);
		
		//$message= $obj->result->message;
		$message = $obj->message->message;
		$value = '';
		$message1 = '';

		//判断返回结果
		if ($message == '00' && mb_strlen($str3, 'UTF8') == $s1) {
			$value = 'true';
			$message1 = '发送成功！';
		} else if ($message == '02') {
			$value = 'faile';
			$message1 = '非法字符串';
		} else if ($message == '03') {
			$value = 'faile';
			$message1 = '命令码异常';
		} else if ($message == '04') {
			$value = 'faile';
			$message1 = '设备校验异常';
		} else if ($message == '05') {
			$value = 'faile';
			$message1 = '设备不在线';
		}else if ($message == '08') {
			$value = 'true';
			$message1 = '已发送离线消息';
		}

		//判断发送失败并记录
		if ($message == '02' || $message == '03' || $message == '04' || $message == '05') {
			$strPoliceIdMessage1 .= $message1;

			$datas = array (
				'result' => $value
			);
			array_push($resArr, $datas);
		}
		socket_close($socket);
		if (count($resArr) > 0) {
			$datas = array (
				'result' => 'false',
				'errmsg' => $strPoliceIdMessage1,
				'msg' => $message
			);
			return $datas;
		} else {
			$datas = array (
				'result' => 'true',
				'errmsg' => $message1,
				'msg' => $message
			);
			return $datas;
		}
	}
	
	/**
	 * getDispatchMenByCjdbh
	 * 根据cjdbh查询出动警力数据列
	 * @param $cjdbh
	 * @return 警力数据组
	 */
	public function getDispatchMenByCjdbh($cjdbh,$zlbh){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.cjdbh,t.zlbh,t.dwdm,t.rybh,t.xm from ZDT_PeProcess_DispatchMen t where t.cjdbh='$cjdbh'";
		if($zlbh!=""){
			$sql = $sql." and t.zlbh='$zlbh' ";
		}
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			$mens = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'cjdbh' => iconv("GBK", "UTF-8", $row["CJDBH"]),
					'zlbh' => iconv("GBK", "UTF-8", $row["ZLBH"]),
					'dwdm' => iconv("GBK", "UTF-8", $row["DWDM"]),
					'rybh' => iconv("GBK", "UTF-8", $row["RYBH"]),
					'xm' => iconv("GBK", "UTF-8", $row["XM"])
				);
				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);

		return $mens;
	}
	
	/**
	 * updateFeedBack
	 * 修改反馈信息所有信息
	 * @param FeedBackEntity $entity
	 * @return true or false
	 */
	public function updateFeedBack(FeedBackEntity $entity){
		$bRet = false;
		$feedBackCount = $this->getFeedbackCount($entity->cjdbh);
		if($feedBackCount){
			$bRet = $this->updateFeedBack_case($entity);
		}else{
			$bRet = $this->insertFeedBack_case($entity);
		}
		$feedBackFireCount = $this->getFeedbackFireCount($entity->cjdbh);
		if($feedBackFireCount){
			$bRet = $this->updateFeedBack_fire($entity);
		}else{
			$bRet = $this->insertFeedBack_fire($entity);
		}
		$feedBackFireCount = $this->getFeedbackTrafficCount($entity->cjdbh);
		if($feedBackFireCount){
			$bRet = $this->updateFeedBack_traffic($entity);
		}else{
			$bRet = $this->insertFeedBack_traffic($entity);
		}
		return $bRet;
		
	}
	
	/**
	 * updateFeedBack_case
	 * 修改反馈案件信息表中反馈信息对象
	 * @param FeedBackEntity $entity
	 * @return true or false
	 */
	public function updateFeedBack_case(FeedBackEntity $entity){
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update ZDT_PeFeedback_Case set ";
		if($entity->cjdbh){
			$sql = $sql."cjdbh='$entity->cjdbh',";
		}
		if($entity->zhrs){
			$sql = $sql."zhrs='$entity->zhrs',";
		}
		if($entity->sars){
			$sql = $sql."sars='$entity->sars',";
		}
		if($entity->jzrs){
			$sql = $sql."jzrs='$entity->jzrs',";
		}
		if($entity->jzrssm){
			$sql = $sql."jzrssm='$entity->jzrssm',";
		}
		if($entity->jjrs){
			$sql = $sql."jjrs='$entity->jjrs',";
		}
		if($entity->jjrssm){
			$sql = $sql."jjrssm='$entity->jjrssm',";
		}
		if($entity->tprs){
			$sql = $sql."tprs='$entity->tprs',";
		}
		if($entity->ssrs){
			$sql = $sql."ssrs='$entity->ssrs',";
		}
		if($entity->ssrssm){
			$sql = $sql."ssrssm='$entity->ssrssm',";
		}
		if($entity->swrs){
			$sql = $sql."swrs='$entity->swrs',";
		}
		if($entity->swrssm){
			$sql = $sql."swrssm='$entity->swrssm',";
		}
		if($entity->djyrs){
			$sql = $sql."djyrs='$entity->djyrs',";
		}
		if($entity->cjryssrs){
			$sql = $sql."cjryssrs='$entity->cjryssrs',";
		}
		if($entity->cjryswrs){
			$sql = $sql."cjryswrs='$entity->cjryswrs',";
		}
		if($entity->jjssqk){
			$sql = $sql."jjssqk='$entity->jjssqk',";
		}
		if($entity->whjjssqk){
			$sql = $sql."whjjssqk='$entity->whjjssqk',";
		}
		if($entity->sfphxsaj){
			$sql = $sql."sfphxsaj='$entity->sfphxsaj',";
		}
		if($entity->sfcczaaj){
			$sql = $sql."sfcczaaj='$entity->sfcczaaj',";
		}
		if($entity->sfjjjf){
			$sql = $sql."sfjjjf='$entity->sfjjjf',";
		}
		if($entity->lzscrs){
			$sql = $sql."lzscrs='$entity->lzscrs',";
		}
		if($entity->jqcljg){
			$sql = $sql."jqcljg='$entity->jqcljg',";
		}
		if($entity->jjfvrs){
			$sql = $sql."jjfvrs='$entity->jjfvrs',";
		}
		if($entity->jjetrs){
			$sql = $sql."jjetrs='$entity->jjetrs',";
		}
		$sql = rtrim($sql, ','); 
		$sql = $sql." where cjdbh='$entity->cjdbh'";
		//echo $sql;
		//$sql = "insert into zdt_pefeedback_case (cjdbh,zhrs,jzrs,jjfvrs,jjetrs,jqcljg,ssrs,swrs,jjssqk) values ('$cjdbh','$zhrs','$jzrs','$jjfvrs','$jjetrs','$jqcljg','$ssrs','$swrs','$jjss')";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $bRet;
		//echo "cjdbh:".$entity->cjdbh;
	}
	
	/**
	 * insertFeedBack_case
	 * 插入反馈对象entity到反馈案件信息表
	 * @param FeedBackEntity $entity
	 * @return true or false
	 */
	public function insertFeedBack_case(FeedBackEntity $entity){
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "insert into ZDT_PeFeedback_Case(cjdbh,zhrs,sars,jzrs,jzrssm,jjrs,jjrssm,tprs,ssrs,ssrssm,swrs,swrssm,djyrs,cjryssrs,cjryswrs,jjssqk,whjjssqk,sfphxsaj,sfcczaaj,sfjjjf,lzscrs,jqcljg,jjfvrs,jjetrs)" .
				"values('$entity->cjdbh','$entity->zhrs','$entity->sars','$entity->jzrs','$entity->jzrssm','$entity->jjrs','$entity->jjrssm','$entity->tprs','$entity->ssrs',
'$entity->ssrssm','$entity->swrs','$entity->swrssm','$entity->djyrs','$entity->cjryssrs','$entity->cjryswrs','$entity->jjssqk','$entity->whjjssqk',
'$entity->sfphxsaj','$entity->sfcczaaj','$entity->sfjjjf','$entity->lzscrs','$entity->jqcljg','$entity->jjfvrs','$entity->jjetrs')";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '新增失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $bRet;
	}
	
	/**
	 * updateFeedBack_fire
	 * 修改反馈消防信息表中反馈信息对象
	 * @param FeedBackEntity $entity
	 * @return true or false
	 */
	public function updateFeedBack_fire(FeedBackEntity $entity){
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update ZDT_PeFeedback_Fire set ";
		if($entity->cjdbh){
			$sql = $sql."cjdbh='$entity->cjdbh',";
		}
		if($entity->hzdjdm){
			$sql = $sql."hzdjdm='$entity->hzdjdm',";
		}
		if($entity->tqqkdm){
			$sql = $sql."tqqkdm='$entity->tqqkdm',";
		}
		if($entity->ssqkms){
			$sql = $sql."ssqkms='$entity->ssqkms',";
		}
		if($entity->hzyydm){
			$sql = $sql."hzyydm='$entity->hzyydm',";
		}
		if($entity->zhsglxdm){
			$sql = $sql."zhsglxdm='$entity->zhsglxdm',";
		}
		if($entity->qhwdm){
			$sql = $sql."qhwdm='$entity->qhwdm',";
		}
		if($entity->qhjzjgdm){
			$sql = $sql."qhjzjgdm='$entity->qhjzjgdm',";
		}
		if($entity->hzcsdm){
			$sql = $sql."hzcsdm='$entity->hzcsdm',";
		}
		if($entity->dycdsj){
			$sql = $sql."dycdsj=to_date('$entity->dycdsj','yyyy-MM-dd hh24:mi:ss'),";
		}
		if($entity->dydcsj){
			$sql = $sql."dydcsj=to_date('$entity->dydcsj','yyyy-MM-dd hh24:mi:ss'),";
		}
		if($entity->hcpmsj){
			$sql = $sql."hcpmsj=to_date('$entity->hcpmsj','yyyy-MM-dd hh24:mi:ss'),";
		}
		if($entity->clsj){
			$sql = $sql."clsj=to_date('$entity->clsj','yyyy-MM-dd hh24:mi:ss'),";
		}
		if($entity->xczzh){
			$sql = $sql."xczzh='$entity->xczzh',";
		}
		if($entity->cdsqs){
			$sql = $sql."cdsqs='$entity->cdsqs',";
		}
		if($entity->sfzddw){
			$sql = $sql."sfzddw='$entity->sfzddw',";
		}
		if($entity->zddwbm){
			$sql = $sql."zddwbm='$entity->zddwbm',";
		}
		if($entity->xlbmrs){
			$sql = $sql."xlbmrs='$entity->xlbmrs',";
		}
		$sql = rtrim($sql, ','); 
		$sql = $sql." where cjdbh='$entity->cjdbh'";
		//echo $sql;
		//$sql = "insert into zdt_pefeedback_case (cjdbh,zhrs,jzrs,jjfvrs,jjetrs,jqcljg,ssrs,swrs,jjssqk) values ('$cjdbh','$zhrs','$jzrs','$jjfvrs','$jjetrs','$jqcljg','$ssrs','$swrs','$jjss')";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $bRet;
		//echo "cjdbh:".$entity->cjdbh;
	}
	
	/**
	 * insertFeedBack_fire
	 * 插入反馈对象entity到反馈消防信息表
	 * @param FeedBackEntity $entity
	 * @return true or false
	 */
	public function insertFeedBack_fire(FeedBackEntity $entity){
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "insert into ZDT_PeFeedback_Fire(cjdbh,hzdjdm,tqqkdm,ssqkms,hzyydm,zhsglxdm,qhwdm,qhjzjgdm,hzcsdm,dycdsj,dydcsj,hcpmsj,clsj,xczzh,cdsqs,sfzddw,zddwbm,xlbmrs)" .
				"values('$entity->cjdbh','$entity->hzdjdm','$entity->tqqkdm','$entity->ssqkms','$entity->hzyydm','$entity->zhsglxdm','$entity->qhwdm','$entity->qhjzjgdm',
'$entity->hzcsdm',to_date('$entity->dycdsj','yyyy-MM-dd hh24:mi:ss'),to_date('$entity->dydcsj','yyyy-MM-dd hh24:mi:ss'),
to_date('$entity->hcpmsj','yyyy-MM-dd hh24:mi:ss'),to_date('$entity->clsj','yyyy-MM-dd hh24:mi:ss'),'$entity->xczzh','$entity->cdsqs',
'$entity->sfzddw','$entity->zddwbm','$entity->xlbmrs') ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '新增失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $bRet;
	}
	
	/**
	 * updateFeedBack_traffic
	 * 修改反馈交通信息表中反馈信息对象
	 * @param FeedBackEntity $entity
	 * @return true or false
	 */
	public function updateFeedBack_traffic(FeedBackEntity $entity){
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update ZDT_PeFeedback_Traffic set ";
		if($entity->cjdbh){
			$sql = $sql."cjdbh='$entity->cjdbh',";
		}
		if($entity->jtsgxtdm){
			$sql = $sql."jtsgxtdm='$entity->jtsgxtdm',";
		}
		if($entity->sfwhp){
			$sql = $sql."sfwhp='$entity->sfwhp',";
		}
		if($entity->sgccyydm){
			$sql = $sql."sgccyydm='$entity->sgccyydm',";
		}
		if($entity->njddm){
			$sql = $sql."njddm='$entity->njddm',";
		}
		if($entity->lmzkdm){
			$sql = $sql."lmzkdm='$entity->lmzkdm',";
		}
		if($entity->shjdcs){
			$sql = $sql."shjdcs='$entity->shjdcs',";
		}
		if($entity->shfjdcs){
			$sql = $sql."shfjdcs='$entity->shfjdcs',";
		}
		if($entity->dllxdm){
			$sql = $sql."dllxdm='$entity->dllxdm',";
		}
		$sql = rtrim($sql, ','); 
		$sql = $sql." where cjdbh='$entity->cjdbh'";
		//echo $sql;
		//$sql = "insert into zdt_pefeedback_case (cjdbh,zhrs,jzrs,jjfvrs,jjetrs,jqcljg,ssrs,swrs,jjssqk) values ('$cjdbh','$zhrs','$jzrs','$jjfvrs','$jjetrs','$jqcljg','$ssrs','$swrs','$jjss')";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $bRet;
		//echo "cjdbh:".$entity->cjdbh;
	}
	
	/**
	 * insertFeedBack_traffic
	 * 插入反馈对象entity到反馈交通信息表
	 * @param FeedBackEntity $entity
	 * @return true or false
	 */
	public function insertFeedBack_traffic(FeedBackEntity $entity){
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "insert into ZDT_PeFeedback_Traffic(cjdbh,jtsgxtdm,sfwhp,sgccyydm,njddm,lmzkdm,shjdcs,shfjdcs,dllxdm)" .
				"values('$entity->cjdbh','$entity->jtsgxtdm','$entity->sfwhp','$entity->sgccyydm','$entity->njddm','$entity->lmzkdm','$entity->shjdcs','$entity->shfjdcs','$entity->dllxdm') ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '新增失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $bRet;
	}
	
		
	/**
	 * getFeedbackCount
	 * 根据cjdbh查询反馈案件信息表count
	 * @param $cjdbh
	 * @return true or false
	 */
	public function getFeedbackCount($cjdbh) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from zdt_pefeedback_case where cjdbh='$cjdbh'";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		oci_close($this->dbconn);
		if ($count > 0) {
			return true;
		} else {
			return false;
		}

	}
	
	/**
	 * getFeedbackFireCount
	 * 根据cjdbh查询反馈消防信息表count
	 * @param $cjdbh
	 * @return true or false
	 */
	public function getFeedbackFireCount($cjdbh) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from zdt_pefeedback_Fire where cjdbh='$cjdbh'";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		oci_close($this->dbconn);
		if ($count > 0) {
			return true;
		} else {
			return false;
		}

	}
	
	/**
	 * getFeedbackTrafficCount
	 * 根据cjdbh查询反馈交通信息表count
	 * @param $cjdbh
	 * @return true or false
	 */
	public function getFeedbackTrafficCount($cjdbh) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from ZDT_PeFeedback_Traffic where cjdbh='$cjdbh'";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		oci_fetch($stmt);
		oci_close($this->dbconn);
		if ($count > 0) {
			return true;
		} else {
			return false;
		}

	}
	
	/**
	 * getPeProcessEventById
	 * 根据jqid查询警情详细信息（web端调用）
	 * @param $jqid
	 * @return 警情详细列表
	 */
	public function getPeProcessEventById($jqid) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.stationhousecode,t.stationhouse,t.jqid,t.jqbh,t.jjlx,t.jjfs,t.bjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdz, t.jqdd, t.bjnr, t.jqlbdm,t.jqlxdm,t.jqxldm,t.jqdjdm,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as jqjqzb,to_char(t.rksj,'yyyy-MM-dd hh24:mi:ss') as rksj,to_char(t.gxsj,'yyyy-MM-dd hh24:mi:ss') as gxsj,t.cfjqbs,t.jqclzt,t.jqzk,t1.bjrxm,t1.bjrsfzh,t1.bjrxbdm,t1.bjdh,t1.lxdh,t1.bjdhyhxm,t1.bjdhyhdz,t2.jjrbh,t2.jjrxm,to_char(t2.jjsj,'yyyy-MM-dd hh24:mi:ss') as jjsj,to_char(t2.hzsj,'yyyy-MM-dd hh24:mi:ss') as hzsj,t3.gxdwdm,t3.hphm,t3.xzqh,t4.ywbzxl,t4.bjchpzldm,t4.bjcph,t4.bkrs,fb_case.ssrs,fb_case.swrs,t4.sfsw,t4.sfswybj,t4.jqztdm,t4.zagjdm,t4.hzdjdm,t4.qhjzjgdm,t4.hzcsdm,t4.qhjzqkms,t4.plqk,t4.qhwdm,t4.ywty,t4.sfswhcl,MDSYS.Sdo_Util.to_wktgeometry_varchar(t5.mhjqzb) as mhjqzb,t6.cjdbh,t7.zlbh,t7.rybh,t7.xm,to_char(t7.zljssj, 'yyyy-MM-dd hh24:mi:ss') as zljssj,to_char(t7.ddxcsj, 'yyyy-MM-dd hh24:mi:ss') as ddxcsj,fb_case.jqcljg,org.orgname,fb_case.zhrs,fb_case.jjssqk,fb_case.jzrs,fb_case.jjfvrs,fb_case.jjetrs from zdt_policeevent t
		left join ZDT_PoliceEvent_Reporter t1 on t.jqid=t1.jqid left join ZDT_PoliceEvent_Receiver t2 on t.jqid=t2.jqid left join ZDT_PoliceEvent_Admin t3 on t.jqid=t3.jqid left join ZDB_Organization org on t3.gxdwdm=org.orgcode left join ZDT_PoliceEvent_AddInfo t4 on t.jqid=t4.jqid left join ZDT_PoliceEvent_Location t5 on t.jqid=t5.jqid left join ZDT_PeProcess t6 on t.jqid=t6.jqid left join ZDT_PeProcess_Command t7 on t6.cjdbh =t7.cjdbh left join ZDT_PeFeedback_Case fb_case on t6.cjdbh = fb_case.cjdbh where t6.cjdzt='1' and t7.zlbs='1' and t.jqid='$jqid' order by t7.zlxdsj desc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$cjdbh = iconv("GBK", "UTF-8", $row["CJDBH"]);
				$array = $this->getDispatchMenByCjdbh($cjdbh,"");
				$jqidArr = $this->getPeProcessByJqbh($jqid);
				$names = "";
				for ($i=0;$i<count($array);$i++) {
					$name = $array[$i]['xm'];
					$p = $i==0 ? "" : ",";
					$names .= $p.$name;
				}
				$men = array (
					'jqid' => iconv("GBK", "UTF-8", $row["JQID"]),
					'jqbh' => iconv("GBK", "UTF-8", $row["JQBH"]),
					'jjlx' => iconv("GBK", "UTF-8", $row["JJLX"]),
					'jjfs' => iconv("GBK", "UTF-8", $row["JJFS"]),
					'bjfs' => iconv("GBK", "UTF-8", $row["BJFS"]),
					'bjsj' => iconv("GBK", "UTF-8", $row["BJSJ"]),
					'jqdz' => iconv("GBK", "UTF-8", $row["JQDZ"]),
					'jqdd' => iconv("GBK", "UTF-8", $row["JQDD"]),
					'bjnr' => iconv("GBK", "UTF-8", $row["BJNR"]),
					'jqlbdm' => iconv("GBK", "UTF-8", $row["JQLBDM"]),
					'jqlxdm' => iconv("GBK", "UTF-8", $row["JQLXDM"]),	
					'jqxldm' => iconv("GBK", "UTF-8", $row["JQXLDM"]),
					'jqdjdm' => iconv("GBK", "UTF-8", $row["JQDJDM"]),	
					'jqjqzb' => iconv("GBK", "UTF-8", $row["JQJQZB"]),
					'rksj' => iconv("GBK", "UTF-8", $row["RKSJ"]),
					'gxsj' => iconv("GBK", "UTF-8", $row["GXSJ"]),
					'cfjqbs' => iconv("GBK", "UTF-8", $row["CFJQBS"]),
					'jqclzt' => iconv("GBK", "UTF-8", $row["JQCLZT"]),
					'bjrxm' => iconv("GBK", "UTF-8", $row["BJRXM"]),
					'bjrsfzh' => iconv("GBK", "UTF-8", $row["BJRSFZH"]),
					'bjrxbdm' => iconv("GBK", "UTF-8", $row["BJRXBDM"]),
					'bjdh' => iconv("GBK", "UTF-8", $row["BJDH"]),
					'lxdh' => iconv("GBK", "UTF-8", $row["LXDH"]),
					'bjdhyhxm' => iconv("GBK", "UTF-8", $row["BJDHYHXM"]),
					'bjdhyhdz' => iconv("GBK", "UTF-8", $row["BJDHYHDZ"]),
					'jjrbh' => iconv("GBK", "UTF-8", $row["JJRBH"]),
					'jjrxm' => iconv("GBK", "UTF-8", $row["JJRXM"]),
					'jjsj' => iconv("GBK", "UTF-8", $row["JJSJ"]),
					'hzsj' => iconv("GBK", "UTF-8", $row["HZSJ"]),
					'gxdwdm' => iconv("GBK", "UTF-8", $row["GXDWDM"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'xzqh' => iconv("GBK", "UTF-8", $row["XZQH"]),
					'ywbzxl' => iconv("GBK", "UTF-8", $row["YWBZXL"]),
					'bjchpzldm' => iconv("GBK", "UTF-8", $row["BJCHPZLDM"]),
					'bjcph' => iconv("GBK", "UTF-8", $row["BJCPH"]),
					'bkrs' => iconv("GBK", "UTF-8", $row["BKRS"]),
					'ssrs' => iconv("GBK", "UTF-8", $row["SSRS"]),
					'swrs' => iconv("GBK", "UTF-8", $row["SWRS"]),
					'sfsw' => iconv("GBK", "UTF-8", $row["SFSW"]),
					'sfswybj' => iconv("GBK", "UTF-8", $row["SFSWYBJ"]),
					'jqztdm' => iconv("GBK", "UTF-8", $row["JQZTDM"]),
					'zagjdm' => iconv("GBK", "UTF-8", $row["ZAGJDM"]),
					'hzdjdm' => iconv("GBK", "UTF-8", $row["HZDJDM"]),
					'qhjzjgdm' => iconv("GBK", "UTF-8", $row["QHJZJGDM"]),
					'hzcsdm' => iconv("GBK", "UTF-8", $row["HZCSDM"]),
					'qhjzqkms' => iconv("GBK", "UTF-8", $row["QHJZQKMS"]),
					'plqk' => iconv("GBK", "UTF-8", $row["PLQK"]),
					'qhwdm' => iconv("GBK", "UTF-8", $row["QHWDM"]),
					'ywty' => iconv("GBK", "UTF-8", $row["YWTY"]),
					'sfswhcl' => iconv("GBK", "UTF-8", $row["SFSWHCL"]),
					'mhjqzb' => iconv("GBK", "UTF-8", $row["MHJQZB"]),
					'cjdbh' => iconv("GBK", "UTF-8", $row["CJDBH"]),
					'zlbh' => iconv("GBK", "UTF-8", $row["ZLBH"]),
					'rybh' => iconv("GBK", "UTF-8", $row["RYBH"]),
					'xm' => iconv("GBK", "UTF-8", $row["XM"]),
					'zljssj' => iconv("GBK", "UTF-8", $row["ZLJSSJ"]),
					'orgname' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'cjr' => $names,
					'cdjl' => count($array),
					'ddxcsj' => iconv("GBK", "UTF-8", $row["DDXCSJ"]),
					'cljg' => iconv("GBK", "UTF-8", $row["JQCLJG"]),
					'zhrs' => iconv("GBK", "UTF-8", $row["ZHRS"]),
					'jjssqk' => iconv("GBK", "UTF-8", $row["JJSSQK"]),
					'jzrs' => iconv("GBK", "UTF-8", $row["JZRS"]),
					'jjfvrs' => iconv("GBK", "UTF-8", $row["JJFVRS"]),
					'jjetrs' => iconv("GBK", "UTF-8", $row["JJETRS"]),
					'stationhousecode' => iconv("GBK", "UTF-8", $row["STATIONHOUSECODE"]),
					'stationhouse' => iconv("GBK", "UTF-8", $row["STATIONHOUSE"]),
					'jqzk' => iconv("GBK", "UTF-8", $row["JQZK"]),
					'processRecords'=>$jqidArr
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		else
			$arr = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $men
			);

		return $arr;
	}
	
	/**
	 * getPeProcessByJqbh
	 * 根据jqbh查询处警表信息
	 * @param $jqid
	 * @return 警力对象组
	 */
	public function getPeProcessByJqbh($jqid){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.cjdbh,to_char(t.pjsj,'yyyy-MM-dd hh24:mi:ss') as pjsj,t.cjdzt from zdt_peprocess t where t.jqid='$jqid' order by t.cjdzt asc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			$mens = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'cjdbh' => iconv("GBK", "UTF-8", $row["CJDBH"]),
					'pjsj' => iconv("GBK", "UTF-8", $row["PJSJ"]),
					'cjdzt' => iconv("GBK", "UTF-8", $row["CJDZT"])
				);
				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);

		return $mens;
	}
	
	/**
	 * getCommandByCjdbh
	 * 根据cjdbh查询出动警力表信息
	 * @param $cjdbh
	 * @return 警力对象组
	 */
	public function getCommandByCjdbh($cjdbh){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.cjld,t.zlbh,t.rybh,t.xm,t.cljg,t.zlnr,to_char(t.zlxdsj, 'yyyy-MM-dd hh24:mi:ss') as zlxdsj,to_char(t.zljssj, 'yyyy-MM-dd hh24:mi:ss') as zljssj,to_char(t.ddxcsj, 'yyyy-MM-dd hh24:mi:ss') as ddxcsj, to_char(t.clwbsj, 'yyyy-MM-dd hh24:mi:ss') as clwbsj,t.cjqk,t.cljgdm,t.cdclqk,t.cdryqk,t.jqztdm,t.zlbs from ZDT_PeProcess_Command t where t.cjdbh = '$cjdbh' order by t.zlbs asc, t.zlxdsj desc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			$mens = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$zlbh = iconv("GBK", "UTF-8", $row["ZLBH"]);
				$array = $this->getDispatchMenByCjdbh($cjdbh,$zlbh);
				$names = "";
				for ($i=0;$i<count($array);$i++) {
					$name = $array[$i]['xm'];
					$p = $i==0 ? "" : ",";
					$names .= $p.$name;
				}
				$men = array (
					'cjld' => iconv("GBK", "UTF-8", $row["CJLD"]),
					'zlbh' => iconv("GBK", "UTF-8", $row["ZLBH"]),
					'rybh' => iconv("GBK", "UTF-8", $row["RYBH"]),
					'xm' => iconv("GBK", "UTF-8", $row["XM"]),
					'zlxdsj' => iconv("GBK", "UTF-8", $row["ZLXDSJ"]),
					'zljssj' => iconv("GBK", "UTF-8", $row["ZLJSSJ"]),
					'ddxcsj' => iconv("GBK", "UTF-8", $row["DDXCSJ"]),
					'clwbsj' => iconv("GBK", "UTF-8", $row["CLWBSJ"]),
					'cjqk' => iconv("GBK", "UTF-8", $row["CJQK"]),
					'cljgdm' => iconv("GBK", "UTF-8", $row["CLJGDM"]),
					'cdclqk' => iconv("GBK", "UTF-8", $row["CDCLQK"]),
					'cdryqk' => iconv("GBK", "UTF-8", $row["CDRYQK"]),
					'jqztdm' => iconv("GBK", "UTF-8", $row["JQZTDM"]),
					'zlbs' => iconv("GBK", "UTF-8", $row["ZLBS"]),
					'zlnr' => iconv("GBK", "UTF-8", $row["ZLNR"]),
					'cljg' => iconv("GBK", "UTF-8", $row["CLJG"]),
					'cjr' => $names,
					'cdjl' => count($array)
				);
				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $mens;
	}
	
	
	/**
	 * getNormalEventById
	 * 根据jqid查询派警警情详细信息（web端调用）
	 * @param $jqid
	 * @return 警情详细列表
	 */
	public function getNormalEventById($jqid) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.stationhousecode,t.stationhouse,t.jqid,t.jqbh,t.jjlx,t.jjfs,t.bjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdz, t.jqdd, t.bjnr, t.jqlbdm,t.jqlxdm,t.jqxldm,t.jqdjdm,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as jqjqzb,to_char(t.rksj,'yyyy-MM-dd hh24:mi:ss') as rksj,to_char(t.gxsj,'yyyy-MM-dd hh24:mi:ss') as gxsj,t.cfjqbs,t.jqclzt,t1.bjrxm,t1.bjrsfzh,t1.bjrxbdm,t1.bjdh,t1.lxdh,t1.bjdhyhxm,t1.bjdhyhdz,t2.jjrbh,t2.jjrxm,to_char(t2.jjsj,'yyyy-MM-dd hh24:mi:ss') as jjsj,to_char(t2.hzsj,'yyyy-MM-dd hh24:mi:ss') as hzsj,t3.gxdwdm,t3.hphm,t3.xzqh,t4.ywbzxl,t4.bjchpzldm,t4.bjcph,t4.bkrs,t4.sfsw,t4.sfswybj,t4.jqztdm,t4.zagjdm,t4.hzdjdm,t4.qhjzjgdm,t4.hzcsdm,t4.qhjzqkms,t4.plqk,t4.qhwdm,t4.ywty,t4.sfswhcl,MDSYS.Sdo_Util.to_wktgeometry_varchar(t5.mhjqzb) as mhjqzb,org.orgname from zdt_policeevent t
		left join ZDT_PoliceEvent_Reporter t1 on t.jqid=t1.jqid left join ZDT_PoliceEvent_Receiver t2 on t.jqid=t2.jqid left join ZDT_PoliceEvent_Admin t3 on t.jqid=t3.jqid left join ZDB_Organization org on t3.gxdwdm=org.orgcode left join ZDT_PoliceEvent_AddInfo t4 on t.jqid=t4.jqid left join ZDT_PoliceEvent_Location t5 on t.jqid=t5.jqid  where t.jqid='$jqid' ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'jqid' => iconv("GBK", "UTF-8", $row["JQID"]),
					'jqbh' => iconv("GBK", "UTF-8", $row["JQBH"]),
					'jjlx' => iconv("GBK", "UTF-8", $row["JJLX"]),
					'jjfs' => iconv("GBK", "UTF-8", $row["JJFS"]),
					'bjfs' => iconv("GBK", "UTF-8", $row["BJFS"]),
					'bjsj' => iconv("GBK", "UTF-8", $row["BJSJ"]),
					'jqdz' => iconv("GBK", "UTF-8", $row["JQDZ"]),
					'jqdd' => iconv("GBK", "UTF-8", $row["JQDD"]),
					'bjnr' => iconv("GBK", "UTF-8", $row["BJNR"]),
					'jqlbdm' => iconv("GBK", "UTF-8", $row["JQLBDM"]),
					'jqlxdm' => iconv("GBK", "UTF-8", $row["JQLXDM"]),	
					'jqxldm' => iconv("GBK", "UTF-8", $row["JQXLDM"]),
					'jqdjdm' => iconv("GBK", "UTF-8", $row["JQDJDM"]),	
					'jqjqzb' => iconv("GBK", "UTF-8", $row["JQJQZB"]),
					'rksj' => iconv("GBK", "UTF-8", $row["RKSJ"]),
					'gxsj' => iconv("GBK", "UTF-8", $row["GXSJ"]),
					'cfjqbs' => iconv("GBK", "UTF-8", $row["CFJQBS"]),
					'jqclzt' => iconv("GBK", "UTF-8", $row["JQCLZT"]),
					'bjrxm' => iconv("GBK", "UTF-8", $row["BJRXM"]),
					'bjrsfzh' => iconv("GBK", "UTF-8", $row["BJRSFZH"]),
					'bjrxbdm' => iconv("GBK", "UTF-8", $row["BJRXBDM"]),
					'bjdh' => iconv("GBK", "UTF-8", $row["BJDH"]),
					'lxdh' => iconv("GBK", "UTF-8", $row["LXDH"]),
					'bjdhyhxm' => iconv("GBK", "UTF-8", $row["BJDHYHXM"]),
					'bjdhyhdz' => iconv("GBK", "UTF-8", $row["BJDHYHDZ"]),
					'jjrbh' => iconv("GBK", "UTF-8", $row["JJRBH"]),
					'jjrxm' => iconv("GBK", "UTF-8", $row["JJRXM"]),
					'jjsj' => iconv("GBK", "UTF-8", $row["JJSJ"]),
					'hzsj' => iconv("GBK", "UTF-8", $row["HZSJ"]),
					'gxdwdm' => iconv("GBK", "UTF-8", $row["GXDWDM"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'xzqh' => iconv("GBK", "UTF-8", $row["XZQH"]),
					'ywbzxl' => iconv("GBK", "UTF-8", $row["YWBZXL"]),
					'bjchpzldm' => iconv("GBK", "UTF-8", $row["BJCHPZLDM"]),
					'bjcph' => iconv("GBK", "UTF-8", $row["BJCPH"]),
					'bkrs' => iconv("GBK", "UTF-8", $row["BKRS"]),
					'sfsw' => iconv("GBK", "UTF-8", $row["SFSW"]),
					'sfswybj' => iconv("GBK", "UTF-8", $row["SFSWYBJ"]),
					'jqztdm' => iconv("GBK", "UTF-8", $row["JQZTDM"]),
					'zagjdm' => iconv("GBK", "UTF-8", $row["ZAGJDM"]),
					'hzdjdm' => iconv("GBK", "UTF-8", $row["HZDJDM"]),
					'qhjzjgdm' => iconv("GBK", "UTF-8", $row["QHJZJGDM"]),
					'hzcsdm' => iconv("GBK", "UTF-8", $row["HZCSDM"]),
					'qhjzqkms' => iconv("GBK", "UTF-8", $row["QHJZQKMS"]),
					'plqk' => iconv("GBK", "UTF-8", $row["PLQK"]),
					'qhwdm' => iconv("GBK", "UTF-8", $row["QHWDM"]),
					'ywty' => iconv("GBK", "UTF-8", $row["YWTY"]),
					'sfswhcl' => iconv("GBK", "UTF-8", $row["SFSWHCL"]),
					'mhjqzb' => iconv("GBK", "UTF-8", $row["MHJQZB"]),
					'orgname' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'stationhousecode' => iconv("GBK", "UTF-8", $row["STATIONHOUSECODE"]),
					'stationhouse' => iconv("GBK", "UTF-8", $row["STATIONHOUSE"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		else
			$arr = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $men
			);

		return $arr;
	}
	
		/**
	 * getDispatchMen
	 * 根据传递参数查询出动警力库中是否存在
	 * @param $cjdbh,$zlbh,$rybh
	 * @return true or false
	 */
	public function getCjdbhByJqid($jqid) {
		$bRet = true;
		$errMsg = '';
		$cjdbh="";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select t.cjdbh from ZDT_PeProcess t where t.jqid='$jqid' and t.cjdzt='1' ";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '查询失败';
		}
		if (($row = oci_fetch_assoc($stmt)) != false) {
			$cjdbh = iconv("GBK", "UTF-8", $row["CJDBH"]);
		}
		oci_fetch($stmt);
		return $cjdbh;
	}
	
	/**
	 * getPeProcessById
	 * 根据jqid查询处警单列表信息（web端调用）
	 * @param $jqid
	 * @return 警情详细列表
	 */
	public function getPeProcessById($jqid) {
		$bRet = true;
		$errMsg = "";
		$datas = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.cjdbh,to_char(t.pjsj,'yyyy-MM-dd hh24:mi:ss') as pjsj,t.cjdzt from zdt_peprocess t where t.jqid='$jqid' order by t.cjdzt asc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$cjdbh = iconv("GBK", "UTF-8", $row["CJDBH"]);
				$jqidArr = $this->getCommandByCjdbh($cjdbh);
				$men = array (
					'cjdbh' => iconv("GBK", "UTF-8", $row["CJDBH"]),
					'pjsj' => iconv("GBK", "UTF-8", $row["PJSJ"]),
					'cjdzt' => iconv("GBK", "UTF-8", $row["CJDZT"]),
					'commandRecords'=>$jqidArr
				);
				array_push($datas, $men);
			}
		}
		oci_free_statement($stmt);
		if ($this->dbconn != null)
		oci_close($this->dbconn);

		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		else
			$arr = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $datas
			);

		return $arr;
	}
	
	/**
	 * getCjdbhById
	 * 根据jqid查询唯一处警单编号信息（web端调用）
	 * @param $jqid
	 * @return 警情详细列表
	 */
	public function getCjdbhById($jqid) {
		$bRet = true;
		$errMsg = "";
		$data = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.jqid,t.jqbh,t6.cjdbh,t7.zlbh from zdt_policeevent t  left join ZDT_PeProcess t6 on t.jqid = t6.jqid  left join ZDT_PeProcess_Command t7 on t6.cjdbh = t7.cjdbhwhere t6.cjdzt = '1'   and t7.zlbs = '1'   and t.jqid = '$jqid'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'jqid' => iconv("GBK", "UTF-8", $row["JQID"]),
					'jqbh' => iconv("GBK", "UTF-8", $row["JQBH"]),
					'cjdbh' => iconv("GBK", "UTF-8", $row["CJDBH"]),
					'zlbh' => iconv("GBK", "UTF-8", $row["ZLBH"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		return $data;
	}
	
	public function updateEventInvalidById($jqid,$userId){
		
		$bRet = true;
		$errMsg = '';
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$res = $this->updateCommByreLoad($jqid,"警情撤销");
		if(!$res){
			$arr = array('result' =>'false', 'errmsg' =>'数据库操作失败');
			return $arr;
		}
		$event = $this->getEventSimpleByid($jqid);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update ZDT_PoliceEvent set JQZT='2',GXSJ=sysdate where jqid='$jqid'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '操作失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$dataTime = date('y-m-d h:i:s',time());
		$url = 'http://10.78.17.154:9999/lbs';
		$params = "operation=FeedbackInfo_AddFeedbackInfo_v001&license=a756244eb0236bdc26061cb6b6bdb481&content=";
		$this->outEventFeedBack($url,$params,$event['jqid'],$event['jqbh'],"",$event['jjlx'],$event['jjfs'],$event['bjfs'],$event['bjsj'],$event['jqdz'],$event['jqdd'],$event['bjnr'],$event['jqlbdm'],$event['jqlxdm'],$event['jqxldm'],$event['jqdjdm'],"","2","","","","","","","","","","","","","","","","","","","","","","","","","","","","",
					"","","","","","警情撤销反馈","","","","1","0","","",$event['pjfs'],$event['jqzbly'],$event['jsjqly'],$event['xjbmbm'],$event['xjbmmc']);
					
		$reloadEvent = $this->getEventReloadDisposalByid($jqid);
		if($reloadEvent['jqid']){
			$url = 'http://10.78.17.154:9999/lbs';
			$params = "operation=FeedbackInfo_AddPoliceEvent_v001&license=a756244eb0236bdc26061cb6b6bdb481&content=";
			$this->outEventReceiveDisposal($url,$params,$reloadEvent['jqid'],$reloadEvent['jqbh'],$reloadEvent['jjlx'],$reloadEvent['jjfs'],$reloadEvent['bjfs'],$reloadEvent['bjsj'],$reloadEvent['jqdd'],$reloadEvent['bjnr'],$reloadEvent['jqlbdm'],$reloadEvent['jqlxdm'],$reloadEvent['jqxldm'],$reloadEvent['jqdjdm'],"","",$reloadEvent['stationhousecode'],$reloadEvent['stationhouse'],"",$reloadEvent['jqclzt'],$reloadEvent['jqzt'],$reloadEvent['sfyj']);
		}
		if ($bRet)
			$result = array('result' =>$bRet,'errmsg' =>'撤销成功');
		else
			$result = array('result' =>$bRet, 'errmsg' =>$errMsg);
		return $result;
	}
	
	/**
	 * getHPHMEventId
	 * 根据警情ID查询所派警人员的hphm
	 * 返回终端需要字段
	 * @param $jqid
	 * @return hphm
	 */
	public function getHPHMEventId($jqid) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.jqid,c.hphm from zdt_policeevent t left join zdt_peprocess t1 on t1.jqid=t.jqid left join zdt_peprocess_command t2 on t2.cjdbh=t1.cjdbh" .
				" left join zdt_duty_group g on g.leaderid=t2.rybh left join zdb_equip_car c on c.id=g.carid where t1.cjdzt='1' and t2.zlbs='1' and g.status!='3' and t.jqid='$jqid'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				
				$men = array (
					'jqid' => iconv("GBK", "UTF-8", $row["JQID"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		else
			$arr = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $men
			);

		return $arr;
	}
	
	/**
	 * getOverEvent
	 * 分页查询结束警情
	 * @param $orgCode,$jqclzt,$page,$rows
	 * @return 结果数组
	 */
	public function getOverEvent($orgCode,$jqclzt,$page,$rows,$xwh,$jqjssj,$bjsj,$zdjq,$jqbh_end4){
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		$arr=array('total'=>0,'rows' => $datas);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		 /*组成sql*/
		$sql = "select count(*) ROWCOUNT  from zdt_policeevent t" .
				"  left join ZDT_PoliceEvent_Location t1 on  t.jqid = t1.jqid  left join ZDT_PoliceEvent_Admin    t2 on  t2.jqid = t.jqid" .
				"  left join ZDT_PoliceEvent_Reporter r on r.jqid=t.jqid".
				"  left join ZDT_PoliceEvent_Receiver rec on rec.jqid=t.jqid".
				"  left join zdb_organization  o on t2.gxdwdm = o.orgcode left join zdt_peprocess t3 on t.jqid = t3.jqid where t3.cjdzt='1' and t.jqzt!='2' ";
		$array = explode(",", $orgCode);
		$codeSql ="";
		for ($i=0;$i<count($array);$i++) {
			$code = $array[$i];
			if ($code != GlobalConfig :: getInstance()->dsdm."00000000"||$code != GlobalConfig :: getInstance()->dsdm."00030000"){
				if($codeSql==""){
					$codeSql = $codeSql . " o.parenttreepath like '%$code%' or o.orgCode = '$code' ";
				}else{
					$codeSql = $codeSql . "or o.parenttreepath like '%$code%' or o.orgCode = '$code' ";
				}
			}
		}
		if($codeSql!=""){
			$sql = $sql." and (".$codeSql.")";
		}
		if ($jqclzt != null) {
			$sql = $sql . " and t . jqclzt in ($jqclzt)";
		}
                if ($jqbh_end4 != null) {
			$sql = $sql . " and substr(t.jqbh,-4,4) = '$jqbh_end4'";
		}
                if ($xwh != null) {
			$sql .=  " and rec.JJRBH ='$xwh'";
		}
                if ($jqjssj != null) {
			$sql .=  " and to_char(t3.jqjssj,'yyyy-mm-dd') ='$jqjssj'";
		}
                if ($bjsj != null) {
			$sql .=  " and to_char(t.bjsj,'yyyy-mm-dd') ='$bjsj'";
		}
                if ($zdjq != null&&$zdjq=='1') {
			$sql .=  " and t.jqzk ='4'";
		}
		$sql = $sql . " order by t.gxsj desc";
		
	    //echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt,"ROWCOUNT",$row_count);
		if (!@oci_execute($stmt)) {
	  		$bRet = false;
	  		$errMsg="查询失败";
		}else{
			
		 /*处理分页*/
			oci_fetch($stmt);
			$total_rec = $row_count;
			oci_free_statement($stmt);
    	
			/*查询部门*/
			if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
                        $sql=str_replace("select count(*) ROWCOUNT","select rec.JJRBH,r.bjdh,t.jqzk,t.jqid,t.jqbh,t.jjlx,t.bjfs,t.jjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdz,t.dzid,t.jqdd,t.bjnr,t.jqlbdm,t.jqlxdm,t.jqdjdm,t.jqxldm,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as jqjqzb,to_char(t.rksj,'yyyy-MM-dd hh24:mi:ss') as rksj,to_char(t.gxsj,'yyyy-MM-dd hh24:mi:ss') as gxsj,t.cfjqbs,t.jqclzt,t.jqzt,t.sfyj,MDSYS.Sdo_Util.to_wktgeometry_varchar(t1.mhjqzb) as mhjqzb,o.orgname,o.orgcode,t2.gxdwdm,t2.hphm ,to_char(t3.jqjssj,'yyyy-MM-dd hh24:mi:ss') as jqjssj ",$sql);
			/*组成sql
			$sql = "select rec.JJRBH,r.bjdh,t.jqzk,t.jqid,t.jqbh,t.jjlx,t.bjfs,t.jjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdz,t.dzid,t.jqdd,t.bjnr,t.jqlbdm,t.jqlxdm,t.jqdjdm,t.jqxldm,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as jqjqzb,to_char(t.rksj,'yyyy-MM-dd hh24:mi:ss') as rksj,to_char(t.gxsj,'yyyy-MM-dd hh24:mi:ss') as gxsj,t.cfjqbs,t.jqclzt,t.jqzt,t.sfyj,MDSYS.Sdo_Util.to_wktgeometry_varchar(t1.mhjqzb) as mhjqzb,o.orgname,o.orgcode,t2.gxdwdm,t2.hphm  from zdt_policeevent t" .
					"  left join ZDT_PoliceEvent_Location t1 on  t.jqid = t1.jqid  left join ZDT_PoliceEvent_Admin    t2 on  t2.jqid = t.jqid" .
					"  left join ZDT_PoliceEvent_Reporter r on r.jqid=t.jqid".
					"  left join ZDT_PoliceEvent_Receiver rec on rec.jqid=t.jqid".
					"  left join zdb_organization  o on t2.gxdwdm = o.orgcode where 1=1 and t.jqzt!='2' ";
			$array = explode(",", $orgCode);
			$codeSql ="";
			for ($i=0;$i<count($array);$i++) {
				$code = $array[$i];
				if ($code != "210200000000"){
					if($codeSql==""){
						$codeSql = $codeSql . " o.parenttreepath like '%$code%' or o.orgCode = '$code' ";
					}else{
						$codeSql = $codeSql . "or o.parenttreepath like '%$code%' or o.orgCode = '$code' ";
					}
				}
			}
			if($codeSql!=""){
				$sql = $sql." and (".$codeSql.")";
			}
			if ($jqclzt != null) {
				$sql = $sql . " and t . jqclzt in ($jqclzt)";
			}
			$sql = $sql . " order by t.gxsj desc";
			*/
                        //echo $sql;
			$sql = pageResultSet($sql, $page, $rows);
			
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)) {
				$bRet = false;
				$errMsg="查询失败";
			}else{
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array(
						'jqid' => iconv("GBK", "UTF-8", $row["JQID"]),
						'jqbh' => iconv("GBK", "UTF-8", $row["JQBH"]),
						'jjlx' => iconv("GBK", "UTF-8", $row["JJLX"]),
						'jjfs' => iconv("GBK", "UTF-8", $row["JJFS"]),
						'bjfs' => iconv("GBK", "UTF-8", $row["BJFS"]),
						'bjsj' => iconv("GBK", "UTF-8", $row["BJSJ"]),
						'jqdz' => iconv("GBK", "UTF-8", $row["JQDZ"]),
						'dzid' => iconv("GBK", "UTF-8", $row["DZID"]),
						'jqdd' => iconv("GBK", "UTF-8", $row["JQDD"]),
						'bjnr' => iconv("GBK", "UTF-8", $row["BJNR"]),
						'jqlbdm' => iconv("GBK", "UTF-8", $row["JQLBDM"]),
						'jqlxdm' => iconv("GBK", "UTF-8", $row["JQLXDM"]),
						'jqxldm' => iconv("GBK", "UTF-8", $row["JQXLDM"]),
						'jqdjdm' => iconv("GBK", "UTF-8", $row["JQDJDM"]),
						'jqjqzb' => iconv("GBK", "UTF-8", $row["JQJQZB"]),
						'rksj' => iconv("GBK", "UTF-8", $row["RKSJ"]),
						'gxsj' => iconv("GBK", "UTF-8", $row["GXSJ"]),
						'cfjqbs' => iconv("GBK", "UTF-8", $row["CFJQBS"]),
						'jqclzt' => iconv("GBK", "UTF-8", $row["JQCLZT"]),
						'gxdwdm' => iconv("GBK", "UTF-8", $row["GXDWDM"]),
						'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
						'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
						'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
						'mhjqzb' => iconv("GBK", "UTF-8", $row["MHJQZB"]),
						'bjdh' => iconv("GBK", "UTF-8", $row["BJDH"]),
						'jjrbh' => iconv("GBK", "UTF-8", $row["JJRBH"]),
						'jqzt' => iconv("GBK", "UTF-8", $row["JQZT"]),
						'jqzk' => iconv("GBK", "UTF-8", $row["JQZK"]),
                                                'jqjssj' => iconv("GBK", "UTF-8", $row["JQJSSJ"]),
						'sfyj' => iconv("GBK", "UTF-8", $row["SFYJ"])
       				);
		   			array_push($datas, $data);
	  		}
	  			oci_free_statement($stmt);
	  			oci_close($this->dbconn);
				$arr=array('total'=>$total_rec,'rows' => $datas);
			}
		}
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $arr);
		return $result;
	}
	
	public function getEventReceiveDisposalByid($jqid) {
		$bRet = true;
		$errMsg = "";
		$men = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.jqid,t.jqbh,t.jjlx,t.jjfs,t.bjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdd,t.bjnr,t.jqlbdm,t.jqlxdm,t.jqxldm,t.jqdjdm,t.stationhousecode,t.stationhouse,t.jqclzt,t.jqzt,t.sfyj,to_char(p.pjsj,'yyyy-MM-dd hh24:mi:ss') as pjsj,to_char(c.zljssj,'yyyy-MM-dd hh24:mi:ss') as zljssj,c.cjdwdm from zdt_policeevent t" .
				" left join zdt_peprocess p on p.jqid=t.jqid left join zdt_peprocess_command c on c.cjdbh=p.cjdbh  where p.cjdzt='1' and c.zlbs='1' and t.jqid = '$jqid' ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
						'jqid' =>	iconv("GBK", "UTF-8", $row["JQID"]),
						'jqbh' =>	iconv("GBK", "UTF-8", $row["JQBH"]),
						'jjlx' =>iconv("GBK", "UTF-8", $row["JJLX"]),
						'jjfs' =>iconv("GBK", "UTF-8", $row["JJFS"]),
						'bjfs' =>iconv("GBK", "UTF-8", $row["BJFS"]),
						'bjsj' =>iconv("GBK", "UTF-8", $row["BJSJ"]),
						'jqdd' =>	iconv("GBK", "UTF-8", $row["JQDD"]),
						'bjnr' =>	iconv("GBK", "UTF-8", $row["BJNR"]),
						'jqlbdm' =>	iconv("GBK", "UTF-8", $row["JQLBDM"]),
						'jqlxdm' =>	iconv("GBK", "UTF-8", $row["JQLXDM"]),
						'jqxldm' =>	iconv("GBK", "UTF-8", $row["JQXLDM"]),
						'jqdjdm' =>	iconv("GBK", "UTF-8", $row["JQDJDM"]),
						'stationhousecode' =>	iconv("GBK", "UTF-8", $row["STATIONHOUSECODE"]),
						'stationhouse' =>	iconv("GBK", "UTF-8", $row["STATIONHOUSE"]),
						'jqclzt' =>	iconv("GBK", "UTF-8", $row["JQCLZT"]),
						'jqzt' =>	iconv("GBK", "UTF-8", $row["JQZT"]),
						'sfyj' =>	iconv("GBK", "UTF-8", $row["SFYJ"]),
						'pjsj' =>	iconv("GBK", "UTF-8", $row["PJSJ"]),
						'zljssj' =>	iconv("GBK", "UTF-8", $row["ZLJSSJ"]),
						'cjdwdm' =>	iconv("GBK", "UTF-8", $row["CJDWDM"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $men;
	}
	
	public function getEventReloadDisposalByid($jqid) {
		$bRet = true;
		$errMsg = "";
		$men = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.jqid,t.jqbh,t.jjlx,t.jjfs,t.bjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdd,t.bjnr,t.jqlbdm,t.jqlxdm,t.jqxldm,t.jqdjdm,t.stationhousecode,t.stationhouse,t.jqclzt,t.jqzt,t.sfyj from zdt_policeevent t" .
				" where t.jqid = '$jqid' ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
						'jqid' =>	iconv("GBK", "UTF-8", $row["JQID"]),
						'jqbh' =>	iconv("GBK", "UTF-8", $row["JQBH"]),
						'jjlx' =>iconv("GBK", "UTF-8", $row["JJLX"]),
						'jjfs' =>iconv("GBK", "UTF-8", $row["JJFS"]),
						'bjfs' =>iconv("GBK", "UTF-8", $row["BJFS"]),
						'bjsj' =>iconv("GBK", "UTF-8", $row["BJSJ"]),
						'jqdd' =>	iconv("GBK", "UTF-8", $row["JQDD"]),
						'bjnr' =>	iconv("GBK", "UTF-8", $row["BJNR"]),
						'jqlbdm' =>	iconv("GBK", "UTF-8", $row["JQLBDM"]),
						'jqlxdm' =>	iconv("GBK", "UTF-8", $row["JQLXDM"]),
						'jqxldm' =>	iconv("GBK", "UTF-8", $row["JQXLDM"]),
						'jqdjdm' =>	iconv("GBK", "UTF-8", $row["JQDJDM"]),
						'stationhousecode' =>	iconv("GBK", "UTF-8", $row["STATIONHOUSECODE"]),
						'stationhouse' =>	iconv("GBK", "UTF-8", $row["STATIONHOUSE"]),
						'jqclzt' =>	iconv("GBK", "UTF-8", $row["JQCLZT"]),
						'jqzt' =>	iconv("GBK", "UTF-8", $row["JQZT"]),
						'sfyj' =>	iconv("GBK", "UTF-8", $row["SFYJ"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $men;
	}
	
}
?>
