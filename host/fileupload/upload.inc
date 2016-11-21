<%
dim oupfilestream

Class upload_file
  
dim objForm,objFile

Public function Form(strForm)
   strForm=lcase(strForm)
   if not objForm.exists(strForm) then
     Form=""
   else
     Form=objForm(strForm)
   end if
 end function

Public function File(strFile)
   strFile=lcase(strFile)
   if not objFile.exists(strFile) then
     set File=new FileInfo
   else
     set File=objFile(strFile)
   end if
 end function


Private Sub Class_Initialize 
  dim RequestData,sStart,vbCrlf,sInfo,iInfoStart,iInfoEnd,tStream,iStart,theFile
  dim iFileSize,sFilePath,sFileType,sFormValue,sFileName
  dim iFindStart,iFindEnd
  dim iFormStart,iFormEnd,sFormName
  if Request.TotalBytes<1 then Exit Sub
  set objForm=Server.CreateObject("Scripting.Dictionary")
  set objFile=Server.CreateObject("Scripting.Dictionary")
  set tStream = Server.CreateObject("adodb.stream")
  set oupfilestream = Server.CreateObject("adodb.stream")
  oupfilestream.Type = 1
  oupfilestream.Mode =3
  oupfilestream.Open
  oupfilestream.Write  Request.BinaryRead(Request.TotalBytes)
  oupfilestream.Position=0
  RequestData =oupfilestream.Read 
  iFormEnd = oupfilestream.Size
    vbCrlf = chrB(13) & chrB(10)

    sStart = MidB(RequestData,1, InStrB(1,RequestData,vbCrLf)-1)
  iStart = LenB (sStart)
  iFormStart=iStart+2

 
Do

	iInfoEnd = InStrB(iFormStart,RequestData,vbCrlf & vbCrlf)+3
	tStream.Type = 1
	tStream.Mode =3
	tStream.Open
	oupfilestream.Position = iFormStart
	oupfilestream.CopyTo tStream,iInfoEnd-iFormStart
	tStream.Position = 0
	tStream.Type = 2
	tStream.Charset ="utf-8"
	sInfo = tStream.ReadText

	iFormStart = InStrB(iInfoEnd,RequestData,sStart)-1
	iFindStart = InStr(22,sInfo,"name=""",1)+6
	iFindEnd = InStr(iFindStart,sInfo,"""",1)
	sFormName = lcase(Mid (sinfo,iFindStart,iFindEnd-iFindStart))

	if InStr (45,sInfo,"filename=""",1) > 0 then
		set theFile=new FileInfo

		iFindStart = InStr(iFindEnd,sInfo,"filename=""",1)+10
		iFindEnd = InStr(iFindStart,sInfo,"""",1)
		sFileName = Mid (sinfo,iFindStart,iFindEnd-iFindStart)
		theFile.FileName=getFileName(sFileName)
		theFile.FilePath=getFilePath(sFileName)
		theFile.FileExt=getFileExt(sFileName)
		iFindStart = InStr(iFindEnd,sInfo,"Content-Type: ",1)+14
		iFindEnd = InStr(iFindStart,sInfo,vbCr)
		theFile.FileType =Mid (sinfo,iFindStart,iFindEnd-iFindStart)
		theFile.FileStart =iInfoEnd
		theFile.FileSize = iFormStart -iInfoEnd -2
		theFile.FormName=sFormName
		if not objFile.Exists(sFormName) then
		 objFile.add sFormName,theFile
		 end if
	else

	tStream.Close
		tStream.Type =1
		tStream.Mode =3
		tStream.Open
		oupfilestream.Position = iInfoEnd 
		oupfilestream.CopyTo tStream,iFormStart-iInfoEnd-2
		tStream.Position = 0
		tStream.Type = 2
		tStream.Charset ="utf-8"
	        sFormValue = tStream.ReadText 
		if objForm.Exists(sFormName) then
		  objForm(sFormName)=objForm(sFormName)&", "&sFormValue		  
		else
		  objForm.Add sFormName,sFormValue
		end if
		
	end if
	tStream.Close
	iFormStart = iFormStart+iStart+2
loop until (iFormStart+2) = iFormEnd 
  RequestData=""
  set tStream =nothing
End Sub

Private Sub Class_Terminate  
if Err < 0 then

	objForm.RemoveAll
	objFile.RemoveAll
	set objForm=nothing
	set objFile=nothing
	oupfilestream.Close
	set oupfilestream =nothing
 end if
End Sub
   

 Private function GetFilePath(FullPath)
  If FullPath <> "" Then
   GetFilePath = left(FullPath,InStrRev(FullPath,"\"))
  Else
   GetFilePath = ""
  End If
 end function
 
Private function GetFileName(FullPath)
If FullPath <> "" Then
GetFileName = mid(FullPath,InStrRev(FullPath, "\")+1)
Else
GetFileName = ""
End If
end function
 

Private function GetFileExt(FullPath)
If FullPath <> "" Then
GetFileExt = mid(FullPath,InStrRev(FullPath, ".")+1)
Else
GetFileExt = ""
End If
end function

End Class

Class FileInfo
  dim FormName,FileName,FilePath,FileSize,FileType,FileStart,FileExt
  Private Sub Class_Initialize 
  FileName = ""
  FilePath = ""
  FileSize = 0
  FileStart= 0
  FormName = ""
  FileType = ""
  FileExt = ""
  End Sub
  
 Public function SaveAs(FullPath)
    dim dr,ErrorChar,i
    SaveAs=true
    if trim(fullpath)="" or FileStart=0 or FileName="" or right(fullpath,1)="/" then exit function
    set dr=CreateObject("Adodb.Stream")
    dr.Mode=3
    dr.Type=1
    dr.Open
    oupfilestream.position=FileStart
    oupfilestream.copyto dr,FileSize
    dr.SaveToFile FullPath,2
    dr.Close
    set dr=nothing 
    SaveAs=false
  end function
End Class
%>