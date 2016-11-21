<%
function postDataDeal(){
	var p=this;
	var flag=trim(Request.Form("flag"));
	var content=trim(Request.Form("content"));
	var extend=trim(Request.Form("extend"));

	this.flagObj=(flag!="" && flag!="undefined")?JSON.parse(flag):{};
	this.contentObj=(content!="" && flag!="undefined")?JSON.parse(content):{};
	this.extendObj=(extend!="" && flag!="undefined")?JSON.parse(extend):{};	

	//完整事件
	this.getEvent=function(){	
		return p.flagObj["page"]+"_"+p.flagObj["action"];
	}
	//获取事件
	function getPage(){	
		return p.flagObj["page"];
	}
	//获取动作
	function getAction(){	
		return p.flagObj["action"];
	}
	//获取条件参数
	this.getConditionVar=function(varname){	
		return p.contentObj["condition"][varname]||"";
	}
	//获取扩展参数
	this.getExtendVar=function(varname){	
		return p.extendObj[varname]||"";
	}	
}
	
//格式化返回
function formatResponse(msgObj){ //msg,code,values
	msgObj["values"]=msgObj["values"]?msgObj["values"]:[];
	var responseObj;
	if(msgObj["values"] && typeof msgObj["values"]=="string"){
		try{		
			msgObj["values"]=JSON.parse(msgObj["values"].replace(/\'/g,"\""));
		}catch(e){
			msgObj["code"]=1;
			msgObj["msg"]="操作失败#001";
			msgObj["values"]=null;
		}
	}
	//返回通用格式
	responseObj={ 
				  "status":{"code":msgObj["code"],"result":(msgObj["code"]==0)?"ok":"err","msg":msgObj["msg"]},  //成功ok,失败err ,msg失败消息
				  "flag":POSTObj.flagObj,
				  "values":msgObj["values"]
				  }	
	return JSON.stringify(responseObj);
}

//出
function logout(){
	var responseObj={
	   "status":{"code":9,"result":"err","msg":"请登录"},
	   "flag":POSTObj.flagObj,
	   "value":[]
	   }		 
	Response.Write(JSON.stringify(responseObj));
	Response.End()	
}
%>
