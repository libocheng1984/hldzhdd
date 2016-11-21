<!--#include file="../admin_inc/conndbt.asp" -->
<%
vars=trim(request.Form("did"))
vara=split(vars,"_")
filename=vara(0)
tid=vara(1)
if filename<>"" then
	call delfile(FilePath&filename)
	if tid<>"" then
		set rs=server.createobject("adodb.recordset")
		sql="select t_id,t_addfiles from news  where t_id="&tid
		rs.open sql,rsconn,1,3
		if not rs.eof then
			t_addfiles=replace(rs("t_addfiles"),filename,"")
			rs("t_addfiles")=t_addfiles
			rs.update			
		end if
		rs.close
		set rs=nothing		
	end if
end if
%>
