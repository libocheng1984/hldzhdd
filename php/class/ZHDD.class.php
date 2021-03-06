<?php

/**
 * class zhdd
 * version: 1.0
 * 指挥调度类
 * author: carl
 * 2014/6/17
 * 
 * 此类定义交通信息中心全部方法
 * 使用前必须先引用TpmsDB.class.php和GlobalConfig.class.php
 */
class ZHDD extends TpmsDB {

	/**
	 * 根据部门编号查询所有巡逻组合
	 * @param orgCode
	 * @return
	 */
	public function GetDutyGroupByOrg($orgCode) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		if ($orgCode == GlobalConfig :: getInstance()->dsdm.'00030000')
			$orgCode = GlobalConfig :: getInstance()->dsdm.'00000000'; // modify by carl

		//$sql = "select gp.*,car.*,org.orgname from zdt_duty_group gp, zdb_organization org, zdb_equip_car car where gp.orgcode=org.orgCode and //(org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode') and not gp.status='3' and car.id=gp.carid";
		$sql = "select gp.*, car.*, org.orgname,m.dhhm  from zdt_duty_group gp left join zdb_equip_model m on m.userid=gp.leaderid" .
		" left join  zdb_organization org on gp.orgcode = org.orgCode left join  zdb_equip_car car on car.id = gp.carid" .
		" where 1=1  and (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode') and not gp.status = '3'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			oci_close($this->dbconn);
			exit;
		} else {
			$groups = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$group = array (
					'gid' => iconv("GBK", "UTF-8", $row["GID"]),
					'carid' => iconv("GBK", "UTF-8", $row["CARID"]),
					'userid' => iconv("GBK", "UTF-8", $row["USERID"]),
					'm350id' => iconv("GBK", "UTF-8", $row["M350ID"]),
					'leaderid' => iconv("GBK", "UTF-8", $row["LEADERID"]),
					'status' => iconv("GBK", "UTF-8", $row["STATUS"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'hpzl' => iconv("GBK", "UTF-8", $row["HPZL"]),
					'pp' => iconv("GBK", "UTF-8", $row["PP"]),
					'orgcode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgname' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'dhhm' => iconv("GBK", "UTF-8", $row["DHHM"])
				);
				array_push($groups, $group);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $groups;
	}

	/**
	 * 根据部门编号查询所有警车
	 * @param orgCode
	 * @return
	 */
	public function GetCarByOrg($orgCode) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select car.hphm,car.pp,org.orgcode,org.orgname,car.id as carId from zdb_equip_car car, zdb_organization org, zdt_gps_dynamic gps where car.dwdm=org.orgCode and (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode') and gps.id=car.id";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {
			$cars = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$car = array (
					'id' => iconv("GBK", "UTF-8", $row["CARID"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'pp' => iconv("GBK", "UTF-8", $row["PP"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'type' => '1'
				);
				array_push($cars, $car);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $cars;
	}

	/**
	 * 根据部门编号查询所有警员
	 * @param orgCode
	 * @return
	 */
	public function GetMenByOrg($orgCode) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		if ($orgCode == GlobalConfig :: getInstance()->dsdm.'00030000')
			$orgCode = GlobalConfig :: getInstance()->dsdm.'00000000'; // modify by carl

		$sql = "select usr.id,usr.userid,usr.username,usr.alarm,org.orgcode,org.orgname from zdb_user usr, zdb_organization org, zdt_gps_dynamic gps where usr.bz=org.orgCode and (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode') and gps.id=usr.userid";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			//echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {
			$mens = array ();

			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'id' => iconv("GBK", "UTF-8", $row["USERID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'userCode' => iconv("GBK", "UTF-8", $row["ALARM"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'type' => '2'
				);
				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $mens;
	}

	/**
	 * 读取单个定位信息
	 * @param id
	 * @return
	 */
	public function GetLocationFromDB($id) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select gps.isonline,gps.deviceid,gps.direction,gps.speed,gps.road,gps.status,gps.savetime,gps.recvtime,gps.locatetime,gps.roletype,gps.devicetype,MDSYS.Sdo_Util.to_wktgeometry_varchar(gps.location) as location from zdt_gps_dynamic gps where gps.id='$id'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($dbconn);
			exit;
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				if ($row["DEVICETYPE"] == 1) {
					$roleInfo = $this->getCarInfoById($id);
				} else {
					$roleInfo = $this->getUserInfoById($id);
					//$groupInfo = $this->getDutyGroup($id);
				}
				$data = array (
					'id' => $id,
					'hphm' => isset ($roleInfo['hphm']) ? $roleInfo['hphm'] : "",
					'hpzl' => isset ($roleInfo['hpzl']) ? $roleInfo['hpzl'] : "",
					'pp' => isset ($roleInfo['pp']) ? $roleInfo['pp'] : "",
					'orgCode' => isset ($roleInfo['orgCode']) ? $roleInfo['orgCode'] : "",
					'orgName' => isset ($roleInfo['orgName']) ? $roleInfo['orgName'] : "",
					'userName' => isset ($roleInfo['userName']) ? $roleInfo['userName'] : "",
					'alarm' => isset ($roleInfo['alarm']) ? $roleInfo['alarm'] : "",
					'isOnLine' => iconv("GBK", "UTF-8", $row["ISONLINE"]),
					'deviceId' => iconv("GBK", "UTF-8", $row["DEVICEID"]),
					'location' => iconv("GBK", "UTF-8", $row["LOCATION"]),
					'direction' => iconv("GBK", "UTF-8", $row["DIRECTION"]),
					'speed' => iconv("GBK", "UTF-8", $row["SPEED"]),
					'road' => iconv("GBK", "UTF-8", $row["ROAD"]),
					'status' => iconv("GBK", "UTF-8", $row["STATUS"]),
					'saveTime' => iconv("GBK", "UTF-8", $row["SAVETIME"]),
					'recvTime' => iconv("GBK", "UTF-8", $row["RECVTIME"]),
					'locateTime' => iconv("GBK", "UTF-8", $row["LOCATETIME"]),
					'roleType' => iconv("GBK", "UTF-8", $row["ROLETYPE"]),
					'deviceType' => iconv("GBK", "UTF-8", $row["DEVICETYPE"]) //,
	//'g_hphm'=>isset($groupInfo['hphm'])?$groupInfo['hphm']:""
	
				);
				return $data;
			}
		}
	}

	/**
	 * 获取巡逻组合详细信息
	 * @param id
	 * @return
	 */
	public function getDutyGroup($id) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select gp.*,car.* from zdt_duty_group gp,zdb_equip_car car where gp.leaderid='$id' and not (gp.status='3') and car.id=gp.carid";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($dbconn);
			exit;
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'gid' => iconv("GBK", "UTF-8", $row["GID"]),
					'carid' => iconv("GBK", "UTF-8", $row["CARID"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'userid' => iconv("GBK", "UTF-8", $row["USERID"]),
					'leaderid' => iconv("GBK", "UTF-8", $row["LEADERID"]),
					'createtime' => iconv("GBK", "UTF-8", $row["CREATETIME"])
				);
				return $data;
			}
		}
	}

	/**
	 * getDynamicLocation
	 * 查询实时定位信息
	 */
	public function getDynamicLocation($orgCode, $lastTime) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		//连接memcache
		$mem = new Memcache;
		$mem_ip = GlobalConfig :: getInstance()->memcache_ip;
		$mem_port = GlobalConfig :: getInstance()->memcache_port;
		$mem->connect($mem_ip, $mem_port);

		//从memcache中查询部门中所有车辆和警员
		$policeInfos = $mem->get($orgCode);
		$policeInfosArray = json_decode($policeInfos, true);
		if ($policeInfos == "") {
			//memcache里没有部门数据，需要从数据库读取
			$carInfosArray = $this->GetCarByOrg($orgCode);
			$menInfosArray = $this->GetMenByOrg($orgCode);
			$policeInfosArray = array_merge($carInfosArray, $menInfosArray);
			$mem->set($orgCode, encodeJson($policeInfosArray));
		}

		//print_r($policeInfosArray);
		//exit;
		$count = count($policeInfosArray);
		$lt = null;
		$gpsInfos = array ();
		for ($i = 0; $i < $count; $i++) {
			$policeId = $policeInfosArray[$i]['id'];
			$type = $policeInfosArray[$i]['type'];
			//$name = $policeInfosArray[$i]['hphm'];
			//$pp = $policeInfosArray[$i]['pp'];
			//$orgCode = $policeInfosArray[$i]['orgCode'];
			//$orgName = $policeInfosArray[$i]['orgName'];

			$gpsInfoStr = $mem->get($policeId);
			$gpsInfo = json_decode($gpsInfoStr, true);
			if ($gpsInfoStr == "") {
				//memcache里没有定位数据，需要从数据库读取
				$gpsInfo = $this->GetLocationFromDB($policeId);
				//echo encodeJson($gpsInfo);
				if ($gpsInfo == false) //db op error
					continue;

				if ($gpsInfo != "" || $gpsInfo != null) {
					$mem->set($policeId, encodeJson($gpsInfo));
				}
			}

			if ($gpsInfo != "" || $gpsInfo != null) {
				//计算图标地址
				$iconSrc = $this->getIconSrc($gpsInfo['roleType'], $gpsInfo['isOnLine'], $gpsInfo['direction']);
				$gpsInfo['iconSrc'] = $iconSrc;
				$DirectionChinz = $this->getDirectionChinz($gpsInfo['direction']);
				$gpsInfo['directionZh'] = $DirectionChinz;

				//判断lasttime是否为null
				if ($lastTime != null) {
					if ($gpsInfo['saveTime'] > $lastTime)
						array_push($gpsInfos, $gpsInfo);
				} else
					array_push($gpsInfos, $gpsInfo);

				//获取最晚时间
				if ($lt == null || $lt < $gpsInfo['saveTime'])
					$lt = $gpsInfo['saveTime'];
			}
		}

		$res = new PolicePoints($lt, $gpsInfos);
		return $res;
	}

	/**
	 * getGroupDynamicLocation
	 * 查询巡逻组实时定位信息
	 */
	public function getGroupDynamicLocation($orgCode, $lastTime) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		//连接memcache
		$mem = new Memcache;
		$mem_ip = GlobalConfig :: getInstance()->memcache_ip;
		$mem_port = GlobalConfig :: getInstance()->memcache_port;
		$result = $mem->connect($mem_ip, $mem_port);
		if (!$result) {
			$res = new PolicePoints($lastTime, array ());
			return $res;
		}
		//从memcache中查询部门中所有巡逻组合
		$groupInfos = $mem->get('g' . $orgCode);
		if ($groupInfos==false||$groupInfos == "" || $groupInfos == '[]') {
			$lastTime = null;
			//memcache里没有部门数据，需要从数据库读取
			$groupInfos = $this->GetDutyGroupByOrg($orgCode);
			$mem->set('g' . $orgCode, encodeJson($groupInfos));
		} else {
			$groupInfos = json_decode($groupInfos, true);
		}

		//print_r($groupInfos);
		//exit;
		$count = count($groupInfos); //巡逻组数量
		$lt = null;
		$policeDynamic = null;
		$gpsInfos = array ();
		$gidInfos = array ();
		for ($i = 0; $i < $count; $i++) {
			array_push($gidInfos, $groupInfos[$i]['gid']);
			$onlineState = "0";
			$onlineSaveTime = "";
			//查询警员定位数据
			$userid = $groupInfos[$i]['userid'];
			$leaderid = $groupInfos[$i]['leaderid'];
			//查询警员在线状态
			$onlineObj = $mem->get("onLineState_" . $leaderid);
			$onlineJsonObj = json_decode($onlineObj, true);
			$onlineState = $onlineJsonObj['isOnLine'];
			$onlineSaveTime = $onlineJsonObj['saveTime'];

			$location = $this->getDeviceLocation($mem, $leaderid);

			/*
			$idArray = explode(",",$userid);
			for ($j = 0; $j < count($idArray); $j++) {
				$location = $this->getDeviceLocation($mem, $idArray[$j]);
				if ($location!=null){
					break;
				}
					
			}
			*/

			if ($location == null || $onlineState == "0") {
				//查询350M定位数据
				$m350id = $groupInfos[$i]['m350id'];
				$idArray = explode(",", $m350id);
				for ($j = 0; $j < count($idArray); $j++) {
					$location_350 = $this->getDeviceLocation($mem, $idArray[$j]);
					if ($location_350 != null) {
						if (isset ($location['saveTime']) && $location_350['saveTime'] < $location['saveTime']) {
							$location_350['saveTime'] = $location['saveTime'];
						}
						if (isset ($location['userName'])) {
							$location_350['userName'] = $location['userName'];
						}
						if (isset ($location['alarm'])) {
							$location_350['alarm'] = $location['alarm'];
						}
						$location = $location_350;
						break;
					}
				}
			}

			if ($location == null) {
				//查询车载北斗定位数据
				$carid = $groupInfos[$i]['carid'];
				//$location = $this->getDeviceLocation($carid);
				$location_bd = $this->getDeviceLocation($mem, $carid);
				if ($location_bd != null) {
					if (isset ($location['saveTime']) && $location_bd['saveTime'] < $location['saveTime']) {
						$location_bd['saveTime'] = $location['saveTime'];
					}
					$location = $location_bd;
					break;
				}
				/*
				if ($location!=null){
					$location['isOnLine']="0";
					break;
				}
				*/
			}

			if ($location == null) {
				continue;
			}

			$location['hphm'] = $groupInfos[$i]['hphm'];
			$location['hpzl'] = $groupInfos[$i]['hpzl'];
			$location['pp'] = $groupInfos[$i]['pp'];
			$location['orgCode'] = $groupInfos[$i]['orgcode'];
			$location['orgName'] = $groupInfos[$i]['orgname'];
			$location['dhhm'] = $groupInfos[$i]['dhhm'];
			$location['qzcy'] = $userid;
			$location['leaderid'] = $groupInfos[$i]['leaderid'];
			$location['m350id'] = $groupInfos[$i]['m350id'];
			$location['gid'] = $groupInfos[$i]['gid'];
			$location['isOnLine'] = $onlineState;

			//print_r($location);
			$orgCodeDatas = array (
				'210200291100',
				'210200291200',
				'210202190000',
				'210202200000',
				'210202210000',
				'210203180000',
				'210203190000',
				'210204210000',
				'210204180000',
				'210204190000',
				'210204200000',
				'210211190000',
				'210211200000',
				'210211210000',
				'210211220000',
				'210211230000',
				'210212200000',
				'210212210000',
				'210299290000',
				'210299300000',
				'210299310000',
				'210213340000',
				'210213350000',
				'210296180000',
				'210297070000',
				'210294180000',
				'210200410900',
				'210282330000',
				'210282340000',
				'210281310000',
				'210281320000',
				'210283330000',
				'210224140000'
			);
			if (in_array($location['orgCode'], $orgCodeDatas)) {
				$iconSrc = $this->getGroupJddIconSrc($onlineState, $location['direction']);
			} else {
				//计算图标地址
				$iconSrc = $this->getGroupIconSrc($onlineState, $location['direction']);
			}

			$location['iconSrc'] = $iconSrc;
			$DirectionChinz = $this->getDirectionChinz($location['direction']);
			$location['directionZh'] = $DirectionChinz;

			//判断lasttime是否为null
			if ($lastTime != null) {
				//$time = $lastTime;
				//$currentDate = strtotime($time);
				//$futureDate = $currentDate-(60);
				//$formatDate = date("Y-m-d H:i:s", $futureDate);
				if ($location['saveTime'] > $lastTime || $onlineSaveTime == "" || $onlineSaveTime > $lastTime)
					array_push($gpsInfos, $location);
				//if ($location['saveTime'] > $formatDate)
				//	array_push($gpsInfos, $location);
			} else
				array_push($gpsInfos, $location);

			//获取最晚时间
			//if(isset($location['saveTime'])){

			//echo $formatDate;
			if ($lt == null || $lt < $location['saveTime'])
				$lt = isset ($location['saveTime']) ? $location['saveTime'] : "";
			/*
			if ($lt == null || $lt < $formatDate){
				$lt = isset($formatDate)?$formatDate:"";
			}else{
				$lt = "";
			}
			*/

			//}

		}

		$res = new PolicePoints($lt, $gpsInfos);
		$res->gids = $gidInfos;
		return $res;
	}

	/**
	 * getDeviceLocation
	 * 查询设备定位信息
	 * $id: 车辆id，350M设备编号，警员身份证号
	 * 查询成功返回json
	 * 查询失败返回null
	 */
	public function getDeviceLocation($mem, $id) {
		$locationInfo = $mem->get($id);
		if ($locationInfo == "") {
			//echo "userdata.........".$id;
			//从数据库读取
			$locationInfo = $this->GetLocationFromDB($id);
			if ($locationInfo != "" || $locationInfo != null) {
				$mem->set($id, encodeJson($locationInfo));
			}
		} else {
			$locationInfo = json_decode($locationInfo, true);
		}
		return $locationInfo;
	}

	/**
	 * getIconSrc
	 * 获取定位图片地址
	 */
	public function getIconSrc($type, $online, $angles) {
		//在线警员方向  	
		if ($type == 2 && $online == 1) {
			if (($angles == null || $angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 337.5)) {
				return 'man_on_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'man_on_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'man_on_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'man_on_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'man_on_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'man_on_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'man_on_7.png';
			}
			if ($angles >= 292.5 && $angles < 337.5) {
				return 'man_on_8.png';
			}
		}
		//离线警员方向
		if ($type == 2 && $online == 0) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'man_off_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'man_off_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'man_off_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'man_off_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'man_off_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'man_off_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'man_off_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'man_off_8.png';
			}
		}
		//在线警车方向
		if ($type == 1 && $online == 1) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'car_on_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'car_on_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'car_on_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'car_on_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'car_on_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'car_on_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'car_on_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'car_on_8.png';
			}
		}
		//离线警车方向
		if ($type == 1 && $online == 0) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'car_off_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'car_off_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'car_off_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'car_off_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'car_off_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'car_off_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'car_off_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'car_off_8.png';
			}
		}
	}

	/**
	 * getDirectionChinz
	 * 获取方向中文
	 */
	public function getDirectionChinz($angles) {
		if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 337.5)) {
			return '北';
		}
		if ($angles >= 22.5 && $angles < 67.5) {
			return '东北';
		}
		if ($angles >= 67.5 && $angles < 112.5) {
			return '东';
		}
		if ($angles >= 112.5 && $angles < 157.5) {
			return '东南';
		}
		if ($angles >= 157.5 && $angles < 202.5) {
			return '南';
		}
		if ($angles >= 202.5 && $angles < 247.5) {
			return '西南';
		}
		if ($angles >= 247.5 && $angles < 292.5) {
			return '西';
		}
		if ($angles >= 292.5 && $angles < 337.5) {
			return '西北';
		}
	}

	/**
	 * getGroupIconSrc
	 * 获取巡逻组定位图片地址
	 */
	public function getGroupIconSrc($online, $angles) {
		//在线警车方向
		if ($online == 1) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'car_on_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'car_on_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'car_on_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'car_on_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'car_on_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'car_on_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'car_on_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'car_on_8.png';
			}
		}
		//离线警车方向
		if ($online == 0) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'car_off_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'car_off_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'car_off_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'car_off_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'car_off_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'car_off_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'car_off_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'car_off_8.png';
			}
		}
	}

	/**
	 * getGroupJddIconSrc
	 * 获取巡逻组定位图片地址
	 */
	public function getGroupJddIconSrc($online, $angles) {
		//在线警车方向
		if ($online == 1) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'ls_on_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'ls_on_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'ls_on_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'ls_on_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'ls_on_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'ls_on_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'ls_on_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'ls_on_8.png';
			}
		}
		//离线警车方向
		if ($online == 0) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'ls_off_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'ls_off_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'ls_off_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'ls_off_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'ls_off_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'ls_off_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'ls_off_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'ls_off_8.png';
			}
		}
	}

	/*********************************************************************************************
	 *            350兆测试
	 ********************************************************************************************/

	/**
	 * 查询所有350兆设备号
	 * @return
	 */
	public function Get350() {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select id from zdt_gps_dynamic where devicetype='2'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {
			$datas = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'id' => iconv("GBK", "UTF-8", $row["ID"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	/**
	 * 查询所有350兆设备号
	 * @return
	 */
	public function Get350ByOrgCode($orgCode) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		if ($orgCode == GlobalConfig :: getInstance()->dsdm.'00030000')
			$orgCode = GlobalConfig :: getInstance()->dsdm.'00000000'; // modify by carl

		//$sql = "select m.id, org.orgcode, org.orgname  from zdb_equip_350m m, zdb_organization org, zdt_gps_dynamic gps where m.dwdm = org.orgCode   and //(org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode')   and gps.id = m.id".
		//		" and m.id not in(select g.m350id from zdt_duty_group g where g.status!='3')";
		$sql = "select id from zdt_gps_dynamic where devicetype='2'";
		$sql = $sql . " and id not in(select g.m350id from zdt_duty_group g where g.status!='3')";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {
			$datas = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'id' => iconv("GBK", "UTF-8", $row["ID"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	/**
	 * getGPS350
	 * 查询350m实时定位信息
	 */
	public function getGPS350_bak($lastTime) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		//连接memcache
		$mem = new Memcache;
		$mem_ip = GlobalConfig :: getInstance()->memcache_ip;
		$mem_port = GlobalConfig :: getInstance()->memcache_port;
		$mem->connect($mem_ip, $mem_port);

		//从memcache中查询所有350兆设备号
		$m350Infos = $mem->get('g350');
		if ($m350Infos == "" || $m350Infos == '[]') {
			//memcache里没有，需要从数据库读取
			$m350Infos = $this->Get350();
			$mem->set('g350', encodeJson($m350Infos));
		} else {
			$m350Infos = json_decode($m350Infos, true);
		}

		//print_r($m350Infos);
		//exit;
		$count = count($m350Infos); //巡逻组数量
		$lt = null;
		$policeDynamic = null;
		$gpsInfos = array ();
		for ($i = 0; $i < $count; $i++) {
			//查询350M定位数据
			$m350id = $m350Infos[$i]['id'];
			$location = $mem->get($m350id);
			if ($location == "") {
				//从数据库读取
				$location = $this->GetLocationFromDB($m350id);
				if ($location != "" || $location != null) {
					$mem->set($m350id, encodeJson($location));
				}
			} else {
				$location = json_decode($location, true);
			}

			//print_r($location);

			//计算图标地址
			$iconSrc = $this->get350mIconSrc($location['isOnLine'], $location['direction']);
			$location['iconSrc'] = $iconSrc;
			$DirectionChinz = $this->getDirectionChinz($location['direction']);
			$location['directionZh'] = $DirectionChinz;

			//判断lasttime是否为null
			if ($lastTime != null) {
				if ($location['saveTime'] > $lastTime)
					array_push($gpsInfos, $location);
			} else
				array_push($gpsInfos, $location);

			//获取最晚时间
			if ($lt == null || $lt < $location['saveTime'])
				$lt = isset ($location['saveTime']) ? $location['saveTime'] : "";
		}

		$res = new PolicePoints($lt, $gpsInfos);
		return $res;
	}

	/**
	 * getGPS350
	 * 查询350m实时定位信息
	 */
	public function getGPS350($orgCode, $lastTime) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		//连接memcache
		$mem = new Memcache;
		$mem_ip = GlobalConfig :: getInstance()->memcache_ip;
		$mem_port = GlobalConfig :: getInstance()->memcache_port;
		$mem->connect($mem_ip, $mem_port);

		//从memcache中查询所有350兆设备号
		$m350Infos = $mem->get('g350' . $orgCode);
		if ($m350Infos == "" || $m350Infos == '[]') {
			//memcache里没有，需要从数据库读取
			$m350Infos = $this->Get350ByOrgCode($orgCode);

			$mem->set('g350' . $orgCode, encodeJson($m350Infos));
		} else {
			$m350Infos = json_decode($m350Infos, true);
		}

		//print_r($m350Infos);
		//exit;
		$count = count($m350Infos); //巡逻组数量
		$lt = null;
		$policeDynamic = null;
		$gpsInfos = array ();
		ini_set("max_execution_time",100);
		for ($i = 0; $i < $count; $i++) {
			//查询350M定位数据
			$m350id = $m350Infos[$i]['id'];
			//echo "i=".$i;
			$location = $mem->get($m350id);
			if ($location == "") {
				//从数据库读取
				$location = $this->GetLocationFromDB($m350id);
				if ($location != "" || $location != null) {
					$mem->set($m350id, encodeJson($location));
				}
			} else {
				$location = json_decode($location, true);
			}

			//print_r($location);

			//计算图标地址
			$iconSrc = $this->get350mIconSrc($location['isOnLine'], $location['direction']);
			$location['iconSrc'] = $iconSrc;
			$DirectionChinz = $this->getDirectionChinz($location['direction']);
			$location['directionZh'] = $DirectionChinz;

			//判断lasttime是否为null
			if ($lastTime != null) {
				if ($location['saveTime'] > $lastTime)
					array_push($gpsInfos, $location);
			} else
				array_push($gpsInfos, $location);

			//获取最晚时间
			if ($lt == null || $lt < $location['saveTime'])
				$lt = isset ($location['saveTime']) ? $location['saveTime'] : "";
		}

		$res = new PolicePoints($lt, $gpsInfos);
		return $res;
	}

	/**
	 * get350mIconSrc
	 * 获取350兆定位图片地址
	 */
	public function get350mIconSrc($online, $angles) {
		//在线350兆方向
		if ($online == 1) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return '350m_on_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return '350m_on_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return '350m_on_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return '350m_on_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return '350m_on_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return '350m_on_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return '350m_on_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return '350m_on_8.png';
			}
		}
		//离线350兆方向
		if ($online == 0) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return '350m_off_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return '350m_off_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return '350m_off_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return '350m_off_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return '350m_off_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return '350m_off_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return '350m_off_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return '350m_off_8.png';
			}
		}
	}

	/*********************************************************************************************
	 *            移动警务测试
	 ********************************************************************************************/

	/**
	 * 查询所有移动警务设备号
	 * @return
	 */
	public function GetMobile() {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select id from zdt_gps_dynamic where devicetype='3'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {
			$datas = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'id' => iconv("GBK", "UTF-8", $row["ID"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	/**
	 * 查询所有移动警务设备号
	 * @return
	 */
	public function GetMobileByOrgCode($orgCode) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		if ($orgCode == GlobalConfig :: getInstance()->dsdm.'00030000')
			$orgCode = GlobalConfig :: getInstance()->dsdm.'00000000'; // modify by carl

		//$sql = "select usr.userid, org.orgcode, org.orgname  from zdb_user usr, zdb_organization org, zdt_gps_dynamic gps where usr.bz = org.orgCode   and (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode')   and gps.id = usr.userid".
		//		" and usr.userid not in(select g.leaderid from zdt_duty_group g where g.status!='3')";

		$sql = "select usr.userid, org.orgcode, org.orgname,dhhm  from zdb_user usr   inner join  zdb_organization org on usr.bz = org.orgCode   inner join zdt_gps_dynamic gps on gps.id = usr.userid   left join zdb_equip_model m on m.userid = usr.userid    where 1=1   and (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode')   and usr.userid not in(select g.leaderid from zdt_duty_group g where g.status!='3')";
		//echo $sql;
		//$sql = "select usr.userid, org.orgcode, org.orgname  from zdb_user usr, zdb_organization org where usr.bz = org.orgCode   and (org.parenttreepath //like '%$orgCode%' or org.orgCode = '$orgCode')".
		//		" and (org.orglevel ='32') and usr.userid not in(select g.leaderid from zdt_duty_group g where g.status!='3')";

		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {
			$datas = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'id' => iconv("GBK", "UTF-8", $row["USERID"]),
					'dhhm' => iconv("GBK", "UTF-8", $row["DHHM"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	/**
	 * getGPSMobile
	 * 查询移动警务实时定位信息
	 */
	public function getGPSMobile_bak($lastTime) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		//连接memcache
		$mem = new Memcache;
		$mem_ip = GlobalConfig :: getInstance()->memcache_ip;
		$mem_port = GlobalConfig :: getInstance()->memcache_port;
		$mem->connect($mem_ip, $mem_port);

		//从memcache中查询所有350兆设备号
		$mobileInfos = $mem->get('gmobile');
		if ($mobileInfos == "" || $mobileInfos == '[]') {
			//memcache里没有，需要从数据库读取
			$mobileInfos = $this->GetMobile();
			$mem->set('gmobile', encodeJson($mobileInfos));
		} else {
			$mobileInfos = json_decode($mobileInfos, true);
		}

		//print_r($mobileInfos);
		//exit;
		$count = count($mobileInfos); //巡逻组数量
		$lt = null;
		$policeDynamic = null;
		$gpsInfos = array ();
		for ($i = 0; $i < $count; $i++) {
			//查询移动警务定位数据
			$id = $mobileInfos[$i]['id'];
			$location = $mem->get($id);
			if ($location == "") {
				//从数据库读取
				$location = $this->GetLocationFromDB($id);
				if ($location != "" || $location != null) {
					$mem->set($id, encodeJson($location));
				}
			} else {
				$location = json_decode($location, true);
			}

			//print_r($location);

			//计算图标地址
			$iconSrc = $this->getMobileIconSrc($location['isOnLine'], $location['direction']);
			$location['iconSrc'] = $iconSrc;
			$DirectionChinz = $this->getDirectionChinz($location['direction']);
			$location['directionZh'] = $DirectionChinz;

			//判断lasttime是否为null
			if ($lastTime != null) {
				if ($location['saveTime'] > $lastTime)
					array_push($gpsInfos, $location);
			} else
				array_push($gpsInfos, $location);

			//获取最晚时间
			if ($lt == null || $lt < $location['saveTime'])
				$lt = isset ($location['saveTime']) ? $location['saveTime'] : "";
		}

		$res = new PolicePoints($lt, $gpsInfos);
		return $res;
	}

	/**
	 * getGPSMobile
	 * 查询移动警务实时定位信息
	 
	public function getGPSMobile($orgCode, $lastTime) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		//连接memcache
		$mem = new Memcache;
		$mem_ip = GlobalConfig :: getInstance()->memcache_ip;
		$mem_port = GlobalConfig :: getInstance()->memcache_port;
		$mem->connect($mem_ip, $mem_port);

		//从memcache中查询所有350兆设备号
		$mobileInfos = $mem->get('gmobile' . $orgCode);
		//if ($mobileInfos == "" || $mobileInfos == '[]') {
			//memcache里没有，需要从数据库读取
			$mobileInfos = $this->GetMobileByOrgCode($orgCode);
			$mem->set('gmobile' . $orgCode, encodeJson($mobileInfos));
		//} else {
		//	$mobileInfos = json_decode($mobileInfos, true);
		//}

		//print_r($mobileInfos);
		//exit;
		$count = count($mobileInfos); //巡逻组数量
		$lt = null;
		$policeDynamic = null;
		$gpsInfos = array ();
		//echo $count;
		for ($i = 0; $i < $count; $i++) {
			//查询移动警务定位数据
			$id = $mobileInfos[$i]['id'];
			//$location = $mem->get($id);
			//echo $location;
			//if ($location == "") {
				//从数据库读取
				$location = $this->GetLocationFromDB($id);
				if ($location != "" || $location != null) {
					$mem->set($id, encodeJson($location));
				}
			//} else {
				//$location = json_decode($location, true);
			//}
			//echo $id;
			//$mem->delete($id);
			//continue;
			//print_r($location);
			if ($location != "") {
				//计算图标地址
				//$iconSrc = $this->getMobileIconSrc($location['isOnLine'],$location['direction']);	
				$iconSrc = $this->getMobileIconSrc("1", $location['direction']);
				$location['iconSrc'] = $iconSrc;
				$DirectionChinz = $this->getDirectionChinz($location['direction']);
				$location['directionZh'] = $DirectionChinz;
				$location['dhhm'] = isset ($mobileInfos[$i]['dhhm']) ? $mobileInfos[$i]['dhhm'] : $location['dhhm'];

				//判断lasttime是否为null
				if ($lastTime != null) {
					//print_r($location);
					if ($location['saveTime'] > $lastTime)
						array_push($gpsInfos, $location);
				} else
					array_push($gpsInfos, $location);
					
			}
			//获取最晚时间
			if ($lt == null || $lt < $location['saveTime'])
				$lt = isset ($location['saveTime']) ? $location['saveTime'] : "";
		}
	  
		$res = new PolicePoints($lt, $gpsInfos);
		return $res;
	}
*/
 public function getGPSMobile($orgCode, $lastTime) {
             
            $sql = "select gps.isonline,gps.deviceid,gps.direction,gps.speed,gps.road,gps.status,gps.savetime,gps.recvtime,gps.locatetime,gps.roletype,gps.devicetype,MDSYS.Sdo_Util.to_wktgeometry_varchar(gps.location) as location , u.username ,u.alarm,org.orgcode,org.orgname,gps.id,m.dhhm,gps.taskcount from zdt_gps_dynamic gps left join zdb_user u on gps.id=u.userid left join zdb_organization org on u.bz = org.orgcode  left join zdb_equip_model m on m.userid = u.userid  where (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode') ";
            if($lastTime!="")
            {
                $sql.=" and gps.savetime>='$lastTime'";
            }
           //echo $sql;
            $lt = null;
            if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
            $stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			oci_close($this->dbconn);
			exit;
		} else {
			$groups = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
                                $iconSrc = $this->getMobileIconSrc($row['ISONLINE'],$row['DIRECTION'],$row['TASKCOUNT']);
                                //print_r($iconSrc) ;
                                $DirectionChinz = $this->getDirectionChinz($row['DIRECTION']);
                                //获取最晚时间
                                if ($lt == null || $lt < $row['SAVETIME'])
                                    $lt = isset ($row['SAVETIME']) ? $row['SAVETIME'] : "";
				$group = array (
					'isOnLine' => iconv("GBK", "UTF-8", $row["ISONLINE"]),
					'deviceId' => iconv("GBK", "UTF-8", $row["DEVICEID"]),
					'location' => iconv("GBK", "UTF-8", $row["LOCATION"]),
					'direction' => iconv("GBK", "UTF-8", $row["DIRECTION"]),
					'speed' => iconv("GBK", "UTF-8", $row["SPEED"]),
					'road' => iconv("GBK", "UTF-8", $row["ROAD"]),
					'status' => iconv("GBK", "UTF-8", $row["STATUS"]),
					'saveTime' => iconv("GBK", "UTF-8", $row["SAVETIME"]),
					'recvTime' => iconv("GBK", "UTF-8", $row["RECVTIME"]),
					'locateTime' => iconv("GBK", "UTF-8", $row["LOCATETIME"]),
					'roleType' => iconv("GBK", "UTF-8", $row["ROLETYPE"]),
					'deviceType' => iconv("GBK", "UTF-8", $row["DEVICETYPE"]),
                                        'location' => iconv("GBK", "UTF-8", $row["LOCATION"]),
                                        'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
                                        'alarm' => iconv("GBK", "UTF-8", $row["ALARM"]),
                                         'id' => iconv("GBK", "UTF-8", $row["ID"]),
                                        'dhhm' => iconv("GBK", "UTF-8", $row["DHHM"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
                                        'iconSrc'=>$iconSrc,
                                        'directionZh' => $DirectionChinz,
					'dhhm' => iconv("GBK", "UTF-8", $row["DHHM"])
				);
				array_push($groups, $group);
				
			}
		}
                $res = new PolicePoints($lt, $groups);
     //print_r( $res);       
		return $res;
            
        }
        
    

	/**
	 * getMobileIconSrc
	 * 获取移动警务定位图片地址
	 */
	public function getMobileIconSrc($online, $angles,$taskcount) {
		//在线移动警务方向
                $image_src = "man";
                if($taskcount>0)
                {
                     $image_src.="_task";
                }
		if ($online == 1) {
                    $image_src.="_on";
                }
                else {
                    $image_src.="_off";
                }
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				$image_src.='_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				$image_src.='_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				$image_src.='_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				$image_src.='_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				$image_src.='_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				$image_src.='_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				$image_src.='_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				$image_src.='_8.png';
			}
		return $image_src;
		//离线移动警务方向
                /*
		if ($online == 0) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'man_off_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'man_off_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'man_off_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'man_off_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'man_off_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'man_off_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'man_off_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'man_off_8.png';
			}
		}
                 
                 */
	}

	/*********************************************************************************************
	 *           消防GPS定位
	 ********************************************************************************************/

	/**
	 * getGPSFire
	 * 查询消防实时定位信息
	 */
	public function getGPSFire($lastTime) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		//连接memcache
		$mem = new Memcache;
		$mem_ip = GlobalConfig :: getInstance()->memcache_ip;
		$mem_port = GlobalConfig :: getInstance()->memcache_port;
		$mem->connect($mem_ip, $mem_port);

		//从memcache中查询所有350兆设备号
		$fireInfos = $mem->get('gfire');
		if ($fireInfos == "" || $fireInfos == '[]') {
			//memcache里没有，需要从数据库读取
			$fireInfos = $this->GetFire();

			$mem->set('gfire', encodeJson($fireInfos));
		} else {
			$fireInfos = json_decode($fireInfos, true);
		}

		//print_r($fireInfos);
		//exit;
		$count = count($fireInfos); //消防车定位数量
		$lt = null;
		$policeDynamic = null;
		$gpsInfos = array ();
		for ($i = 0; $i < $count; $i++) {
			//查询350M定位数据
			$fireid = $fireInfos[$i]['id'];
			$location = $mem->get($fireid);
			if ($location == "") {
				//从数据库读取
				$location = $this->GetLocationFromDB($fireid);
				if ($location != "" || $location != null) {
					$mem->set($fireid, encodeJson($location));
				}
			} else {
				$location = json_decode($location, true);
			}

			//print_r($location);

			//计算图标地址
			$iconSrc = $this->getFireIconSrc($location['isOnLine'], $location['direction']);
			$location['iconSrc'] = $iconSrc;
			$DirectionChinz = $this->getDirectionChinz($location['direction']);
			$location['directionZh'] = $DirectionChinz;

			//判断lasttime是否为null
			if ($lastTime != null) {
				if ($location['saveTime'] > $lastTime)
					array_push($gpsInfos, $location);
			} else
				array_push($gpsInfos, $location);

			//获取最晚时间
			if ($lt == null || $lt < $location['saveTime'])
				$lt = isset ($location['saveTime']) ? $location['saveTime'] : "";
		}

		$res = new PolicePoints($lt, $gpsInfos);
		return $res;
	}

	/**
	 * 查询所有消防设备号
	 * @return
	 */
	public function GetFire() {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select id from zdt_gps_dynamic where devicetype='4'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {
			$datas = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'id' => iconv("GBK", "UTF-8", $row["ID"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	/**
	 * getFireIconSrc
	 * 获取消防定位图片地址
	 */
	public function getFireIconSrc($online, $angles) {
		//在线350兆方向
		if ($online == 1) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'fire_on_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'fire_on_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'fire_on_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'fire_on_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'fire_on_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'fire_on_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'fire_on_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'fire_on_8.png';
			}
		}
		//离线350兆方向
		if ($online == 0) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'fire_off_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'fire_off_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'fire_off_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'fire_off_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'fire_off_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'fire_off_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'fire_off_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'fire_off_8.png';
			}
		}
	}
	
	/*********************************************************************************************
	 *           危险品GPS定位
	 ********************************************************************************************/

	/**
	 * getGPSWxp
	 * 查询危险品车辆实时定位信息
	 */
	public function getGPSWxp($lastTime) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		//连接memcache
		$mem = new Memcache;
		$mem_ip = GlobalConfig :: getInstance()->memcache_ip;
		$mem_port = GlobalConfig :: getInstance()->memcache_port;
		$mem->connect($mem_ip, $mem_port);

		//从memcache中查询所有350兆设备号
		$wxpInfos = $mem->get('gwxp');
		if ($wxpInfos == "" || $wxpInfos == '[]') {
			//memcache里没有，需要从数据库读取
			$wxpInfos = $this->GetWxp();

			$mem->set('gwxp', encodeJson($wxpInfos));
		} else {
			$wxpInfos = json_decode($wxpInfos, true);
		}

		//print_r($fireInfos);
		//exit;
		$count = count($wxpInfos); //消防车定位数量
		$lt = null;
		$policeDynamic = null;
		$gpsInfos = array ();
		for ($i = 0; $i < $count; $i++) {
			//array_push($gpsInfos, $wxpInfos[$i]);
			//查询350M定位数据
			$wxpid = $wxpInfos[$i]['id'];
			$location = $mem->get($wxpid);
			if ($location == "") {
				//从数据库读取
				$location = $this->GetLocationFromDB($wxpid);
				if ($location != "" || $location != null) {
					$mem->set($wxpid, encodeJson($location));
				}
			} else {
				$location = json_decode($location, true);
			}

			//print_r($location);
			if($location){
				$location = array_merge($wxpInfos[$i],$location);
				$location['hphm'] = $wxpInfos[$i]['hphm'];
				//计算图标地址
				$iconSrc = $this->getWxpIconSrc($location['isOnLine'], $location['direction']);
				$location['iconSrc'] = $iconSrc;
				$DirectionChinz = $this->getDirectionChinz($location['direction']);
				$location['directionZh'] = $DirectionChinz;
	
				//判断lasttime是否为null
				if ($lastTime != null) {
					if ($location['saveTime'] > $lastTime)
						array_push($gpsInfos, $location);
				} else{
					array_push($gpsInfos, $location);
				}
					
				//获取最晚时间
				if ($lt == null || $lt < $location['saveTime']){
					$lt = isset ($location['saveTime']) ? $location['saveTime'] : "";
				}
			}
		}

		$res = new PolicePoints($lt, $gpsInfos);
		return $res;
	}
	
	/**
	 * 查询所有危险品车辆编号
	 * @return
	 */
	public function GetWxp() {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select w.id,w.hphm,w.ssgs,w.ssqy,w.clys,w.gpssj,w.wd,w.jd,w.sd,w.fx,w.yyzh,w.yyfw,w.yyqssj,w.yyzzsj,w.yyqsdd,w.yyzsdd,w.yynr from zdb_wxp w left join zdt_gps_dynamic d on w.id=d.id ";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {
			$datas = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'id' => iconv("GBK", "UTF-8", $row["ID"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'ssgs' => iconv("GBK", "UTF-8", $row["SSGS"]),
					'ssqy' => iconv("GBK", "UTF-8", $row["SSQY"]),
					'clys' => iconv("GBK", "UTF-8", $row["CLYS"]),
					'gpssj' => iconv("GBK", "UTF-8", $row["GPSSJ"]),
					'wd' => iconv("GBK", "UTF-8", $row["WD"]),
					'jd' => iconv("GBK", "UTF-8", $row["JD"]),
					'sd' => iconv("GBK", "UTF-8", $row["SD"]),
					'fx' => iconv("GBK", "UTF-8", $row["FX"]),
					'yyzh' => iconv("GBK", "UTF-8", $row["YYZH"]),
					'yyfw' => iconv("GBK", "UTF-8", $row["YYFW"]),
					'yyqssj' => iconv("GBK", "UTF-8", $row["YYQSSJ"]),
					'yyzzsj' => iconv("GBK", "UTF-8", $row["YYZZSJ"]),
					'yyqsdd' => iconv("GBK", "UTF-8", $row["YYQSDD"]),
					'yyzsdd' => iconv("GBK", "UTF-8", $row["YYZSDD"]),
					'yynr' => iconv("GBK", "UTF-8", $row["YYNR"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	/**
	 * getWxpIconSrc
	 * 获取危险品车辆定位图片地址
	 */
	public function getWxpIconSrc($online, $angles) {
		//在线350兆方向
		//if ($online == 1) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'wxp_on_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'wxp_on_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'wxp_on_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'wxp_on_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'wxp_on_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'wxp_on_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'wxp_on_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'wxp_on_8.png';
			}
		//}
		//离线350兆方向
		/*
		if ($online == 0) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'wxp_off_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'wxp_off_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'wxp_off_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'wxp_off_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'wxp_off_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'wxp_off_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'wxp_off_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'wxp_off_8.png';
			}
		}
		*/
	}

	/**
	 * getAndroidDynamicLocation
	 * 查询会话组位置信息
	 */
	public function getAndroidDynamicLocation($qzcy, $lastTime) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		//连接memcache
		$mem = new Memcache;
		$mem_ip = GlobalConfig :: getInstance()->memcache_ip;
		$mem_port = GlobalConfig :: getInstance()->memcache_port;
		$mem->connect($mem_ip, $mem_port);

		$mobileInfos = explode(",", $qzcy);
		$count = count($mobileInfos); //巡逻组数量
		$lt = null;
		$policeDynamic = null;
		$gpsInfos = array ();
		//echo $count;
		for ($i = 0; $i < $count; $i++) {
			//查询移动警务定位数据
			$id = $mobileInfos[$i];
			$location = $mem->get($id);
			if ($location == "") {
				//从数据库读取
				$location = $this->GetLocationFromDB($id);
				if ($location != "" || $location != null) {
					$mem->set($id, encodeJson($location));
				}
			} else {
				$location = json_decode($location, true);
			}
			if ($location != "") {
				$DirectionChinz = $this->getDirectionChinz($location['direction']);
				$location['directionZh'] = $DirectionChinz;
				$result = array (
					"id" => $location['id'],
					"orgCode" => $location['orgCode'],
					"orgName" => $location['orgName'],
					"userName" => $location['userName'],
					"directionZh" => $location['directionZh'],
					"locateTime" => $location['locateTime'],
					"location" => $location['location']
				);

				//判断lasttime是否为null
				if ($lastTime != null) {
					if ($location['saveTime'] > $lastTime)
						array_push($gpsInfos, $result);
				} else
					array_push($gpsInfos, $result);
			}
			//获取最晚时间
			if ($lt == null || $lt < $location['saveTime'])
				$lt = isset ($location['saveTime']) ? $location['saveTime'] : "";
		}

		$res = new PolicePoints($lt, $gpsInfos);
		if ($res) {
			$res->result = "true";
			$res->errmsg = "";
		} else {
			$res->result = "false";
			$res->errmsg = "查询失败";
		}
		return $res;
	}

}
?>
