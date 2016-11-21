<%
function getTestRs(healthrs,isOld){//是否老年版
	  var VAL=healthrs;
	  
	  var tizhiTitle={"A":"平和","B":"气虚","C":"阳虚","D":"阴虚","E":"痰湿","F":"湿热","G":"血瘀","H":"气郁","I":"特禀"};
	  var nums={"A":8,"B":8,"C":7,"D":8,"E":8,"F":6,"G":7,"H":7,"I":7};
	  	
	  var tizi=new Array();
	  var pt=0;//判断
	  var ct=0;//倾向	
	  
	  for(var na in VAL){
		  if(!isOld){
			  VAL[na]=((VAL[na]-nums[na])/(nums[na]*4))*100;  //转化分数=〔(原始分-条目数) /(条目数× 4 )〕×100	
		  }
		  if(na!="A"){
			  tizi.push(VAL[na]);
		  }
	  }		
  
	  tizi.sort(function(a,b){return a>b?-1:1}); //从大到小排序,按数字,默认按字符			
	  var ptt=tizi[0];//最大值
	  var ctt=tizi[1];//第二			
  
	  if(isOld){ //老年算法
		  if(ptt>=11){
			  pt=ptt;//
			  if(ctt>=9){
				  ct=ctt;
			  }					
		  }else if(VAL["A"]>=17){
			  pt=0;//平和体质
			  ct=ptt;
		  }			
	  }else{
		  if(ptt>=40){
			  pt=ptt;//
			  if(ctt>=30){
				  ct=ctt;
			  }
		  }else if(ptt>=30){
			  pt=0;//没有则判断平和
			  ct=ptt;
		  }
	  }		
	  var panding="";
	  var tizhi_1;
	  var tizhi_2;
	  //开始判断主型			
	  if(pt==0){ //&& VAL["A"]>=40){
		  panding=tizhiTitle["A"]+"型体质";				
		  tizhi_1="A";
	  }else{
		  tizhi_1=pt;
		  for(var na in VAL){
			  if(pt==VAL[na]){
				  panding=tizhiTitle[na]+"型体质";				
				  tizhi_1=na;
				  break;	
			  }
		  }			
	  }		
	  //偏向判定
	  if(ct!=0){		
		  for(var na in VAL){
			  if(ct==VAL[na] && tizhi_1!=na){
				  panding+="，偏向"+tizhiTitle[na]+"型体质";				
				  tizhi_2=na;
				  break;	
			  }
		  }								
	  }			
	  for(var na in VAL){
	  	  //老年版折算，平和25，其他20；
		  if(isOld){
		  	VAL[na]=100*VAL[na]/((na=="A")?25:20);	
		  }
		  tizhiTitle[na]=Math.round(VAL[na]);	
	  }
	  return {"panding":panding,"tizhi":[tizhi_1,tizhi_2],"defen":tizhiTitle};
  }

%>

