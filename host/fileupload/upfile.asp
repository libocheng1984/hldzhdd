<!--#include file="../admin_inc/conndbt.asp" -->
<%
if request.QueryString("from")="swf" then 'flash�ϴ����
  if request.servervariables("REQUEST_METHOD")="POST" then '��ǰΪpost
  	PicName=request.QueryString("n")
	if PicName="" then
		randomize
		p = Int((8999 * Rnd) + 1000)
		PicName = "up" & FormatDateTime(Now()) & p
		PicName = replace(PicName, ":", "")
		PicName = replace(PicName, "-", "")
		PicName = replace(PicName, "/", "")
		PicName = replace(PicName, " ", "")
		PicName=PicName&".jpg"
	end if		
	 FormSize=Request.TotalBytes 
	 FormData=Request.BinaryRead(FormSize) '�������ݵ�FormData
	 set dr=CreateObject("Adodb.Stream")
	 dr.Mode=3
	 dr.Type=1
	 dr.Open
	 dr.Write(FormData) 
	 if request.QueryString("p")="small" then
	 	uppath=PicPath&subpath
	 else
	 	uppath=PicPath
	 end if
	 dr.SaveToFile Server.MapPath(uppath&PicName),2
	 '��url����name���������ļ�������
	 dr.Close
	 set dr=nothing
	 response.write(PicName)'�����ļ���
  end if
end if
%>