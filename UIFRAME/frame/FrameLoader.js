﻿var frameCSSConfig={mainframe:[{url:host+"style/cssfont/font-awesome.min.css",cache:!0},{url:framePos+"jPlayer/skin/blue.monday/jplayer.blue.monday.css",cache:!0},{url:framePos+"frame/mainframe"+(GLOBAL.style||"")+".css"}],login:[{url:framePos+"frame/login"+(GLOBAL.style||"")+".css"}]},frameJSConfig={mainframe:[{url:framePos+"Scripts/jquery-1.12.0.min.js",cache:!0},{url:framePos+"easyui/easyloader.js",cache:!0},{url:framePos+"frame/mainframe.js"}],login:[{url:framePos+"Scripts/jquery-1.12.0.min.js",
cache:!0},{url:framePos+"frame/login.js"}]},browserIsIE=0<navigator.userAgent.indexOf("MSIE")||0<navigator.userAgent.indexOf("Trident")||0<navigator.userAgent.indexOf("Edge"),browserIsIE6=0<navigator.userAgent.indexOf("MSIE 6"),browserIsIE7=0<navigator.userAgent.indexOf("MSIE 7"),browserIsIE8=0<navigator.userAgent.indexOf("MSIE 8"),browserIsIE9=0<navigator.userAgent.indexOf("MSIE 9"),browserIsIE10=0<navigator.userAgent.indexOf("MSIE 10"),browserIsIE11=0<navigator.userAgent.indexOf("Trident"),browserIsOpera=
0<navigator.userAgent.indexOf("Opera"),sUserAgent=navigator.userAgent,isWin32="Windows"==navigator.platform&&"Win32"==navigator.platform,isWinXP=-1<sUserAgent.indexOf("Windows NT 5.1")||-1<sUserAgent.indexOf("Windows XP"),isWin7=-1<sUserAgent.indexOf("Windows NT 6.1")||-1<sUserAgent.indexOf("Windows 7");if("undefined"==typeof cacheFrame)var cacheFrame=!0;var pageName="undefind"==typeof pageName?"mainframe":pageName?pageName:"mainframe";installUIMainFrameSet(pageName);
function installUIMainFrameSet(b){if(browserIsIE6||browserIsIE7||browserIsIE8){var c='<h1 style="text-align:center;line-height:32px;font-size:24px;font-weight: normal;margin-top:200px"><p>\u60a8\u4f7f\u7528\u7684\u6d4f\u89c8\u5668\u7248\u672c\u8fc7\u4f4e, \u8bf7\u4f7f\u7528IE8\u4ee5\u4e0a\u7248\u672c\u6216\u8c37\u6b4cChrome\u3001\u706b\u72d0FireFox\u6d4f\u89c8\u5668\u6216360SE\u6781\u901f\u6a21\u5f0f.</p><p>'+("undefined"!=typeof exploreDownLoadUrl?'<a href="'+exploreDownLoadUrl+'" target="_blank">\u70b9\u51fb\u8fd9\u91cc\u4e0b\u8f7d\u6d4f\u89c8\u5668</a>':
"")+"</p></h1>";try{setTimeout(function(){document.body.innerHTML=c},500)}catch(f){document.write(c)}}else{var a=document.createElement("meta");a.name="viewport";a.content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0";document.getElementsByTagName("head")[0].appendChild(a);if("object"==typeof pluginJS)for(a=0;a<pluginJS.length;a++)"string"==typeof pluginJS[a]?frameJSConfig.mainframe.push({url:pluginJS[a],compress:!1}):"object"==typeof pluginJS[a]&&
frameJSConfig.mainframe.push(pluginJS[a]);if("object"==typeof pluginCSS)for(a=0;a<pluginCSS.length;a++)"string"==typeof pluginCSS[a]?frameCSSConfig.mainframe.push({url:pluginCSS[a],compress:!1}):"object"==typeof pluginCSS[a]&&frameCSSConfig.mainframe.push(pluginCSS[a]);"undefined"==typeof compress&&(browserIsIE8&&frameCSSConfig.mainframe.push({url:framePos+"style/comstyleIE8.css"}),b="undefind"==typeof b?"mainframe":b?b:"mainframe",a=new loadFrame(function(){"login"==b&&(new loginPage).install()},
frameJSConfig[b||"mainframe"],frameCSSConfig[b]),a.installJS(),a.installStyle())}}
function loadFrame(b,c,a){function f(){e++;e==h?null!=b&&b():g.LOAD(c[e].url,f,c[e].cache)}var g=this,h=0,e=0;this.installJS=function(){h=c.length;e=0;g.LOAD(c[e].url,f,c[e].cache)};this.installStyle=function(){for(var b=0;b<a.length;b++)g.LOADCSS(a[b].url,a[b].cache)};this.LOADCSS=function(a,b){var c=document.createElement("link");c.type="text/css";c.rel="stylesheet";cacheFrame||b?c.href=a:(addstr=new Date,addstr=addstr.getTime(),c.href=a+"?i="+addstr);document.getElementsByTagName("head")[0].appendChild(c)};
this.LOAD=function(a,b,c){var d=document.createElement("script");d.type="text/javascript";null!=b&&(d.onload=d.onreadystatechange=function(){d.readyState&&"loaded"!=d.readyState&&"complete"!=d.readyState||(d.onreadystatechange=d.onload=null,b())});cacheFrame||c?d.src=a:(addstr=new Date,addstr=addstr.getTime(),d.src=a+"?i="+addstr);document.getElementsByTagName("head")[0].appendChild(d)}};