<?php 
$flag=isset($_REQUEST['event'])?$_REQUEST['event']:"";
$flag = trim($flag);
$content=trim(isset($_REQUEST['content'])?$_REQUEST['content']:"");
$content = Json_decode($content,true);
$responseObj= array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => array(),
				'extend' => array()
			);
 //{"code":1,"msg":"ok","exp":"ok"};
$data= array ();

switch($flag){	
	case "newgroup":	
		$data='[{"groupid":"gid123456_"+Math.floor(Math.random()*100),"userid":"jc12345","iscreator":true,"groupname":"新建组","users":[{"guid":"jc12345","name":"我"},{"guid":"1111","name":"张拉大"},{"guid":"2222","name":"张拉二"},{"guid":"3333","name":"张拉三"},{"guid":"4444","name":"张拉四"}]}]';
		break;
	case "getgroup":
		$gid=$content["condition"][0]["groupid"];
		$data='[{"groupid":gid,"userid":"uid123456","iscreator":false,"groupname":"历史组","users":[{"guid":"g1111","name":"A张拉大"},{"guid":"g2222","name":"B张拉二"},{"guid":"g3333","name":"C张拉三"},{"guid":"g4444","name":"D张拉四"}],"msg":[{"groupid":gid,"SELF":false,"chattime":"10:00:25","name":"历史消息","chat":"过去的消息,发送时间"+now.getFullYear()+"年"+(now.getMonth()+1)+"月"+now.getDate()+"日"}]}]';
		break;	
	case "msg":		
		$data='[{"msg":[{"groupid":"gid123456","SELF":false,"chattime":"10:00:25","name":"消息推送","chat":"新消息,推送"+now.getFullYear()+"年"+(now.getMonth()+1)+"月"+now.getDate()+"日"}],"userlist":{"groupid":"gid123456","users":[{"guid":"jc12345","name":"我"},{"guid":"rrr1111","name":"张耷拉"},{"guid":"2222","name":"张里拉"},{"guid":"3333","name":"张忘啦"},{"guid":"4444","name":"张溜辣"}]}	}]';
		break;
}


//<img src=\"host/upload/up_1428548851374_208.jpg\" />
$responseObj["value"]=$data;
//if(flag=="msg"){
//	var now=(new Date()).getTime();
//	var totime=now+20000;
//	while((new Date()).getTime()<totime){
//		
//	}
//	Response.Write(JSON.stringify(responseObj));
//}else{
echo json_encode($responseObj, JSON_UNESCAPED_UNICODE);	
//}

//"send":HOST+"send", //发消息
//							"getlist":HOST+"getlist",//获取人员列表
//							"delren":HOST+"delren",//删除人
//							"addren":HOST+"addren",//加人							
//							"quit":HOST+"quit",//退出
//							"newgroup":HOST+"newgroup",//新建组
//							"getgroups":HOST+"getgroups",//群列表
//							"closegroup":HOST+"closegroup",//注销群
//							"msg":HOST+"msg" //消息推送


?>