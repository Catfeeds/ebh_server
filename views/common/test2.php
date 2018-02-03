<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>测试</title>
<script type="text/javascript" src="http://static.ebanhui.com/ebh/js/jquery.js"></script>
<script type="text/javascript">
function test() {
<<<<<<< .mine
	var domain='http://i.ebanhui.com/';
=======
	var domain='http://i.ebh.net/';
>>>>>>> .r20
	var url = "";
	if($("#rdologin").attr("checked")==true) {
		url = domain+"login";
	} else if($("#rdoroom").attr("checked")==true) {
		url = domain+"room";
	} else if($("#rdoreg").attr("checked")==true) {
		url = domain+"reg";
	} else if($("#rdofolder").attr("checked")==true) {
		url = domain+"folder";
	} else if($("#rdoclist").attr("checked")==true) {
		url = domain+"clist";
	} else if($("#rdocourse").attr("checked")==true) {
		url = domain+"course";
	} else if($("#rdoinfo").attr("checked")==true) {
		url = domain+"info";
	} else if($("#rdoexam").attr("checked")==true) {
		url = domain+"exam";
	} else if($("#rdoask").attr("checked")==true) {
		url = domain+"ask";
	} else if($("#rdoaskdetail").attr("checked")==true) {
		url = domain+"ask/detail";
	} else if($("#rdonotice").attr("checked")==true) {
		url = domain+"notice";
	} else if($("#rdonoticedetail").attr("checked")==true) {
		url = domain+"notice/detail";
	} else if($("#rdoreview").attr("checked")==true) {
		url = domain+"review";
	} else if($("#rdoreviewdel").attr("checked")==true) {
		url = domain+"review/del";
	} else if($("#rdocalendar").attr("checked")==true) {
		url = domain+"calendar";
	} else if($("#rdocount").attr("checked")==true) {
		url = domain+"count";
	} else if($("#rdoupsetting").attr("checked")==true) {
		url = domain+"upsetting";
	} else if($("#rdoreviewadd").attr("checked")==true) {
		url = domain+"review/add";
	} else if($("#rdostudylog").attr("checked")==true) {
		url = domain+"studylog";
	} else if($("#forgetpwd").attr("checked")==true) {
		url = domain+"forget";
	} else if($("#userstate").attr("checked")==true) {
		url = domain+"userstate";
	}

	$.ajax(
	{
		type: "POST",
		url:url,
		data: $("#myform").serialize(),
		dataType :"json",
		success: function(msg){
		 alert( msg);
	   }
	}
	);
}
</script>
</head>
<body>
<input type="radio" id="rdologin" name="action"/>login
<input type="radio" id="rdoreg" name="action" />reg
<input type="radio" id="rdoroom" name="action" />room
<input type="radio" id="rdofolder" name="action" />folder
<input type="radio" id="rdoclist" name="action" />clist
<input type="radio" id="rdocourse" name="action" />course
<input type="radio" id="rdoinfo" name="action" />info
<input type="radio" id="rdoexam" name="action" />exam
<input type="radio" id="rdoask" name="action" />ask
<input type="radio" id="rdoaskdetail" name="action" />askdetail
<input type="radio" id="rdonotice" name="action" />notice
<input type="radio" id="rdonoticedetail" name="action" />noticedetail
<input type="radio" id="rdoreview" name="action" />我的评论 
<input type="radio" id="rdoreviewadd" name="action" />添加评论
<input type="radio" id="rdoreviewdel" name="action" />删除评论
<input type="radio" id="rdocalendar" name="action" />学习课表
<input type="radio" id="rdocount" name="action" />count
<input type="radio" id="rdoupsetting" name="action" />upsetting
<input type="radio" id="rdostudylog" name="action" />提交学习时间
<input type="radio" id="forgetpwd" name="action" />忘记密码
<input type="radio" id="userstate" name="action" />获取记录数





<br /><br />
<form id="myform">
user:<input type="text" name="user" value="xs00045"/><br />
passwd:<input type="text" name="passwd" value="123456"/><br />
email:<input type="text" name="email" value=""/><br />
version:<input type="text" name="version" value="1.0.0.1"/><br />
from:<input type="text" name="from" value="2"/><br />
k<input type="text" name="k" value="" style="width:800px;"/><br />
rid<input type="text" name="rid" value="10194"/><br />
fid<input type="text" name="fid" value=""/><br />
cwid<input type="text" name="id" value=""/><br />
type<input type="text" name="type" value=""/><br />
page<input type="text" name="page" value=""/><br />
qid<input type="text" name="qid" value=""/><br />
lid<input type="text" name="lid" value=""/><br />
begindate<input type="text" name="begindate" value=""/><br />
enddate<input type="text" name="enddate" value=""/><br />
oldpass<input type="text" name="oldpass" value=""/><br />
newpass<input type="text" name="newpass" value=""/><br />
nickname<input type="text" name="nickname" value=""/><br />
sex<input type="text" name="sex" value=""/><br />
birthday<input type="text" name="birthday" value=""/><br />
address<input type="text" name="address" value=""/><br />
mobile<input type="text" name="mobile" value=""/><br />
cid<input type="text" name="cid" value=""/><br />
message<input type="text" name="message" value=""/><br />
ctime<input type="text" name="ctime" value=""/><br />
ltime<input type="text" name="ltime" value=""/><br />
typeid<input type="text" name="type" value=""/><br />



</form>
<br /><br />
<input type="button" value="test" onclick="test()" />
</body>
</html>