<%
//数据库对象--------------------------------------------------------------
function DataBase(DbPath){
	var p=this;
	this.CONN; //连接
	this.RS;  //数据集
	this.isSQLDB=false; //数据库类型
	this.SQLConfig={"sqlserver":"","sqluser":"","sqlpws":"","sqldb":""};
	
	//初始化连接
	this.connect=function(){
		if(!p.CONN){
		  p.CONN=new ActiveXObject("ADODB.Connection");
		  p.CONN.cursorLocation = 3; //这个是为了在执行sql语句时用connect.Execute ,而不是繁琐的RecordSet 
		  if(p.isSQLDB){  
			  var strconnect="Provider=SQLOLEDB.1;Persist Security Info=False;Data Source="+SQLConfig["sqlserver"]+
							  ";User ID="+SQLConfig["sqluser"]+
							  ";Password="+SQLConfig["sqlpws"]+
							  ";Initial Catalog="+SQLConfig["sqldb"]+";";
			  p.CONN.Open(strconnect); 
			}else{
			  p.CONN.Open("Provider=Microsoft.Jet.OLEDB.4.0;Data Source="+Server.MapPath(DbPath));
			} 			 
		}	
		if(p.RS){
			try{
				p.RS.Close();
			}catch(e){				
			}
		}
		p.RS=Server.CreateObject("ADODB.Recordset"); 
		p.RS.ActiveConnection = p.CONN; 
		p.RS.CursorType = 1; 
	}
	
	//执行
	this.EXECUTE=function(sql){
		return p.CONN.Execute(sql);  //rs.fields("souquan").value  取值
	} 
	
	//查询,获取数据
	this.SELECT=function(sql,page,num){ 
		var rows=[];
		var rs = Server.CreateObject("ADODB.Recordset"); 
		rs.ActiveConnection = p.CONN; 	
		rs.CursorType = 1; 
		try{
			rs.Open(sql);
		}catch(e){
			return {"status":"error","data":rows};
		} 
		if(rs.eof){
			rs.Close();  
			return {"status":"ok","data":rows}; 
		}
		var total=rs.recordcount;
		if(!page){ //不分页
			while(!rs.eof){ 
				var row={};  
				for(i=0;i<rs.fields.count;i++){
					row[rs.fields(i).name]=getValueType(rs.fields(i).type,rs.fields(i).value); 
				}  
				rows.push(row); 
				rs.MoveNext();  
			}
		}else{ //分页
			rs.MoveFirst();
			rs.Move((page-1)*num);
			var id=0;
			while(!rs.eof && id<num){ 			
				var row={};  
				for(i=0;i<rs.fields.count;i++){
					row[rs.fields(i).name]=getValueType(rs.fields(i).type,rs.fields(i).value); 
				}  
				rows.push(row); 
				id++;			
				rs.MoveNext();  				
			}
		}  
		rs.Close();  
		rs=null;
		return {"status":"ok","data":rows,"total":total};  
	} 
	
	//获取一条数据
	this.GETONE=function(table,condition,formatli){
		var sql="Select top 1 * From "+table+" where "+condition;	
		var rs = Server.CreateObject("ADODB.Recordset"); 
		rs.ActiveConnection = p.CONN; 	
		rs.CursorType = 1; 
		var row={}; 
		try{
			rs.Open(sql);
		}catch(e){
			return {"status":"error","data":row};
		}		
		var stat="ok";
		if(!rs.eof){
			var row={};
			for(i=0;i<rs.fields.count;i++){			
				row[rs.fields(i).name]=getValueType(rs.fields(i).type,rs.fields(i).value); 
			} 		
			var LI=cloneJson(formatli);
			for(var na in LI){
				LI[na]=row[LI[na]];
			}
		}else{
			stat="error";
		}	
		rs.Close();  
		rs=null;
		return {"status":stat,"data":LI}; 
	} 
	
	//获取一条数据 sql
	this.GETONESQL=function(sql,formatli){
		var rs = Server.CreateObject("ADODB.Recordset"); 
		rs.ActiveConnection = p.CONN; 	
		rs.CursorType = 1; 
		var row={}; 
		try{
			rs.Open(sql);
		}catch(e){		
			return {"status":"error","data":row};
		}		
		var stat="ok";
		if(!rs.eof){
			var row={};
			for(i=0;i<rs.fields.count;i++){			
				row[rs.fields(i).name]=getValueType(rs.fields(i).type,rs.fields(i).value); 
			} 		
			var LI=cloneJson(formatli);
			for(var na in LI){
				LI[na]=row[LI[na]];
			}
		}else{
			stat="error";
		}	
		rs.Close();  
		rs=null;
		return {"status":stat,"data":LI}; 
	} 
	
	//保存数据
	this.SAVEUPDATE=function(table,condition,values,backName){ //表,条件string,值Object,返回值id
		var typeobj=p.getDbArray(table);
		var firstClum;
		condition=condition?condition:"";
		var update=(condition!="")?true:false;			
		var sql="Select * From ["+table+"] ";		
		update?sql+=" where "+condition:sql+=" where "+backName+" is NULL";	
		var rs = Server.CreateObject("ADODB.Recordset"); 
		rs.ActiveConnection = p.CONN; 	
		rs.CursorType = 1;  
		rs.LockType = 2;  
		rs.Open(sql);  
		if(!update){
			rs.AddNew()
		}else if(rs.eof){
			rs.Close(); 
			rs=null
			return null;
		}  		
		for(na in values){     
			if(typeobj[na]){
				rs(na)=chgValueType(typeobj[na],values[na]);
			}
		}
		rs.Update(); 		
		var nid = rs(backName)+"";
		rs.Close(); 
		rs=null
		return nid;
	}
	//更新
	this.UPDATE=function(table,condition,values){ //表,条件string,值Object
		condition=condition?condition:"";	
		var sql="Select * From ["+table+"]  where "+condition;
		//Response.Write(sql)	
		var rs = Server.CreateObject("ADODB.Recordset"); 
		rs.ActiveConnection = p.CONN; 	
		rs.CursorType = 1;  
		rs.LockType = 2;  
		rs.Open(sql);
		if(rs.eof){
			rs.Close(); 
			rs=null
			return "error";
		} 		
		for(var i=0;i<rs.Fields.Count;i++){
			  if(values[rs.Fields(i).Name]!=null){
				  rs(rs.Fields(i).Name)=chgValueType(rs.Fields(i).Type,values[rs.Fields(i).Name]);
			  }
		 }	
		rs.Update();	
		rs.Close(); 
		rs=null;
		return "ok";
	}
	//插入
	this.INSERT=function(table,values,backName){ //表,值Object,返回值id
		//var typeobj=p.getDbArray(table);
		var sql="Select * From ["+table+"] where "+backName+" is NULL";		
		var rs = Server.CreateObject("ADODB.Recordset"); 
		rs.ActiveConnection = p.CONN; 	
		rs.CursorType = 1;  
		rs.LockType = 3;  
		rs.Open(sql);  		
		rs.AddNew();  	
		for(var i=0;i<rs.Fields.Count;i++){
		  if(values[rs.Fields(i).Name]!=null && !(rs.Fields(i).Type==7 && !values[rs.Fields(i).Name])){
			  rs(rs.Fields(i).Name)=chgValueType(rs.Fields(i).Type,values[rs.Fields(i).Name]);
		  }
		}	
		rs.Update(); 		
		var nid = rs(backName)+""; 
		rs.Close(); 
		rs=null;
		return nid;
	}
	//删除
	this.DELECT=function(table,condition){
		condition=condition?" where "+condition:"";		
		p.EXECUTE("Delete From [" +table+ "] "+condition);  
	}
	//查重
	this.checkExist=function(table,condition){
		var check=p.EXECUTE("select * from ["+table+"] where "+condition);
		if(!check.eof){
			return true;
		};
		return false;
	}	

 	//获取记录行数
	this.count=function(){
		return p.RS.recordCount; 
	} 	
	
	//取数据结构---------------------------------------------------------------------------------------
	this.getDbArray=function(table){
		var clums={};
		var sql = "select * from ["+table+"] where 1<>1"; 
		var rs = Server.CreateObject("ADODB.Recordset"); 
		rs.ActiveConnection = p.CONN; 	
		rs.CursorType = 1;  
		rs.Open(sql);  
		for(var i=0;i<rs.Fields.Count;i++){
			clums[rs.Fields(i).Name]=rs.Fields(i).Type;
		}
		rs.Close();
		rs=null;
		return clums;
	}
}

//转化数据类型
function getValueType(coltype,value){
	var rsstr=value;
	switch(coltype){ 
		case 3://"自动编号(数字)"
			rsstr=parseInt(rsstr+"")||0;			
			break; 
		case 202: //"字符"
		case 203://"备注"
			rsstr=(rsstr==null)?"":rsstr;			
			break;  
		case 7://"日期"
			try{
				var newdate=new Date(rsstr)
				rsstr=newdate.getFullYear()+"-"+format2((newdate.getMonth()+1),2)+"-"+format2(newdate.getDate(),2)+" "+format2(newdate.getHours(),2)+":"+format2(newdate.getMinutes(),2)+":"+format2(newdate.getSeconds(),2);
				rsstr=rsstr.replace(" 00:00:00","")
			}catch(e){
				rsstr="";
			}			
			break;  
		case 11: //"真/假(是/否)" 
			if(rsstr=="0" || rsstr=="" || rsstr=="false"){
				rsstr=false;
			}else{
				rsstr=true;
			}
			
			break; 
	}
	return rsstr; 	
}

//格式化2位
function format2(str,num){
	var tstr=trim(str)+"";
	while(tstr.length<num){
		tstr="0"+tstr;	
	}
	return tstr;
}

//转化数据类型
function chgValueType(coltype,value){
	var rsstr=value;
	switch(coltype){ 
		case 3:
			rsstr=parseInt(rsstr)||0;			
			//filtype="自动编号(数字)"
			break; 
		case 202: //"字符" 
		case 203://"备注"
			if(typeof rsstr=="object"){
				rsstr=JSON.stringify(rsstr);
				rsstr=rsstr.replace(/\"/g,"\'");
			}else{
				rsstr=rsstr.toString();
			}
			break;  
		case 7:
			rsstr=rsstr.toString();
			//filtype="日期"
			break;  
		case 11: 
			if(rsstr=="0" || rsstr=="" || rsstr=="false"){
				rsstr=false;
			}else{
				rsstr=true;
			}
			//filtype="真/假(是/否)" 
			break; 
	}
	return rsstr; 	
}

//取列表
function getListData(sql,formatli,page,num){
	var listData=DB.SELECT(sql,page,num);
	var LIST=[];	
	if(listData["status"]=="ok"){
	
//for(var na in listData["data"][0]){
//			Response.Write(na+"/n")
//		}	
		var len=listData["data"].length;
		for(var i=0;i<len;i++){
			var LI=cloneJson(formatli);
			LIST.push(LI);
			for(var na in LI){
				LI[na]=listData["data"][i][LI[na]];
			}
		}		
	}else{
		return null;
	}
	return {"list":LIST,"total":listData["total"]};
}

//send email----------------------------------------------------------------------------------
function sendmail(mailto,Subject,content){
  try{
  		var msg = Server.CreateObject("JMail.Message") ;
		msg.silent = true ;
		msg.Logging = false; 
		msg.Charset = "gb2312" ;
		msg.MailServerUserName = mail_id;
		msg.MailServerPassword = mail_pws;
		msg.From = mail_id;
		msg.FromName = mail_name;
		msg.AddRecipient(mailto);
		msg.Subject = Subject;
		msg.Body = content;
		msg.Send(mail_smtp);
		msg.close();
		msg = null 
  }catch(e){
  		//Response.Write(e.toString());
  }
}


//克隆JSON
function cloneJson(jsonobj){
	try{
		return JSON.parse(JSON.stringify(jsonobj));
	}catch(e){
		return null;
	}
}

//去掉前后空格-----
function trim(str){ 
	return (str+'').replace(/^\s*|\s*$/g,''); 
}

//格式化sql
function formatSQL(sql){
	return sql.replace(/'/g,chr(34));
}
%>