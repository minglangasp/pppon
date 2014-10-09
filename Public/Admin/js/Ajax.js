function CheckName() {
    var laoy8 = UserReg.UserName.value.toLowerCase();
	if(document.UserReg.UserName.value.length<2)
	{
		enter_name.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<font color='#FF0000'>?????????С??2?????</font>";
	return false;
	}
	else if(document.UserReg.UserName.value.length>8)
	{
		enter_name.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<font color='#FF0000'>????????????8?????</font>";
	return false;
	}
	else if (!/[\u4e00-\u9fa5]/g.test(document.UserReg.UserName.value) && document.UserReg.UserName.value.length<4)
	{
		enter_name.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<font color='#FF0000'>????????????????4?????</font>";
	return false;
	}
	else if (!/[\u4e00-\u9fa5]/g.test(document.UserReg.UserName.value) && document.UserReg.UserName.value.length>15)
	{
		enter_name.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<font color='#FF0000'>???????????????15?????</font>";
	return false;
	}
	else{
		enter_name.innerHTML = "&nbsp;<font color='#008000'>?????...</font>";
		var oBao = new ActiveXObject("Microsoft.XMLHTTP");
		oBao.open("post","CheckRegName.asp?UserName=" + laoy8,false);
		oBao.send();
		var text = unescape(oBao.responseText);
		if(text=="WordErr")
		{
			enter_name.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<font color='#FF0000'>?????????????к??б????????????</font>";
			return false;
		}
		if(text=="True")
		{
			enter_name.innerHTML = "<img src='../images/check_right.gif' height='13' width='13'>&nbsp;<font color='#2F5FA1'>???????</font>";
			return true;
		}
		else{
			enter_name.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<font color='#FF0000'>????????????</font>";
		}
	}
}
function UserPassword_enter()
{
	if(document.UserReg.UserPassword.value.length<1)
	{
		enter_pwd.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<font color='#FF0000'>????????????д</font>";
	}
	if(document.UserReg.UserPassword.value.length<5)
	{
		enter_pwd.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<font color='#FF0000'>???????????????λ</font>";
	}
	if(document.UserReg.UserPassword.value.length>5)
	{
		enter_pwd.innerHTML = "<img src='../images/check_right.gif' height='13' width='13'>&nbsp;<font color='#2F5FA1'>???????</font>";
	}
}
function PwdConfirm_enter()
{
	if(document.UserReg.PwdConfirm.value != document.UserReg.UserPassword.value)
	{
		enter_repwd.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<font color='#FF0000'>???????????????</font>";
	}
	else
	{
		if (document.UserReg.PwdConfirm.value.length<1)
		{
			enter_repwd.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<font color='#FF0000'>????????????</font>";
		}
		else
		{
			enter_repwd.innerHTML = "<img src='../images/check_right.gif' height='13' width='13'>&nbsp;<font color='#2F5FA1'>???????</font>";
		}
	}
}
function UserEmail_enter()
{
	mail_text = document.UserReg.UserEmail.value
	var myreg = /^([a-zA-Z0-9]+[_|\_|\.|-]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	if(!myreg.test(mail_text))
	{
		enter_mail.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<font color='#FF0000'>???????????????</font>";
	}
	else
	{
		enter_mail.innerHTML = "<img src='../images/check_right.gif' height='13' width='13'>&nbsp;<font color='#2F5FA1'>???????</font>";
	}

}
function UserQQ_enter()
{
	if(document.UserReg.UserQQ.value.length<5 || document.UserReg.UserQQ.value.match(/[^0-9]/))
	{
		enter_qq.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<font color='#FF0000'>QQ???????????</font>";
	}
	else
	{
		enter_qq.innerHTML = "<img src='../images/check_right.gif' height='13' width='13'>&nbsp;<font color='#2F5FA1'>???????</font>";
	}

}

function user_from()
{
	if(!/[\u4e00-\u9fa5]/g.test(document.UserReg.UserName.value) && document.UserReg.UserName.value.length<4)
	{
		enter_name.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<strong><font color='#ff0000'>????????4-15???,?????????2-8???</font></strong>";
		document.UserReg.UserName.focus();
		return false;
	}
	if(document.UserReg.UserPassword.value.length<1)
	{
		enter_pwd.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<strong><font color='#ff0000'>????????????д</font></strong>";
		document.UserReg.UserPassword.focus();
		return false;
	}
	if(document.UserReg.UserPassword.value.length<5)
	{
		enter_pwd.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<strong><font color='#ff0000'>???????????????λ</font></strong>";
		document.UserReg.UserPassword.focus();
		return false;
	}
	if(document.UserReg.PwdConfirm.value != document.UserReg.UserPassword.value)
	{
		enter_repwd.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<strong><font color='#ff0000'>???????????????</font></strong>";
		document.UserReg.PwdConfirm.focus();
		return false;
	}
	mail_text = document.UserReg.UserEmail.value
	var myreg = /^([a-zA-Z0-9]+[_|\_|\.|-]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	if(!myreg.test(mail_text))
	{
		enter_mail.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<strong><font color='#ff0000'>???????????????</font></strong>";
		document.UserReg.UserEmail.focus();
		return false;
	}
	if(document.UserReg.UserQQ.value.length<5 || document.UserReg.UserQQ.value.match(/[^0-9]/))
	{
		enter_qq.innerHTML = "<img src='../images/check_err.gif' height='13' width='13'>&nbsp;<strong><font color='#ff0000'>QQ???????????</font></strong>";
		document.UserReg.UserQQ.focus();
		return false;
	}
return true;
}
function writeOption(varFrom,varTo)
{
	for(var i=varFrom;i<=varTo;i++)
	{
		document.write("<option VALUE="+i+">"+i+"</option>");
	}
}
