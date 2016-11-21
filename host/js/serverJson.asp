<%
function postDataDealServer(){
	var p=this;
	var pevent=trim(Request.Form("event"));
	var content=trim(Request.Form("content"));
	var extend=trim(Request.Form("extend"));

	this.pevent=(pevent!="" && pevent!="undefined")?pevent:"";
	this.contentObj=(content!="" && pevent!="undefined")?JSON.parse(content):{};
	this.extendObj=(extend!="" && pevent!="undefined")?JSON.parse(extend):{};
		
	this.contentObj=this.contentObj["root"][0]||{};

	//完整事件
	this.getEvent=function(){	
		var switchstr=p.extendObj["eventswitch"]||"";	
		return switchstr?(p.pevent+"_"+switchstr):p.pevent;
	}
	//获取条件参数
	this.getConditionVar=function(varname){	
		return p.contentObj["condition"][varname]||"";
	}
	
	//获取分页参数
	this.getPageVar=function(varname){	
		return p.contentObj[varname]||"";
	}
	
	//获取扩展参数
	this.getExtendVar=function(varname){	
		return p.extendObj[varname]||"";
	}	
}
	
//格式化返回
function formatResponseServer(msgObj){ //msg,code,values
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
	responseObj={ "root":{
				  "head":{"code":msgObj["code"],"message1":msgObj["msg"],"message":""},  //成功ok,msg失败消息
				  "pageID":POSTObj.pevent,
				  "extend":msgObj["extend"]||{},
				  "value":msgObj["values"]
				  }	
				}
	return JSON.stringify(responseObj);
}

//出
function logout(){
	var responseObj={"sessionOut":true};
//	var responseObj={
//	   "root":{
//	   "head":{"code":"9","message":"","message1":"请登录"},
//	   "extend":{},
//	   "value":[]
//	   }
//	 }			 
	Response.Write(JSON.stringify(responseObj));
	Response.End()	
}

%>
