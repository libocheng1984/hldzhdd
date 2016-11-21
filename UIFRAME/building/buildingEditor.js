function buildingEditor(j,k){var m=this;this.option={"title":"装户图","space":4,"roomwidth":72,"roomheight":48,"minwidth":12,"minheight":8,"zoomstep":10,"zindex":999,"default":{"D":3,"L":6,"R":3,"G":0,"S":0},"editable":true};var f;var s;var r;var q;var i;var v;var l=false;this.install=function(){$.extend(m.option,k);j.css({"position":"relative"});r=new BuildingCore(j);r.install(m.option);v=r.getCanvars();$(r).bind("RESET_ACTION",g);i=$('<div class="bd_infobox"></div>');j.append(i);i.css({"position":"absolute","top":"40px","left":"10px","z-index":m.option["zindex"]+1})};this.LOAD=function(z,x,B){for(var A=0;A<z.length;A++){for(var p=0;p<z[A]["children"].length;p++){for(var y=0;y<z[A]["children"][p]["children"].length;y++){z[A]["children"][p]["children"][y]["olddata"]=true;z[A]["children"][p]["children"][y]["rid"]=z[A]["children"][p]["children"][y]["name"]}}}r.creatBuilding(z,true);r.isNewBuilding=false};this.EDIT=function(p){if(p){sysIcon=$('<div class="bd_sysIcon">'+'<a  id="building_new" class="bd_new"><i class="fa fa-file-o" ></i>新建结构</a>'+'<a  id="building_merge" class="bd_edit"><i class="fa fa-chain"></i>合并结构</a>'+'<a  id="building_rename" class="bd_insert"><i class="fa fa-sort-numeric-desc"></i>更新命名</a>'+"</div>");j.append(sysIcon);sysIcon.css({"top":"10px","right":"10px","position":"absolute","z-index":m.option["zindex"]+2});sysIcon.find(">a").bind("click",n)}else{if(sysIcon){sysIcon.find(">a").unbind();sysIcon.del();sysIcon=null}}r.setEdit(p)};this.INSERT=function(p){};this.SHOW=function(p){};function g(z,p){var A=p["rooms"];var x=p["floors"];var y=p["status"];if(r.isEdit){A.on("contextmenu",a);x.on("contextmenu",a)}else{if(typeof m.option["roomClick"]=="function"){A.on("click",function(D){if(r.isDraged()){return}var C=$(this).data("data");var C=r.getRealRoom(C);var B=r.getGroupData(C["groupid"]);m.setSelected(C["div"]);m.option["roomClick"](D,$(this),C,B)})}if(typeof m.option["addinfoFun"]=="function"){A.on("mouseenter",function(){if(l){return}var C=$(this).data("data");var C=r.getRealRoom(C);var B=r.getGroupData(C["groupid"]);var D=m.option["addinfoFun"](C,B);D?$.tips($(this),"right",D,[200],10000):null}).on("mousedown",function(){$.closetips();l=true}).on("mouseleave",function(){$.closetips()}).on("mouseup",function(){l=false})}}}this.getRoomByGuid=function(p){};this.renderRoom=function(x,p){r.renewRoom(x,p)};this.setSelected=function(x,p){r.setSelected(x,p)};this.showContInfo=function(p){i.empty().append(p)};function a(p){var y=p.pageX-2;var x=p.pageY-2;if(q){q.del();q=null}q=t($(this));if(!q){return false}q.data({"target":$(this)});m.popoutRight(q,y,x);p.stopPropagation();return false}this.popoutRight=function(p,y,x){p.css({"position":"absolute","top":x+"px","left":y+"px","z-index":m.option["zindex"]+10});$("body").append(p);p.one("mouseleave",function(){p.remove();p=null})};function t(x){var y;var p=x.hasClass("bd_floor");y=$('<div class="bd_rightmenu"><p><span>房间</span>'+(p?"":'<a id="delroom">删除</a>')+'<a id="addroom">增加</a></p>'+'<p><span>楼层</span><a id="delfloor">删除</a><a id="addfloor">复制</a></p>'+'<p><span>单元</span><a id="delpart">删除</a><a id="addpart">复制</a></p>'+'<p><span>地下室</span><a id="addgroundfloor">加</a><a id="delgroundfloor">减</a></p>'+'<p><span>商铺</span><a id="addstorefloor">加</a><a id="delstorefloor">减</a></p></div>');y.find("a").bind("click",d);return y}function d(){var p=$(this).parent().parent().data("target");switch($(this).attr("id")){case"delroom":r.deleteRoom(p.data("D"),p.data("L"),p.data("R"));break;case"delfloor":r.deleteFloor(p.data("D"),p.data("L"));break;case"delpart":r.deletePart(p.data("D"));break;case"addpart":r.addpart(p.data("D"));break;case"addroom":r.addRoom(p.data("D"),p.data("L"));break;case"addfloor":r.addFloor(p.data("D"),p.data("L"));break;case"addgroundfloor":r.setGroundNum(true,p.data("D"));break;case"delgroundfloor":r.setGroundNum(false,p.data("D"));break;case"addstorefloor":r.setStoreNum(true,p.data("D"));break;case"delstorefloor":r.setStoreNum(false,p.data("D"));break}if(q){q.remove();q=null}}function n(y){switch($(this).attr("id")){case"building_merge":if($(this).hasClass("nowid")){r.setMerge(false);$(this).removeClass("nowid")}else{r.setMerge(true);$(this).addClass("nowid")}break;case"building_rename":var p=w();j.append(p);p.css({"position":"absolute","top":"0px","bottom":"0px","right":"0px","z-index":m.option["zindex"]+3});break;case"building_new":var x=function(){var z=h();j.append(z);z.css({"position":"absolute","top":"0px","bottom":"0px","right":"0px","z-index":m.option["zindex"]+3})};if(!r.isNewBuilding){$.confirm("新建层户结构将删除原结构数据，是否继续？",function(z){if(z){x()}else{return}})}else{x()}break}}function o(z){var y=0;var p=false;for(var x=0;x<z.length;x++){y=Math.max(y,z[x]["id"]||0);if(z[x]["color"]){p=true}}y++;for(var x=0;x<z.length;x++){delete z[x]["update"];if(!z[x]["id"]&&z[x]["R"]){z[x]["id"]=y;z[x]["update"]=true;y++}}if(p){for(var x=0;x<z.length;x++){if(z[x]["color"]){u(z[x],z)}}}return z}function u(y,x){var z=y["color"];var A=[];for(var p=0;p<x.length;
p++){if(x[p]["color"]==z){A.push(x[p]["id"])}}y["merge"]=A;y["groupid"]=y["id"];y["update"]=true;for(var p=0;p<x.length;p++){if(x[p]["color"]==z){x[p]["groupid"]=y["id"];x[p]["merge"]=A;x[p]["update"]=true;delete x[p]["color"]}}}this.formatBuildingData=function(A){if(!A){return}A=o(A);var C=[];var x;var E;for(var y=0;y<A.length;y++){var B=A[y]["D"];var p=A[y]["L"];var z=A[y]["R"];if(!p){C.push(A[y]);x=A[y];x["children"]=[]}else{if(!z){x["children"].push(A[y]);E=A[y];E["children"]=[]}else{E["children"].push(A[y])}}}return C};this.getBuildingData=function(){var I=cloneJson({"data":r.buildingData});var p=[];var A=[];var J=[];var B=[];var F=I["data"].length;var S=0;var C=0;var N=0;var E=0;var Q=0;for(var P=0;P<I["data"].length;P++){var G=cloneJson(I["data"][P]);var O=G["ground"];if(O){S++}N=Math.max(N,O);C=Math.max(C,(G["children"].length-O));delete G["children"];G["D"]=P+1;e(G);J.push(G);for(var K=0;K<I["data"][P]["children"].length;K++){var z=cloneJson(I["data"][P]["children"][K]);var y=K-O;y=(y>=0)?y+1:y;if(y>0){E=Math.max(E,z["children"].length)}else{Q=Math.max(Q,z["children"].length)}delete z["children"];z["D"]=G["D"];z["L"]=y;z["name"]=""+(K+1);e(z);B.push(z);for(var H=0;H<I["data"][P]["children"][K]["children"].length;H++){var x=I["data"][P]["children"][K]["children"][H];if(x["olddata"]&&!x["update"]){}else{x["D"]=G["D"];x["L"]=z["L"];x["R"]=H+1;e(x);if(x["update"]){A.push(x)}else{p.push(x)}}}}}var T=r.getToDelete();for(var M=0;M<T.length;M++){e(T[M])}return{"units":J,"floor":B,"update":A,"new":p,"todelete":T,"unitmaxup":F,"unitmaxdown":S,"floormaxup":C,"floormaxdown":N,"roommaxup":E,"roommaxdown":Q}};function e(p){delete p["div"];delete p["info"];delete p["bounce"];delete p["olddata"];delete p["rid"]}function h(){var x;x=$('<div class="bd_edittools bd_pannel">'+"<p>建筑基础结构</p>"+'<div class="bd_menupart">'+'<label><span>单元</span><input name="bd_danyuans" type="text"  value="'+m.option["default"]["D"]+'" maxlength="2"/>个</label>'+'<label><span>住宅</span><input name="bd_floors" type="text"  value="'+m.option["default"]["L"]+'" maxlength="2"/>层</label>'+'<label><span>商铺</span><input name="bd_store" type="text" value="'+(m.option["default"]["S"]||0)+'"  maxlength="2"/>层</label>'+'<label><span>地下</span><input name="bd_ground" type="text" value="'+(m.option["default"]["G"]||0)+'"  maxlength="2"/>层</label>'+'<label><span>房间</span><input name="bd_rooms" type="text"  value="'+m.option["default"]["R"]+'"  maxlength="2"/>间</label>'+'<div class="cl"></div>'+"</div>"+'<a id="bd_confirm">确定</a><a id="bd_cancel" class="bd_cancle">取消</a></div>');var p=function(){x.del();x=null};x.find("#bd_confirm").bind("click",function(){b(x);p()});x.find("#bd_cancel").bind("click",p);return x}function b(D){var p=parseInt(D.find("[name=bd_danyuans]").val());var A=parseInt(D.find("[name=bd_floors]").val());var x=parseInt(D.find("[name=bd_rooms]").val());var E=parseInt(D.find("[name=bd_store]").val());var F=parseInt(D.find("[name=bd_ground]").val());if(isNaN(p)||isNaN(A)||isNaN(x)||isNaN(F)||isNaN(E)){return}p=p>0?p:1;D.find("[name=bd_danyuans]").val(p);A=A>0?A:1;D.find("[name=bd_floors]").val(A);x=x>0?x:1;D.find("[name=bd_rooms]").val(x);F=F<0?0:F;F=F<=A?F:A;D.find("[name=bd_ground]").val(F);E=E<0?0:E;E=E<=(A-F)?E:(A-F);D.find("[name=bd_store]").val(E);A+=E;A+=F;var I=[];var H=[];var B=[];for(var z=0;z<p;z++){var C=[];for(var y=0;y<A;y++){C.push(x)}I.push(C);H.push(F);B.push(E)}var G=r.creatNewBuildingData(I,H,B);r.creatBuilding(G,true);r.isNewBuilding=true}function w(){var x=$('<div class="bd_quentools bd_pannel">'+"<p>设置房间号排序规则</p>"+'<div class="bd_menupart"><h3>前缀</h3>'+'<label><span>住宅前缀</span><input name="bd_pre" type="text"  maxlength="5"  value="'+r.rules["pre"]+'"/></label>'+'<label><span>地下前缀</span><input name="bd_preunder" type="text"  maxlength="5"  value="'+r.rules["preunder"]+'"/></label>'+'<label><span>门市前缀</span><input name="bd_premenshi" type="text"  maxlength="5"  value="'+r.rules["premenshi"]+'"/></label>'+"</div>"+'<div class="bd_menupart"><h3>数字位数</h3>'+'<label><span>单元</span><input name="bd_partleng" type="text" maxlength="1" rel="num"  value="'+r.rules["partleng"]+'"/>位</label>'+'<label><span>楼层</span><input name="bd_floorleng" type="text"  maxlength="1"   rel="num"  value="'+r.rules["floorleng"]+'"/>位</label>'+'<label><span>房间</span><input name="bd_roomleng" type="text"  maxlength="1"  rel="num"  value="'+r.rules["roomleng"]+'"/>位</label>'+"</div>"+'<div class="bd_menupart noline"><h3>其他</h3>'+'<label><span>连字符</span><input name="bd_split" type="text"   maxlength="1" value="'+r.rules["split"]+'"/></label>'+'<label><span>后缀</span><input name="bd_end" type="text"  maxlength="5"  value="'+r.rules["end"]+'"/></label>'+'<label><input type="checkbox" name="bd_danyuan" />英文单元号</label>'+'<label><input type="checkbox" name="bd_singlequn" />商住分开排序</label>'+"</div>"+'<div class="cl"></div>'+'<a id="bd_quenconfirm" class="confirmbut">确定</a><a id="bd_quencancle"  class="bd_cancle">取消</a></div>');if(r.rules["danyuan"]=="english"){x.find("[name='bd_danyuan']").get(0).checked=true
}if(r.rules["singlequn"]==true){x.find("[name='bd_singlequn']").get(0).checked=true}var p=function(){x.del();x=null};x.find("#bd_quenconfirm").bind("click",function(){c(x);p()});x.find("#bd_quencancle").bind("click",p);return x}function c(z){for(var p in r.rules){var x=z.find("[name='bd_"+p+"']");var y=x.val();if(p=="danyuan"){if(z.find("[name='bd_danyuan']").get(0).checked){r.rules["danyuan"]="english"}else{r.rules["danyuan"]="num"}}else{if(p=="singlequn"){if(z.find("[name='bd_singlequn']").get(0).checked){r.rules["singlequn"]=true}else{r.rules["singlequn"]=false}}else{if(x.attr("rel")=="num"){if(!isNaN(y)){r.rules[p]=(y>1)?y:1}}else{r.rules[p]=y}}}}r.renameBuilding()}this.resize=function(){if(r){r.resize()}};this.destroy=function(){if(r){r.destroy();r=null}if(f){f.del();f=null}if(s){s.del();s=null}if(q){q.del();q=null}if(i){i.del();i=null}j.empty()}}function ZhuangHuTuEditor(a,b){var c=this;var d;var e={"title":"装户图","space":4,"roomwidth":72,"roomheight":48};this.zhtData;this.install=function(){$.extend(e,b||{});d=new buildingEditor(a);d.install(e);d.EDIT(true)};this.showInfo=function(f){d.showContInfo(f)};this.LOAD=function(f,g){var h=new AJAXObj();$(h).one("JSON_LOADED",function(j,i){c.zhtData=i["value"];c.setBDdata(i["value"]);$(c).trigger("DATA_LOADED",i)});h.POSTDATA(f,g||{})};this.setBDdata=function(f){var g=d.formatBuildingData(f);g?d.LOAD(g):null};this.getBuildingData=function(){return d.getBuildingData()};this.resize=function(){if(d){d.resize()}};this.destroy=function(){if(d){d.destroy();d=null}};this.install()}function ZhuangHuTuInsert(a,b){var c=this;var d;var e={"title":"装户图","space":4,"roomwidth":72,"roomheight":48};this.zhtData;this.install=function(){$.extend(e,b||{});d=new buildingEditor(a,e);d.install(e)};this.renderRoom=function(g,f){d.renderRoom(g,f)};this.showInfo=function(f){d.showContInfo(f)};this.LOAD=function(f,g){var h=new AJAXObj();$(h).one("JSON_LOADED",function(j,i){c.zhtData=i["value"];c.setBDdata(i["value"])});h.POSTDATA(f,g||{})};this.setBDdata=function(f){var g=d.formatBuildingData(f);g?d.LOAD(g,c.formatFun):null;$(c).trigger("DATA_LOADED")};this.resize=function(){if(d){d.resize()}};this.destroy=function(){if(d){$(d).unbind();d.destroy();d=null}};this.install()};