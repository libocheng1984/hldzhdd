var projectName="MAPZDPT";//本系统识别标识
var projectTitle="葫芦岛市智能指挥调度系统";//本系统名称
var projectVer="Ver 1.0.1";//系统版本
var projectCopyRight="[普康迪 技术支持]";//版权设置
var framePos="UIFRAME/";//框架服务地址

var SingleLoginUrl="login.html"; //登录入口页面
var loginPort="php/system/Login.php";//登录验证接口
//var MsgPushPort="ws://192.168.25.29:8001/jms";//消息推送接口
var useCache=false;//是否使用缓存html / js
var offlinePort="php/system/UpdateSession.php";//离线状态更新接受接口
var swfPath = null;
//var host = "/zhdd/";
var loginAddinfo="";                                                           //登录附加版权信息
var globalPort=null;                                       //获取全局初始化数据接口
var DEBUG=true;                                                                //调试状态开关
//全局参数--------
var host="Resouce/";                                                         //本地资源文件位置 
var swfPath=host;                                                             //flash文件的位置
var useCache=!DEBUG;                                                    //true;//false;//是否使用缓存html 
var cacheFrame=!DEBUG;                                                //true;//false;//是否使用缓存框架 js
var msgSound=framePos+"frame/msg.mp3";                 //消息提示音
var powerDataFlag="allow";                                            //校验权限标记和数据id,没有不校验
var keepFormBlank=true;                                               //保留提交表单的空值，铁路用



//插件扩展---------
var pluginJS=[
	 host+"paibanbiao.js",
	 {"url":framePos+"webChat/webChat.js","cache":true}, //微信
];//附加的js插件url
var pluginCSS=[
	"style/style.css",
	host+"paibanbiao.css",
	framePos+"style/zhzxSpacile.css" 
	];//附加的css文件url
//全局参数--------
//var GLOBAL={"windis":{"topdis":0,"leftdis":0,"rightdis":0,"bottomdis":10}}
var GLOBAL={"windis":{"topdis":50,"leftdis":0,"rightdis":0,"bottomdis":6},"style":0,"wallpaper":null};					
//框架加载完成后自动执行此方法----------
function FrameLoaded(){
	//需要在框架加载后处理的内容---
}

var script=document.createElement('script');
script.type='text/javascript';
script.src = framePos+"frame/FrameLoader.js";
document.getElementsByTagName('head')[0].appendChild(script);	
function getHostUrl(a) {
    switch (a) {
		case "delfile":
			return "host/delfile.asp";
			break
		case "uploadpic":
			return "host/entry_uploadpic.asp";
			break
		case "uploadfile":
			return "host/entry_uploadpic.asp";
			break
    }
}

