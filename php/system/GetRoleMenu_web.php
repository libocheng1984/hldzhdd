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
//$roleInfo = '{ "code":"1", "msg":"查询成功！", "exp":null, "data":[ { 	"ResourceID":"59504955", 	"ResourceName":"接处警", 	"ResourceType":"2 ", 	"ResourceLevel":"1", 	"ResourceUrl":"",	"ResourceIconType":"icon0", 	"ResourceOpenType":"2 ", 	"ResourceSeq":"1", 	"UpperResourceKey":"", 	"ResourceSysID":"",	"Comment":""	}, 	{ 	"ResourceID":"69152434", 	"ResourceName":"指挥调度", 	"ResourceType":"2 ", 	"ResourceLevel":"2 ", 	"ResourceUrl":"pages/zhdd/zhdd.html", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"1", 	"UpperResourceKey":"59504955", 	"ResourceSysID":"",	"Comment":"name=window_001,width=670,height=270,position=left_top,toggle=true"	}, 	{ 	"ResourceID":"69152436",	"ResourceName":"结束警情",	"ResourceType":"2 ", 	"ResourceLevel":"2 ", 	"ResourceUrl":"pages/zhdd/overEvent.html", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"59504955", 	"ResourceSysID":"",	"Comment":"name=window_002,width=750,height=270,position=left_top"	}, 	{ 	"ResourceID":"69152223", 	"ResourceName":"勤务管理", 	"ResourceType":"2 ", 	"ResourceLevel":"1 ", 	"ResourceUrl":"", 	"ResourceIconType":"icon1", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"", 	"ResourceSysID":"",	"Comment":""	}, 	{ 	"ResourceID":"69152224", 	"ResourceName":"巡逻组查询", 	"ResourceType":"2 ", 	"ResourceLevel":"2 ", 	"ResourceUrl":"pages/equip/policeGroup.html", 	"ResourceIconType":"icon-manage", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"69152223", 	"ResourceSysID":"",	"Comment":"name=window_003,width=600,height=270,position=left_bottom"	}, 	{ 	"ResourceID":"69152225", 	"ResourceName":"巡逻特征查询", 	"ResourceType":"2 ", 	"ResourceLevel":"2 ", 	"ResourceUrl":"pages/equip/xltzbcx.html", 	"ResourceIconType":"icon-manage", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"69152223", 	"ResourceSysID":"",	"Comment":"name=window_004,width=600,height=270,position=left_bottom"	}, 	{ 	"ResourceID":"69152226", 	"ResourceName":"巡逻绑定", 	"ResourceType":"2 ", 	"ResourceLevel":"2 ", 	"ResourceUrl":"pages/equip/xkzhcx.html", 	"ResourceIconType":"icon-manage", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"69152223", 	"ResourceSysID":"",	"Comment":"name=window_005,width=600,height=270,position=left_bottom"	}, 	{ 	"ResourceID":"69152227", 	"ResourceName":"装备绑定", 	"ResourceType":"2 ", 	"ResourceLevel":"2 ", 	"ResourceUrl":"pages/zhdd/zbbd.html", 	"ResourceIconType":"icon-manage", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"69152223", 	"ResourceSysID":"",   "Comment":"name=window_009,width=1200,height=470,side=right"		}, 	{ 	"ResourceID":"69152228", 	"ResourceName":"合成作战", 	"ResourceType":"2 ", 	"ResourceLevel":"1 ", 	"ResourceUrl":"", 	"ResourceIconType":"icon2", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"", 	"ResourceSysID":"",	"Comment":""	}, 	{ 	"ResourceID":"69152229", 	"ResourceName":"合成作战", 	"ResourceType":"2 ", 	"ResourceLevel":"2 ", 	"ResourceUrl":"pages/zhdd/zhddIM.html", 	"ResourceIconType":"icon-manage", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"69152228", 	"ResourceSysID":"",	"Comment":"name=window_006,width=600,height=600,position=left_top,toggle=true"	},{ 	"ResourceID":"69152234", 	"ResourceName":"消息管理", 	"ResourceType":"2 ", 	"ResourceLevel":"2 ", 	"ResourceUrl":"pages/zhdd/ImMsgHistroy.html", 	"ResourceIconType":"icon-manage", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"69152228", 	"ResourceSysID":"",	"Comment":"name=window_008,width=800,height=600,toggle=true,locksize=true"	}, 	{ 	"ResourceID":"69152230", 	"ResourceName":"预案管理", 	"ResourceType":"2 ", 	"ResourceLevel":"1 ", 	"ResourceUrl":"", 	"ResourceIconType":"icon2", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"", 	"ResourceSysID":"",	"Comment":""	}, 	{ 	"ResourceID":"69152231", 	"ResourceName":"决策分析", 	"ResourceType":"2 ", 	"ResourceLevel":"1 ", 	"ResourceUrl":"", 	"ResourceIconType":"icon3", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"", 	"ResourceSysID":"",	"Comment":""	}, 	{ 	"ResourceID":"69152232", 	"ResourceName":"情报分析", 	"ResourceType":"2 ", 	"ResourceLevel":"2 ", 	"ResourceUrl":"pages/qjfx/IntelligenceList.html", 	"ResourceIconType":"icon-manage", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"69152231", 	"ResourceSysID":"",	"Comment":"name=window_007,width=460,height=270,position=right_top,toggle=true"	}, 	{ 	"ResourceID":"69152233", 	"ResourceName":"系统维护", 	"ResourceType":"2 ", 	"ResourceLevel":"1 ", 	"ResourceUrl":"", 	"ResourceIconType":"icon2", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"", 	"ResourceSysID":"",	"Comment":""	},{ 	"ResourceID":"69152235", 	"ResourceName":"用户维护", 	"ResourceType":"2 ", 	"ResourceLevel":"2 ", 	"ResourceUrl":"pages/user/UserList.html", 	"ResourceIconType":"icon-manage", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"69152233", 	"ResourceSysID":"",	"Comment":"name=window_010,width=460,height=270,side=right"	},{ 	"ResourceID":"69152236", 	"ResourceName":"辖区维护", 	"ResourceType":"2 ", 	"ResourceLevel":"2 ", 	"ResourceUrl":"pages/org/orgList.html", 	"ResourceIconType":"icon-manage", 	"ResourceOpenType":"1 ", 	"ResourceSeq":"2", 	"UpperResourceKey":"69152233", 	"ResourceSysID":"",	"Comment":"name=window_011,width=460,height=270,side=right"	}], 	"datalen":14, 	"total":14 	}';
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
            "ResourceIconType": "icon0",
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
            "ResourceIconType": "icon1",
            "ResourceOpenType": "9 ",
            "ResourceSeq": "2"
        },
        {
            "ResourceID": "671117480dd34bd5b8777ed21756c714",
            "ResourceName": "巡逻绑定",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/equip/xkzhcx.html",
            "ResourceSeq": "2",
            "UpperResourceKey": "371a7471445e40f7bb9f978a3e391bc7",
            "Comment": "name=window_005,width=600,height=270,position=left_bottom"
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
            "ResourceIconType": "icon2",
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
            "ResourceID": "380df2ddb6c44598b1c717ff1cd6cb0d",
            "ResourceName": "巡逻特征查询",
            "ResourceType": "1 ",
            "ResourceLevel": "1 ",
            "ResourceUrl": "pages/equip/xltzbcx.html",
            "ResourceSeq": "3",
            "UpperResourceKey": "371a7471445e40f7bb9f978a3e391bc7",
            "Comment": "name=window_004,width=600,height=270,position=left_bottom"
        },
        {
            "ResourceID": "5337244fdc124d5db6e044e8638a7ce3",
            "ResourceName": "综合查询",
            "ResourceType": "1 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd/",
            "ResourceIconType": "icon2",
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
            "ResourceIconType": "icon3",
            "ResourceSeq": "5"
        },
        {
            "ResourceID": "b230f6137bae4f338c83a0f81514e093",
            "ResourceName": "统计报表",
            "ResourceType": "1 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd",
            "ResourceIconType": "icon4",
            "ResourceSeq": "6"
        },
        {
            "ResourceID": "a2da819224384d1c96925820de6073f9",
            "ResourceName": "系统维护",
            "ResourceType": "1 ",
            "ResourceLevel": "9 ",
            "ResourceUrl": "zhdd/",
            "ResourceIconType": "icon2",
            "ResourceSeq": "7"
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
//echo $url;
$result = json_decode($roleInfo, true);
//print_r($result['data']);
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
} else {

	$roleDatas = $result['data'];
	$roleResult = array ();
	//循环一级菜单
	//{ "ResourceID":"fb8be19b8aa34013a31f7c733fd11dcd", "ResourceName":"警情反馈(PC)", "ResourceType":"1", "ResourceLevel":"1", "ResourceUrl":"button", "ResourceOpenType":"1", "ResourceSeq":"3", "UpperResourceKey":"7cb1842b316541feb724c0d61d56a0a4", "Comment":"but_jqfk" },
	for ($i = 0; $i < count($roleDatas); $i++) {
		$ResourceType = isset($roleDatas[$i]['ResourceType'])?trim($roleDatas[$i]['ResourceType']):"";
		$isbutton = isset($roleDatas[$i]['ResourceUrl'])?trim($roleDatas[$i]['ResourceUrl']):"";
		//print_r($ResourceType."==========".$isbutton);

		if (trim($ResourceType)=="2") {
			$roleArr = array (
				"title" => trim($roleDatas[$i]['ResourceName']),
				"setcookie"=>""
			);
			array_push($roleResult, $roleArr);
		}
		if(trim($ResourceType)=="1"&&$isbutton=="button")
          {
										
             $coment = isset ($roleDatas[$i]['Comment']) ? $roleDatas[$i]['Comment'] : "";
			 $roleArr = array (
				"title" => trim($roleDatas[$i]['ResourceName']),
				"setcookie"=>$_SESSION['userId'].$coment
			);
			array_push($roleResult, $roleArr);
										
          }
	}
	$resultRole = array (
		'head' => array (
			'code' => 1,
			'message' => ''
		),
		'value' => $roleResult,
		'extend' => ''
	);
	echo json_encode($resultRole, JSON_UNESCAPED_UNICODE);

}
?>