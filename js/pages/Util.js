function Util(){
	
	//查询警情等级
	this.getJqdj = function(jqdjdm){
		var result = "";
		if(jqdjdm=="3"){
			result = "重大";
		}else if(jqdjdm=="2"){
			result = "一般";
		}else if(jqdjdm=="1"){
			result = "简易";
		}
		return result;
	}
	
	//查询警情处理状态
	this.getJqclzt = function(jqclzt){
		var result = "";
		if(jqclzt=="1"){
			result = "未派警";
		}else if(jqclzt=="2"){
			result = "已派警";
		}else if(jqclzt=="3"){
			result = "已接收";
		}else if(jqclzt=="4"){
			result = "处理中";
		}else if(jqclzt=="5"){
			result = "已完成";
		}
		return result;
	}
	
	//查询报警方式
	this.getBjfs = function(bjfs){
		var result = "";
		if(bjfs=="1"){
			result = "执勤巡逻发现";
		}else if(bjfs=="2"){
			result = "扭送现行";
		}else if(bjfs=="8"){
			result = "电话报警";
		}else if(bjfs=="4"){
			result = "举报";
		}else if(bjfs=="5"){
			result = "投案自首";
		}else if(bjfs=="6"){
			result = "其它部门移送";
		}else if(bjfs=="7"){
			result = "其它";
		}else if(bjfs=="0"){
			result = "人工报警";
		}else{
			result = "电话报警";
		}
		return result;
	}
	
	//查询接警类型
	this.getJjlx = function(jjlx){
		var result = "";
		if(jjlx=="001"){
			result = "三合一平台";
		}else{
			result = "其它";
		}
		return result;
	}
	
	//接警方式
	this.getJjfs = function(jjfs){
		var result = "";
		if(jjfs=="001"){
			result = "电话";
		}else{
			result = "其它";
		}
		return result;
	}
	
	//警情案件类型
	this.getAjlx = function(ajlx){
		var result = "";
		if(ajlx=="U00400155"){
			result = "交通案件";
		}else if(ajlx=="U00400156"){
			result = "火灾案件";
		}else if(ajlx=="U00400101"){
			result = "刑事案件";
		}else if(ajlx=="U00400102"){
			result = "治安案件";
		}
		//else if(ajlx=="50000"){
		//	result = "群众求助";
		//}else if(ajlx=="90000"){
		//	result = "应急联动";
		//}
		else if(ajlx=="U00400105"){
			result = "举报投诉";
		}
		//else if(ajlx=="70000"){
		//	result = "事件";
		//}else if(ajlx=="80000"){
		//	result = "举报投诉";
		//}else if(ajlx=="80000"){
		//	result = "纠纷";
		//}
		else{
			result = "其它警情";
		}
		return result;
	}
	
	
	//警情案件类别
	this.getAjlb = function(ajlb){
		var result = "";
		if(ajlb=="U00400102_1"){
			result = "打架斗殴";
		}else if(ajlb=="U00400102_2"){
			result = "盗窃";
		}else if(ajlb=="U00400102_3"){
			result = "诈骗";
		}else if(ajlb=="U00400102_4"){
			result = "抢夺";
		}else if(ajlb=="U00400102_5"){
			result = "敲诈勒索";
		}else if(ajlb=="U00400102_6"){
			result = "故意损毁财务";
		}else if(ajlb=="U00400102_7"){
			result = "寻衅滋事";
		}else if(ajlb=="U00400102_8"){
			result = "殴打他人";
		}else if(ajlb=="U00400102_9"){
			result = "故意伤害";
		}else if(ajlb=="U00400102_10"){
			result = "假币假券";
		}else if(ajlb=="U00400102_11"){
			result = "闹事";
		}else if(ajlb=="U00400102_12"){
			result = "卖淫";
		}else if(ajlb=="U00400102_13"){
			result = "嫖娼";
		}else if(ajlb=="U00400102_14"){
			result = "赌博";
		}else if(ajlb=="U00400102_15"){
			result = "吸毒";
		}else if(ajlb=="U00400102_16"){
			result = "生活噪音";
		}else if(ajlb=="U00400102_17"){
			result = "噪音扰民";
		}else if(ajlb=="U00400102_18"){
			result = "侮辱";
		}else if(ajlb=="U00400102_19"){
			result = "诽谤";
		}else if(ajlb=="U00400102_20"){
			result = "短信诈骗";
		}else if(ajlb=="U00400102_21"){
			result = "贩卖管制刀具";
		}else if(ajlb=="U00400102_22"){
			result = "虐待";
		}else if(ajlb=="U00400102_23"){
			result = "遗弃";
		}else if(ajlb=="U00400102_24"){
			result = "扰乱公共场所秩序";
		}else if(ajlb=="U00400102_25"){
			result = "扰乱单位秩序";
		}else if(ajlb=="U00400102_26"){
			result = "扰乱公共交通工具上的秩序";
		}else if(ajlb=="U00400102_27"){
			result = "发送信息干扰正常生活";
		}else if(ajlb=="U00400102_28"){
			result = "损毁公共设施";
		}else{
			result = "其它";
		}
		return result;
	}
	
	//警情案件类型
	this.getChinaNum = function(val){
		var result = val;
		if(val=="1"){
			result = "一";
		}else if(val=="2"){
			result = "二";
		}else if(val=="3"){
			result = "三";
		}else if(val=="4"){
			result = "四";
		}else if(val=="5"){
			result = "五";
		}else if(val=="6"){
			result = "六";
		}
		return result;
	}
	
	//警情案件类型
	this.getSex = function(val){
		var result = val;
		if(val=="1"){
			result = "男";
		}else {
			result = "女";
		}
		return result;
	}
	
	this.getOrganization = function(){
		var postData={
					"event":"",
					"extend":{"eventswitch":""},
					"content":{"condition":{}}
				};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			bmdmData = backJson['value'];
		});
		$(Loader).bind("SYS_ERROR",function(e,msg){
			//$.error("该派出所失败!");
		});
		Loader.POSTDATA("php/system/GetOrganization.php",postData);
	}
	
	this.getOrgNameById=function(orgCode){
		var orgName = "";
		for (var j=0;j<bmdmData.length;j++) {
			if(orgCode == bmdmData[j]['orgCode']){
				orgName = bmdmData[j]['orgName'];
				break;
			}
		}
		return orgName;
	}
	this.getOrganizationAll = function(){
		var postData={
					"event":"",
					"extend":{"eventswitch":""},
					"content":{"condition":{}}
				};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			bmdmDataAll = backJson['value'];
		});
		$(Loader).bind("SYS_ERROR",function(e,msg){
			//$.error("该派出所失败!");
		});
		Loader.POSTDATA("php/system/GetOrganizationAll.php",postData);
	}
	
	this.getOrgNameByIdAll=function(orgCode){
		var orgName = "";
		for (var j=0;j<bmdmDataAll.length;j++) {
			if(orgCode == bmdmDataAll[j]['orgCode']){
				orgName = bmdmDataAll[j]['orgName'];
				break;
			}
		}
		return orgName;
	}
	//出警单状态
	this.getCjdzt = function(val){
		var result = val;
		if(val=="1"){
			result = "有效";
		}else {
			result = "无效";
		}
		return result;
	}
	//指令标识
	this.getZlbs = function(val){
		var result = val;
		if(val=="1"){
			result = "首接";
		}else {
			result = "增援";
		}
		return result;
	}
	
	//指令标识
	this.getDeviceType = function(val){
		var result = val;
		if(val=="1"){
			result = "车载北斗";
		}else if(val=="2"){
			result = "350兆";
		}else{
			result = "移动警务";
		}
		return result;
	}
	
	//查询预案案件级别
	this.getAjjb = function(value){
		var result = "";
		if(value=="1"){
			result = "I级";
		}else if(value=="2"){
			result = "II级";
		}else if(value=="3"){
			result = "III级";
		}else if(value=="4"){
			result = "IV级";
		}
		return result;
	}
	
	//查询预案处置级别
	this.getCzjb = function(value){
		var result = "";
		if(value=="1"){
			result = "I级指挥处置";
		}else if(value=="2"){
			result = "II级指挥处置";
		}else if(value=="3"){
			result = "III级指挥处置";
		}else if(value=="4"){
			result = "IV级指挥处置";
		}
		return result;
	}
	
	this.getYalb = function(value){
		var result = "";
		if(value=="1"){
			result = "总体应急预案";
		}else if(value=="2"){
			result = "专项应急预案";
		}else if(value=="3"){
			result = "应急保障预案";
		}else if(value=="4"){
			result = "部门应急预案";
		}
		return result;
	}

}