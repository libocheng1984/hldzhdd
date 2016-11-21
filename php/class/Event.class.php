<?php


/**
 * class event
 * version: 1.0
 * 指挥调度类
 * author: carl
 * 2014/6/17
 * 
 * 此类定义交通信息中心全部方法
 * 使用前必须先引用TpmsDB.class.php和GlobalConfig.class.php
 */
class Event extends TpmsDB {

	/**
	 * getEvent
	 * 根据部门编号以及用户ID,警情状态查询用户所涉及的警情列表
	 * @param orgCode,$rybh,$jqclzt
	 * @return警情列表(终端调用)
	 */
	public function getEvent($orgCode,$rybh,$jqclzt) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.jqid,t.jqbh,t.jjlx,t.bjfs,t.jjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdz,t.dzid,t.jqdd,t.bjnr,t.jqlbdm,t.jqlxdm,t.jqdjdm,t.jqxldm,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as jqjqzb,to_char(t.rksj,'yyyy-MM-dd hh24:mi:ss') as rksj,to_char(t.gxsj,'yyyy-MM-dd hh24:mi:ss') as gxsj,t.cfjqbs,t.jqclzt,MDSYS.Sdo_Util.to_wktgeometry_varchar(t1.mhjqzb) as mhjqzb,o.orgname,o.orgcode,t2.gxdwdm,t2.hphm  from zdt_policeevent t
							  left join ZDT_PoliceEvent_Location t1 on  t.jqid = t1.jqid  left join ZDT_PoliceEvent_Admin    t2 on  t2.jqid = t.jqid
				  			  left join zdt_peprocess p on p.jqid=t.jqid  left join zdt_peprocess_command c on c.cjdbh=p.cjdbh left join zdb_organization o on c.cjdwdm = o.orgcode where 1 = 1 and t.jqzt!='2' and p.cjdzt='1' and c.rybh='$rybh' ";
//		if ($orgCode != "210200000000") {
//			$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
//		}
		if ($jqclzt != null) {
			$sql = $sql . " and t . jqclzt in ($jqclzt)";
		}
		$sql = $sql."group by t.jqid,t.jqbh,t.jjlx, t.bjfs,t.jjfs,t.bjsj,t.jqdz,t.dzid,t.jqdd,t.bjnr,t.jqlbdm,t.jqlxdm,t.jqdjdm,t.jqxldm,
MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb),t.rksj,t.gxsj,t.cfjqbs,t.jqclzt,MDSYS.Sdo_Util.to_wktgeometry_varchar(t1.mhjqzb),o.orgname,o.orgcode,t2.gxdwdm,t2.hphm ";
		$sql = $sql . " order by t.jqclzt asc,t.gxsj desc";
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
					'mhjqzb' => iconv("GBK", "UTF-8", $row["MHJQZB"])
				);

				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$count = count($mens);

		if (!$bRet)
			$res = array (
				'result' => 'false',
				'errmsg' => $errMsg,
				'points' => $mens
			);
		else
			$res = array (
				'result' => 'true',
				'errmsg' => '',
				'points' => $mens
			);

		//$res = new PolicePoints($lt, $mens);
		return $res;
	}
	
	/**
	 * getWebEvent
	 * 根据部门编号以及更新时间实时查询警情数据
	 * @param orgCode,$lastTime,$jqclzt
	 * @return 增量刷新数据
	 */
	public function getWebEvent($orgCode, $lastTime, $jqclzt,$jjrbh) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select rec.JJRBH,r.bjdh,t.jqzk,t.jqid,t.jqbh,t.jjlx,t.bjfs,t.jjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdz,t.dzid,t.jqdd,t.bjnr,t.jqlbdm,t.jqlxdm,t.jqdjdm,t.jqxldm,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as jqjqzb,to_char(t.rksj,'yyyy-MM-dd hh24:mi:ss') as rksj,to_char(t.gxsj,'yyyy-MM-dd hh24:mi:ss') as gxsj,t.cfjqbs,t.jqclzt,t.jqzt,t.sfyj,MDSYS.Sdo_Util.to_wktgeometry_varchar(t1.mhjqzb) as mhjqzb,t.sfqr,o.orgname,o.orgcode,t2.gxdwdm,t2.hphm  from zdt_policeevent t" .
				"  left join ZDT_PoliceEvent_Location t1 on  t.jqid = t1.jqid  left join ZDT_PoliceEvent_Admin    t2 on  t2.jqid = t.jqid" .
				"  left join ZDT_PoliceEvent_Reporter r on r.jqid=t.jqid".
				"  left join ZDT_PoliceEvent_Receiver rec on rec.jqid=t.jqid".
				"  left join zdb_organization  o on t2.gxdwdm = o.orgcode where 1=1 ";
		if ($lastTime != null) {
			$sql = $sql . " and t . gxsj > to_date('$lastTime','yyyy-MM-dd hh24:mi:ss')";
		}
		$array = explode(",", $orgCode);
		$codeSql ="";
		for ($i=0;$i<count($array);$i++) {
			$code = $array[$i];
			if ($code != GlobalConfig :: getInstance()->dsdm."00000000"){ //modify by carl
				if($codeSql==""){
					$codeSql = $codeSql . " o.parenttreepath like '%$code%' or o.orgCode = '$code' ";
				}else{
					$codeSql = $codeSql . "or o.parenttreepath like '%$code%' or o.orgCode = '$code' ";
				}
				$codeSql = $codeSql."or t.bmbh='$code' ";
			}else{
				if($codeSql==""){
					$codeSql = $codeSql." t.bmbh='".GlobalConfig :: getInstance()->dsdm."00000000' ";
				}else{
					$codeSql = $codeSql."and t.bmbh='".GlobalConfig :: getInstance()->dsdm."00000000' ";
				}
				
			}
		}
		if($codeSql!=""){
			$sql = $sql." and (".$codeSql.")";
		}
//		if ($orgCode != "210200000000") {
//			$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode' or t.jqclzt='1')";
//		}
		if ($jqclzt != null) {
			$sql = $sql . " and t . jqclzt in ($jqclzt)";
		}
		if ($jjrbh != null) {
			$sql = $sql . " and rec . jjrbh ='$jjrbh' ";
		}
		$sql = $sql . " order by t.gxsj desc";
		//echo $sql;
		//return;
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
					'sfyj' => iconv("GBK", "UTF-8", $row["SFYJ"]),
					'sfqr' => iconv("GBK", "UTF-8", $row["SFQR"])
				);

				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$count = count($mens);
		$lt = null;
		if ($count > 0) {
			$lt = $mens[0]['gxsj'];
		}else{
			$lt = $lastTime;
		}

		if (!$bRet)
			$res = array (
				'result' => 'false',
				'errmsg' => $errMsg,
				'lastTime' => $lt,
				'points' => $mens
			);
		else
			$res = array (
				'result' => 'true',
				'errmsg' => '',
				'lastTime' => $lt,
				'points' => $mens
			);

		//$res = new PolicePoints($lt, $mens);
		return $res;
	}

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
	 * getEventByid
	 * 根据终端人员编号和警情ID查询最新警情（只供终端调用）
	 * 返回终端需要字段
	 * @param $jqid,$rybh
	 * @return 警情对象
	 */
	public function getEventByid($jqid,$rybh) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.jqid,t.jqbh,t.jjlx,t.jjfs,t.bjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdz, t.jqdd, t.bjnr, t.jqlbdm,t.jqlxdm,t.jqxldm,t.jqdjdm,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as jqjqzb,to_char(t.rksj,'yyyy-MM-dd hh24:mi:ss') as rksj,to_char(t.gxsj,'yyyy-MM-dd hh24:mi:ss') as gxsj,t.cfjqbs,t.jqclzt,t1.bjrxm,t1.bjrsfzh,t1.bjrxbdm,t1.bjdh,t1.lxdh,t1.bjdhyhxm,t1.bjdhyhdz,t2.jjrbh,t2.jjrxm,to_char(t2.jjsj,'yyyy-MM-dd hh24:mi:ss') as jjsj,to_char(t2.hzsj,'yyyy-MM-dd hh24:mi:ss') as hzsj,t3.gxdwdm,t3.hphm,t3.xzqh,t4.ywbzxl,t4.bjchpzldm,t4.bjcph,t4.bkrs,fb_case.ssrs,fb_case.swrs,t4.sfsw,t4.sfswybj,t4.jqztdm,t4.zagjdm,t4.hzdjdm,t4.qhjzjgdm,t4.hzcsdm,t4.qhjzqkms,t4.plqk,t4.qhwdm,t4.ywty,t4.sfswhcl,MDSYS.Sdo_Util.to_wktgeometry_varchar(t5.mhjqzb) as mhjqzb,t6.cjdbh,t7.zlbh,t7.rybh,t7.xm,to_char(t7.zljssj, 'yyyy-MM-dd hh24:mi:ss') as zljssj,to_char(t7.ddxcsj, 'yyyy-MM-dd hh24:mi:ss') as ddxcsj,t7.cjld,t7.zlnr,fb_case.jqcljg,org.orgname,fb_case.zhrs,fb_case.jjssqk,fb_case.jzrs,fb_case.jjfvrs,fb_case.jjetrs,fb.belong,fb.scene from zdt_policeevent t
		left join ZDT_PoliceEvent_Reporter t1 on t.jqid=t1.jqid left join ZDT_PoliceEvent_Receiver t2 on t.jqid=t2.jqid left join ZDT_PoliceEvent_Admin t3 on t.jqid=t3.jqid left join ZDB_Organization org on t3.gxdwdm=org.orgcode left join ZDT_PoliceEvent_AddInfo t4 on t.jqid=t4.jqid left join ZDT_PoliceEvent_Location t5 on t.jqid=t5.jqid left join ZDT_PeProcess t6 on t.jqid=t6.jqid left join ZDT_PeProcess_Command t7 on t6.cjdbh =t7.cjdbh left join ZDT_PEFEEDBACK fb on fb.cjdbh=t6.cjdbh left join ZDT_PeFeedback_Case fb_case on t6.cjdbh = fb_case.cjdbh where t6.cjdzt='1' and t7.zlbs='1' and t.jqid='$jqid' order by t7.zlxdsj desc";
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
				$zlbh = iconv("GBK", "UTF-8", $row["ZLBH"]);
				$array = $this->getDispatchMenByCjdbh($cjdbh,$zlbh);
				$zlbhArr = $this->getPeProcessCommandByCjdbh($cjdbh,$rybh);
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
					'cjld' => iconv("GBK", "UTF-8", $row["CJLD"]),
					'zhrs' => iconv("GBK", "UTF-8", $row["ZHRS"]),
					'jjssqk' => iconv("GBK", "UTF-8", $row["JJSSQK"]),
					'jzrs' => iconv("GBK", "UTF-8", $row["JZRS"]),
					'jjfvrs' => iconv("GBK", "UTF-8", $row["JJFVRS"]),
					'jjetrs' => iconv("GBK", "UTF-8", $row["JJETRS"]),
					'belong' => iconv("GBK", "UTF-8", $row["BELONG"]),
					'scene' => iconv("GBK", "UTF-8", $row["SCENE"]),
					'commandRecords'=>$zlbhArr
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
	 * getEventByid
	 * 根据终端人员编号和警情ID查询最新警情
	 * 返回终端需要字段
	 * @param $jqid,$rybh
	 * @return 警情对象
	 */
	public function getFeedBackDetailByid($jqid) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.jqid,t.jqbh,t.jqdz,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as jqjqzb,t.jqclzt,fb_case.ssrs,fb_case.swrs,fb_case.jqcljg,org.orgcode,org.orgname,fb_case.zhrs,fb_case.jjssqk,fb_case.jzrs,fb_case.jjfvrs,fb_case.jjetrs,fb.belong,fb.scene,t7.cjdbh,t7.zlbh,t7.cjld from zdt_policeevent t
		left join ZDT_PoliceEvent_Admin t3 on t.jqid=t3.jqid left join ZDB_Organization org on t3.gxdwdm=org.orgcode left join ZDT_PeProcess t6 on t.jqid=t6.jqid left join ZDT_PeProcess_Command t7 on t6.cjdbh =t7.cjdbh left join ZDT_PEFEEDBACK fb on fb.cjdbh=t6.cjdbh left join ZDT_PeFeedback_Case fb_case on t6.cjdbh = fb_case.cjdbh where t6.cjdzt='1' and t7.zlbs='1' and t.jqid='$jqid' order by t7.zlxdsj desc";
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
					'jqdd' => iconv("GBK", "UTF-8", $row["JQDZ"]),
					'jqjqzb' => iconv("GBK", "UTF-8", $row["JQJQZB"]),
					'jqclzt' => iconv("GBK", "UTF-8", $row["JQCLZT"]),
					'cjdbh' => iconv("GBK", "UTF-8", $row["CJDBH"]),
					'zlbh' => iconv("GBK", "UTF-8", $row["ZLBH"]),
					'ssrs' => iconv("GBK", "UTF-8", $row["SSRS"]),
					'swrs' => iconv("GBK", "UTF-8", $row["SWRS"]),
					'cljg' => iconv("GBK", "UTF-8", $row["JQCLJG"]),
					'orgcode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgname' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'zhrs' => iconv("GBK", "UTF-8", $row["ZHRS"]),
					'jjssqk' => iconv("GBK", "UTF-8", $row["JJSSQK"]),
					'jzrs' => iconv("GBK", "UTF-8", $row["JZRS"]),
					'jjfvrs' => iconv("GBK", "UTF-8", $row["JJFVRS"]),
					'jjetrs' => iconv("GBK", "UTF-8", $row["JJETRS"]),
					'belong' => iconv("GBK", "UTF-8", $row["BELONG"]),
					'scene' => iconv("GBK", "UTF-8", $row["SCENE"]),
					'cjld' => iconv("GBK", "UTF-8", $row["CJLD"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $men;
	}
	
	/**
	 * getDispatchMenByCjdbh
	 * 根据cjdbh查询出动警力表信息
	 * @param $cjdbh
	 * @return 警力对象组
	 */
	public function getDispatchMenByCjdbh($cjdbh,$zlbh){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.cjdbh,t.zlbh,t.dwdm,t.rybh,t.xm from ZDT_PeProcess_DispatchMen t where t.cjdbh='$cjdbh' and t.zlbh='$zlbh'";
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
	 * getDispatchMenByCjdbh
	 * 根据cjdbh查询出动警力表信息
	 * @param $cjdbh
	 * @return 警力对象组
	 */
	public function getPeProcessCommandByCjdbh($cjdbh,$rybh){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.zlbh,t.rybh,t.cljg,t.zlnr,t.xm,to_char(t.zljssj, 'yyyy-MM-dd hh24:mi:ss') as zljssj,to_char(t.ddxcsj, 'yyyy-MM-dd hh24:mi:ss') as ddxcsj, to_char(t.clwbsj, 'yyyy-MM-dd hh24:mi:ss') as clwbsj,t.cjqk,t.cljgdm,t.cdclqk,t.cdryqk,t.jqztdm,t.zlbs from ZDT_PeProcess_Command t where t.cjdbh = '$cjdbh' and t.rybh='$rybh'";
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
					'zlbh' => iconv("GBK", "UTF-8", $row["ZLBH"]),
					'rybh' => iconv("GBK", "UTF-8", $row["RYBH"]),
					'xm' => iconv("GBK", "UTF-8", $row["XM"]),
					'zljssj' => iconv("GBK", "UTF-8", $row["ZLJSSJ"]),
					'ddxcsj' => iconv("GBK", "UTF-8", $row["DDXCSJ"]),
					'clwbsj' => iconv("GBK", "UTF-8", $row["CLWBSJ"]),
					'cljg' => iconv("GBK", "UTF-8", $row["CLJG"]),
					'cjqk' => iconv("GBK", "UTF-8", $row["CJQK"]),
					'cljgdm' => iconv("GBK", "UTF-8", $row["CLJGDM"]),
					'cdclqk' => iconv("GBK", "UTF-8", $row["CDCLQK"]),
					'cdryqk' => iconv("GBK", "UTF-8", $row["CDRYQK"]),
					'jqztdm' => iconv("GBK", "UTF-8", $row["JQZTDM"]),
					'zlbs' => iconv("GBK", "UTF-8", $row["ZLBS"]),
					'zlnr' => iconv("GBK", "UTF-8", $row["ZLNR"])
				);
				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);

		return $mens;
	}
	
/**
	 * reLoadEventStatus
	 * 终端转派警情
	 * @param $jqid
	 * @return arr数组
	 */
	public function reLoadEventStatus($jqid,$zlbh) {
		$bRet = true;
		$errMsg = '';
		
		if ($this->dbconn == null)
		$this->dbconn = $this->LogonDB();
			$cljg = iconv("UTF-8", "GBK", "警情移交");
			$sql_event = "update  ZDT_PoliceEvent set gxsj=sysdate, jqclzt='1',sfyj='1' where jqid='$jqid';";
			$sql_command = "update ZDT_PeProcess_Command set CLWBSJ=sysdate,JQZTDM='5',CLJG='$cljg' where zlbh in ($zlbh);";
			$sql = "begin ";
			$sql .= $sql_event;
			$sql .= $sql_command;
			$sql .= " end;";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		
		$event = $this->getEventReloadDisposalByid($jqid);
		if($event['jqid']){
			$url = 'http://192.168.20.215:9999/lbs';
			$params = "operation=FeedbackInfo_AddPoliceEvent_v001&license=a756244eb0236bdc26061cb6b6bdb481&content=";
			$this->outEventReceiveDisposal($url,$params,$event['jqid'],$event['jqbh'],$event['jjlx'],$event['jjfs'],$event['bjfs'],$event['bjsj'],$event['jqdd'],$event['bjnr'],$event['jqlbdm'],$event['jqlxdm'],$event['jqxldm'],$event['jqdjdm'],"","",$event['stationhousecode'],$event['stationhouse'],"",$event['jqclzt'],$event['jqzt'],$event['sfyj']);
		}
		if (!$bRet)
			$arr = array('result' =>'false', 'errmsg' =>$errMsg);
		else
			$arr = array('result' =>'true', 'errmsg' =>'转派成功');
	
	
		
		return $arr;
	}
	
	/**
	 * updateEventSfqr
	 * 领导确认重大警情
	 * @param $jqid
	 * @return arr数组
	 */
	public function updateEventSfqr($jqid) {
		$bRet = true;
		$errMsg = '';
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		if ($this->dbconn == null)
		$this->dbconn = $this->LogonDB();
			$sql = "update  ZDT_PoliceEvent set gxsj=sysdate, sfqr='1' where jqid='$jqid'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '确认失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if ($bRet)
			$result = array('result' =>$bRet,'errmsg' =>'');
		else
			$result = array('result' =>$bRet, 'errmsg' =>$errMsg);
		
		return $result;
	}
	
	/**
	 * updateEventStatus
	 * 终端修改警情状态
	 * @param $jqid
	 * @return arr数组
	 */
	public function updateEventStatus($jqid,$cjdbh,$status,$jqjqzb,$userId,$userName) {
		$bRet = true;
		$errMsg = '';
		
		$event = $this->getEventSimpleByid($jqid);
		if($event['jqzt']=="2"){
			$arr = array('result' =>'false', 'errmsg' =>'该警情已经撤销');
			return $arr;	
		}
		if($status=="5"){
			if($event['jqclzt']!="4"){
				if($event['jqclzt']=="5"){
					$arr = array('result' =>'false', 'errmsg' =>'该警情已经结束');
					return $arr;	
				}else{
					$arr = array('result' =>'false', 'errmsg' =>'不允许提前结束警情');
					return $arr;
				}
			}
			$feedBackCount = $this->getFeedbackSize($jqid);
			if(!$feedBackCount){
				$arr = array('result' =>'false', 'errmsg' =>'请先反馈处警信息');
				return $arr;
			}
			$processEntity = $this->getProcessByCjdbh($cjdbh);
			if($processEntity['cjdzt']=="2"){
				$arr = array('result' =>'false', 'errmsg' =>'该警情已经失效');
				return $arr;
			}
//			$feedBackRecod = $this->getPeFeedbackByid($jqid);
//			if(!($feedBackRecod&&$feedBackRecod['jqcljg']&&$feedBackRecod['cjld'])){
//				$arr = array('result' =>'false', 'errmsg' =>'处理结果或处警领导不能为空');
//				return $arr;
//			}
		}
		
		if($status>$event['jqclzt']){
			if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
				$sql = "update  ZDT_PoliceEvent set gxsj=sysdate, jqclzt='$status'";
			if ($jqjqzb != "") {
				$sql = $sql . ", jqjqzb=sdo_geometry('$jqjqzb',4326) ";
                                $x="";
                                $y="";
                                $point = str_replace(")", "", str_replace("(", "", str_replace("POINT", "", $jqjqzb)));
								//echo $point;
                                $pointArr=explode(" ", trim($point));
                                if(count($pointArr)==2)
                                {
                                    $x=$pointArr[0];
                                    $y=$pointArr[1];
                                    $zrqarr=$this->getOrgbyAir("SZZRQ", $x, $y);//取责任区 
                                    $xqarr=$this->getOrgbyAir("SZXLQ", $x, $y);//取巡区 
                                    $zrqmc= iconv("UTF-8", "GBK//TRANSLIT", $zrqarr['orgname']);
                                    $zrqcode= $zrqarr['orgcode'];
                                    $xqmc= iconv("UTF-8", "GBK//TRANSLIT", $xqarr['orgname']);
									$xqcode= $xqarr['orgcode'];
                                    $sqcode= $xqarr['orgcode'];
                                    $sql .=  ",FK_ZRQBM='$zrqcode',FK_ZRQMC='$zrqmc',FK_XJBMMC='$xqmc',FK_XJBMBM='$xqcode',fk_zbx='$x',fk_zby='$y' ";
                                }
			}
			if($status=="4"){
				$sql = $sql . ", jqzbly='1' ";
			}
			if($status=="5"){
				$sql = $sql . ", jsjqly='1' ";
			}
			$sql = $sql."where jqid='$jqid'";
			//echo $sql;
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@ oci_execute($stmt)) {
				$bRet = false;
				$errMsg = '修改失败';
			}
			oci_free_statement($stmt);
			if($status=="3"){
				$bRet = $this->updatePeProcessCommand_ZLJSSJ($jqid,$cjdbh);
				$event = $this->getEventReceiveDisposalByid($jqid);
				if($event['jqid']){
					$url = 'http://192.168.20.215:9999/lbs';
					$params = "operation=FeedbackInfo_AddPoliceEvent_v001&license=a756244eb0236bdc26061cb6b6bdb481&content=";
					$this->outEventReceiveDisposal($url,$params,$event['jqid'],$event['jqbh'],$event['jjlx'],$event['jjfs'],$event['bjfs'],$event['bjsj'],$event['jqdd'],$event['bjnr'],$event['jqlbdm'],$event['jqlxdm'],$event['jqxldm'],$event['jqdjdm'],$event['pjsj'],$event['zljssj'],$event['stationhousecode'],$event['stationhouse'],$event['cjdwdm'],$event['jqclzt'],$event['jqzt'],$event['sfyj']);
				}
			}else if($status=="4"){
				$bRet = $this->updatePeProcessCommand_DDXCSJ($jqid,$cjdbh);
			}else if($status=="5"){
				$bRet = $this->updatePeProcessCommand_JQJSSJ($jqid,$cjdbh);
			}
			oci_close($this->dbconn);
			
			if($status=="5"){
				$event = $this->getEventFeedbackDetailByid($jqid);
				$resoutEvent = $this->getEventFeedbackPictureByid($jqid);
				if($event['jqid']&&$event['jqbh']&&$event['jqjssj']){
					$url = 'http://192.168.20.215:9999/lbs';
					$params = "operation=FeedbackInfo_AddFeedbackInfo_v001&license=a756244eb0236bdc26061cb6b6bdb481&content=";
					$event['jqcljg'] = isset($event['jqcljg'])?$event['jqcljg']:"警情处理完毕";
					$sysTime = date('Y-m-d H:i:s',time());
					$this->outEventFeedBack($url,$params,$event['jqid'],$event['jqbh'],"",$event['jjlx'],$event['jjfs'],$event['bjfs'],$event['bjsj'],$event['jqdz'],$event['jqdd'],$event['bjnr'],$event['jqlbdm'],$event['jqlxdm'],$event['jqxldm'],$event['jqdjdm'],$event['jqzk'],$event['jqzt'],$event['stationhousecode'],$event['stationhouse'],$event['sfyj'],$event['jqfssj'],$event['ddxcsj'],$event['jqjssj'],$event['pjsj'],$event['xzqh'],$event['fkdwdm'],$event['fkybh'],$event['fkyxm'],$event['belong'],$event['scene'],"","",$event['zhrs'],$event['jjrs'],$event['ssrs'],$event['swrs'],$event['jqcljg'],$event['jjfvrs'],$event['jjetrs'],$event['jjrs'],$event['cdclqk'],$event['bjlxdm'],$event['cdryqk'],"",$event['zljssj'],
					"","","","","",$event['jqcljg'],$event['jjssqk'],$event['zbx'],$event['zby'],"0","1",$sysTime,$sysTime,$event['zrqCode'],$event['zrqName'],$event['pjfs'],$event['jqzbly'],$event['jsjqly'],$event['xjbmbm'],$event['xjbmmc']);
					//$uploadSrc = GlobalConfig :: getInstance()->upload_src;
					$this->outEventFeedBackPicture($resoutEvent);
				}
			}
			
			if (!$bRet)
				$arr = array('result' =>'false', 'errmsg' =>$errMsg);
			else
				$arr = array('result' =>'true', 'errmsg' =>'操作成功');
		}else if($event['jqclzt']=="4"){
			$arr = array('result' =>'false', 'errmsg' =>'警情正在处理');
		}else if($event['jqclzt']=="3"){
			$arr = array('result' =>'false', 'errmsg' =>'警情已出警');
		}else if($event['jqclzt']=="5"){
			$arr = array('result' =>'false', 'errmsg' =>'警情已结束');
		}
		return $arr;
	}
	
	/**
	 * overEventStatus
	 * 终端修改警情状态
	 * @param $jqid
	 * @return arr数组
	 */
	public function overEventStatus($jqid,$cjdbh,$status,$jqdd,$jqjqzb,$userId,$userName) {
		$bRet = true;
		$errMsg = '';
		
		$event = $this->getEventSimpleByid($jqid);
		if($event['jqzt']=="2"){
			$arr = array('result' =>'false', 'errmsg' =>'该警情已经撤销');
			return $arr;	
		}
		if($status=="5"){
			if($event['jqclzt']=="5"){
					$arr = array('result' =>'false', 'errmsg' =>'该警情已经结束');
					return $arr;	
			}
			$feedBackCount = $this->getFeedbackSize($jqid);
			if(!$feedBackCount){
				$arr = array('result' =>'false', 'errmsg' =>'请先反馈处警信息');
				return $arr;
			}
			$processEntity = $this->getProcessByCjdbh($cjdbh);
			if($processEntity['cjdzt']=="2"){
				$arr = array('result' =>'false', 'errmsg' =>'该警情已经失效');
				return $arr;
			}
			//$feedBackRecod = $this->getPeFeedbackByid($jqid);
			//if(!($feedBackRecod&&$feedBackRecod['jqcljg']&&$feedBackRecod['cjld'])){
				//echo encodeJson($feedBackRecod);
				//$arr = array('result' =>'false', 'errmsg' =>'处理结果或处警领导不能为空');
				//return $arr;
			//}
		}
		
			if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
				$sql = "update  ZDT_PoliceEvent set gxsj=sysdate, jqclzt='$status',jsjqly='2' ";
			if ($jqjqzb != "") {
				$sql = $sql . ", jqjqzb=sdo_geometry('$jqjqzb',4326) ";
                                $x="";
                                $y="";
								//echo $jqjqzb;
								//POINT(121.6176055695 38.915592789246)
                                $point = str_replace(")", "", str_replace("(", "", str_replace("POINT", "", $jqjqzb)));
                                $pointArr=explode(" ", trim($point));
								//echo $pointArr[0];
								//echo $pointArr[1];
                                if(count($pointArr)==2)
                                {
                                    $x=$pointArr[0];
                                    $y=$pointArr[1];
                                    $zrqarr=$this->getOrgbyAir("SZZRQ", $x, $y);//取责任区 
                                    $xqarr=$this->getOrgbyAir("SZXLQ", $x, $y);//取巡区 
                                    $zrqmc= iconv("UTF-8", "GBK//TRANSLIT", $zrqarr['orgname']);
                                    $zrqcode= $zrqarr['orgcode'];
                                    $xqmc= iconv("UTF-8", "GBK//TRANSLIT", $xqarr['orgname']);
                                    $sqcode= $xqarr['orgcode'];
                                    $sql .=  ",FK_ZRQBM='$zrqcode',FK_ZRQMC='$zrqmc',FK_XJBMMC='$xqmc',FK_XJBMBM=(select code from ZHDD_XLQY where name='$xqmc'),fk_zbx='$x',fk_zby='$y' ";
                                }
			}
			if($event['jqjqzb']==""){
				$sql = $sql . ", jqzbly='2' ";
			}
			if ($jqdd != "") {
				$sql = $sql . ", jqdz='$jqdd' ";
			}
			$sql = $sql."where jqid='$jqid'";
			//echo $sql;
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@ oci_execute($stmt)) {
				$bRet = false;
				$errMsg = '修改失败';
			}
			oci_free_statement($stmt);
			$bRet = $this->updatePeProcessCommand_JQJSSJ($jqid,$cjdbh);
			oci_close($this->dbconn);
			
			if($status=="5"){
				$event = $this->getEventFeedbackDetailByid($jqid);
				$resoutEvent = $this->getEventFeedbackPictureByid($jqid);
				if($event['jqid']&&$event['jqbh']&&$event['jqjssj']){
					$url = 'http://10.78.17.154:9999/lbs';
					$params = "operation=FeedbackInfo_AddFeedbackInfo_v001&license=a756244eb0236bdc26061cb6b6bdb481&content=";
					$event['jqcljg'] = isset($event['jqcljg'])?$event['jqcljg']:"警情处理完毕";
					$sysTime = date('Y-m-d H:i:s',time());
					$this->outEventFeedBack($url,$params,$event['jqid'],$event['jqbh'],"",$event['jjlx'],$event['jjfs'],$event['bjfs'],$event['bjsj'],$event['jqdz'],$event['jqdd'],$event['bjnr'],$event['jqlbdm'],$event['jqlxdm'],$event['jqxldm'],$event['jqdjdm'],$event['jqzk'],$event['jqzt'],$event['stationhousecode'],$event['stationhouse'],$event['sfyj'],$event['jqfssj'],$event['ddxcsj'],$event['jqjssj'],$event['pjsj'],$event['xzqh'],$event['fkdwdm'],$event['fkybh'],$event['fkyxm'],$event['belong'],$event['scene'],"","",$event['zhrs'],$event['jjrs'],$event['ssrs'],$event['swrs'],$event['jqcljg'],$event['jjfvrs'],$event['jjetrs'],$event['jjrs'],$event['cdclqk'],$event['bjlxdm'],$event['cdryqk'],"",$event['zljssj'],
					"","","","","",$event['jqcljg'],$event['jjssqk'],$event['zbx'],$event['zby'],"0","1",$sysTime,$sysTime,$event['zrqCode'],$event['zrqName'],$event['pjfs'],$event['jqzbly'],$event['jsjqly'],$event['xjbmbm'],$event['xjbmmc']);
					//$uploadSrc = GlobalConfig :: getInstance()->upload_src;
					$this->outEventFeedBackPicture($resoutEvent);
				}
			}
			
			if (!$bRet)
				$arr = array('result' =>'false', 'errmsg' =>$errMsg);
			else
				$arr = array('result' =>'true', 'errmsg' =>'操作成功');
		return $arr;
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

		$sql = "select t1.xzqh, t.jqid,t.jqbh,t.jjlx,t.jjfs,t.bjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdz, t.jqdd, t.bjnr, t.jqlbdm,t.jqlxdm,t.jqxldm,t.jqdjdm,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as jqjqzb,to_char(t.rksj,'yyyy-MM-dd hh24:mi:ss') as rksj,to_char(t.gxsj,'yyyy-MM-dd hh24:mi:ss') as gxsj,t.cfjqbs,t.jqclzt,t.jqzt from zdt_policeevent t left join ZDT_PoliceEvent_Admin t1 on t1.jqid=t.jqid where 1=1 and t.jqid='$jqid'";
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
					'jqzt' => iconv("GBK", "UTF-8", $row["JQZT"])
				);
				//array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		return $men;
	}
	
	public function getPeFeedbackByid($jqid) {
		$bRet = true;
		$errMsg = "";
		$men = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.jqid,t.jqclzt,t.jqbh,c.cjld,f.jqcljg from zdt_policeevent t left join zdt_peprocess p on p.jqid=t.jqid left join zdt_peprocess_command c on c.cjdbh=p.cjdbh " .
				" left join zdt_pefeedback_case f on f.jqid=t.jqid where p.cjdzt='1' and c.zlbs='1' and jqid='$jqid'";
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
					'jqclzt' => iconv("GBK", "UTF-8", $row["JQCLZT"]),
					'cjld' => iconv("GBK", "UTF-8", $row["CJLD"]),
					'jqcljg' => iconv("GBK", "UTF-8", $row["JQCLJG"])					
				);
				//array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		return $men;
	}
	
	/**
	* insertPeFeedbackCase
	* 添加或修改反馈案件表中的反馈信息（上传图片时终端调用接口）
	* 网页不调用这个接口
	*/
	public function insertPeFeedbackCase($jqid,$cjdbh,$zlbh, $jqcljg, $type, $url, $ssrs, $swrs, $zhrs, $jjss, $jzrs, $jjfvrs, $jjetrs) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$feedBackCount = $this->getFeedbackCount($jqid);
		$jqcljg = iconv("UTF-8","GBK",$jqcljg);
			if ($feedBackCount) {
				$sql = "update zdt_pefeedback_case set ";
				if($cjdbh){
					$sql = $sql."cjdbh='$cjdbh',";
				}
				if($zhrs){
					$sql = $sql."zhrs='$zhrs',";
				}
				if($jjfvrs){
					$sql = $sql."jjfvrs='$jjfvrs',";
				}
				if($jjetrs){
					$sql = $sql."jjetrs='$jjetrs',";
				}
				if($jqcljg){
					$sql = $sql."jqcljg='$jqcljg',";
				}
				if($ssrs){
					$sql = $sql."ssrs='$ssrs',";
				}
				if($swrs){
					$sql = $sql."swrs='$swrs',";
				}
				if($jjss){
					$sql = $sql."jjssqk='$jjss',";
				}
				if($jzrs){
					$sql = $sql."jzrs='$jzrs',";
				}
				$sql = rtrim($sql, ','); 
				$sql = $sql." where jqid='$jqid'";
			}else{
				$sql = "insert into zdt_pefeedback_case (jqid,cjdbh,zhrs,jzrs,jjfvrs,jjetrs,jqcljg,ssrs,swrs,jjssqk) values ('$jqid','$cjdbh','$zhrs','$jzrs','$jjfvrs','$jjetrs','$jqcljg','$ssrs','$swrs','$jjss')";
			}
			//echo $sql;
		//$sql = "insert into zdt_pefeedback_case (cjdbh,zhrs,jzrs,jjfvrs,jjetrs,jqcljg,ssrs,swrs,jjssqk) values ('$cjdbh','$zhrs','$jzrs','$jjfvrs','$jjetrs','$jqcljg','$ssrs','$swrs','$jjss')";
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
	* insertPeFeedbackResource
	* 添加图片或语音路径到数据库
	*/
	public function insertPeFeedbackResource($cjdbh,$zlbh, $type, $url) {
		$bRet = true;
		$resouceCount = $this->getResourceCount($url);
		if($resouceCount){
			return $bRet;	
		}
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "insert into ZDT_PeProcess_Resource (CJDBH,ZLBH,ZYLX,ZYDZ) values('$cjdbh','$zlbh','$type','$url')";
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
	* sendEventToTerminal 
	* 向终端发送指令信息 
	*/
	public function sendEventToTerminal($sendPid, $location, $jqid, $sendMsg, $receivePids, $gid) {

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

		$str = '{"message":{"comCode":"18","codeId":"' . $date . '"},"result":{"sendPid":"' . $sendPid . '","location":"' . $location . '","jqid":"' . $jqid . '","sendMsg":"' . $sendMsg . '","receivePids":"' . $receivePids . '","gid":"' . $gid . '"}}';

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
		if ($message == 00 && mb_strlen($str3, 'UTF8') == $s1) {
			$value = 'true';
			$message1 = '发送成功！';
		} else
			if ($message == 02) {
				$value = 'faile';
				$message1 = '非法字符串';
			} else
				if ($message == 03) {
					$value = 'faile';
					$message1 = '命令码异常';
				} else
					if ($message == 04) {
						$value = 'faile';
						$message1 = '设备校验异常';
					} else
						if ($message == 05) {
							$value = 'faile';
							$message1 = '设备不在线';
						}

		//判断发送失败并记录
		if ($message == 02 || $message == 03 || $message == 04 || $message == 05) {
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
				'errmsg' => $strPoliceIdMessage1
			);
			return $datas;
		} else {
			$datas = array (
				'result' => 'true',
				'errmsg' => '发送成功'
			);
			return $datas;
		}
	}

	/**
	 * getFileByCjdbh
	 * 根据处警单编号查询资源地址
	 */
	public function getFileByCjdbh($cjdbh,$zlbh) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.zylx,t.zydz,t.cjdbh,t.zlbh,t1.jqcljg from ZDT_PeProcess_Resource t left join ZDT_PeFeedback_Case t1 on t.cjdbh=t1.cjdbh where t.cjdbh='$cjdbh' and t.zlbh='$zlbh'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			$mens = array ();
			$PHP_SELF = $_SERVER['PHP_SELF'];
			$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
			$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
			$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/')); //公安网
			$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/') + 1);
			$url_base = 'http://' . $_SERVER['HTTP_HOST'] . $PHP_SELF;
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$url = iconv("GBK", "UTF-8", $row["ZYDZ"]);
				//$url = str_replace("..","php",$url);		//互联网
				$url = str_replace("//192.168.20.75/","",$url); //公安网
				$url = $url_base.$url;
				$men = array (
					'zylx' => iconv("GBK", "UTF-8", $row["ZYLX"]),
					'zydz' => $url,
					'cjdbh' => iconv("GBK", "UTF-8", $row["CJDBH"]),
					'zlbh' => iconv("GBK", "UTF-8", $row["ZLBH"]),
					'jqcljg' => iconv("GBK", "UTF-8", $row["JQCLJG"])
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
	
	/**
	 * getFileByCjdbh
	 * 根据处警单编号查询资源地址
	 */
	public function getFileByJqid($jqid) {
		$cjdObj = $this->getCjdbhById($jqid);
		if(!$cjdObj){
			$arr = array (
				'result' => 'false',
				'errmsg' => '未查询到资源信息'
			);
			return $arr;
		}
		$cjdbh = $cjdObj['cjdbh'];
		$zlbh = $cjdObj['zlbh'];
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.zylx,t.zydz,t.cjdbh,t.zlbh,t1.jqcljg from ZDT_PeProcess_Resource t left join ZDT_PeFeedback_Case t1 on t.cjdbh=t1.cjdbh where t.cjdbh='$cjdbh' and t.zlbh='$zlbh'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			$mens = array ();
			$PHP_SELF = $_SERVER['PHP_SELF'];
			$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
			$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
			$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/')); //公安网
			$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/') + 1);
			$url_base = 'http://' . $_SERVER['HTTP_HOST'] . $PHP_SELF;
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$url = iconv("GBK", "UTF-8", $row["ZYDZ"]);
				//$url = str_replace("..","php",$url);		//互联网
				$url = str_replace("//192.168.20.179/","",$url); //公安网
				$url = $url_base.$url;
				$men = array (
					'zylx' => iconv("GBK", "UTF-8", $row["ZYLX"]),
					'zydz' => $url,
					'cjdbh' => iconv("GBK", "UTF-8", $row["CJDBH"]),
					'zlbh' => iconv("GBK", "UTF-8", $row["ZLBH"]),
					'jqcljg' => iconv("GBK", "UTF-8", $row["JQCLJG"])
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
	
	/**
	* updatePeProcessCommand_DDXCSJ
	* 巡逻人员到达现场时根据cjdbh修改指令表中到达现场时间
	* 以及修改出动警员表中到达现场时间
	*/
	public function updatePeProcessCommand_DDXCSJ($jqid,$cjdbh) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update zdt_peprocess_command set DDXCSJ=sysdate,JQZTDM='4' where zlbh in (select t.zlbh from zdt_peprocess_command t,ZDT_PeProcess t1 where t.cjdbh=t1.cjdbh and t1.jqid='$jqid' and t.cjdbh='$cjdbh') ";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);
		$sql = "update ZDT_PeProcess_DispatchMen set DDXCSJ=sysdate where zlbh in (select t.zlbh from zdt_peprocess_command t,ZDT_PeProcess t1 where t.cjdbh=t1.cjdbh and t1.jqid='$jqid' and t.cjdbh='$cjdbh') ";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);
		return $bRet;

	}
	
	/**
	* updatePeProcessCommand_ZLJSSJ
	* 警情接收时根据cjdbh修改指令接收时间
	*/
	public function updatePeProcessCommand_ZLJSSJ($jqid,$cjdbh) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update zdt_peprocess_command set JQZTDM='3',ZLJSSJ=sysdate where zlbh in (select t.zlbh from zdt_peprocess_command t,ZDT_PeProcess t1 where t.cjdbh=t1.cjdbh and t1.jqid='$jqid' and t.cjdbh='$cjdbh') ";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);
		return $bRet;

	}
	
	/**
	* updatePeProcessCommand_JQJSSJ
	* 警情结束时根据cjdbh修改警情结束时间
	*/
	public function updatePeProcessCommand_JQJSSJ($jqid,$cjdbh) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update ZDT_PeProcess t set t.JQJSSJ=sysdate where  t.jqid='$jqid' and t.cjdbh='$cjdbh'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);
		$sql = "update ZDT_PeProcess_Command set CLWBSJ=sysdate,JQZTDM='5' where zlbh in (select t.zlbh from zdt_peprocess_command t,ZDT_PeProcess t1 where t.cjdbh=t1.cjdbh and t1.jqid='$jqid' and t.cjdbh='$cjdbh') ";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);
		return $bRet;

	}
	
	/**
	* getFeedbackById
	* 根据cjdbh查询反馈详细信息
	*/
	public function getFeedbackById($cjdbh){
		$bRet = true;
		$errMsg = "";
		$men = null;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select p.fkdbh,p.cjdbh,p.jqid,p.xzqhdm,p.fkdwdm,p.fkybh,p.fkyxm,c.zhrs,c.sars,c.jzrs,c.jzrssm,c.jjrs,c.jjrssm,c.tprs,c.ssrs,c.ssrssm,c.swrs,";
		$sql = $sql."c.swrssm,c.djyrs,c.cjryssrs,c.cjryswrs,c.jjssqk,c.whjjssqk,c.sfphxsaj,c.sfcczaaj,c.sfjjjf,c.lzscrs,c.jqcljg,c.jjfvrs,c.jjetrs,";
		$sql = $sql."f.hzdjdm,f.tqqkdm,f.ssqkms,f.hzyydm,f.zhsglxdm,f.qhwdm,f.qhjzjgdm,f.hzcsdm,to_char(f.dycdsj,'yyyy-MM-dd hh24:mi:ss') as dycdsj,";
		$sql = $sql."to_char(f.dydcsj,'yyyy-MM-dd hh24:mi:ss') as dydcsj,to_char(f.hcpmsj,'yyyy-MM-dd hh24:mi:ss') as hcpmsj,to_char(f.clsj,'yyyy-MM-dd hh24:mi:ss') as clsj,f.xczzh,";
		$sql = $sql."f.cdsqs,f.sfzddw,f.zddwbm,f.xlbmrs,t.jtsgxtdm,t.sfwhp,t.sgccyydm,t.njddm,t.lmzkdm,t.shjdcs,t.shfjdcs,t.dllxdm from ZDT_PeFeedback p";
		$sql = $sql." left join zdt_pefeedback_case c on p.cjdbh=c.cjdbh";
		$sql = $sql." left join zdt_pefeedback_fire f on p.cjdbh = f.cjdbh";
		$sql = $sql." left join zdt_pefeedback_traffic t on p.cjdbh = t.cjdbh where 1=1 and p.cjdbh='$cjdbh'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			//$mens = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'fkdbh' => iconv("GBK", "UTF-8", $row["FKDBH"]),
					'cjdbh' => iconv("GBK", "UTF-8", $row["CJDBH"]),
					'jqid' => iconv("GBK", "UTF-8", $row["JQID"]),
					'xzqhdm' => iconv("GBK", "UTF-8", $row["XZQHDM"]),
					'fkdwdm' => iconv("GBK", "UTF-8", $row["FKDWDM"]),
					'fkybh' => iconv("GBK", "UTF-8", $row["FKYBH"]),
					'fkyxm' => iconv("GBK", "UTF-8", $row["FKYXM"]),
					'zhrs' => iconv("GBK", "UTF-8", $row["ZHRS"]),
					'sars' => iconv("GBK", "UTF-8", $row["SARS"]),
					'jzrs' => iconv("GBK", "UTF-8", $row["JZRS"]),
					'jzrssm' => iconv("GBK", "UTF-8", $row["JZRSSM"]),	
					'jjrs' => iconv("GBK", "UTF-8", $row["JJRS"]),
					'jjrssm' => iconv("GBK", "UTF-8", $row["JJRSSM"]),	
					'tprs' => iconv("GBK", "UTF-8", $row["TPRS"]),
					'ssrs' => iconv("GBK", "UTF-8", $row["SSRS"]),
					'ssrssm' => iconv("GBK", "UTF-8", $row["SSRSSM"]),
					'swrs' => iconv("GBK", "UTF-8", $row["SWRS"]),
					'swrssm' => iconv("GBK", "UTF-8", $row["SWRSSM"]),
					'djyrs' => iconv("GBK", "UTF-8", $row["DJYRS"]),
					'cjryssrs' => iconv("GBK", "UTF-8", $row["CJRYSSRS"]),
					'cjryswrs' => iconv("GBK", "UTF-8", $row["CJRYSWRS"]),
					'jjssqk' => iconv("GBK", "UTF-8", $row["JJSSQK"]),
					'whjjssqk' => iconv("GBK", "UTF-8", $row["WHJJSSQK"]),
					'sfphxsaj' => iconv("GBK", "UTF-8", $row["SFPHXSAJ"]),
					'sfcczaaj' => iconv("GBK", "UTF-8", $row["SFCCZAAJ"]),
					'sfjjjf' => iconv("GBK", "UTF-8", $row["SFJJJF"]),
					'lzscrs' => iconv("GBK", "UTF-8", $row["LZSCRS"]),
					'jqcljg' => iconv("GBK", "UTF-8", $row["JQCLJG"]),
					'jjfvrs' => iconv("GBK", "UTF-8", $row["JJFVRS"]),
					'jjetrs' => iconv("GBK", "UTF-8", $row["JJETRS"]),
					'hzdjdm' => iconv("GBK", "UTF-8", $row["HZDJDM"]),
					'tqqkdm' => iconv("GBK", "UTF-8", $row["TQQKDM"]),
					'ssqkms' => iconv("GBK", "UTF-8", $row["SSQKMS"]),
					'hzyydm' => iconv("GBK", "UTF-8", $row["HZYYDM"]),
					'zhsglxdm' => iconv("GBK", "UTF-8", $row["ZHSGLXDM"]),
					'qhwdm' => iconv("GBK", "UTF-8", $row["QHWDM"]),
					'qhjzjgdm' => iconv("GBK", "UTF-8", $row["QHJZJGDM"]),
					'hzcsdm' => iconv("GBK", "UTF-8", $row["HZCSDM"]),
					'dycdsj' => iconv("GBK", "UTF-8", $row["DYCDSJ"]),
					'dydcsj' => iconv("GBK", "UTF-8", $row["DYDCSJ"]),
					'hcpmsj' => iconv("GBK", "UTF-8", $row["HCPMSJ"]),
					'clsj' => iconv("GBK", "UTF-8", $row["CLSJ"]),
					'xczzh' => iconv("GBK", "UTF-8", $row["XCZZH"]),
					'cdsqs' => iconv("GBK", "UTF-8", $row["CDSQS"]),
					'sfzddw' => iconv("GBK", "UTF-8", $row["SFZDDW"]),
					'zddwbm' => iconv("GBK", "UTF-8", $row["ZDDWBM"]),
					'xlbmrs' => iconv("GBK", "UTF-8", $row["XLBMRS"]),
					'jtsgxtdm' => iconv("GBK", "UTF-8", $row["JTSGXTDM"]),
					'sfwhp' => iconv("GBK", "UTF-8", $row["SFWHP"]),
					'sgccyydm' => iconv("GBK", "UTF-8", $row["SGCCYYDM"]),
					'njddm' => iconv("GBK", "UTF-8", $row["NJDDM"]),
					'lmzkdm' => iconv("GBK", "UTF-8", $row["LMZKDM"]),
					'shjdcs' => iconv("GBK", "UTF-8", $row["SHJDCS"]),
					'shfjdcs' => iconv("GBK", "UTF-8", $row["SHFJDCS"]),
					'dllxdm' => iconv("GBK", "UTF-8", $row["DLLXDM"])
				);
				//array_push($mens, $men);
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
	* getFeedbackById
	* 根据cjdbh查询反馈详细信息
	*/
	public function getFeedbackByJqid($jqid){
		$cjdObj = $this->getCjdbhById($jqid);
		if(!$cjdObj){
			$arr = array (
				'result' => 'false',
				'errmsg' => '未查询到反馈信息'
			);
			return $arr;
		}
		$cjdbh = $cjdObj['cjdbh'];
		$bRet = true;
		$errMsg = "";
		$men = null;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select p.fkdbh,p.cjdbh,p.jqid,p.xzqhdm,p.fkdwdm,p.fkybh,p.fkyxm,p.belong,p.scene,c.zhrs,c.sars,c.jzrs,c.jzrssm,c.jjrs,c.jjrssm,c.tprs,c.ssrs,c.ssrssm,c.swrs,";
		$sql = $sql."c.swrssm,c.djyrs,c.cjryssrs,c.cjryswrs,c.jjssqk,c.whjjssqk,c.sfphxsaj,c.sfcczaaj,c.sfjjjf,c.lzscrs,c.jqcljg,c.jjfvrs,c.jjetrs,";
		$sql = $sql."f.hzdjdm,f.tqqkdm,f.ssqkms,f.hzyydm,f.zhsglxdm,f.qhwdm,f.qhjzjgdm,f.hzcsdm,to_char(f.dycdsj,'yyyy-MM-dd hh24:mi:ss') as dycdsj,";
		$sql = $sql."to_char(f.dydcsj,'yyyy-MM-dd hh24:mi:ss') as dydcsj,to_char(f.hcpmsj,'yyyy-MM-dd hh24:mi:ss') as hcpmsj,to_char(f.clsj,'yyyy-MM-dd hh24:mi:ss') as clsj,f.xczzh,";
		$sql = $sql."f.cdsqs,f.sfzddw,f.zddwbm,f.xlbmrs,t.jtsgxtdm,t.sfwhp,t.sgccyydm,t.njddm,t.lmzkdm,t.shjdcs,t.shfjdcs,t.dllxdm from ZDT_PeFeedback p";
		$sql = $sql." left join zdt_pefeedback_case c on p.cjdbh=c.cjdbh";
		$sql = $sql." left join zdt_pefeedback_fire f on p.cjdbh = f.cjdbh";
		$sql = $sql." left join zdt_pefeedback_traffic t on p.cjdbh = t.cjdbh where 1=1 and p.cjdbh='$cjdbh'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			//$mens = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'fkdbh' => iconv("GBK", "UTF-8", $row["FKDBH"]),
					'cjdbh' => iconv("GBK", "UTF-8", $row["CJDBH"]),
					'jqid' => iconv("GBK", "UTF-8", $row["JQID"]),
					'xzqhdm' => iconv("GBK", "UTF-8", $row["XZQHDM"]),
					'fkdwdm' => iconv("GBK", "UTF-8", $row["FKDWDM"]),
					'fkybh' => iconv("GBK", "UTF-8", $row["FKYBH"]),
					'fkyxm' => iconv("GBK", "UTF-8", $row["FKYXM"]),
					'belong' => iconv("GBK", "UTF-8", $row["BELONG"]),
					'scene' => iconv("GBK", "UTF-8", $row["SCENE"]),
					'zhrs' => iconv("GBK", "UTF-8", $row["ZHRS"]),
					'sars' => iconv("GBK", "UTF-8", $row["SARS"]),
					'jzrs' => iconv("GBK", "UTF-8", $row["JZRS"]),
					'jzrssm' => iconv("GBK", "UTF-8", $row["JZRSSM"]),	
					'jjrs' => iconv("GBK", "UTF-8", $row["JJRS"]),
					'jjrssm' => iconv("GBK", "UTF-8", $row["JJRSSM"]),	
					'tprs' => iconv("GBK", "UTF-8", $row["TPRS"]),
					'ssrs' => iconv("GBK", "UTF-8", $row["SSRS"]),
					'ssrssm' => iconv("GBK", "UTF-8", $row["SSRSSM"]),
					'swrs' => iconv("GBK", "UTF-8", $row["SWRS"]),
					'swrssm' => iconv("GBK", "UTF-8", $row["SWRSSM"]),
					'djyrs' => iconv("GBK", "UTF-8", $row["DJYRS"]),
					'cjryssrs' => iconv("GBK", "UTF-8", $row["CJRYSSRS"]),
					'cjryswrs' => iconv("GBK", "UTF-8", $row["CJRYSWRS"]),
					'jjssqk' => iconv("GBK", "UTF-8", $row["JJSSQK"]),
					'whjjssqk' => iconv("GBK", "UTF-8", $row["WHJJSSQK"]),
					'sfphxsaj' => iconv("GBK", "UTF-8", $row["SFPHXSAJ"]),
					'sfcczaaj' => iconv("GBK", "UTF-8", $row["SFCCZAAJ"]),
					'sfjjjf' => iconv("GBK", "UTF-8", $row["SFJJJF"]),
					'lzscrs' => iconv("GBK", "UTF-8", $row["LZSCRS"]),
					'jqcljg' => iconv("GBK", "UTF-8", $row["JQCLJG"]),
					'jjfvrs' => iconv("GBK", "UTF-8", $row["JJFVRS"]),
					'jjetrs' => iconv("GBK", "UTF-8", $row["JJETRS"]),
					'hzdjdm' => iconv("GBK", "UTF-8", $row["HZDJDM"]),
					'tqqkdm' => iconv("GBK", "UTF-8", $row["TQQKDM"]),
					'ssqkms' => iconv("GBK", "UTF-8", $row["SSQKMS"]),
					'hzyydm' => iconv("GBK", "UTF-8", $row["HZYYDM"]),
					'zhsglxdm' => iconv("GBK", "UTF-8", $row["ZHSGLXDM"]),
					'qhwdm' => iconv("GBK", "UTF-8", $row["QHWDM"]),
					'qhjzjgdm' => iconv("GBK", "UTF-8", $row["QHJZJGDM"]),
					'hzcsdm' => iconv("GBK", "UTF-8", $row["HZCSDM"]),
					'dycdsj' => iconv("GBK", "UTF-8", $row["DYCDSJ"]),
					'dydcsj' => iconv("GBK", "UTF-8", $row["DYDCSJ"]),
					'hcpmsj' => iconv("GBK", "UTF-8", $row["HCPMSJ"]),
					'clsj' => iconv("GBK", "UTF-8", $row["CLSJ"]),
					'xczzh' => iconv("GBK", "UTF-8", $row["XCZZH"]),
					'cdsqs' => iconv("GBK", "UTF-8", $row["CDSQS"]),
					'sfzddw' => iconv("GBK", "UTF-8", $row["SFZDDW"]),
					'zddwbm' => iconv("GBK", "UTF-8", $row["ZDDWBM"]),
					'xlbmrs' => iconv("GBK", "UTF-8", $row["XLBMRS"]),
					'jtsgxtdm' => iconv("GBK", "UTF-8", $row["JTSGXTDM"]),
					'sfwhp' => iconv("GBK", "UTF-8", $row["SFWHP"]),
					'sgccyydm' => iconv("GBK", "UTF-8", $row["SGCCYYDM"]),
					'njddm' => iconv("GBK", "UTF-8", $row["NJDDM"]),
					'lmzkdm' => iconv("GBK", "UTF-8", $row["LMZKDM"]),
					'shjdcs' => iconv("GBK", "UTF-8", $row["SHJDCS"]),
					'shfjdcs' => iconv("GBK", "UTF-8", $row["SHFJDCS"]),
					'dllxdm' => iconv("GBK", "UTF-8", $row["DLLXDM"])
				);
				//array_push($mens, $men);
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
	* getFeedbackCount
	* 根据cjdbh查询反馈案件表count
	*/
	public function getFeedbackCount($jqid) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from zdt_pefeedback_case where jqid='$jqid'";
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
	* getFeedbackCount
	* 根据cjdbh查询反馈案件表count
	*/
	public function getFeedbackSize($jqid) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from zdt_pefeedback where jqid='$jqid'";
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
	 * insertPeFeedback
	 * 插入反馈基本信息
	 */
	public function insertPeFeedback($jqid,$cjdbh,$orgCode,$userId,$userName,$belong,$scene,$fkly){
		$bRet = true;
		$errMsg = '';
		$event = '';
		$event = $this->getEventSimpleByid($jqid);
		$feedBackSize = $this->getFeedbackSize($jqid);
		$userName = iconv("UTF-8","GBK",$userName);
		$xzqh = $event['xzqh'];
			if ($feedBackSize) {
				$sql = "update zdt_pefeedback set ";
				if($xzqh){
					$sql = $sql."XZQHDM='$xzqh',";
				}
				if($orgCode){
					$sql = $sql."FKDWDM='$orgCode',";
				}
				if($userId){
					$sql = $sql."FKYBH='$userId',";
				}
				if($userName){
					$sql = $sql."FKYXM='$userName',";
				}
				if($belong!=""){
					$sql = $sql."BELONG='$belong',";
				}
				if($scene!=""){
					$sql = $sql."SCENE='$scene',";
				}
				if($cjdbh!=""){
					$sql = $sql."CJDBH='$cjdbh',";
				}
				if($fkly!=""){
					$sql = $sql."FKLY='$fkly',";
				}
				$sql = rtrim($sql, ','); 
				$sql = $sql." where jqid='$jqid'";
			}else{
				$seq = $this->getSequenceByTable("ZDT_PeFeedback");
				$sql = "insert into ZDT_PeFeedback(FKDBH,CJDBH,JQID,XZQHDM,FKDWDM,FKYBH,FKYXM,BELONG,SCENE,FKLY) values('$seq','$cjdbh','$jqid','$xzqh','$orgCode','$userId','$userName','$belong','$scene','$fkly')";
			}
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
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
	* updatePeProcessCommand
	* 指令反馈时根据指令编号修改处理结果
	*/
	public function updatePeProcessCommand($zlbh,$jqcljg) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update ZDT_PeProcess_Command t set t.CLWBSJ=sysdate,t.JQZTDM='5',t.CLJG='$jqcljg' where  t.zlbh='$zlbh'";
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

		$sql = "select t.jqid,t.jqbh,t6.cjdbh,t7.zlbh from zdt_policeevent t  left join ZDT_PeProcess t6 on t.jqid = t6.jqid  left join ZDT_PeProcess_Command t7 on t6.cjdbh = t7.cjdbh where t6.cjdzt = '1'   and t7.zlbs = '1'   and t.jqid = '$jqid' order by t7.jqztdm desc nulls last";
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
	
	/**
	* getResourceCount
	* 根据文件名查询资源count
	*/
	public function getResourceCount($fireName) {
		$bRet = true;
		$errMsg = '';
		$count = 0;
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select count(1) as ROWCOUNT from ZDT_PeProcess_Resource where ZYDZ='$fireName'";
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
	
   public function updateCommanMessage($zlbh, $jqcljg,$cjld){
   		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$jqcljg = iconv("UTF-8","GBK",$jqcljg);
		$cjld = iconv("UTF-8","GBK",$cjld);
		$sql = "update ZDT_PeProcess_Command t set t.cjld='$cjld',t.CLJG='$jqcljg' where  t.zlbh='$zlbh'";
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
   
   public function getProcessByCjdbh($cjdbh){
   		$bRet = true;
		$errMsg = "";
		$data = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.jqid,t.cjdbh,t.jqdbh,t.cjdzt from zdt_peprocess t   where t.cjdbh = '$cjdbh'";
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
					'cjdbh' => iconv("GBK", "UTF-8", $row["CJDBH"]),
					'jqdbh' => iconv("GBK", "UTF-8", $row["JQDBH"]),
					'cjdzt' => iconv("GBK", "UTF-8", $row["CJDZT"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		return $data;
   }
   
   /**
	 * getEventFeedDetailByid
	 * 警情反馈时查询警情反馈详细信息
	 * 返回终端需要字段
	 * @param $jqid
	 * @return 警情对象
	 */
	public function getEventFeedDetailByid($jqid) {
		$bRet = true;
		$errMsg = "";
		$men = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.jqlbdm, t.jqdjdm,to_char(t.bjsj, 'yyyy-MM-dd hh24:mi:ss') as jqfs_sj,to_char(t6.jqjssj, 'yyyy-MM-dd hh24:mi:ss') as jqjs_sj,to_char(t7.ddxcsj, 'yyyy-MM-dd hh24:mi:ss') as ddxc_sj," .
				"to_char(t7.clwbsj, 'yyyy-MM-dd hh24:mi:ss') as clwb_sj,t7.cjqk,t7.cljgdm,t7.jqztdm,t7.cljg,t7.cdclqk,t7.cdryqk,fb_case.zhrs as zh_rs,fb_case.sars as sa_rs,fb_case.jzrs as jz_rs," .
				"fb_case.jzrssm as jzrssm,fb_case.tprs as jj_rf,fb_case.jjrs as jj_rs,fb_case.jjrssm,fb_case.swrs as sw_rs,fb_case.ssrs as ss_rs,fb_case.ssrssm,fb_case.swrssm,fb_case.djyrs as djy_rs," .
				"fb_case.cjryssrs as cjryss_rs,fb_case.cjryswrs as cjrysw_rs,fb_case.jjssqk,fb_case.whjjssqk,fb_case.sfphxsaj,fb_case.sfcczaaj,fb_case.sfjjjf,fb_case.lzscrs as lzsc_rs,fb_case.jqcljg as jqcl_jg," .
				"t.jqbh,t.cjdbh,t6.cjdbh as pro_cjdbh,t7.zlbh  from zdt_policeevent t" .
				"  left join ZDT_PoliceEvent_Reporter t1 on t.jqid = t1.jqid" .
				"  left join ZDT_PeProcess t6 on t.jqid = t6.jqid" .
				"  left join ZDT_PeProcess_Command t7 on t6.cjdbh = t7.cjdbh" .
				"  left join ZDT_PEFEEDBACK fb on fb.cjdbh = t6.cjdbh" .
				"  left join ZDT_PeFeedback_Case fb_case on t6.cjdbh = fb_case.cjdbh" .
				"  where t6.cjdzt = '1'   and t7.zlbs = '1'   and t.jqid = '$jqid' order by t7.zlxdsj desc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$cjdbh = iconv("GBK", "UTF-8", $row["PRO_CJDBH"]);
				$zlbh = iconv("GBK", "UTF-8", $row["ZLBH"]);
				$array = $this->getDispatchMenByCjdbh($cjdbh,$zlbh);
				$men = array (
						'jqlbdm' =>	iconv("GBK", "UTF-8", $row["JQLBDM"]),
						'jqdjdm' =>	iconv("GBK", "UTF-8", $row["JQDJDM"]),
						'jqfs_sj' =>iconv("GBK", "UTF-8", $row["JQFS_SJ"]),
						'jqjs_sj' =>iconv("GBK", "UTF-8", $row["JQJS_SJ"]),
						'ddxc_sj' =>iconv("GBK", "UTF-8", $row["DDXC_SJ"]),
						'clwb_sj' =>iconv("GBK", "UTF-8", $row["CLWB_SJ"]),
						'cjqk' =>   iconv("GBK", "UTF-8", $row["CJQK"]),
						'cljgdm' =>	iconv("GBK", "UTF-8", $row["CLJGDM"]),
						'jqztdm' =>	iconv("GBK", "UTF-8", $row["JQZTDM"]),
						'cljg' =>	iconv("GBK", "UTF-8", $row["CLJG"]),
						'cdclqk' =>	"1",
						'cdryqk' =>	count($array),
						'zh_rs' =>	iconv("GBK", "UTF-8", $row["ZH_RS"]),
						'sa_rs' =>	iconv("GBK", "UTF-8", $row["SA_RS"]),
						'jz_rs' =>	iconv("GBK", "UTF-8", $row["JZ_RS"]),
						'jzrssm' =>	iconv("GBK", "UTF-8", $row["JZRSSM"]),
						'jj_rf' =>	iconv("GBK", "UTF-8", $row["JJ_RF"]),
						'jj_rs' =>	iconv("GBK", "UTF-8", $row["JJ_RS"]),
						'jjrssm' =>	iconv("GBK", "UTF-8", $row["JJRSSM"]),
						'sw_rs' =>	iconv("GBK", "UTF-8", $row["SW_RS"]),
						'ss_rs' =>	iconv("GBK", "UTF-8", $row["SS_RS"]),
						'ssrssm' =>	iconv("GBK", "UTF-8", $row["SSRSSM"]),
						'swrssm' =>	iconv("GBK", "UTF-8", $row["SWRSSM"]),
						'djy_rs' =>	iconv("GBK", "UTF-8", $row["DJY_RS"]),
						'cjryss_rs' =>	iconv("GBK", "UTF-8", $row["CJRYSS_RS"]),
						'cjrysw_rs' =>	iconv("GBK", "UTF-8", $row["CJRYSW_RS"]),
						'jjssqk' =>	    iconv("GBK", "UTF-8", $row["JJSSQK"]),
						'whjjssqk' =>	iconv("GBK", "UTF-8", $row["WHJJSSQK"]),
						'sfphxsaj' =>	iconv("GBK", "UTF-8", $row["SFPHXSAJ"]),
						'sfcczaaj' =>	iconv("GBK", "UTF-8", $row["SFCCZAAJ"]),
						'sfjjjf' =>	    iconv("GBK", "UTF-8", $row["SFJJJF"]),
						'lzsc_rs' =>	iconv("GBK", "UTF-8", $row["LZSC_RS"]),
						'jqcl_jg' =>	iconv("GBK", "UTF-8", $row["JQCL_JG"]),
						'jqbh' =>	    iconv("GBK", "UTF-8", $row["JQBH"]),
						'cjdbh' =>	    iconv("GBK", "UTF-8", $row["CJDBH"]),
						'pro_cjdbh' =>	iconv("GBK", "UTF-8", $row["PRO_CJDBH"]),
						'zlbh' =>	    iconv("GBK", "UTF-8", $row["ZLBH"])																																																																																										
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $men;
	}
	
	/**
	 * getSimpleById
	 * 根据警情编号查询基本信息
	 * @param $jqbh
	 * @return 增量刷新数据
	 */
	public function getSimpleById($jqbh) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select rec.JJRBH,r.bjdh,r.bjrxm,t.jqzk,t.jqid,t.jqbh,t.jjlx,t.bjfs,t.jjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdz,t.dzid,t.jqdd,t.bjnr,t.jqlbdm,t.jqlxdm,t.jqdjdm,t.jqxldm,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as jqjqzb,to_char(t.rksj,'yyyy-MM-dd hh24:mi:ss') as rksj,to_char(t.gxsj,'yyyy-MM-dd hh24:mi:ss') as gxsj,t.cfjqbs,t.jqclzt,t.jqzt,t.sfyj,MDSYS.Sdo_Util.to_wktgeometry_varchar(t1.mhjqzb) as mhjqzb,t.sfqr,o.orgname,o.orgcode,t2.gxdwdm,t2.hphm  from zdt_policeevent t" .
				"  left join ZDT_PoliceEvent_Location t1 on  t.jqid = t1.jqid  left join ZDT_PoliceEvent_Admin    t2 on  t2.jqid = t.jqid" .
				"  left join ZDT_PoliceEvent_Reporter r on r.jqid=t.jqid".
				"  left join ZDT_PoliceEvent_Receiver rec on rec.jqid=t.jqid".
				"  left join zdb_organization  o on t2.gxdwdm = o.orgcode where 1=1 ";
		if ($jqbh != "") {
			$sql = $sql . " and t . jqbh = '$jqbh' ";
		}
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
					'bjrxm' => iconv("GBK", "UTF-8", $row["BJRXM"]),
					'jjrbh' => iconv("GBK", "UTF-8", $row["JJRBH"]),
					'jqzt' => iconv("GBK", "UTF-8", $row["JQZT"]),
					'jqzk' => iconv("GBK", "UTF-8", $row["JQZK"]),
					'sfyj' => iconv("GBK", "UTF-8", $row["SFYJ"]),
					'sfqr' => iconv("GBK", "UTF-8", $row["SFQR"])
				);

				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if (!$bRet)
			$res = array (
				'result' => 'false',
				'errmsg' => $errMsg,
				'records' => $mens
			);
		else
			$res = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $mens
			);

		return $res;
	}
	
	public function getEventFeedbackDetailByid($jqid) {
		$bRet = true;
		$errMsg = "";
		$men = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.zrqbm,t.zrqmc,t.xjbmmc,t.xjbmbm, t7.zlbh,t7.cjdbh as PRO_CJDBH,t.jqid,t.jqbh,t.jjlx,t.jjfs,t.bjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdz,t.jqdd,t.bjnr,t.jqlbdm,t.jqlxdm," .
				" t.jqxldm,t.jqdjdm,t.jqzk,t.jqzt,t.stationhousecode,t.stationhouse,t.sfyj,t.pjfs,t.jqzbly,t.jsjqly,fb.fkdwdm,to_char(t6.jqfssj,'yyyy-MM-dd hh24:mi:ss') as jqfssj," .
				" to_char(t7.ddxcsj,'yyyy-MM-dd hh24:mi:ss') as ddxcsj,to_char(t6.jqjssj,'yyyy-MM-dd hh24:mi:ss') as jqjssj,to_char(t6.pjsj,'yyyy-MM-dd hh24:mi:ss') as pjsj," .
				" t6.xzqh,fb.fkybh,fb.fkyxm,fb.belong,fb.scene,fb_case.zhrs,fb_case.jjrs,fb_case.ssrs,fb_case.swrs,fb_case.jqcljg,fb_case.jjfvrs," .
				" fb_case.jjetrs,fb_case.jzrs,t7.cdclqk,t.jqlxdm as bjlxdm,t7.cdryqk,to_char(t7.zljssj,'yyyy-MM-dd hh24:mi:ss') as zljssj,fb_case.jjssqk," .
				" round(t.jqjqzb.SDO_POINT.x,10) as zbx,round(t.jqjqzb.SDO_POINT.y,10) as zby,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as mhjqzb" .
				" from zdt_policeevent t  left join ZDT_PoliceEvent_Reporter t1 on t.jqid = t1.jqid  left join ZDT_PeProcess t6 on t.jqid = t6.jqid" .
				" left join ZDT_PeProcess_Command t7 on t6.cjdbh = t7.cjdbh  left join ZDT_PEFEEDBACK fb on fb.cjdbh = t6.cjdbh  left join ZDT_PeFeedback_Case fb_case on t6.cjdbh = fb_case.cjdbh" .
				" where t6.cjdzt = '1' and t.jqid = '$jqid'  and t7.zlbs = '1' order by t7.zlxdsj desc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$cjdbh = iconv("GBK", "UTF-8", $row["PRO_CJDBH"]);
				$zlbh = iconv("GBK", "UTF-8", $row["ZLBH"]);
				$array = $this->getDispatchMenByCjdbh($cjdbh,$zlbh);
				$men = array (
						'jqid' =>	iconv("GBK", "UTF-8", $row["JQID"]),
						'jqbh' =>	iconv("GBK", "UTF-8", $row["JQBH"]),
						'jjlx' =>iconv("GBK", "UTF-8", $row["JJLX"]),
						'jjfs' =>iconv("GBK", "UTF-8", $row["JJFS"]),
						'bjfs' =>iconv("GBK", "UTF-8", $row["BJFS"]),
						'bjsj' =>iconv("GBK", "UTF-8", $row["BJSJ"]),
						'jqdz' =>   iconv("GBK", "UTF-8", $row["JQDZ"]),
						'jqdd' =>	iconv("GBK", "UTF-8", $row["JQDD"]),
						'bjnr' =>	iconv("GBK", "UTF-8", $row["BJNR"]),
						'jqlbdm' =>	iconv("GBK", "UTF-8", $row["JQLBDM"]),
						'jqlxdm' =>	iconv("GBK", "UTF-8", $row["JQLXDM"]),
						'jqxldm' =>	iconv("GBK", "UTF-8", $row["JQXLDM"]),
						'jqdjdm' =>	iconv("GBK", "UTF-8", $row["JQDJDM"]),
						'jqzk' =>	iconv("GBK", "UTF-8", $row["JQZK"]),
						'jqzt' =>	iconv("GBK", "UTF-8", $row["JQZT"]),
						'stationhousecode' =>	iconv("GBK", "UTF-8", $row["STATIONHOUSECODE"]),
						'stationhouse' =>	iconv("GBK", "UTF-8", $row["STATIONHOUSE"]),
						'fkdwdm' =>	iconv("GBK", "UTF-8", $row["FKDWDM"]),
						'sfyj' =>	iconv("GBK", "UTF-8", $row["SFYJ"]),
						'jqfssj' =>	iconv("GBK", "UTF-8", $row["JQFSSJ"]),
						'ddxcsj' =>	iconv("GBK", "UTF-8", $row["DDXCSJ"]),
						'jqjssj' =>	iconv("GBK", "UTF-8", $row["JQJSSJ"]),
						'pjsj' =>	iconv("GBK", "UTF-8", $row["PJSJ"]),
						'xzqh' =>	iconv("GBK", "UTF-8", $row["XZQH"]),
						'fkybh' =>	iconv("GBK", "UTF-8", $row["FKYBH"]),
						'fkyxm' =>	iconv("GBK", "UTF-8", $row["FKYXM"]),
						'belong' =>	iconv("GBK", "UTF-8", $row["BELONG"]),
						'scene' =>	iconv("GBK", "UTF-8", $row["SCENE"]),
						'zhrs' =>	iconv("GBK", "UTF-8", $row["ZHRS"]),
						'jjrs' =>	iconv("GBK", "UTF-8", $row["JJRS"]),
						'ssrs' =>	iconv("GBK", "UTF-8", $row["SSRS"]),
						'swrs' =>	iconv("GBK", "UTF-8", $row["SWRS"]),
						'jqcljg' =>	iconv("GBK", "UTF-8", $row["JQCLJG"]),
						'jjfvrs' =>	iconv("GBK", "UTF-8", $row["JJFVRS"]),
						'jjetrs' =>	iconv("GBK", "UTF-8", $row["JJETRS"]),
						'cdclqk' =>	"1",
						'cdryqk' =>	count($array),
						'jzrs' =>	iconv("GBK", "UTF-8", $row["JZRS"]),
						'bjlxdm' =>	iconv("GBK", "UTF-8", $row["BJLXDM"]),
						'zljssj' =>	iconv("GBK", "UTF-8", $row["ZLJSSJ"]),
						'jjssqk' =>	iconv("GBK", "UTF-8", $row["JJSSQK"]),
						'zbx' =>	iconv("GBK", "UTF-8", $row["ZBX"]),
						'zby' =>	iconv("GBK", "UTF-8", $row["ZBY"]),
						'mhjqzb' =>	iconv("GBK", "UTF-8", $row["MHJQZB"]),
						'zrqCode' =>	iconv("GBK", "UTF-8", $row["ZRQBM"]),
						'zrqName' =>	iconv("GBK", "UTF-8", $row["ZRQMC"]),
						'xjbmbm' =>	iconv("GBK", "UTF-8", $row["XJBMBM"]),
						'xjbmmc' =>	iconv("GBK", "UTF-8", $row["XJBMMC"]),
						'pjfs' =>	iconv("GBK", "UTF-8", $row["PJFS"]),
						'jqzbly' =>	iconv("GBK", "UTF-8", $row["JQZBLY"]),
						'jsjqly' =>	iconv("GBK", "UTF-8", $row["JSJQLY"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $men;
	}
	
	public function getEventFeedbackPictureByid($jqid) {
		$bRet = true;
		$errMsg = "";
		$men = array();$datas = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select t.jqid,r.zydz,r.zylx  from zdt_policeevent t  inner join ZDT_PeProcess t6 on t.jqid = t6.jqid  inner join zdt_peprocess_resource r on r.cjdbh=t6.cjdbh where  t.jqid = '$jqid'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
						'jqid' =>	iconv("GBK", "UTF-8", $row["JQID"]),
						'zydz' =>	iconv("GBK", "UTF-8", $row["ZYDZ"]),
						'zylx' =>iconv("GBK", "UTF-8", $row["ZYLX"])
				);
				array_push($datas,$men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}
	
	public function getEventReceiveDisposalByid($jqid) {
		$bRet = true;
		$errMsg = "";
		$men = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.zrqbm,t.zrqmc,t.jqid,t.jqbh,t.jjlx,t.jjfs,t.bjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdd,t.bjnr,t.jqlbdm,t.jqlxdm,t.jqxldm,t.jqdjdm,t.stationhousecode,t.stationhouse,t.jqclzt,t.jqzt,t.sfyj,to_char(p.pjsj,'yyyy-MM-dd hh24:mi:ss') as pjsj,to_char(c.zljssj,'yyyy-MM-dd hh24:mi:ss') as zljssj,c.cjdwdm from zdt_policeevent t" .
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
						'cjdwdm' =>	iconv("GBK", "UTF-8", $row["CJDWDM"]),
						'zrqCode' =>	iconv("GBK", "UTF-8", $row["ZRQBM"]),
						'zrqName' =>	iconv("GBK", "UTF-8", $row["ZRQMC"])
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
	
	public function getDynamicGroup($jqid) {
		$bRet = true;
		$errMsg = "";
		$men = array();$datas = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select hphm from zdt_policeevent t left join zdt_peprocess p on p.jqid=t.jqid" .
				" left join zdt_peprocess_command c on c.cjdbh=p.cjdbh" .
				" left join zdt_duty_group g on g.leaderid=c.rybh" .
				" left join zdb_equip_car car on car.id=g.carid where t.jqid = '$jqid'" .
				"  group by car.hphm";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
						'hphm' =>	iconv("GBK", "UTF-8", $row["HPHM"])
				);
				array_push($datas,$men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}
         public function getOrgbyAir($airType,$x,$y) {
		$bRet = true;
		$url='http://192.168.20.75/zhdd/php/event/getOrgByAir.php?airType='.$airType.'&x='.$x.'&y='.$y;    
		try{
		//echo file_get_contents($url);
		$rs = Json_decode(file_get_contents($url),true);		
        return $rs;
		}catch(Exception $e)
		{
                    $men = array (
						'orgcode' =>	"",
                                                'orgname' =>	""
				); 
                    return $men;
		}
		
	}
}
?>
