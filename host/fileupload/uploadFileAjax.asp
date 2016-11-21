<!--#include file="upload.inc"-->
<%
Upfile_MAX_SIZE=1024000
FilePath="../upload/"
Upfile_TYPE_WHTIE_LIST="jpg,jpeg,png,bmp,gif"
UpPHOTO_TYPE_BLACK_LIST="exe,asp,asc,php,aspx,com,dll"

Dim Msg,erre,FileTypeIcon,upok,PicName
Msg=""
erre=0
code=1

'upload start
oldname=request.QueryString("na")
upobj=request.QueryString("id")
fileLocalname=""

Set UploadRequest = new upload_file
Set file = UploadRequest.file(upobj)
fileLocalname=file.FileName
if oldname<>"" then
	PicName = oldname
else
	If file.FileSize > 0 Then
		PicName = "file" & FormatDateTime(Now())
		PicName = replace(PicName, ":", "")
		PicName = replace(PicName, "-", "")
		PicName = replace(PicName, "/", "")
		PicName = replace(PicName, " ", "")
		PicName=PicName&"."&lcase(file.FileExt)
		erre=SaveFile(PicName)
		set UploadRequest=nothing
		if erre=1 then		
			code=0	
			Msg="文件不能大于"&Upfile_MAX_SIZE&"K!"
			PicName=""
		elseif erre=2 then
			code=0	
			Msg="文件格式不正确！"
			PicName=""
		end if
	else
		code=0
		Msg="没有选择图片!"
	end if			
end if
%>
{
     "root":{
         "head":{"code":"<%=code%>","message":"<%=Msg%>"},
         "pageID":"",
         "flag":"upload",
         "filename":"<%=PicName%>",
         "title":"<%=PicName%>"
     }
 }
<%
Function SaveFile(PName)
	If file.FileSize > Upfile_MAX_SIZE * 1024 Then 
	SaveFile=1
	Exit Function
	End If
	
	isFileTypeSafe = false
	fileExt = lcase(file.FileExt)
	FileTypeWhiteList = split(Upfile_TYPE_WHTIE_LIST, ",")
	For i = 0 to ubound(FileTypeWhiteList)
	If fileExt = lcase(FileTypeWhiteList(i)) Then
	isFileTypeSafe = true
	Exit For
	End If
	Next	
	
	If isFileTypeSafe Then
	FileTypeBlackList = split(UpPHOTO_TYPE_BLACK_LIST, ",")
	For i = 0 to ubound(FileTypeBlackList)
	If fileExt = lcase(FileTypeBlackList(i)) Then
	isFileTypeSafe = false
	Exit For
	End If
	Next
	End If
	
	If Not isFileTypeSafe Then
	SaveFile=2 
	Exit Function
	End If
	file.SaveAs Server.mappath(FilePath&PicName)
	
	SaveFile=0
end function
%>

