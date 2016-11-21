
var Common = Common || {};
/************************************
 * 配置参数
 ***********************************/
Common.TPMS_VERSION = '1.0.0';

/************************************
 * 全局变量
 ***********************************/
Common.userData ="";

//全局Json格式过滤,EasyUI组件都要加,统一数据格式
function formatCommonJson(JSONObj){
	var resultObj = {};
	if(JSONObj){
		var obj = JSONObj["value"];
		resultObj={
						"total":obj.length,
						"rows":obj
					};
	}
	return resultObj;	
}


//全局Json格式过滤,EasyUI组件都要加,统一数据格式
function formatCommonPointJson(JSONObj){
	var resultObj = {};
	if(JSONObj){
		var obj = JSONObj["value"]["points"];
		resultObj={
						"total":obj.length,
						"rows":obj
					};
	}
	return resultObj;	
}
