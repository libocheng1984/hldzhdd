//临时排班
function paibanActionPoint(container){
	var p=this;
	var calander;
	var nowBanchi=[];
	var workDay;
	var loop;
	var selector;
	var option={"editable":true}
	
	this.install=function(){
		$.extend(option,container.dataOptions()||{});
		calander=new CalanderObj(container); 
		if(option["editable"]){ 
			$(calander).bind("DATE_ACTION",setPaiban)
		}
		$(calander).bind("CHANGE_MONTH",setDefaultSelected);		
	}
	
	//加载默认值
	this.setDefultValue=function(dateJson){  //LINSHIPAIBAN
		if(!dateJson){		
			return;
		}
		nowBanchi.splice(0,nowBanchi.length);
		var today=new Date();
		for(var i=0;i<dateJson.length;i++){
			var banchi={};
			banchi["WORK"]=dateJson[i]["WORK"];
			banchi["DATE"]=dateJson[i]["DATE"].split("-");
			var thisDate=new Date(parseInt(banchi["DATE"][0]),parseInt(banchi["DATE"][1])-1,parseInt(banchi["DATE"][2]));  
			//过滤掉已失效的任务
			if(today<=thisDate){
				banchi["DATE"]=thisDate.getFullYear()+"-"+thisDate.getMonth()+"-"+thisDate.getDate();			
				nowBanchi.push(banchi);
			}
		}
		setDefaultSelected();
		$(p).trigger("DATE_UPDATE",{"dates":formatDateData()});
	}
	
	function setDefaultSelected(){
		clearSelected();
		setSelected();
		updateAlltdDo();
	}
	
	//设置选中
	function setSelected(){  
		var Datetds=calander.findAllDateTd(); 
		var dateForCheck={};
		Datetds.each(function(){
			var dateTd=$(this);
			dateForCheck[dateTd.data("y")+"-"+dateTd.data("m")+"-"+dateTd.data("d")]=dateTd;		
		});
		for(var i=0;i<nowBanchi.length;i++){
			if(dateForCheck[nowBanchi[i]["DATE"]]){
				var dateTd=dateForCheck[nowBanchi[i]["DATE"]];
				var type=nowBanchi[i]["WORK"];
				var workIcon=$('<div class="userSelectIcon workIcon '+(type?"onwork":"offwork")+'">'+(type?"班":"休")+'</div>');
				dateTd.addClass("UserSelected").css({"position":"relative"}).prepend(workIcon);					
			}
		}
	}
	
	//日期设置
	function setPaiban(e,Vars){
		if(Vars["type"]=="click"){
			var thisDay=new Date(Vars["y"],Vars["m"],Vars["d"]);
			var today=new Date();
			if(thisDay<=today){
				$.message("日期无效")
				return;	
			}
			//显示弹出
			showSelectList(Vars["item"]);			
		}		
	}	
	
	function showSelectList(block){
		//隐藏弹出
		hideSelectList();		
		selector=$('<div class="StatusSelector"><a id="onworkday">上班</a><a id="sleepday">休息</a><a id="cancelthis">取消</a></div>')
		block.prepend(selector);
		selector.data("item",block);
		selector.one("mouseleave",hideSelectList).find(">a").one("click",changeStatus);
	}
	
	function hideSelectList(){   
		if(selector){
			selector.del();
			selector=null	
		}		
	}
	
	this.clearAll=function(){
		if(!option["editable"]){ 
			return;
		}
		var Datetds=calander.findAllDateTd(); 
		Datetds.removeClass("UserSelected").find(".userSelectIcon").remove();
		nowBanchi.splice(0,nowBanchi.length);
		$(p).trigger("DATE_UPDATE",{"dates":formatDateData()});
	}
	
	function changeStatus(){  
		var but=$(this);		
		var block=but.parent().data("item");
		//更新数据
		var dateStr=block.data("y")+"-"+block.data("m")+"-"+block.data("d");
		var oldData=getDataByDate(dateStr);
		var nowdata;
		var nowindex;
		if(!oldData){
			nowdata={"DATE":dateStr}
			nowBanchi.push(nowdata);
			nowindex=nowBanchi.length-1;
		}else{
			nowindex=oldData["index"];
			nowdata=oldData["data"];
		}
		//更新单元格
		block.removeClass("UserSelected").find(".userSelectIcon").remove();
		switch(but.attr("id")){
			case "onworkday":
				nowdata["WORK"]=true;
				var workIcon=$('<div class="userSelectIcon workIcon '+(nowdata["WORK"]?"onwork":"offwork")+'">'+(nowdata["WORK"]?"班":"休")+'</div>');
				block.addClass("UserSelected").css({"position":"relative"}).prepend(workIcon);	
				break;
			case "sleepday":
				nowdata["WORK"]=false;
				var workIcon=$('<div class="userSelectIcon workIcon '+(nowdata["WORK"]?"onwork":"offwork")+'">'+(nowdata["WORK"]?"班":"休")+'</div>');
				block.addClass("UserSelected").css({"position":"relative"}).prepend(workIcon);	
				break;
			case "cancelthis":
				nowBanchi.splice(nowindex,1);			
				break;
		}
		hideSelectList();
		$(p).trigger("DATE_UPDATE",{"dates":formatDateData()});
		return false;
	}
	
	function formatDateData(){
		var dateData=[];
		for(var i=0;i<nowBanchi.length;i++){
			var nowd=nowBanchi[i]["DATE"].split("-");
			var thedate={"WORK":nowBanchi[i]["WORK"],"DATE":parseInt(nowd[0])+"-"+parseInt(nowd[1])+"-"+parseInt(nowd[2])}; 
			dateData.push(thedate);
		}
		return dateData;	
	}
	
	//获取数据
	function getDataByDate(dateStr){
		for(var i=0;i<nowBanchi.length;i++){
			if(nowBanchi[i]["DATE"]==dateStr){
				return {"data":nowBanchi[i],"index":i};
				break;	
			}			
		}
		return null;
	}
	
	function getNearistWorkDate(fromDate,loop,today){   
		today=today?today:new Date();
		today=new Date(today.getFullYear(),today.getMonth(),today.getDate());
		var disDay=(today.getTime()-fromDate.getTime())/(24*60*60*1000);
		var loopTime=Math.ceil(disDay/loop);
		disDay=loopTime*loop;				
		var nearisstDay=new Date(fromDate.getFullYear(),fromDate.getMonth(),fromDate.getDate());
		nearisstDay.setDate(nearisstDay.getDate()+disDay);                            
		return nearisstDay;
	}
	
	
	//显示全部上班状态
	this.updateAllDate=function(workGuizhe){  
		if(!workGuizhe["startDay"] || !workGuizhe["loopStep"]){
			return;	
		}
		workDay=workGuizhe["startDay"].split("-");
		workDay=new Date(parseInt(workDay[0]),parseInt(workDay[1])-1,parseInt(workDay[2]));	
		loop=parseInt(workGuizhe["loopStep"]);	
		updateAlltdDo();
	}
	
	//更新状态
	function updateAlltdDo(){   
		if(!workDay || !loop){
			return;	
		}
		var Datetds=calander.findAllDateTd(); 
		Datetds.removeClass("workUnSelected").find(".normalworkIcon").remove();
		
		//往回减
		var nowWork=1;
		var nowOn=calander.getNowDate(); //日历上的日期
		var ereryDay=getNearistWorkDate(workDay,loop,nowOn); 
		for(var i=0;i<=31;i++){			
			var datetd=calander.findTdByDate(ereryDay.getFullYear(),ereryDay.getMonth(),ereryDay.getDate());      
			if(datetd){	  
				var workIcon=$('<div class="normalworkIcon workIcon '+(nowWork== 1?"onwork":"offwork")+'">'+(nowWork==1?"班":"休")+'</div>');
				datetd.addClass("workUnSelected").css({"position":"relative"}).prepend(workIcon);					
			}else{
				break;	
			}
			nowWork==loop?nowWork=1:nowWork++;
			ereryDay.setDate(ereryDay.getDate()-1);
		}
		
		//往后加		
		nowWork=1;
		ereryDay=getNearistWorkDate(workDay,loop,nowOn);  
		for(var i=0;i<=31;i++){			
			var datetd=calander.findTdByDate(ereryDay.getFullYear(),ereryDay.getMonth(),ereryDay.getDate());  
			if(datetd){	
				var workIcon=$('<div class="normalworkIcon workIcon '+(nowWork==1?"onwork":"offwork")+'">'+(nowWork==1?"班":"休")+'</div>');
				datetd.addClass("workUnSelected").css({"position":"relative"}).prepend(workIcon);	
			}else{
				break;	
			}	
			ereryDay.setDate(ereryDay.getDate()+1);
			nowWork==loop?nowWork=1:nowWork++;		
		}			
	}	
	
	function clearSelected(){     
		var Datetds=calander.findAllDateTd(); 
		Datetds.removeClass("UserSelected").removeClass("workUnSelected").find(".workIcon").remove();
	}	
	this.install();	
}


//排班组件
function paibanAction(container){
	var p=this;
	var calander;
	var selectStart=false;
	var workDay;
	var sleepDay;
	var loop;
	
	this.install=function(){
		calander=new CalanderObj(container);
		$(calander).bind("DATE_ACTION",setPaiban).bind("CHANGE_MONTH",setDefaultSelected);		
	}
	
	function setPaiban(e,Vars){
		if(Vars["type"]=="click"){
			if(!selectStart){
				workDay=new Date(Vars["y"],Vars["m"],Vars["d"]);
				resetSelected(Vars["item"]);
				selectStart=true;
			}else{	
				sleepDay=new Date(Vars["y"],Vars["m"],Vars["d"]);			
				if(workDay>sleepDay){
					return;
				}				
				selectStart=false;
				loop=Math.floor((sleepDay.getTime()-workDay.getTime())/(24*60*60*1000))+1;
				setSelected(workDay,sleepDay);
			}			
		}		
	}	
	
	//加载默认值
	this.setDefultValue=function(dateJson){
		if(!dateJson || !dateJson["startDay"] || !dateJson["loopStep"]){		
			return;
		}
		loop=parseInt(dateJson["loopStep"]);
		var fromD=dateJson["startDay"].split("-");
		var start=new Date(parseInt(fromD[0]),parseInt(fromD[1])-1,parseInt(fromD[2]));		  
		workDay=getNearistWorkDate(start,loop);                      
		sleepDay=new Date(workDay.getFullYear(),workDay.getMonth(),workDay.getDate());
		sleepDay.setDate(sleepDay.getDate()+loop-1);		
		setDefaultSelected();
	}
	
	//显示全部上班状态
	function computAllTdStatus(){
		//往回减
		var nowWork=1;
		var nowOn=calander.getNowDate(); //日历上的日期
		var ereryDay=getNearistWorkDate(workDay,loop,nowOn); 
		for(var i=0;i<=31;i++){			
			var datetd=calander.findTdByDate(ereryDay.getFullYear(),ereryDay.getMonth(),ereryDay.getDate());      
			if(datetd){	
				if(!datetd.hasClass("workSelected")){			  
					var workIcon=$('<div class="normalworkIcon workIcon '+(nowWork== 1?"onwork":"offwork")+'">'+(nowWork==1?"班":"休")+'</div>');
					datetd.addClass("workUnSelected").css({"position":"relative"}).prepend(workIcon);					
				}
			}else{
				break;	
			}
			nowWork==loop?nowWork=1:nowWork++;
			ereryDay.setDate(ereryDay.getDate()-1);
		}
		
		//往后加		
		nowWork=1;
		ereryDay=getNearistWorkDate(workDay,loop,nowOn);  
		for(var i=0;i<=31;i++){			
			var datetd=calander.findTdByDate(ereryDay.getFullYear(),ereryDay.getMonth(),ereryDay.getDate());  
			if(datetd){	
				if(!datetd.hasClass("workSelected")){   		
					var workIcon=$('<div class="normalworkIcon workIcon '+(nowWork==1?"onwork":"offwork")+'">'+(nowWork==1?"班":"休")+'</div>');
					datetd.addClass("workUnSelected").css({"position":"relative"}).prepend(workIcon);					
				}
			}else{
				break;	
			}	
			ereryDay.setDate(ereryDay.getDate()+1);
			nowWork==loop?nowWork=1:nowWork++;		
		}			
	}	
	
	function getNearistWorkDate(fromDate,loop,today){   
		today=today?today:new Date();
		today=new Date(today.getFullYear(),today.getMonth(),today.getDate());
		var disDay=(today.getTime()-fromDate.getTime())/(24*60*60*1000);
		var loopTime=Math.ceil(disDay/loop);
		disDay=loopTime*loop;				
		var nearisstDay=new Date(fromDate.getFullYear(),fromDate.getMonth(),fromDate.getDate());
		nearisstDay.setDate(nearisstDay.getDate()+disDay);                            
		return nearisstDay;
	}
	
	//设置默认选择状态
	function setDefaultSelected(){
		clearSelected();
		setSelected(workDay,sleepDay);
	}
	
	//重新开始选择
	function resetSelected(datetd){	
		clearSelected();		
		var workIcon=$('<div class="workIcon onwork">班</div>');
		datetd.addClass("workSelected").css({"position":"relative"}).prepend(workIcon);	
	}
	
	function clearSelected(){     
		var Datetds=calander.findAllDateTd(); 
		Datetds.removeClass("workSelected").removeClass("workUnSelected").find(".workIcon").remove();
	}
	
	//显示选择结果
	function setSelected(start,end){		
		var 	from=new Date(start.getFullYear(),start.getMonth(),start.getDate());
		var 	to=new Date(end.getFullYear(),end.getMonth(),end.getDate());
		
		var datetd=calander.findTdByDate(start.getFullYear(),start.getMonth(),start.getDate());   
		if(datetd){
			var workIcon=$('<div class="workIcon onwork">班</div>');
			datetd.removeClass("workUnSelected").addClass("workSelected").css({"position":"relative"}).prepend(workIcon);
		}	
		
		while(from<to){		  		
			from.setDate(from.getDate()+1);
			var datetd=calander.findTdByDate(from.getFullYear(),from.getMonth(),from.getDate());	
			if(datetd){
				var workIcon=$('<div class="workIcon offwork">休</div>');
				datetd.removeClass("workUnSelected").addClass("workSelected").css({"position":"relative"}).prepend(workIcon);	
			}
		}
		computAllTdStatus();		
		$(p).trigger("PART_SELECTED",{"startDay":start.getFullYear()+"-"+(start.getMonth()+1)+"-"+start.getDate(),"loopStep":loop});
	}	
	
	this.install();	
}

//日历
function CalanderObj(container,formatFun){	
	var p=this;
	var calanderBox;
	var nowDay;
	var calanderDateBox;
	var calanderTitleBox;
	var weekStr="一二三四五六日";
	var option={"beforeMonth":6,"afterMonth":6};
	var enableTitle=[];//有效月份标记
	var selectField;
	
	this.install=function(){	 
		if(container.data("obj")){			
			return 	container.data("obj");
		}	
		var newoption=$(container).dataOptions()||{};
		$.extend(option,newoption);		 
		 var fieldStart=option["beforeMonth"]>0?getField(-option["beforeMonth"]):new Date(1900,1,1);
		 var fieldEnd=option["afterMonth"]>0?getField(option["afterMonth"]):new Date(3000,1,1);
		 selectField=[fieldStart,fieldEnd];		 
		calanderBox=$('<div class="widget_calander"></div>');
		calanderBox.css({"overflow":"hidden","position":"relative"});
		container.empty().append(calanderBox);
		//标题
		calanderDateBox=$('<table width="100%" border="0" cellspacing="0" cellpadding="0"></table>');
		calanderTitleBox=$('<div class="calander_title"><a class="prev"><</a><span></span><a class="next">></a></div>');
		calanderBox.append(calanderTitleBox).append(calanderDateBox);
		//日期		
		for(var i=0;i<7;i++){
			var tr='<tr>';
			for(var j=0;j<7;j++){				
				tr+=(i==0)?'<th width="14.28%">'+weekStr.substr(j,1)+'</th>':'<td>'+(i+j)+'</td>';
			}
			tr+='</tr>';
			calanderDateBox.append($(tr));
		}	
				
		nowDay=new Date();	
		creatCalander(nowDay);			
		calanderTitleBox.find(">a").bind("click",changeMonth);
		calanderDateBox.find("tr:eq(0) th:gt(4)").addClass("calander_weekend");		
		calanderDateBox.find("tr >td").bind("click contextmenu dblclick",showEditor);		
		container.data("obj",p);
	}	
	
	function getField(dism){
		var td=new Date();	
		td.setMonth(td.getMonth()+dism);	
		return td;				
	}

	//显示日程
	this.showMemo=function(memory){
		MyMemory=memory?memory:{};
		if(!saveToHost){
			//清除超期数据,保留前后2个月
			clearOldData();		
		}
		//检查备注
		checkMemory();			
	}
	//清除超期数据,保留前后1个月
	function clearOldData(){		
		for(var na in MyMemory){
			  if(!checkEnable(na)){
				  delete MyMemory[na];
			  }
		}	
	}	
	//月份切换
	function changeMonth(){
		var tm=new Date(nowDay.getFullYear(),nowDay.getMonth(),1);
		var minm=new Date(selectField[0].getFullYear(),selectField[0].getMonth(),1);
		var maxm=new Date(selectField[1].getFullYear(),selectField[1].getMonth(),1);
		
		if($(this).hasClass("prev") && tm>minm){
			nowDay.setMonth(nowDay.getMonth()-1);
		}else if($(this).hasClass("next") && tm<maxm){
			nowDay.setMonth(nowDay.getMonth()+1);
		}else{
			return;	
		}
		creatCalander(nowDay);
		$(p).trigger("CHANGE_MONTH",{"y":nowDay.getFullYear(),"m":nowDay.getMonth(),"d":nowDay.getDate()});
	}
	//更新日历
	function creatCalander(dateObj){
		var today=new Date();
		var todayM=today.getMonth();
		var todayD=today.getDate();
		var todayY=today.getFullYear();			
		var TM=dateObj.getMonth();
		var TY=dateObj.getFullYear();
		
		var startday=new Date(TY,TM,1);
		startday.setDate(1);
		calanderTitleBox.find(">span").text(TY+"年 "+(TM+1)+"月");
		calanderDateBox.find("td").removeClass("calander_otherMonth").removeClass("calander_today");
		var startpos=startday.getDay()-1;//适应后面周日
		if(startpos<0){startpos=6};		
		startday.setDate(1);
		startday.setDate(startday.getDate()-startpos-1);	
		var len=42;
		
		for(var i=0;i<len;i++){			
			startday.setDate(startday.getDate()+1);  
			var D=startday.getDate();
			var M=startday.getMonth();
			var Y=startday.getFullYear();
			var row=Math.floor(i/7)+1;
			var clum=i%7;
			var box=calanderDateBox.find("tr:eq("+row+") td:eq("+clum+")");
			
			var dateObj=formatFun?formatFun(box,D):$('<span>'+D+'</span>');
			box.empty().append(dateObj);			
			
			box.data({"y":Y,"m":M,"d":D,"id":Y+"_"+M+"_"+D});
			box.attr("id",Y+"_"+M+"_"+D);
			if(TM!=M){
				box.addClass("calander_otherMonth");				
			}
			if(todayY==Y && todayM==M && todayD==D){
				box.addClass("calander_today");				
			}
		}					
	}
	
	//获取当前显示日期
	this.getNowDate=function(){
		return new Date(nowDay.getFullYear(),nowDay.getMonth(),nowDay.getDate());
	}
	
	//返回全部日期单元格
	this.findAllDateTd=function(){
		return calanderDateBox.find("tr:gt(0) >td");
	}
	
	//查找日期对应的单元格
	this.findTdByDate=function(y,m,d){
		var dateblocks=calanderDateBox.find("tr:gt(0) >td");   
		var dateTd;
		dateblocks.each(function(){	
				var box=$(this);
				if(box.data("y")==y &&box.data("m")==m&&box.data("d")==d){
					dateTd=box;
					return;
				}			
			}
		)		
		return 	dateTd;
	}
	
	//进入编辑
	function showEditor(e){ 
		var block=$(this);
		$(p).trigger("DATE_ACTION",{"type":e.type,"item":block,"y":block.data("y"),"m":block.data("m"),"d":block.data("d"),"dateStr":block.data("y")+"年"+(block.data("m")+1)+"月"+block.data("d")+"日"});
	}
	
	this.destroy=function(){
		$(p).unbind();
		calanderBox.del();
	}

	this.install();
}