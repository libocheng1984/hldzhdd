<?php
error_reporting(E_ALL || ~E_NOTICE);
class Communication extends TpmsDB {

	public function getContactsByOrg($orgCode, $id) {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select  o.orgname,o.orgcode,o.id from zdb_organization o where 1=1 and (o.orglevel ='10' or o.orglevel ='20' or o.orglevel ='21' or o.orglevel ='32') ";
		if ($orgCode != "") {
			$sql = $sql . " and o.orgCode = '$orgCode'";
		}else{
			$sql = $sql . " and o.orgCode = '210200000000'";
		}
		$sql = $sql . " order by  orgcode asc ";
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
					'id' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'value' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'text' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'orgId' => iconv("GBK", "UTF-8", $row["ID"]),
					'state' => 'close',
					'children' => array ()
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		
		if($id!=""){
			$chirdArray = array();
			if ($datas && $datas[0]['id']) {
				$chirdPolice = $this->getPoliceByOrg($datas[0]['id']);
				$chirdOrg = $this->getChirdOrgeByOrg($datas[0]['orgId']);
				$chirdArray = array_merge($chirdOrg,$chirdPolice);
			}
			return $chirdArray;
		}else{
			$chirdArray = array();
			if ($datas && $datas[0]['id']) {
				$chirdPolice = $this->getPoliceByOrg($datas[0]['id']);
				$chirdOrg = $this->getChirdOrgeByOrg($datas[0]['orgId']);
				$chirdArray = array_merge($chirdOrg,$chirdPolice);
				$datas[0]['state'] = 'open';
				$datas[0]['children'] = $chirdArray;
			}
			return $datas;
		}
		
	}

	public function getPoliceByOrg($orgCode) {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.userid,t.username,o.orgcode,o.orgname,t.alarm,'1' as flag ,t.bz from zdb_user t  left join zdb_organization o on o.orgcode=t.bz where 1=1 "; //t.bz='$orgCode' ";
		if ($orgCode != "") {
			$sql = $sql . " and (o.parenttreepath like '%$orgCode" . "_' and o.orglevel='50' or o.orgCode = '$orgCode')";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'parentId' => iconv("GBK", "UTF-8", $row["BZ"]),
					'id' => iconv("GBK", "UTF-8", $row["USERID"]),
					'value' => iconv("GBK", "UTF-8", $row["USERID"]),
					'text' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'userId' => iconv("GBK", "UTF-8", $row["USERID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}
	
	public function getChirdOrgeByOrg($parentId) {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		if ($parentId != "") {
			if ($this->dbconn == null)
				$this->dbconn = $this->LogonDB();

			$sql = "select  o.orgname,o.orgcode,o.id from zdb_organization o where 1=1 and (o.orglevel ='10' or o.orglevel ='20' or o.orglevel ='21' or o.orglevel ='32') ";
			$sql = $sql . " and o.parentid = '$parentId'";
			$sql = $sql . " order by  orgcode asc ";
			//echo $sql;
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@ oci_execute($stmt)) {
				$bRet = false;
				echo getOciError($stmt);
				oci_close($this->dbconn);
				$errMsg = '查询失败';
			} else {
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array (
						'id' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
						'value' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
						'text' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
						'orgId' => iconv("GBK", "UTF-8", $row["ID"]),
						'state' => 'close',
						'children' => array ()
					);
					array_push($datas, $data);
				}
			}
			oci_free_statement($stmt);
			oci_close($this->dbconn);
		}
		return $datas;
	}

	public function getGroupsByOrg($orgCode, $id) {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		if ($id != "") {
			$datas = $this->getHphmByOrg($id);
		} else {
			$sql = "select o.orgname,o.orgcode  from zdb_organization o, zdt_duty_group g where g.orgcode=o.orgcode ";
			if ($orgCode != "210200000000") {
				$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
			}
			$sql = $sql . "group by o.orgcode,o.orgname order by o.orgcode";
			//echo $sql;
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@ oci_execute($stmt)) {
				$bRet = false;
				echo getOciError($stmt);
				oci_close($this->dbconn);
				$errMsg = '查询失败';
			} else {
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array (
						'id' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
						'value' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
						'text' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
						'children' => array ()
					);
					array_push($datas, $data);
				}
			}
			oci_free_statement($stmt);
			oci_close($this->dbconn);

			if ($datas && $datas[0]['id']) {
				$datas[0]['children'] = $this->getHphmByOrg($datas[0]['id']);
				$datas[0]['state'] = 'open';
			}
		}

		return $datas;
	}

	public function getHphmByOrg($orgCode) {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = " select t.gid,c.hphm,t.leaderid,'1' as flag ,t.orgcode from zdt_duty_group t,zdb_equip_car c where c.id=t.carid and t.status!='3' and t.orgcode='$orgCode' ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'parentId' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'id' => iconv("GBK", "UTF-8", $row["GID"]),
					'value' => iconv("GBK", "UTF-8", $row["GID"]),
					'text' => iconv("GBK", "UTF-8", $row["HPHM"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	public function getContactsGroupsByOrg($orgCode) {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select g.gid,g.userid,g.orgcode,o.orgname,c.hphm from zdt_duty_group g left join zdb_organization o on o.orgcode=g.orgcode left join zdb_equip_car c on c.id=g.carid where 1=1 and g.status!='3' ";
		if ($orgCode != "210200000000") {
			$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'groupid' => iconv("GBK", "UTF-8", $row["GID"]),
					'groupname' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$result = array (
			'total' => "",
			'rows' => $datas
		);
		return $result;
	}

	public function getContactsGroupsById($gid) {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select g.userid,o.orgcode,o.orgname,usr.userid,usr.username from zdt_duty_group g,zdb_organization o,zdb_user usr where g.status!='3' and o.orgcode=g.orgcode and instr(g.userid,usr.userid)>0 and g.gid='$gid' ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'userId' => iconv("GBK", "UTF-8", $row["USERID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$result = array (
			'total' => "",
			'rows' => $datas
		);
		return $result;
	}

	function createGroups($qzmc, $cjrId, $qzcy, $gids, $jqbh, $groupid) {
		$members = $this->getMembersByCy($qzcy, $gids, $groupid);
		$qzmc = iconv("UTF-8", "GBK", $qzmc);
		$members = iconv("UTF-8", "GBK", $members);
		//		$id = $this->getGroupCountByMembers($members);
		//		if($id!=""){
		//			$arr = array (
		//				'result' => 'true',
		//				'errmsg' => '',
		//				'groupid' => $id
		//			);
		//			return $arr;
		//		}
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select GROUPID_SEQ.NEXTVAL GROUPID from dual";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "GROUPID", $groupId);
		if (!@ oci_execute($stmt)) {
			$arr = array (
				'result' => 'false',
				'errmsg' => '获取群组ID出错'
			);
			return $arr;
		} else {
			oci_fetch($stmt);
		}
		$sql = "insert  into  ZDT_MSG_GROUP  values('$groupId','$qzmc','$cjrId','$members','',to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS'),'$jqbh','0')";

		$stmt = oci_parse($this->dbconn, $sql);
		$r = @ oci_execute($stmt);
		if (!$r) {
			$bRet = false;
			$errMsg = '';
		}
		oci_free_statement($stmt);
		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);

		else
			$arr = array (
				'result' => 'true',
				'errmsg' => '',
				'groupid' => $groupId
			);

		return $arr;

	}

	public function getMembersByCy($cjrId, $gids, $groupid) {
		$userids = array ();
		if ($gids) {
			$userids = $this->getQzcyByGid($gids);
		}
		$groupmenbers = array ();
		if ($groupid) {
			$groupmenbers = $this->getCommonGroupByGid($groupid);
		}

		$mens = "";
		$members = "";
		if ($cjrId != "") {
			for ($i = 0; $i < count($userids); $i++) {
				$cjrId = $cjrId . "," . $userids[$i];
			}
			for ($i = 0; $i < count($groupmenbers); $i++) {
				$cjrId = $cjrId . "," . $groupmenbers[$i]['qzcy'];
			}
		} else {
			for ($i = 0; $i < count($userids); $i++) {
				if ($i != 0) {
					$cjrId = $cjrId . "," . $userids[$i];
				} else {
					$cjrId = $userids[$i];
				}
			}
			if ($cjrId != "") {
				for ($i = 0; $i < count($groupmenbers); $i++) {
					$cjrId = $cjrId . "," . $groupmenbers[$i]['qzcy'];
				}
			} else {
				for ($i = 0; $i < count($groupmenbers); $i++) {
					if ($i != 0) {
						$cjrId = $cjrId . "," . $groupmenbers[$i]['qzcy'];
					} else {
						$cjrId = $groupmenbers[$i]['qzcy'];
					}
				}
			}
		}

		$arr1 = explode(",", $cjrId);
		$arr1 = array_unique($arr1);
		sort($arr1);
		for ($i = 0; $i < count($arr1); $i++) {
			if ($i == 0) {
				$members = $arr1[$i];
			} else {
				$members = $members . "," . $arr1[$i];
			}

		}

		return $members;
	}

	public function getQzcyByGid($gids) {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select g.userid from zdt_duty_group g where g.status!='3' and g.gid in ($gids)";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				array_push($datas, iconv("GBK", "UTF-8", $row["USERID"]));
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	/**
	 * getGroupCountByMembers
	 * 根据群组组员查询群组是否存在
	 * @param $members
	 * @return true or false
	 */
	public function getGroupCountByMembers($members) {
		$bRet = true;
		$id = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select id from zdt_msg_group where qzcy='$members'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$id = iconv("GBK", "UTF-8", $row["ID"]);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $id;

	}

	public function getMsgGroupsById($userId) {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select g.id,g.qzmc,p.jqbh,g.cjrid from zdt_msg_group g " .
		" left join zdt_policeevent p on g.jqbh=p.jqbh " .
		//" left join (select * from zdt_msg where (fssj,gid) in (select max(fssj),gid from zdt_msg group by gid)) m on g.id=m.gid" .
	" where g.sfsc!='1' and g.qzcy like '%$userId%' order by g.cjsj desc ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$iscreator = false;
				//判断是否是自己创建的群组
				$cjrid = iconv("GBK", "UTF-8", $row["CJRID"]);
				if ($cjrid == $userId)
					$iscreator = true;
				$data = array (
					'groupid' => iconv("GBK", "UTF-8", $row["ID"]),
					'groupname' => iconv("GBK", "UTF-8", $row["QZMC"]),
					//'cjrid' => iconv("GBK", "UTF-8", $row["CJRID"]),
	//'qzcy' => iconv("GBK", "UTF-8", $row["QZCY"]),
	//'cjsj' => iconv("GBK", "UTF-8", $row["CJSJ"]),
	//'jsrid' => iconv("GBK", "UTF-8", $row["JSRID"]),
	//'fsrid' => iconv("GBK", "UTF-8", $row["FSRID"]),
	//'fssj' => iconv("GBK", "UTF-8", $row["FSSJ"]),
	'xxnr' => iconv("GBK", "UTF-8", $row["XXNR"]),
					'xxlx' => iconv("GBK", "UTF-8", $row["XXLX"]),
					'jingqing' => iconv("GBK", "UTF-8", $row["JQBH"]),
					'iscreator' => $iscreator,
					'disabled' => false
					//'username' => iconv("GBK", "UTF-8", $row["USERNAME"])

	
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$result = array (
			'total' => "",
			'rows' => $datas
		);
		return $result;
	}

	public function getGroups() {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select t.gid,c.hphm,t.leaderid,'1' as flag ,t.orgcode,o.orgname from zdt_duty_group t,zdb_equip_car c,zdb_organization o where o.orgcode=t.orgcode and c.id=t.carid and t.status!='3' ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'gid' => iconv("GBK", "UTF-8", $row["GID"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'leaderid' => iconv("GBK", "UTF-8", $row["LEADERID"]),
					'orgcode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgname' => iconv("GBK", "UTF-8", $row["ORGNAME"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if (!$bRet)
			$res = array (
				'result' => 'false',
				'errmsg' => $errMsg,
				'records' => $datas
			);
		else
			$res = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $datas
			);
		return $res;
	}

	public function updateGroups($gid, $qzmc, $qzcy, $gids) {
		$members = $this->getMembersByCy($qzcy, $gids, "");
		$qzmc = iconv("UTF-8", "GBK", $qzmc);
		$members = iconv("UTF-8", "GBK", $members);
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "update ZDT_MSG_GROUP  set qzcy='$members' ";
		if ($qzmc) {
			$sql = $sql . ",qzmc='$qzmc'";
		}
		$sql = $sql . "where id='$gid'";
		$stmt = oci_parse($this->dbconn, $sql);
		$r = @ oci_execute($stmt);
		if (!$r) {
			$bRet = false;
			$errMsg = '';
		}
		oci_free_statement($stmt);
		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);

		else
			$arr = array (
				'result' => 'true',
				'errmsg' => ''
			);

		return $arr;
	}

	public function AndroidUpdateGroup($groupid, $qzmc, $qzcy, $gids, $userid) {
		$qzcyOld = "";
		$result = array ();
		$ret = array ();
		$qzcyData = $this->getQzcyByByGid($groupid);
		if ($qzcyData['records']['qzcy']) {
			$arr1 = explode(",", $qzcyData['records']['qzcy']);
			if (!in_array($userid, $arr1)) {
				$result = array (
					'result' => 'false',
					'errmsg' => "没有权限修改会话组",
					'records' => ""
				);
				return $result;
			}
		}
		if ($qzcyData['result'] == 'true') {
			$qzcyOld = $qzcyData['records']['qzcy'];
		}
		//获取新加入的成员姓名
		$addMembers = $this->getMembersByCy($qzcy, $gids, "");

		$diffMenbers_add = $this->diffentArr($qzcyOld, $addMembers);
		$diffMenbers_del = $this->diffentArr($addMembers, $qzcyOld);
		if ($diffMenbers_add) {
			$msgChat = $this->getmsgArr($qzcyOld, $addMembers, "add");
		}
		if ($diffMenbers_del) {
			for ($i = 0; $i < count($diffMenbers_del); $i++) {
				if ($i == 0)
					$userids = $diffMenbers_del[$i];
				else
					$userids = $userids . $diffMenbers_del[$i];
			}
			$msgChat = $this->getmsgArr($qzcyOld, $userids, "del");
		}

		$ret = $this->updateGroups($groupid, $qzmc, $qzcy, $gids);
		$result = $this->getGroupByGroupid("", $groupid);
		$userlist = $result['records']["userlist"];
		for ($i = 0; $i < count($userlist); $i++) {
			if ($i == 0)
				$userids = $userlist[$i]['userId'];
			else
				$userids = $userids . "," . $userlist[$i]['userId'];
		}
		if ($diffMenbers_add || $diffMenbers_del) {
			$msgData = array (
				"msg" => array (
					"groupid" => $groupid,
					"type" => "9",
					"chat" => $msgChat
				),
				"userlist" => array (
					"groupid" => $groupid,
					"users" => $userlist
				)
			);
			$this->writeFile($groupid, $msgData, $userids, $userid);
			$resSend = $this->sendOtherMsg($groupid, $msgChat, $userid, "", "9");
		}
		if ($qzmc != $qzcyData['records']['qzmc']) {
			$msgData = array (
				//"msg"=>"",
	"msg" => array (
					"groupid" => $groupid,
					"type" => "9",
					"chat" => "会话更名为" . $qzmc
				),
				"groups" => array (
					"groupid" => $groupid,
					"groupname" => $result['records']['groupname'],
					"disabled" => false
				)
			);
			$this->writeFile($groupid, $msgData, $qzcyOld, $userid);
			$resSend = $this->sendOtherMsg($groupid, "会话更名为" . $qzmc, $userid, "", "9");
		}

		//if($result['records']&&$result['records']['userlist'])$result = $result['records']['userlist'];
		return $result;
	}

	public function getGroupById($gid) {
		$bRet = true;
		$errMsg = "";
		$data = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select g.jqbh,g.id,g.qzmc,g.cjrid,g.qzcy,u.username from zdt_msg_group g,zdb_user u where u.userid=g.cjrid and g.sfsc!='1' and g.id='$gid'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$qzcy = iconv("GBK", "UTF-8", $row["QZCY"]);
				$array = explode(",", $qzcy);
				$userids = "";
				$members = array ();
				for ($i = 0; $i < count($array); $i++) {
					if ($i == 0)
						$userids = "'" . $array[$i] . "'";
					else
						$userids = $userids . ",'" . $array[$i] . "'";
				}
				//echo $userids;
				if ($userids != "")
					$members = $this->getMembersByIds($userids);
				$data = array (
					'id' => iconv("GBK", "UTF-8", $row["ID"]),
					'qzmc' => iconv("GBK", "UTF-8", $row["QZMC"]),
					'cjrid' => iconv("GBK", "UTF-8", $row["CJRID"]),
					'cjrxm' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'jqbh' => iconv("GBK", "UTF-8", $row["JQBH"]),
					'qzcy' => $members
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if (!$bRet)
			$res = array (
				'result' => 'false',
				'errmsg' => $errMsg,
				'records' => $data
			);
		else
			$res = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $data
			);
		return $res;
	}

	/**
	 * getGroupByGroupid
	 * web端event为getgroup时调用
	 * 根据groupid查询对应群组信息以及群组消息
	 * @param $gid
	 * @return
	 */
	public function getGroupByGroupid($userid, $gid) {
		$bRet = true;
		$errMsg = "";
		$data = array ();
		$msgDatas = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select g.id,g.qzmc,g.cjrid,g.qzcy,g.sfsc,p.jqbh,u.username from zdt_msg_group g left join zdb_user u on u.userid=g.cjrid left join zdt_policeevent p on p.jqbh=g.jqbh where g.id='$gid'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$qzcy = iconv("GBK", "UTF-8", $row["QZCY"]);
				$array = explode(",", $qzcy);
				$userids = "";
				$members = array ();
				for ($i = 0; $i < count($array); $i++) {
					if ($i == 0)
						$userids = "'" . $array[$i] . "'";
					else
						$userids = $userids . ",'" . $array[$i] . "'";
				}
				//echo $userids;
				if ($userids != "")
					$members = $this->getUserInfoByIds($userids);
				$members = $this->changeArray($members, $userid);
				//if($userid!="")	$msgDatas = $this->getMemcacheMsg($userid,$gid);
				$this->delMemcacheMsg($userid, $gid);
				$data = array (
					'groupid' => iconv("GBK", "UTF-8", $row["ID"]),
					'groupname' => iconv("GBK", "UTF-8", $row["QZMC"]),
					'userId' => iconv("GBK", "UTF-8", $row["CJRID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'jingqing' => iconv("GBK", "UTF-8", $row["JQBH"]),
					'disabled' => iconv("GBK", "UTF-8", $row["SFSC"]) == "1" ? true : false,
					'userlist' => $members
					//'msg' => $msgDatas

	
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if (!$bRet)
			$res = array (
				'result' => 'false',
				'errmsg' => $errMsg,
				'records' => $data
			);
		else
			$res = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $data
			);
		return $res;
	}

	/**
	 * getMsgByGid
	 * web端event为getgroup时调用
	 * 通过群组ID查询对应群组消息
	 * @param $gid
	 * @return
	 */
	public function getMsgByGid($gid) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$bRet = false;
		$datas = array ();
		$sql = "select t.fsrid,t.jsrid,t.xxnr,t.xxlx,u.username from zdt_msg t,zdb_user u where t.fsrid=u.userid and t.gid='$gid' and rownum<=5 order by t.fssj desc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (@ oci_execute($stmt)) {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'fsrId' => iconv("GBK", "UTF-8", $row["FSRID"]),
					'jsrId' => iconv("GBK", "UTF-8", $row["JSRID"]),
					'xxnr' => iconv("GBK", "UTF-8", $row["XXNR"]),
					'xxlx' => iconv("GBK", "UTF-8", $row["XXLX"]),
					'fsrxm' => iconv("GBK", "UTF-8", $row["USERNAME"])
				);
				array_push($datas, $data);
			}
		}
		return $datas;
	}

	/**
	 * addMemberGroups
	 * 对单个群组添加人员
	 * 通过群组ID查询对应群组消息
	 * @param $gid
	 * @return
	 */
	public function addMemberGroups($groupid, $qzmc, $qzcy, $gids, $userid) {

		$qzcyOld = "";
		$result = array ();
		$qzcyData = $this->getQzcyByByGid($groupid);
		if ($qzcyData['result'] == 'true') {
			$qzcyOld = $qzcyData['records']['qzcy'];
		}
		//获取新加入的成员姓名
		$addMembers = $this->getMembersByCy($qzcy, $gids, "");
		$msgChat = $this->getmsgArr($qzcyOld, $addMembers, "add");

		if ($qzcyOld != "") {
			if ($qzcy != "")
				$qzcy = $qzcyOld . "," . $qzcy;
			else
				$qzcy = $qzcyOld;
		}
		$this->updateGroups($groupid, $qzmc, $qzcy, $gids);
		$result = $this->getGroupByGroupid("", $groupid);
		$userlist = $result['records']["userlist"];
		for ($i = 0; $i < count($userlist); $i++) {
			if ($i == 0)
				$userids = $userlist[$i]['userId'];
			else
				$userids = $userids . "," . $userlist[$i]['userId'];
		}
		$msgData = array (
			"msg" => array (
				"groupid" => $groupid,
				"type" => "9",
				"chat" => $msgChat
			),
			"userlist" => array (
				"groupid" => $groupid,
				"users" => $userlist
			)
		);
		$this->writeFile($groupid, $msgData, $userids, $userid);
		$resSend = $this->sendOtherMsg($groupid, $msgChat, $userid, "", "9");
		//if($result['records']&&$result['records']['userlist'])$result = $result['records']['userlist'];
		return $result['records'];
	}

	public function getQzcyByByGid($gid) {
		$bRet = true;
		$errMsg = "";
		$data = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select g.id,g.qzmc,g.cjrid,g.qzcy from zdt_msg_group g where g.id='$gid'";
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
					'qzcy' => iconv("GBK", "UTF-8", $row["QZCY"]),
					'qzmc' => iconv("GBK", "UTF-8", $row["QZMC"]),
					'cjrid' => iconv("GBK", "UTF-8", $row["CJRID"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if (!$bRet)
			$res = array (
				'result' => 'false',
				'errmsg' => $errMsg,
				'records' => $data
			);
		else
			$res = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $data
			);
		return $res;
	}

	/**
	 * delMemberGroups
	 * 对单个群组删除人员
	 * 通过群组ID查询对应群组消息
	 * @param $groupid组id,$qzcy成员数组
	 * @return
	 */
	public function delMemberGroups($groupid, $qzcy, $userid) {
		$qzcyOld = "";
		$updateRet = array ();
		$result = array ();
		$res = array ();
		$qzcyData = $this->getQzcyByByGid($groupid);
		if ($qzcyData['result'] == 'true') {
			$qzcyOld = $qzcyData['records']['qzcy'];
		}
		$menbers = $this->getDistanceMembersByCy($qzcyOld, $qzcy);
		//获取删除人员姓名
		$dels = "";
		for ($i = 0; $i < count($qzcy); $i++) {
			if ($i == 0)
				$dels = $qzcy[$i];
			else
				$dels = $dels . "," . $qzcy[$i];
		}

		$msgChat = $this->getmsgArr($qzcyOld, $dels, "del");
		$resSend = $this->sendOtherMsg($groupid, $msgChat, $userid, "", "9");

		$updateRet = $this->updateGroups($groupid, "", $menbers, "");
		$result = $this->getGroupByGroupid("", $groupid);
		$msgData = array (
			"msg" => array (
				"groupid" => $groupid,
				"type" => "9",
				"chat" => $msgChat
			),
			"userlist" => array (
				"groupid" => $groupid,
				"users" => $result['records']["userlist"]
			)
		);
		$this->writeFile($groupid, $msgData, $qzcyOld, $userid);
		$res = array (
			'result' => $updateRet['result'],
			'errmsg' => $updateRet['errmsg'],
			'records' => $result['records']
		);
		//if($result['records']&&$result['records']['userlist'])$result = $result['records']['userlist'];
		return $res;
	}

	public function getDistanceMembersByCy($qzcyOld, $qzcy) {
		$userids = array ();
		$qzcyOldData = explode(",", $qzcyOld);
		$qzcyData = $qzcy;
		$mens = "";
		$members = "";
		$result = array_diff($qzcyOldData, $qzcyData);
		//echo encodeJson($result);
		$arr1 = array_unique($result);
		sort($arr1);
		for ($i = 0; $i < count($arr1); $i++) {
			if ($i == 0) {
				$members = $arr1[$i];
			} else {
				$members = $members . "," . $arr1[$i];
			}

		}

		return $members;
	}

	public function quitMemberGroups($groupid, $iscreator, $userid) {
		$result = array (
			'result' => 'false',
			'errmsg' => '退出失败'
		);
		$qzcyData = $this->getQzcyByByGid($groupid);
		if ($qzcyData['result'] == 'true') {
			$qzcyOld = $qzcyData['records']['qzcy'];
		}
		if ($iscreator) {
			$msgData = array (
				//"msg"=>"",
	"msg" => array (
					"groupid" => $groupid,
					"type" => "9",
					"chat" => "会话组退出"
				),
				"groups" => array (
					"groupid" => $groupid,
					"groupname" => $qzcyData['records']['qzmc'],
					"disabled" => true
				)
			);
			$this->writeFile($groupid, $msgData, $qzcyOld, $userid);
			$resSend = $this->sendOtherMsg($groupid, "会话组退出", $userid, "", "9");
			$result = $this->deleteGroupsById($groupid);
		} else {
			$qzcy = array (
				$userid
			);
			$result = $this->delMemberGroups($groupid, $qzcy, $userid);
		}
		return $result;
	}

	public function deleteGroupsById($groupid) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = " update zdt_msg_group set sfsc='1' where id='$groupid' ";
		//echo  $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = "删除失败";
		}
		oci_free_statement($stmt);
		if ($bRet)
			$arr = array (
				'result' => 'true',
				'errmsg' => '删除成功'
			);
		else
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);

		return $arr;
	}

	public function AndroidDeleteGroup($groupid, $userid) {
		$result = array (
			'result' => 'false',
			'errmsg' => '退出失败'
		);
		$qzcyData = $this->getQzcyByByGid($groupid);
		if ($qzcyData['result'] == 'true') {
			$qzcyOld = $qzcyData['records']['qzcy'];
		}
		$msgData = array (
			//"msg"=>"",
	"msg" => array (
				"groupid" => $groupid,
				"type" => "9",
				"chat" => "会话组退出"
			),
			"groups" => array (
				"groupid" => $groupid,
				"groupname" => "会话组退出",
				"disabled" => true
			)
		);
		$this->writeFile($groupid, $msgData, $qzcyOld, $userid);
		$resSend = $this->sendOtherMsg($groupid, "会话组退出", $userid, "", "9");
		$result = $this->deleteGroupsById($groupid);
		return $result;
	}

	public function sendMemberGroups($groupid, $msg, $userid, $username, $orgCode, $orgName) {
		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		$ret = array ();
		$errMsg = "群组不存在";
		$sendPoliceId = $userid;
		$sendPoliceName = $username;

		$result = array ();
		$result = $this->getGroupDetailByGroupid($groupid, $userid);
		if ($result['result'] = 'true' && $result['records']) {
			$messageStr = str_replace("\n", "<br/>", $msg);
			$messageStr = str_replace("\\n", "<br/>", $messageStr);
			$messageStr = str_replace("\\r\\n", "<br/>", $messageStr);
			//$messageStr = str_replace("<br/>", "\n", $msg);
			$recievePoliceName = $result['records']['qzcyName'];
			$recievePoliceId = $result['records']['qzcyIds'];
			$groupName = $result['records']['groupname'];
			$str = '{"message":{"comCode":"05","codeId":"' . $date . '"},"result":{"recievePoliceId":"' . $recievePoliceId . '","recievePoliceName":"' . $recievePoliceName . '","msg":"' . $messageStr . '","sendPoliceId":"' . $userid . '","sendPoliceName":"' . $sendPoliceName . '","orgCode":"' . $orgCode . '","orgName":"' . $orgName . '","gid":"' . $groupid . '","groupName":"' . $groupName . '"}}';
//			$ret = array (
//				'result' => 'false',
//				'errmsg' => $str
//			);
//			return $ret;
			$ret = $this->send($str);
		} else {
			$ret = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		}
		return $ret;
	}

	public function sendOtherMsg($groupid, $msg, $userid, $username, $type) {
		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		$ret = array ();
		$errMsg = "群组不存在";
		$sendPoliceId = $userid;
		$sendPoliceName = $username;

		$result = array ();
		$result = $this->getGroupDetailByGroupid($groupid, $userid);
		if ($result['result'] = 'true' && $result['records']) {
			$messageStr = str_replace("\n", "", $msg);
			$recievePoliceName = $result['records']['qzcyName'];
			$recievePoliceId = $result['records']['qzcyIds'];
			$groupName = $result['records']['groupname'];
			$str = '{"message":{"comCode":"05","codeId":"' . $date . '"},"result":{"type":"9","recievePoliceId":"' . $recievePoliceId . '","recievePoliceName":"' . $recievePoliceName . '","msg":"' . $messageStr . '","sendPoliceId":"' . $userid . '","sendPoliceName":"' . $sendPoliceName . '","gid":"' . $groupid . '","groupName":"' . $groupName . '"}}';
			$ret = $this->send($str);
		} else {
			$ret = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		}
		return $ret;
	}

	/** 
	* sendPictureToTerminal 
	* 向终端发送图片  
	*/
	public function sendPicture($groupid, $userid, $username, $size, $filename, $filename2, $sendName, $orgCode, $orgName) {
		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		$ret = array ();
		$errMsg = "群组不存在";
		$sendPoliceId = $userid;
		$sendPoliceName = $username;

		/**公安网start */
		$PHP_SELF = $_SERVER['PHP_SELF'];
		$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
		$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
		$url = 'http://' . $_SERVER['HTTP_HOST'] . substr($PHP_SELF, 0, strrpos($PHP_SELF, '/') + 1) . 'php/uploads/';
		$filename = str_replace($url, "", $filename);
		$filename2 = str_replace($url, "", $filename2);
		/**公安网end */

		$result = array ();
		$result = $this->getGroupDetailByGroupid($groupid, $userid);
		if ($result['result'] = 'true' && $result['records']) {
			//$messageStr = str_replace("\n", "", $msg);
			$recievePoliceName = $result['records']['qzcyName'];
			$recievePoliceId = $result['records']['qzcyIds'];
			$groupName = $result['records']['groupname'];
			$str = '{"message":{"comCode":"10","codeId":"' . $date . '"},"result":{"type":"2","gid":"' . $groupid . '","msg":"' . $sendName . '","recvPids":"' . $recievePoliceId . '","recvName":"' . $recievePoliceName . '","size":"' . $size . '","sendPid":"' . $userid . '","sendName":"' . $sendPoliceName . '","orgCode":"' . $orgCode . '","orgName":"' . $orgName . '","src1":"' . $filename . '","src2":"' . $filename2 . '","groupName":"' . $groupName . '"}}';
			$ret = $this->send($str);
		} else {
			$ret = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		}
		return $ret;
	}

	/** 
	* sendPictureToTerminal 
	* 向终端发送图片  
	*/
	public function sendAudio($groupid, $userid, $username, $size, $filename, $filename2) {
		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		$ret = array ();
		$errMsg = "群组不存在";
		$sendPoliceId = $userid;
		$sendPoliceName = $username;

		/**公安网start */
		$PHP_SELF = $_SERVER['PHP_SELF'];
		$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
		$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
		$url = 'http://' . $_SERVER['HTTP_HOST'] . substr($PHP_SELF, 0, strrpos($PHP_SELF, '/') + 1) . 'php/uploads/';
		$filename = str_replace($url, "", $filename);
		$filename2 = str_replace($url, "", $filename2);
		/**公安网end */

		$result = array ();
		$result = $this->getGroupDetailByGroupid($groupid, $userid);
		if ($result['result'] = 'true' && $result['records']) {
			//$messageStr = str_replace("\n", "", $msg);
			$recievePoliceName = $result['records']['qzcyName'];
			$recievePoliceId = $result['records']['qzcyIds'];
			$groupName = $result['records']['groupname'];
			$str = '{"message":{"comCode":"10","codeId":"' . $date . '"},"result":{"type":"3","gid":"' . $groupid . '","recvPids":"' . $recievePoliceId . '","recvName":"' . $recievePoliceName . '","size":"' . $size . '","sendPid":"' . $userid . '","sendName":"' . $sendPoliceName . '","src1":"' . $filename . '","src2":"' . $filename2 . '","groupName":"' . $groupName . '"}}';
			$ret = $this->send($str);
		} else {
			$ret = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		}
		return $ret;
	}

	/** 
	* sendPictureToTerminal 
	* 向终端发送图片  
	*/
	public function sendOthers($groupid, $userid, $username, $size, $filename, $filename2, $type, $sendName, $orgCode, $orgName) {
		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		$ret = array ();
		$errMsg = "群组不存在";
		$sendPoliceId = $userid;
		$sendPoliceName = $username;

		/**公安网start */
		$PHP_SELF = $_SERVER['PHP_SELF'];
		$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
		$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
		$url = 'http://' . $_SERVER['HTTP_HOST'] . substr($PHP_SELF, 0, strrpos($PHP_SELF, '/') + 1) . 'php/uploads/';
		$filename = str_replace($url, "", $filename);
		$filename2 = str_replace($url, "", $filename2);
		/**公安网end */

		$result = array ();
		$result = $this->getGroupDetailByGroupid($groupid, $userid);
		if ($result['result'] = 'true' && $result['records']) {
			//$messageStr = str_replace("\n", "", $msg);
			$recievePoliceName = $result['records']['qzcyName'];
			$recievePoliceId = $result['records']['qzcyIds'];
			$groupName = $result['records']['groupname'];
			$str = '{"message":{"comCode":"10","codeId":"' . $date . '"},"result":{"type":"' . $type . '","msg":"' . $sendName . '","gid":"' . $groupid . '","recvPids":"' . $recievePoliceId . '","recvName":"' . $recievePoliceName . '","size":"' . $size . '","sendPid":"' . $userid . '","sendName":"' . $sendPoliceName . '","orgCode":"' . $orgCode . '","orgName":"' . $orgName . '","src1":"' . $filename . '","src2":"' . $filename2 . '","groupName":"' . $groupName . '"}}';
			$ret = $this->send($str);
		} else {
			$ret = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		}
		return $ret;
	}

	public function send($msg) {
		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		$resArr = array ();
		$array2 = array ();
		$arr1 = array ();
		$resArrTrue = array ();
		$strPoliceIdMessage1 = '';

		//解析群发警员编号	
		//$messageStr = $msg;
		//$messageStr = str_replace("\n", "", $messageStr);
		//$str = '{"message":{"comCode":"05","codeId":"' . $date . '"},"result":{"recievePoliceId":"' . $RecivePoliceId . '","msg":"' . $messageStr . '","sendPoliceId":"' . $SendPoliceId . '","gid":"' . $id . '"}}';

		$length = mb_strlen($msg, 'UTF8');
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
		$word = $len . $msg;
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('could not create socket');
		$connect = @ socket_connect($socket, GlobalConfig :: getInstance()->socket_ip, GlobalConfig :: getInstance()->socket_port) or die('could not connect socket	server');

		//向服务端发送数据
		socket_write($socket, $word . "\n");

		//接受服务端返回数据
		$str = socket_read($socket, 1024, PHP_NORMAL_READ);

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
		$message = @ $obj->message->message;
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
			$strPoliceIdMessage1 .= $message1 . '<br>';

			$datas = array (
				'result' => $value
			);
			array_push($resArr, $datas);
		}

		//连接memcache
		$mem = new Memcache;
		$mem->connect(GlobalConfig :: getInstance()->memcache_ip, GlobalConfig :: getInstance()->memcache_port);

		//如果离线则把消息存到memcache中
		$memValue = '';
		$arrMessage = array ();
		$outLineArr = array ();

		socket_close($socket);
		//echo 'count($resArr)'.count($resArr);
		if (count($resArr) > 0) {
			$datas = array (
				'result' => 'faile',
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

	public function getGroupDetailByGroupid($gid, $userid) {
		$bRet = true;
		$errMsg = "";
		$data = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select g.id,g.qzmc,g.cjrid,g.qzcy,u.username from zdt_msg_group g,zdb_user u where u.userid=g.cjrid and g.id='$gid' and g.qzcy like '%$userid%'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$qzcy = iconv("GBK", "UTF-8", $row["QZCY"]);
				$array = explode(",", $qzcy);
				$userids = "";
				$members = array ();
				$qzcyNames = "";
				$qzcyIds = "";
				for ($i = 0; $i < count($array); $i++) {
					if ($i == 0)
						$userids = "'" . $array[$i] . "'";
					else
						$userids = $userids . ",'" . $array[$i] . "'";
				}
				//echo $userids;
				if ($userids != "") {
					$members = $this->getUserInfoByIds($userids);
					for ($i = 0; $i < count($members); $i++) {
						if ($i == 0) {
							$qzcyNames = $members[$i]['userName'];
							$qzcyIds = $members[$i]['userId'];
						} else {
							$qzcyNames = $qzcyNames . "," . $members[$i]['userName'];
							$qzcyIds = $qzcyIds . "," . $members[$i]['userId'];
						}
					}
				}

				$data = array (
					'groupid' => iconv("GBK", "UTF-8", $row["ID"]),
					'groupname' => iconv("GBK", "UTF-8", $row["QZMC"]),
					'qzcyIds' => $qzcyIds,
					'qzcyName' => $qzcyNames
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if (!$bRet)
			$res = array (
				'result' => 'false',
				'errmsg' => $errMsg,
				'records' => $data
			);
		else
			$res = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $data
			);
		return $res;
	}

	public function getMemcacheMsg($userid, $groupid) {
		$bRet = true;
		$errMsg = "";

		//连接memcache
		$mem = new Memcache;
		$mem_ip = GlobalConfig :: getInstance()->memcache_ip;
		$mem_port = GlobalConfig :: getInstance()->memcache_port;
		$mem->connect($mem_ip, $mem_port);

		//从memcache中查询部门中所有巡逻组合
		$msgs = $mem->get('msg_' . $userid);
		if ($msgs != "" && $msgs != '[]') {
			$msgs = json_decode($msgs, true);
		}
		//echo count($msgs);
		$groupMsg = array ();
		/*
		for ($i = 0; $i <  count($msgs); $i++) {
			$memcGid = $msgs[$i]['groupid'];
			if($groupid==$memcGid){
				array_push($groupMsg,$msgs[$i]);
			}
		}
		*/
		for ($i = count($msgs) - 1; $i >= 0; $i--) {
			$memcGid = $msgs[$i]['groupid'];
			if ($groupid == $memcGid) {
				if (isset ($msgs[$i]['type']) && ($msgs[$i]['type'] == "2" || $msgs[$i]['type'] == "3")) {
					$fileMsg = $msgs[$i];
					$PHP_SELF = $_SERVER['PHP_SELF'];
					$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
					$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
					$url = 'http://' . $_SERVER['HTTP_HOST'] . substr($PHP_SELF, 0, strrpos($PHP_SELF, '/') + 1) . 'php/uploads/';
					$fileMsg['chat'] = $url . $fileMsg['chat'];
					$fileMsg['src2'] = $url . $fileMsg['src2'];
					array_push($groupMsg, $fileMsg);
				} else {
					array_push($groupMsg, $msgs[$i]);
				}
				array_splice($msgs, $i, 1);
			}
		}

		if (count($msgs) > 0) {
			$mem->set('msg_' . $userid, encodeJson($msgs));
		} else {
			$mem->delete('msg_' . $userid);
		}

		//按时间排序
		$sort = array (
			'direction' => 'SORT_ASC', // SORT_DESC, SORT_ASC
	'field' => 'chattime'
		);
		$arrSort = array ();
		foreach ($groupMsg as $uniqid => $row) {
			foreach ($row as $key => $value) {
				$arrSort[$key][$uniqid] = $value;
			}
		}
		if ($sort['direction']) {
			array_multisort($arrSort[$sort['field']], constant($sort['direction']), $groupMsg);
		}

		//sort($groupMsg);

		return $groupMsg;
	}

	public function delMemcacheMsg($userid, $groupid) {
		$bRet = true;
		$errMsg = "";

		//连接memcache
		$mem = new Memcache;
		$mem_ip = GlobalConfig :: getInstance()->memcache_ip;
		$mem_port = GlobalConfig :: getInstance()->memcache_port;
		$mem->connect($mem_ip, $mem_port);

		$mem->delete('msg_' . $userid);

	}

	/***********************************公共组********************************************************/

	function createCommonGroups($qzmc, $cjrId, $qzcy) {
		$members = $this->getMembersByCy($qzcy, "", "");
		$qzmc = iconv("UTF-8", "GBK", $qzmc);
		$members = iconv("UTF-8", "GBK", $members);
		$id = $this->getCommonGroupCountByMembers($members);
		if ($id != "") {
			$arr = array (
				'result' => 'true',
				'errmsg' => '',
				'groupid' => $id
			);
			return $arr;
		}
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select COMMON_GROUPID_SEQ.NEXTVAL GROUPID from dual";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "GROUPID", $groupId);
		if (!@ oci_execute($stmt)) {
			$arr = array (
				'result' => 'false',
				'errmsg' => '获取群组ID出错'
			);
			return $arr;
		} else {
			oci_fetch($stmt);
		}
		$sql = "insert  into  ZDT_COMMON_GROUP  values('$groupId','$qzmc','$cjrId','$members','',to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS'))";

		$stmt = oci_parse($this->dbconn, $sql);
		$r = @ oci_execute($stmt);
		if (!$r) {
			$bRet = false;
			$errMsg = '';
		}
		oci_free_statement($stmt);
		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);

		else
			$arr = array (
				'result' => 'true',
				'errmsg' => '',
				'groupid' => $groupId
			);

		return $arr;

	}

	/**
	 * getCommonGroupCountByMembers
	 * 根据公共群组组员查询群组是否存在
	 * @param $members
	 * @return true or false
	 */
	public function getCommonGroupCountByMembers($members) {
		$bRet = true;
		$id = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select id from zdt_common_group where qzcy='$members'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$id = iconv("GBK", "UTF-8", $row["ID"]);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $id;

	}

	public function getCommonGroups($orgCode,$userId) {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select g.id,g.qzmc,g.cjrid,g.qzcy from zdt_common_group g where g.cjrid = '$userId' or instr(g.qzcy,'$userId')>0 ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$iscreator = false;
				//判断是否是自己创建的群组
				$cjrid = iconv("GBK", "UTF-8", $row["CJRID"]);
				if ($orgCode == "210200000000")
					$iscreator = true;
				$data = array (
					'groupid' => iconv("GBK", "UTF-8", $row["ID"]),
					'groupname' => iconv("GBK", "UTF-8", $row["QZMC"]),
					'iscreator' => $iscreator,
					'disabled' => false
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$result = array (
			'total' => "",
			'rows' => $datas
		);
		return $result;
	}

	public function deleteCommonGroupsById($id) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = " delete from zdt_common_group where id='$id' ";
		//echo  $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = "删除失败";
		}
		oci_free_statement($stmt);
		if ($bRet)
			$arr = array (
				'result' => 'true',
				'errmsg' => '删除成功'
			);
		else
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);

		return $arr;
	}

	public function getCommonGroupByGid($gids) {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select g.id,g.qzmc,g.cjrid,g.qzcy from zdt_common_group g where g.id in ($gids)";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'id' => iconv("GBK", "UTF-8", $row["ID"]),
					'qzmc' => iconv("GBK", "UTF-8", $row["QZMC"]),
					'cjrid' => iconv("GBK", "UTF-8", $row["CJRID"]),
					'qzcy' => iconv("GBK", "UTF-8", $row["QZCY"]),

					
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	public function writeFile($groupid, $msgData, $qzcyOld, $mineId) {
		$qzcyOldData = explode(",", $qzcyOld);
		for ($i = 0; $i < count($qzcyOldData); $i++) {
			$userStr = json_encode($msgData, JSON_UNESCAPED_UNICODE);
			$userid = $qzcyOldData[$i];
			if ($mineId == $userid)
				continue;
			$filename = GlobalConfig :: getInstance()->message_src . $userid . '.txt';
			//判断新号文件是否存在,不存在则创建
			$file_path = GlobalConfig :: getInstance()->message_src;
			$file = GlobalConfig :: getInstance()->message_src . $userid . '.txt';
			if (!file_exists($file_path)) {
				$TpmsDB = new TpmsDB(); //创建tpms数据库实例
				$res1 = $TpmsDB->mkdirs($file_path);
				$file = fopen($file, 'w');
				fclose($file);
			}

			$filename = fopen($filename, 'a');
			fwrite($filename, $userStr);
			fwrite($filename, "\r\n");
			fclose($filename);
		}
	}

	public function diffentArr($qzcyOld, $delMenbers) {
		//110102197105182337,210202197005245416,21021919720327643X....110102197105182337..........
		//110102197105182337,210202197005245416,21021919720327643X....110102197105182337......
		$result = array ();
		$qzcyOldData = explode(",", $qzcyOld);
		$delMenbersData = explode(",", $delMenbers);
		//$result = array_intersect($delMenbersData,$qzcyOldData);
		//$result = array_diff($delMenbersData,$result);
		//print_r($result);
		$result = array_diff($delMenbersData, $qzcyOldData);
		//print_r($result);
		$result = array_unique($result);
		sort($result);
		//print_r($result);
		return $result;
	}

	public function getmsgArr($qzcyOld, $qzcy, $type) {
		$result = "";
		//210202197005245416,210212197806013933....110102197105182337,210202197005245416,21020319680531301X......'110102197105182337',''....
		//echo $qzcyOld."....".$qzcy."......";
		if ($type == "del") {
			$diffMenbers = explode(",", $qzcy);
			;
		} else {
			$diffMenbers = $this->diffentArr($qzcyOld, $qzcy);
		}
		for ($i = 0; $i < count($diffMenbers); $i++) {
			if ($i == 0)
				$userids = "'" . $diffMenbers[$i] . "'";
			else
				$userids = $userids . ",'" . $diffMenbers[$i] . "'";
		}
		//echo $userids."....";
		$diffArr = $this->getUserInfoByIds($userids);
		for ($i = 0; $i < count($diffArr); $i++) {
			if ($i == 0) {
				$qzcyNames = $diffArr[$i]['userName'];
				$qzcyIds = $diffArr[$i]['userId'];
			} else
				if ($i == 1) {
					$qzcyNames = $qzcyNames . "," . $diffArr[$i]['userName'];
					$qzcyIds = $qzcyIds . "," . $diffArr[$i]['userId'];
				} else {
					$qzcyNames = $qzcyNames . "...";
					$qzcyIds = $qzcyIds . "...";
					break;
				}
		}
		if ($type == "del") {
			$result = $qzcyNames . "退出群组";
		} else {
			$result = $qzcyNames . "加入群组";
		}
		//print_r($result);
		return $result;

	}

	public function reGroupName($groupid, $qzmc, $userid) {
		$qzcyOld = "";
		$result = array ();
		$qzcyData = $this->getQzcyByByGid($groupid);
		if ($qzcyData['result'] == 'true') {
			$qzcyOld = $qzcyData['records']['qzcy'];
		}
		$result = $this->updateGroupsName($groupid, $qzmc);
		$msgData = array (
			//"msg"=>"",
	"msg" => array (
				"groupid" => $groupid,
				"type" => "9",
				"chat" => "会话更名为" . $qzmc
			),
			"groups" => array (
				"groupid" => $groupid,
				"groupname" => $result['records']['qzmc'],
				"disabled" => false
			)
		);
		$this->writeFile($groupid, $msgData, $qzcyOld, $userid);
		$resSend = $this->sendOtherMsg($groupid, "会话更名为" . $qzmc, $userid, "", "9");
		//if($result['records']&&$result['records']['userlist'])$result = $result['records']['userlist'];
		return $result;
	}

	public function updateGroupsName($gid, $qzmc) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$qzmc = iconv("UTF-8", "GBK", $qzmc);
		$sql = "update ZDT_MSG_GROUP  set qzmc='$qzmc' ";
		$sql = $sql . "where id='$gid'";
		$stmt = oci_parse($this->dbconn, $sql);
		$r = @ oci_execute($stmt);
		if (!$r) {
			$bRet = false;
			$errMsg = '';
		}
		oci_free_statement($stmt);
		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);

		else
			$arr = array (
				'result' => 'true',
				'errmsg' => ''
			);

		return $arr;
	}

	public function getEventBygroupId($gid) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.jqid,t.jqbh,t.jjlx,t.jjfs,t.bjfs,to_char(t.bjsj,'yyyy-MM-dd hh24:mi:ss') as bjsj,t.jqdz, t.jqdd, t.bjnr, t.jqlbdm,t.jqlxdm,t.jqxldm,t.jqdjdm,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.jqjqzb) as jqjqzb,to_char(t.rksj,'yyyy-MM-dd hh24:mi:ss') as rksj,to_char(t.gxsj,'yyyy-MM-dd hh24:mi:ss') as gxsj,t.cfjqbs,t.jqclzt,t1.bjrxm,t1.bjrsfzh,t1.bjrxbdm,t1.bjdh,t1.lxdh,t1.bjdhyhxm,t1.bjdhyhdz,t2.jjrbh,t2.jjrxm,to_char(t2.jjsj,'yyyy-MM-dd hh24:mi:ss') as jjsj,to_char(t2.hzsj,'yyyy-MM-dd hh24:mi:ss') as hzsj,t3.gxdwdm,t3.hphm,t3.xzqh,t4.ywbzxl,t4.bjchpzldm,t4.bjcph,t4.bkrs,fb_case.ssrs,fb_case.swrs,t4.sfsw,t4.sfswybj,t4.jqztdm,t4.zagjdm,t4.hzdjdm,t4.qhjzjgdm,t4.hzcsdm,t4.qhjzqkms,t4.plqk,t4.qhwdm,t4.ywty,t4.sfswhcl,MDSYS.Sdo_Util.to_wktgeometry_varchar(t5.mhjqzb) as mhjqzb,t6.cjdbh,t7.zlbh,t7.rybh,t7.xm,to_char(t7.zljssj, 'yyyy-MM-dd hh24:mi:ss') as zljssj,to_char(t7.ddxcsj, 'yyyy-MM-dd hh24:mi:ss') as ddxcsj,t7.cjld,t7.zlnr,fb_case.jqcljg,org.orgname,fb_case.zhrs,fb_case.jjssqk,fb_case.jzrs,fb_case.jjfvrs,fb_case.jjetrs,fb.belong,fb.scene from zdt_policeevent t
		    left join zdt_msg_group mg on mg.jqbh=t.jqbh left join ZDT_PoliceEvent_Reporter t1 on t.jqid=t1.jqid left join ZDT_PoliceEvent_Receiver t2 on t.jqid=t2.jqid left join ZDT_PoliceEvent_Admin t3 on t.jqid=t3.jqid left join ZDB_Organization org on t3.gxdwdm=org.orgcode left join ZDT_PoliceEvent_AddInfo t4 on t.jqid=t4.jqid left join ZDT_PoliceEvent_Location t5 on t.jqid=t5.jqid left join ZDT_PeProcess t6 on t.jqid=t6.jqid left join ZDT_PeProcess_Command t7 on t6.cjdbh =t7.cjdbh left join ZDT_PEFEEDBACK fb on fb.cjdbh=t6.cjdbh left join ZDT_PeFeedback_Case fb_case on t6.cjdbh = fb_case.cjdbh where t6.cjdzt='1' and t7.zlbs='1' and mg.id='$gid' order by t7.zlxdsj desc";
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
				$array = $this->getDispatchMenByCjdbh($cjdbh, $zlbh);
				$zlbhArr = $this->getPeProcessCommandByCjdbh($cjdbh, $rybh);
				$names = "";
				for ($i = 0; $i < count($array); $i++) {
					$name = $array[$i]['xm'];
					$p = $i == 0 ? "" : ",";
					$names .= $p . $name;
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
					'commandRecords' => $zlbhArr
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
	 * getDispatchMenByCjdbh
	 * 根据cjdbh查询出动警力表信息
	 * @param $cjdbh
	 * @return 警力对象组
	 */
	public function getDispatchMenByCjdbh($cjdbh, $zlbh) {
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
	public function getPeProcessCommandByCjdbh($cjdbh, $rybh) {
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
	 * 查询通信录根据模糊条件查询
	 */
	public function getSearchAllContact($type, $key, $orgCode) {
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$key = iconv("UTF-8", "GBK", $key);
		if ($type == "1") {
			$sql = "select usr.userid,usr.username,o.orgcode,o.orgname,o.orgcode as groupcode,o.orgname as groupname from zdb_user usr left join zdb_organization o on o.orgcode=usr.bz where 1=1 and (o.orglevel ='10' or o.orglevel ='20' or o.orglevel ='21' or o.orglevel ='32') and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode') and (usr.userid like '%$key%' or usr.username like '%$key%') and rownum<200 order by o.orgcode asc";
		} else
			if ($type == "2") {
				$sql = "select usr.userid,usr.username,o.orgcode,o.orgname,o.orgcode as groupcode,o.orgname as groupname from zdb_user usr left join zdb_organization o on o.orgcode=usr.bz where 1=1 and (o.orglevel ='10' or o.orglevel ='20' or o.orglevel ='21' or o.orglevel ='32') and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode') and (o.orgcode like '%$key%' or o.orgname like '%$key%') and rownum<200 order by o.orgcode asc";
			} else
				if ($type == "3") {
					$sql = "SELECT usr.userid,usr.username,gp.orgcode,gp.orgname,gp.hphm||' '||gp.orgname as groupname,gp.gid as groupcode FROM Zdb_User usr,(";
					$sql = $sql . "select g.gid,g.userid,g.orgcode,o.orgname,c.hphm from zdt_duty_group g left join zdb_organization o on o.orgcode=g.orgcode left join zdb_equip_car c on c.id=g.carid";
					$sql = $sql . " where g.status!='3' and (c.hphm like '%$key%' or o.orgcode like '%$key%' or o.orgname like '%$key%') order by o.orgcode asc) gp";
					$sql = $sql . " where instr(gp.userid,usr.userid)>0";
				}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'userId' => iconv("GBK", "UTF-8", $row["USERID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'groupCode' => iconv("GBK", "UTF-8", $row["GROUPCODE"]),
					'groupName' => iconv("GBK", "UTF-8", $row["GROUPNAME"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$result = array (
			'total' => "",
			'rows' => $datas
		);
		return $result;
	}

	/**
	 * 查询通信录根据模糊条件查询
	 * 针对巡逻组
	 */
	public function getSearchGroupContact($key, $orgCode) {
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$key = iconv("UTF-8", "GBK", $key);
		$sql = "select g.gid,g.userid,g.orgcode,o.orgname,c.hphm from zdt_duty_group g left join zdb_organization o on o.orgcode=g.orgcode";
		$sql = $sql . " left join zdb_equip_car c on c.id=g.carid where g.status!='3' and (o.orglevel ='10' or o.orglevel ='20' or o.orglevel ='21' or o.orglevel ='32')";
		$sql = $sql . " and (c.hphm like '%$key%' or o.orgcode like '%$key%' or o.orgname like '%$key%') and rownum<200 order by o.orgcode asc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$qzcy = iconv("GBK", "UTF-8", $row["USERID"]);
				$array = explode(",", $qzcy);
				$userids = "";
				$members = array ();
				for ($i = 0; $i < count($array); $i++) {
					if ($i == 0)
						$userids = "'" . $array[$i] . "'";
					else
						$userids = $userids . ",'" . $array[$i] . "'";
				}
				if ($userids != "")
					$members = $this->getMembersByIds($userids);
				$data = array (
					'userId' => iconv("GBK", "UTF-8", $row["USERID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$result = array (
			'total' => "",
			'rows' => $datas
		);
		return $result;
	}

	public function getDutyGroupById($gid) {
		$bRet = true;
		$errMsg = "";
		$data = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select g.jqbh,g.id,g.qzmc,g.cjrid,g.qzcy,u.username from zdt_msg_group g,zdb_user u where u.userid=g.cjrid and g.sfsc!='1' and g.id='$gid'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$qzcy = iconv("GBK", "UTF-8", $row["QZCY"]);
				$array = explode(",", $qzcy);
				$userids = "";
				$members = array ();
				for ($i = 0; $i < count($array); $i++) {
					if ($i == 0)
						$userids = "'" . $array[$i] . "'";
					else
						$userids = $userids . ",'" . $array[$i] . "'";
				}
				//echo $userids;
				if ($userids != "")
					$members = $this->getMembersByIds($userids);
				$data = array (
					'id' => iconv("GBK", "UTF-8", $row["ID"]),
					'qzmc' => iconv("GBK", "UTF-8", $row["QZMC"]),
					'cjrid' => iconv("GBK", "UTF-8", $row["CJRID"]),
					'cjrxm' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'jqbh' => iconv("GBK", "UTF-8", $row["JQBH"]),
					'qzcy' => $members
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if (!$bRet)
			$res = array (
				'result' => 'false',
				'errmsg' => $errMsg,
				'records' => $data
			);
		else
			$res = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $data
			);
		return $res;
	}

	public function getCommonGroupsById($id) {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select g.qzcy,o.orgcode,o.orgname,usr.userid,usr.username from zdt_common_group g,zdb_organization o,zdb_user usr where o.orgcode=usr.bz and instr(g.qzcy,usr.userid)>0 and g.id='$id' ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'userId' => iconv("GBK", "UTF-8", $row["USERID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$result = array (
			'total' => "",
			'rows' => $datas
		);
		return $result;
	}

	public function changeArray($menbers, $userid) {
		for ($i = 0; $i < count($menbers); $i++) {
			if ($menbers[$i]['userId'] == $userid) {
				$temp1 = $menbers[$i];
				$temp2 = $menbers[0];
				$menbers[0] = $temp1;
				$menbers[$i] = $temp2;
				break;
			}
		}
		return $menbers;
	}

	public function getImMsgList($userid,$groupname, $page, $rows) {
		$bRet = true;
		$errMsg = "";
		$row_count = 0;
		$datas = array ();
		$result = array (
			'total' => 0,
			'rows' => $datas
		);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		/*组成sql*/
		$sql = "select count(*) ROWCOUNT from (select m.gid from zdt_msg m  group by m.gid) msg left join zdt_msg_group g on g.id=msg.gid where 1=1 and g.qzcy like '%$userid%'";
		if ($groupname != "") {
			$groupname = iconv("UTF-8", "GBK", $groupname);
			$sql = $sql . " and g.qzmc like '%$groupname%'";
		}
		$sql = $sql . " order by g.cjsj desc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $row_count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = "查询失败";
		} else {

			/*处理分页*/
			oci_fetch($stmt);
			$total_rec = $row_count;
			oci_free_statement($stmt);

			/*查询部门*/
			if ($this->dbconn == null)
				$this->dbconn = $this->LogonDB();
			$sql = str_replace("select count(*) ROWCOUNT", "select msg.gid,g.qzmc,g.jqbh", $sql);
			$sql = pageResultSet($sql, $page, $rows);

			$stmt = oci_parse($this->dbconn, $sql);
			if (!@ oci_execute($stmt)) {
				$bRet = false;
				$errMsg = "查询失败";
			} else {
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array (
						'groupid' => iconv("GBK", "UTF-8", $row["GID"]),
						'groupname' => iconv("GBK", "UTF-8", $row["QZMC"]),
						'jqbh' => iconv("GBK", "UTF-8", $row["JQBH"])
					);
					array_push($datas, $data);
				}
				oci_free_statement($stmt);
				oci_close($this->dbconn);
				$result = array (
					'total' => $total_rec,
					'rows' => $datas
				);
			}
		}
		return $result;
	}
	
	public function getImMsgDetailList($key, $serachDate,$groupid,$page, $rows) {
		$bRet = true;
		$errMsg = "";
		$row_count = 0;
		$datas = array ();
		$result = array (
			'total' => 0,
			'rows' => $datas
		);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		/*组成sql*/
		$sql = "select count(*) ROWCOUNT from zdt_msg t left join zdb_user usr on usr.userid=t.fsrid left join zdb_organization o on o.orgcode=usr.bz where t.gid='$groupid'";
		if ($key != "") {
			$key = iconv("UTF-8", "GBK", $key);
			$sql = $sql . " and t.xxnr like '%$key%'";
		}
		if ($serachDate != "") {
			$key = iconv("UTF-8", "GBK", $key);
			$sql = $sql . " and to_char(to_date(t.fssj,'yyyy-mm-dd hh24:mi:ss'),'yyyy-mm-dd')='$serachDate'";
		}
		$sql = $sql . " order by t.fssj asc";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "ROWCOUNT", $row_count);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = "查询失败";
		} else {

			/*处理分页*/
			oci_fetch($stmt);
			$total_rec = $row_count;
			oci_free_statement($stmt);

			/*查询部门*/
			if ($this->dbconn == null)
				$this->dbconn = $this->LogonDB();
			$sql = str_replace("select count(*) ROWCOUNT", "select t.fsrid,t.gid,t.xxnr,t.fssj,t.xxlx,usr.username,o.orgname", $sql);
			$sql = pageResultSet($sql, $page, $rows);
			//echo $sql;
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@ oci_execute($stmt)) {
				$bRet = false;
				$errMsg = "查询失败";
			} else {
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$type = iconv("GBK", "UTF-8", $row["XXLX"]);
					$chat = iconv("GBK", "UTF-8", $row["XXNR"]);
					if (isset($type) && ($type == "2" || $type == "3"|| $type == "4"|| $type == "5")) {
						$PHP_SELF = $_SERVER['PHP_SELF'];
						$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
						$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
						$url = 'http://' . $_SERVER['HTTP_HOST'] . substr($PHP_SELF, 0, strrpos($PHP_SELF, '/') + 1) . 'php/uploads/';
						$chat = $url . $chat;
					}
					$data = array (
						'groupid' => iconv("GBK", "UTF-8", $row["GID"]),
						'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
						'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
						'chat' => $chat,
						'type' => $type,
						'chattime' => iconv("GBK", "UTF-8", $row["FSSJ"])
					);
					array_push($datas, $data);
				}
				oci_free_statement($stmt);
				oci_close($this->dbconn);
				$result = array (
					'total' => $total_rec,
					'rows' => $datas
				);
			}
		}
		return $result;
	}
	
	public function getAllContactsGroupsById($groupArray) {
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		$result = array ();
		$groupIds = "";
		
		if(count($groupArray)<=0){
			return $datas;
		}
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		for ($i = 0; $i < count($groupArray); $i++) {
			if ($i == 0)
				$groupIds = "'" . $groupArray[$i]['id'] . "'";
			else
				$groupIds = $groupIds . ",'" . $groupArray[$i]['id'] . "'";
		}
		if($groupIds==""){
			return $datas;
		}
		$sql = "select g.userid,o.orgcode,o.orgname,usr.userid,usr.username from zdt_duty_group g,zdb_organization o,zdb_user usr where g.status!='3' and o.orgcode=g.orgcode and instr(g.userid,usr.userid)>0 and g.gid in ($groupIds) ";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'userId' => iconv("GBK", "UTF-8", $row["USERID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		$result = array (
			'total' => "",
			'rows' => $datas
		);
		return $result;
	}

}
?>
