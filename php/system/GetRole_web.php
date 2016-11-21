<?php
try{
	session_start();
	session_commit();
	}catch(Exception $e){
		$result = array (
			'head' => array (
						'code' => 1,
						'message' => ''
						),
			'value' => '',
			'extend' => ''
		);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
	return;
	}
header('Content-Type: text/html; charset=UTF-8');
header("Expires: 0");
header("Cache-Control: no-cache");
header("Pragma content: no-cache");

?>
<?php


include_once ('../GlobalConfig.class.php');
include_once ('../class/TpmsDB.class.php');
include_once ('../class/SystemDB.class.php');

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
} else {
	$userId = $_SESSION["userId"];
}
$out_url = GlobalConfig :: getInstance()->out_url;
$license = GlobalConfig :: getInstance()->license;
$xtmc = urlencode("葫芦岛市智能指挥调度系统");
$url = $out_url . '?operation=AuthManagement_GetResourceByAccount_v002&' . 'license=' . $license . '&content={"data":[{"ResourceSysName":"'.$xtmc.'","Account":"' . $userId . '"}],"pageindex":0,"pagesize":999999}';
//echo $url;
$roleInfo = file_get_contents($url);
/*
$roleInfo = '{
    "code": "1",
    "msg": "查询成功！",
    "exp": null,
    "data": [
        {
            "ResourceID": "80913aa89af0416dbb2aa6fb7c10fdc2",
            "ResourceName": "移动警力",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/geometrySearch/policeMobileSearch.html",
            "ResourceSeq": "1",
            "UpperResourceKey": "5337244fdc124d5db6e044e8638a7ce3",
            "Comment": "name=window_012,width=600,height=600,position=right_top,toggle=true"
        },
        {
            "ResourceID": "2774ef34edfa4610a86f2279498e28d3",
            "ResourceName": "情报分析",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/qjfx/IntelligenceList.html",
            "ResourceSeq": "1",
            "UpperResourceKey": "50d439aee5f04674bc32bd1276132559",
            "Comment": "name=window_007,width=460,height=270,position=right_top,toggle=true"
        },
        {
            "ResourceID": "ce5caa26d1074f73b7cec200c8531b13",
            "ResourceName": "报表",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/report/reportList.html",
            "ResourceSeq": "1",
            "UpperResourceKey": "b230f6137bae4f338c83a0f81514e093",
            "Comment": "name=window_016.width=760.height=470,side=right"
        },
        {
            "ResourceID": "5a72c72d12dd4bd8bbd68a5d067d0c6f",
            "ResourceName": "用户维护",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/user/UserList.html",
            "ResourceSeq": "1",
            "UpperResourceKey": "a2da819224384d1c96925820de6073f9",
            "Comment": "name=window_010,width=760,height=470,side=right"
        },
        {
            "ResourceID": "8ad5283ebc794e02b5a48169927b17a4",
            "ResourceName": "接处警",
            "ResourceType": "1 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd",
            "ResourceIconType": "fa fa-warning",
            "ResourceOpenType": "9 ",
            "ResourceSeq": "1"
        },
        {
            "ResourceID": "62ced2470302475ca09162b134c3a15d",
            "ResourceName": "指挥调度",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/zhdd/zhdd.html",
            "ResourceOpenType": "9 ",
            "ResourceSeq": "1",
            "UpperResourceKey": "8ad5283ebc794e02b5a48169927b17a4",
            "Comment": "name=window-001,width=670,height=270,position=left_top,toggle=true"
        },
        {
            "ResourceID": "bdfd535302qe4363acf2841qbdedff58",
            "ResourceName": "区域管理",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/equip/qygl.html",
            "ResourceSeq": "2",
            "UpperResourceKey": "371a7471445e40f7bb9f978a3e391bc7",
            "Comment": "name=window_032,width=450,target=panel,side=left"
        },
        {
            "ResourceID": "bdfd5356412be4363acf2841qbdedff58",
            "ResourceName": "任务点管理",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/equip/rwdgl.html",
            "ResourceSeq": "3",
            "UpperResourceKey": "371a7471445e40f7bb9f978a3e391bc7",
            "Comment": "name=window_034,width=500,target=panel,side=left"
        },
        {
            "ResourceID": "bdfd5356q2eqbe4363acf2841qbdedff58",
            "ResourceName": "任务管理",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/equip/rwgl.html",
            "ResourceSeq": "4",
            "UpperResourceKey": "371a7471445e40f7bb9f978a3e391bc7",
            "Comment": "name=window_033,width=500,target=pop,position=left_top"
        },
        {
            "ResourceID": "fghj5356122b24363acf2841qbdedff58",
            "ResourceName": "任务点分布",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/equip/rwdSearch.html",
            "ResourceSeq": "9",
            "UpperResourceKey": "371a7471445e40f7bb9f978a3e391bc7",
            "Comment": "name=window_0310,width=300,target=panel,dragable=false,side=left"
        },
        {
            "ResourceID": "plmk5356122b24363acf2841qbdedff58",
            "ResourceName": "任务考核查询",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/rwkhcx/rwkhcx.html",
            "ResourceSeq": "5",
            "UpperResourceKey": "371a7471445e40f7bb9f978a3e391bc7",
            "Comment": "name=window_035,width=800,target=slide,side=right,fullscreen=true"
        },{
            "ResourceID": "bdfd535602be4363acf2845bbdedff58",
            "ResourceName": "巡逻组查询",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/equip/policeGroup.html",
            "ResourceSeq": "1",
            "UpperResourceKey": "371a7471445e40f7bb9f978a3e391bc7",
            "Comment": "name=window_003,width=600,height=270,position=left_bottom"
        },
        {
            "ResourceID": "bce1355f9b4b4c90988e6a337a182223",
            "ResourceName": "消息管理",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/zhdd/ImMsgHistroy.html",
            "ResourceSeq": "1",
            "UpperResourceKey": "408ec72846db41c2a42a41da23211488",
            "Comment": "name=window_008,width=800,height=600,toggle=true,locksize=true"
        },
        {
            "ResourceID": "c22062b1f090449190563f14708efd8e",
            "ResourceName": "地理",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/geometrySearch/geometrySearch.html",
            "ResourceSeq": "2",
            "UpperResourceKey": "5337244fdc124d5db6e044e8638a7ce3",
            "Comment": "name=window_013,width=600,height=600,position=right_top,toggle=true"
        },
        {
            "ResourceID": "90b433005ee249168b3ffc53db50d1da",
            "ResourceName": "辖区维护",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/org/orgList.html",
            "ResourceSeq": "2",
            "UpperResourceKey": "a2da819224384d1c96925820de6073f9",
            "Comment": "name=window_011,width=760,height=470,side=right"
        },
        {
            "ResourceID": "4c62cb71fc0e4d7ba67bc556de537a77",
            "ResourceName": "结束警情",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/zhdd/overEvent.html",
            "ResourceSeq": "2",
            "UpperResourceKey": "8ad5283ebc794e02b5a48169927b17a4",
            "Comment": "name=window_002,width=750,height=270,position=left_top"
        },
        {
            "ResourceID": "371a7471445e40f7bb9f978a3e391bc7",
            "ResourceName": "勤务管理",
            "ResourceType": "1 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd/",
            "ResourceIconType": "fa fa-cab",
            "ResourceOpenType": "9 ",
            "ResourceSeq": "2"
        },
        {
            "ResourceID": "42bded625588419f9b1173a49229e794",
            "ResourceName": "合成作战",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/zhdd/zhddIM.html",
            "ResourceSeq": "2",
            "UpperResourceKey": "408ec72846db41c2a42a41da23211488",
            "Comment": "name=window_006,width=600,height=600,position=left_top,toggle=true"
        },
        {
            "ResourceID": "408ec72846db41c2a42a41da23211488",
            "ResourceName": "合成作战",
            "ResourceType": "1 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd/",
            "ResourceIconType": "fa fa-comments",
            "ResourceSeq": "3"
        },
        {
            "ResourceID": "6117026d6de14798b9e158875446d02d",
            "ResourceName": "机构",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/geometrySearch/orgGeometrySearch.html",
            "ResourceSeq": "3",
            "UpperResourceKey": "5337244fdc124d5db6e044e8638a7ce3",
            "Comment": "name=window_014,width=600,height=600,position=right_top,toggle=true"
        },
        {
            "ResourceID": "416d8aa9df6842b1b1b613de24632263",
            "ResourceName": "轨迹回放",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/zhdd/gjhfMap.html",
            "ResourceSeq": "3",
            "UpperResourceKey": "8ad5283ebc794e02b5a48169927b17a4",
            "Comment": "name=window_002,width=750,height=750,position=right_center"
        },
        {
            "ResourceID": "5337244fdc124d5db6e044e8638a7ce3",
            "ResourceName": "综合查询",
            "ResourceType": "1 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd/",
            "ResourceIconType": "fa fa-search",
            "ResourceSeq": "4"
        },
        {
            "ResourceID": "1c660fdec1f541108fb599ad9206857d",
            "ResourceName": "装备绑定",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/zhdd/zbbd.html",
            "ResourceSeq": "4",
            "UpperResourceKey": "371a7471445e40f7bb9f978a3e391bc7",
            "Comment": "name=window_009,width=1200,height=470,side=right"
        },
        {
            "ResourceID": "50d439aee5f04674bc32bd1276132559",
            "ResourceName": "决策分析",
            "ResourceType": "1 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd/",
            "ResourceIconType": "fa fa-cubes",
            "ResourceSeq": "5"
        },
        {
            "ResourceID": "b230f6137bae4f338c83a0f81514e093",
            "ResourceName": "统计报表",
            "ResourceType": "1 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd",
            "ResourceIconType": "fa fa-bar-chart",
            "ResourceSeq": "6"
        },
        {
            "ResourceID": "5337242fdc124d5db6e044e8638a7ce3",
            "ResourceName": "预案管理",
            "ResourceType": "1 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd/",
            "ResourceIconType": "fa fa-bell",
            "ResourceSeq": "7"
        },
        {
            "ResourceID": "90b433005ee2491w68b3ffc53db50d1da",
            "ResourceName": "预案制定",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/plan/eventPlan.html",
            "ResourceSeq": "2",
            "UpperResourceKey": "5337242fdc124d5db6e044e8638a7ce3",
            "Comment": "name=window_071,width=760,height=470,side=right"
        },
        {
            "ResourceID": "90b433005ee2491w68b3ffc53db50d1d1",
            "ResourceName": "预案启动",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/plan/planOpen.html",
            "ResourceSeq": "3",
            "UpperResourceKey": "5337242fdc124d5db6e044e8638a7ce3",
            "Comment": "name=window_072,width=760,height=470,side=right"
        },
        {
            "ResourceID": "90b433005ee2491w68b3ffc53db50d1d2",
            "ResourceName": "预案接收",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/plan/planReceive.html",
            "ResourceSeq": "4",
            "UpperResourceKey": "5337242fdc124d5db6e044e8638a7ce3",
            "Comment": "name=window_073,width=760,height=470,side=right"
        },
        {
            "ResourceID": "a2da819224384d1c96925820de6073f9",
            "ResourceName": "系统维护",
            "ResourceType": "1 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd/",
            "ResourceIconType": "fa fa-cog",
            "ResourceSeq": "8"
        },
        {
            "ResourceID": "21a538a666144669bfcf52e299f3ed2c",
            "ResourceName": "消息",
            "ResourceType": "2 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd/",
            "UpperResourceKey": "408ec72846db41c2a42a41da23211488"
        },
        {
            "ResourceID": "50674dfebf1945d5bdd396516dda6b50",
            "ResourceName": "警力",
            "ResourceType": "2 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd/",
            "UpperResourceKey": "8ad5283ebc794e02b5a48169927b17a4"
        },
        {
            "ResourceID": "9ebc1c7b471c4717999ef7a7a4d74c08",
            "ResourceName": "关注",
            "ResourceType": "2 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd/",
            "UpperResourceKey": "8ad5283ebc794e02b5a48169927b17a4"
        },
        {
            "ResourceID": "862cfe2c80b144a3ac26b9f95825277c",
            "ResourceName": "警情",
            "ResourceType": "2 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd/",
            "UpperResourceKey": "8ad5283ebc794e02b5a48169927b17a4"
        }
    ],
    "datalen": 27,
    "total": 27
}';
*/
$result = json_decode($roleInfo, true);
if ($result['code'] != 1) {
	$ret = array (
		'head' => array (
			'code' => 0,
			'message' => $result['msg']
		),
		'value' => '',
		'extend' => ''
	);
	echo json_encode($ret, JSON_UNESCAPED_UNICODE);
}else if(!isset($result['data'])){
	$ret = array (
		'head' => array (
			'code' => 0,
			'message' => '该用户未赋予权限!'
		),
		'value' => '',
		'extend' => ''
	);
	echo json_encode($ret, JSON_UNESCAPED_UNICODE);
} else {
	
	$roleDatas = $result['data'];
	$roleResult = array ();
	//循环一级菜单
	for ($i = 0; $i < count($roleDatas); $i++) {
		$upperResourceKey = isset($roleDatas[$i]['UpperResourceKey'])?trim($roleDatas[$i]['UpperResourceKey']):"";
		$ResourceType = isset($roleDatas[$i]['ResourceType'])?trim($roleDatas[$i]['ResourceType']):"";
		if ($upperResourceKey == ""&&trim($ResourceType)=="1") {
			$roleArr = array (
				"title" => trim($roleDatas[$i]['ResourceName']),
				"iconclass" => trim($roleDatas[$i]['ResourceIconType'])
			);
			$roleMenus = array ();
			//循环二级菜单
			for ($j = 0; $j < count($roleDatas); $j++) {
				$parentKey = isset($roleDatas[$j]['UpperResourceKey'])?trim($roleDatas[$j]['UpperResourceKey']):"";
				$resourceType_chird = isset($roleDatas[$j]['ResourceType'])?trim($roleDatas[$j]['ResourceType']):"";
				$isbutton = isset($roleDatas[$j]['ResourceUrl'])?trim($roleDatas[$j]['ResourceUrl']):"";
				if ($parentKey == trim($roleDatas[$i]['ResourceID'])&&$resourceType_chird=="1") {
					if($isbutton=="button")
          {
										
              //$coment = isset ($roleDatas[$j]['Comment']) ? $roleDatas[$j]['Comment'] : "";
              //setcookie($_SESSION['userId'].$coment,$coment,time()+315360000);
			  //setcookie('jsonstr',$roleInfo,time()+315360000);
										//echo $_cookie[$_SESSION['userId'].$coment];
          }
          else {
					$roleMenu = array ();
					$coment = isset ($roleDatas[$j]['Comment']) ? $roleDatas[$j]['Comment'] : "";
					$sxArr = explode(",", $coment);
					for ($l = 0; $l < count($sxArr); $l++) {
						$sx = explode("=", $sxArr[$l]);
						//echo $sxArr[$l];
						$roleMenu[$sx[0]] = $sx[1];
					}
					if ($roleDatas[$j]['ResourceName'])
						$roleMenu['title'] = $roleDatas[$j]['ResourceName'];
					if ($roleDatas[$j]['ResourceUrl'])
						$roleMenu['url'] = $roleDatas[$j]['ResourceUrl'];
					array_push($roleMenus, $roleMenu);
					}
				}
			}
			if ($roleMenus)
				$roleArr['menu'] = $roleMenus;
			array_push($roleResult, $roleArr);
		}
	}
	if ($roleResult) {
		$resultRole = array (
			'head' => array (
				'code' => 1,
				'message' => ''
			),
			'value' => $roleResult,
			'extend' => ''
		);
	} else {
		$resultRole = array (
			'head' => array (
				'code' => 0,
				'message' => '加载权限错误'
			),
			'value' => $roleResult,
			'extend' => ''
		);
	}
	echo json_encode($resultRole, JSON_UNESCAPED_UNICODE);

}
?>