		var IsD = false;
		var Temptop = 0;
		var MinTop = 0;
		var MaxTop = 0;
		var MaxScroll = 0;
		var MouseLen = 0;
		var Distance = 0;
		var IsDate = false;
		var ThFlChinHei="100%";
		var DAppW="",DAppH="",DAppT="",DAppR="",DAppB="",DAppL="",SingDiv=null;LST="",WBMes="";//zujian

		function SettingDiv()	//zujian		
		{
			if (document.body.scrollTop==LST)
				return;
			LST = document.body.scrollTop;
			if (document.body.scrollHeight-document.body.clientHeight>0)
				$("DivflashAppClose").style.right="24px";
			else
				$("DivflashAppClose").style.right="20px";
			$("DivflashAppClose").style.top=document.body.scrollTop+16+"px";
			$("DivflashApp").style.top=document.body.scrollTop+DAppT+"px";			
			$("DivflashAppFull").style.top=document.body.scrollTop+"px";						
		}
	
		function HideDiv()	//zujian		
		{
			window.clearInterval(SingDiv);
		}	

		function MyLoadSwf(w,h,swfsrc)	//zujian		
		{
			if (w=="")
				w="100%";
				
			if (h=="")
				h="100%";
			
			var tswmode="transparent";
			//if (swfsrc.lastIndexOf("g013")>-1)
			//	tswmode="window";
	
			return AC_FL_RunContent_My(
				'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0',
				'width', w,
				'height', h,
				'quality', 'high',
				'pluginspage', 'http://get.adobe.com/flashplayer',
				'id', 'flashapp',
				'wmode',tswmode,
				'allowFullScreen','true',
				'allowScriptAccess','always',
				'swLiveConnect','true',
				'src', swfsrc,
				'movie', swfsrc);		
		}    
		
		function LoadDivAppHide()	//zujian
		{
			$("DivflashAppFull").style.display = "none";
			$("DivflashAppClose").style.display = "none";

			if ($("DFull")!=null)
				$("DFull").style.display = "none";

			if ($("DInfo")!=null)
				$("DInfo").style.display = "none";		

			if ($("liinfo")!=null)
				$('liinfo').innerHTML = "";

			if ($("DivflashApp")!=null)
				$("DivflashApp").style.display = "none";
			if ($("DivflashApp")!=null) $("DivflashApp").innerHTML="";
			HideDiv();
				
		}
		function LoadDivAppShow(TMes)	//zujian
		{	
			$("DivflashAppFull").style.zIndex=1;
			$("DivflashApp").style.zIndex=2;
			$("DivflashAppClose").style.zIndex=3;
			WBMes = "";
			if (TMes!="")
			{
				$("DivflashApp").style.height = document.body.clientHeight-102+"px";
				SetDivAppVar(OtherMesGetVal(TMes,"width:"),
							OtherMesGetVal(TMes,"height:"),
							OtherMesGetVal(TMes,"top:"),
							OtherMesGetVal(TMes,"right:"),
							OtherMesGetVal(TMes,"bottom:"),
							OtherMesGetVal(TMes,"left:"));
				if (OtherMesGetVal(TMes,"isswf:")=="true")
				{
					var sw = OtherMesGetVal(TMes,"sw:")+"px";
					var sh = OtherMesGetVal(TMes,"sh:")+"px";
					if (sw=="px")	sw="100%";
					if (sh=="px")	sh="100%";
					$("DivflashApp").innerHTML = "<table cellpadding=0 cellspacing=0 style=\"width:100%;height:100%;\"><tr><td align=center><table cellpadding=0 cellspacing=0 style=\"width:"+sw+";height:"+sh+";\"><tr><td>"+MyLoadSwf("","",OtherMesGetVal(TMes,"swfsrc:"));+"</td></tr></table></td></tr></table>"
				}
				else
				{
					if (OtherMesGetVal(TMes,"iswb:")=="true"||OtherMesGetVal(TMes,"iswbtx:")=="true"||OtherMesGetVal(TMes,"isshop:")=="true")
					{
						$("DivflashAppFull").style.zIndex=901;
						$("DivflashApp").style.zIndex=902;
						$("DivflashAppClose").style.zIndex=903;
						var twb = OtherMesGetVal(TMes,"wbid:");

						if (OtherMesGetVal(TMes,"iswbtx:")=="true")
							twb = "<iframe frameborder=\"0\" scrolling=no src=\"http://show.v.t.qq.com/index.php?c=show&a=index&n="+twb+"&w=0&h=$h$&fl=0&l=0&o=17&co=4&cs=000000_FFFFFF_555555_BBBBBB\" width=\"100%\" height=\"100%\"></iframe>";
						if (OtherMesGetVal(TMes,"iswb:")=="true")
						{
							twb = "http://service.weibo.com/staticjs/weiboshow?verifier=570da6d5&uid="+twb+"&noborder=0&width=$w$&height=$h$&fansRow=1&isTitle=0&isWeibo=1&isFans=1&noborder=0&ptype=1&colors=cfe1f3,ffffff,555555,5093d5";
							twb = MyLoadSwf("","",twb);
						}
						if (OtherMesGetVal(TMes,"isshop:")=="true")
							twb = "<iframe frameborder=\"0\" scrolling=auto src=\""+twb+"\" width=\"100%\" height=\"100%\"></iframe>";
						WBMes = twb;
						if (DAppW!="" && DAppW!="0")
							twb = twb.replace("$w$",document.body.clientWidth-parseInt(DAppW)+1);
						else
							twb = twb.replace("$w$",document.body.clientWidth+1);
						if (DAppH!="" && DAppH!="0")
						{
							if (twb.lastIndexOf("<iframe")<0)
								twb = twb.replace("$h$",document.body.clientHeight-parseInt(DAppH)+1);
							else
								twb = twb.replace("$h$",document.body.clientHeight-parseInt(DAppH)-11);
						}
						else
						{
							if (twb.lastIndexOf("<iframe")<0)
								twb = twb.replace("$h$",document.body.clientHeight+1);
							else
								twb = twb.replace("$h$",document.body.clientHeight-11);
						}

						$("DivflashApp").innerHTML = "<table cellpadding=0 cellspacing=0 style=\"width:100%;height:100%;\"><tr><td align=center><table cellpadding=0 cellspacing=0 style=\"width:100%;height:100%;\"><tr><td>"+twb+"</td></tr></table></td></tr></table>";			
					}
					else
						$("DivflashApp").innerHTML = MyLoadSwf("","",OtherMesGetVal(TMes,"swfsrc:"));
				}	
			}
			
			if (OtherMesGetVal(TMes,"fullscreen:")=="true")
			{
				$("DivflashAppFull").style.zIndex=901;
				$("DivflashApp").style.zIndex=902;
				$("DivflashAppClose").style.zIndex=903;
				$("DivflashAppFull").style.display = "block";
				$("DivflashAppClose").style.display = "block";
				SingDiv=window.setInterval("SettingDiv()",100);
			}				
			
			$("DivflashApp").style.display = "";
		}
		
		function SetDivAppVar(W,H,T,R,B,L)	//zujian
		{	
			DAppW=W;
			DAppH=H;
			DAppT=T;
			DAppR=R;
			DAppB=B;
			DAppL=L;
			LoadDivAppWZ();
		}
		
		function LoadDivAppWZ()	//zujian
		{	
			if ($("DivflashApp")==null)
				return;
			$("DivflashApp").style.top ="0px";
			$("DivflashApp").style.left ="0px";
			if (DAppW!="" && DAppW!="0")
				$("DivflashApp").style.width = document.body.clientWidth-parseInt(DAppW)+"px";
			else
				$("DivflashApp").style.width = "100%";
				
			if (DAppH!="" && DAppH!="0")
				$("DivflashApp").style.height = document.body.clientHeight-parseInt(DAppH)+"px";
			else
				$("DivflashApp").style.height = "100%";
			
			if (DAppT!="" && DAppT!="0")	
				$("DivflashApp").style.top = DAppT+"px";
			if (DAppR!="" && DAppR!="0")	
				$("DivflashApp").style.right = DAppR+"px";
			if (DAppB!="" && DAppB!="0")	
				$("DivflashApp").style.bottom = DAppB+"px";
			if (DAppL!="" && DAppL!="0")	
				$("DivflashApp").style.left = DAppL+"px";
		}
		
		function OtherMesGetVal(Mes,type)	//zujian
		{
			var TM = Mes;
			var TV = "";
			if (type=="swfsrc:")
			{
				if (TM.lastIndexOf(type)<0)
				{
					var w = TM.split(type);

					w = w[0].split("|");
					return w[0];		
				}
			}
			if (TM.lastIndexOf(type)>-1)
			{
				var w = TM.split(type);
				if (w.length>1)
				{
					w = w[1].split("|");
					TV=w[0];
				}						
			}
			return TV;
		}

		function LoaTheH(MinH,ThT)
		{
			if (parseInt(screen.height)<MinH)
			{
				var temp_h1 = document.body.clientHeight;
				var temp_h2 = document.documentElement.clientHeight;
				var isXhtml = (temp_h2<=temp_h1&&temp_h2!=0)?true:false; 
				var htmlbody = isXhtml?document.documentElement:document.body;
				htmlbody.style.overflowY = "auto";
				ThFlChinHei= ThT;
			}
		}
		function $(KJID) { return document.getElementById(KJID); }
		
		function Point(iX, iY){ this.x = iX; this.y = iY; }
		
		function fGetXY(aTag)
		{
			if (aTag==null) return;
			var oTmp = aTag;
			var pt = new Point(0,0);
			do 
			{
  				pt.x += oTmp.offsetLeft; pt.y += oTmp.offsetTop; oTmp = oTmp.offsetParent;
			} 
			while(oTmp.tagName!="BODY");
			return pt;
		}
		
		function Changing(fdiv,tdiv)
		{
			LoadDivAppWZ();	
			var windowWidth = document.body.clientWidth | document.documentElement.clientWidth;
			var windowHeight = document.body.clientHeight;
			var windowTop = document.documentElement.scrollTop;
			if (windowHeight-40>300)
			{
				$(tdiv).style.height = windowHeight-40+"px";
				$("liinfo").style.height = windowHeight-40-68+"px";
				$("liScrollbar").style.height = windowHeight-40-68+"px";
			}
			var TTop = parseInt($(tdiv).offsetHeight);
			$(tdiv).style.top = windowTop+(windowHeight/2)-TTop/2+parseInt(Distance)+"px";
			$(tdiv).style.left = (windowWidth/2)-parseInt($(tdiv).offsetWidth)/2;
			
			if (parseInt(windowHeight)<parseInt(document.body.clientHeight))
				windowHeight = document.body.clientHeight;
			var tsh = document.body.scrollHeight | document.documentElement.scrollHeight;
			if (parseInt(windowHeight)<parseInt(tsh))
				windowHeight = tsh;
			$(fdiv).style.height = windowHeight+"px";
			$(fdiv).style.width = windowWidth+"px";
			
			$('liinfo').scrollTop = 0;
			$("ScrollbarHandle").style.top = "0px";
			
			if ($('liinfo').offsetHeight>$('liinfo').scrollHeight)
				$("liScrollbar").style.display = "none";
			else
				$("liScrollbar").style.display = "block";
				
			LoadScroll();

			if (WBMes!="")
			{
				twb=WBMes;
				if (DAppW!="" && DAppW!="0")
					twb = twb.replace("$w$",document.body.clientWidth-parseInt(DAppW)+1);
				else
					twb = twb.replace("$w$",document.body.clientWidth+1);
				if (DAppH!="" && DAppH!="0")
				{
					if (twb.lastIndexOf("<iframe")<0)
						twb = twb.replace("$h$",document.body.clientHeight-parseInt(DAppH)+1);
					else
						twb = twb.replace("$h$",document.body.clientHeight-parseInt(DAppH)-11);
				}
				else
				{
					if (twb.lastIndexOf("<iframe")<0)
						twb = twb.replace("$h$",document.body.clientHeight+1);
					else
						twb = twb.replace("$h$",document.body.clientHeight-11);
				}
				$("DivflashApp").innerHTML = "<table cellpadding=0 cellspacing=0 style=\"width:100%;height:100%;\"><tr><td align=center><table cellpadding=0 cellspacing=0 style=\"width:100%;height:100%;\"><tr><td>"+twb+"</td></tr></table></td></tr></table>";			
			}
		}
		
		function LoadDivWZ(fdiv,tdiv,title,date,mes)
		{
			if (date=="-" || date=="")
			{
				IsDate = false;
				$('lititle').innerHTML = title;
				if (date=="-")
					MusicStop();
			}
			else
			{
				IsDate = true;
				if (date=="--")
					$('lititle').innerHTML = title;
				else
					$('lititle').innerHTML = title+"<span> ["+date+"]</span>";
			}

			$('liinfo').innerHTML = mes.replace(/<font size=\"/g,"<font style=\"font-size:");
			
			$(fdiv).style.display = "block";
			$(tdiv).style.display = "block";
			
			var windowWidth = document.body.clientWidth | document.documentElement.clientWidth;
			var windowHeight = document.body.clientHeight ;
			var windowTop = document.documentElement.scrollTop;
			if (windowHeight-40>300)
			{
				$(tdiv).style.height = windowHeight-40+"px";
				$("liinfo").style.height = windowHeight-40-68+"px";
				$("liScrollbar").style.height = windowHeight-40-68+"px";
			}
			var TTop = parseInt($(tdiv).offsetHeight);
			$(tdiv).style.top = windowTop+(windowHeight/2)-TTop/2+parseInt(Distance)+"px";
			$(tdiv).style.left = (windowWidth/2)-parseInt($(tdiv).offsetWidth)/2;

			
			if (parseInt(windowHeight)<parseInt(document.body.clientHeight))
				windowHeight = document.body.clientHeight;
			var tsh = document.body.scrollHeight | document.documentElement.scrollHeight;
			if (parseInt(windowHeight)<parseInt(tsh))
				windowHeight = tsh;

			$(fdiv).style.height = windowHeight+"px";
			$(fdiv).style.width = windowWidth+"px";
			
			
			$('liinfo').scrollTop = 0;
			$("ScrollbarHandle").style.top = "0px";
			
			if ($('liinfo').offsetHeight>$('liinfo').scrollHeight)
				$("liScrollbar").style.display = "none";
			else
				$("liScrollbar").style.display = "block";

			LoadScroll();
		}
		
		
		function LoadScroll()
		{
			var a = $('liinfo').offsetHeight;
			var b = $('liinfo').scrollHeight;

			if ($('liinfo').offsetHeight>$('liinfo').scrollHeight)
				$("liScrollbar").style.display = "none";
			else
				$("liScrollbar").style.display = "block";

			var ptls = fGetXY($("liScrollbar"));
			MinTop = ptls.y;
			MaxTop = ptls.y+$("liScrollbar").offsetHeight - $("ScrollbarHandle").offsetHeight-MinTop;
			MaxScroll = $('liinfo').scrollHeight-$('liinfo').offsetHeight;
			MouseLen = MaxTop/10;
		}
		
		function Closeing(fdiv,tdiv)
		{
			//AppRun();

			WBMes = "";
			$(fdiv).style.display = "none";
			$(tdiv).style.display = "none";
			$('liinfo').innerHTML = "";

			if (IsDate==false)
				MusicRun();

			if (IsIE)
				$("ScrollbarHandle").releaseCapture();  
			else 
				window.releaseEvents(Event.MOUSEMOVE|Event.MOUSEUP); 
		}
		
		function AppRun()
		{		
			if (thisMovie("index").TAppRun!=undefined && thisMovie("index").TAppRun!=null)
				thisMovie("index").TAppRun();

			if ($("DivflashApp").innerHTML.lastIndexOf("flashapp")<0)
				return;
			if (thisMovie("flashapp").TAppRun!=undefined && thisMovie("flashapp").TAppRun!=null)
				thisMovie("flashapp").TAppRun();
		}

		function MusicStop()
		{		
			if (thisMovie("index").TMusicStop!=undefined && thisMovie("index").TMusicStop!=null)
				thisMovie("index").TMusicStop();
		}

		function MusicRun()
		{		
			if (thisMovie("index").TMusicRun!=undefined && thisMovie("index").TMusicRun!=null)
				thisMovie("index").TMusicRun();
		}

		function Loadd(fdiv,tdiv,tt)
		{
			if ($("DivflashApp")==null)	
			{
				var newDiv=document.createElement("div");
				document.body.appendChild(newDiv);
				newDiv.id="DivflashApp";
				//newDiv.style.cssText="-webkit-user-select:none;-moz-user-select:none; hutia:expression(this.onselectstart=function(){return(false)});Z-INDEX:2;width:100%;height:100%;top:0px;left:0px;display:none;position:absolute;cursor:default;";		
				newDiv.style.cssText="-webkit-user-select:none;-moz-user-select:none; Z-INDEX:2;width:100%;height:100%;top:0px;left:0px;display:none;position:absolute;cursor:default;";	
				
				var newDivfull=document.createElement("div");
				document.body.appendChild(newDivfull);
				newDivfull.id="DivflashAppFull";
				newDivfull.className="appfull";				
				//newDivfull.style.cssText="appfull";				
				
				var newDivClose=document.createElement("div");
				document.body.appendChild(newDivClose);
				newDivClose.id="DivflashAppClose";
				newDivClose.className = "appclose";
				newDivClose.onclick = function CloseAllDiv(){if ($("DivflashApp")!=null) $("DivflashApp").innerHTML="";$("DivflashApp").style.display = "none";$("DivflashAppFull").style.display = "none";$("DivflashAppClose").style.display = "none";HideDiv();if (thisMovie("index").closeDivHandler!=undefined && thisMovie("index").closeDivHandler!=null) thisMovie("index").closeDivHandler();}
				//document.body.innerHTML+="<div id=\"DivflashApp\" style=\"z-index:0;position:absolute;display:none;width:100%;height:100%;left:0px;top:0px;\"></div>";
			}

			//if (tt=="" || tt==null)
				tt="0";
			Distance = tt;

			$(fdiv).left = 0+"px";
			$(fdiv).style.top = 0+"px";
		
			$(fdiv).style.display = "none";
			$(tdiv).style.display = "none";

			var TMU = window.location.href.toLowerCase();
			if (TMU.indexOf("a001")>-1||TMU.indexOf("a002")>-1||TMU.indexOf("a003")>-1||TMU.indexOf("a004")>-1||TMU.indexOf("a005")>-1||TMU.indexOf("a006")>-1||TMU.indexOf("a007")>-1||
				TMU.indexOf("a008")>-1||TMU.indexOf("a009")>-1||TMU.indexOf("a010")>-1||TMU.indexOf("a011")>-1||TMU.indexOf("a012")>-1||TMU.indexOf("a013")>-1||TMU.indexOf("a014")>-1||
				TMU.indexOf("a015")>-1||TMU.indexOf("a016")>-1||TMU.indexOf("a017")>-1||TMU.indexOf("a018")>-1||TMU.indexOf("a019")>-1||TMU.indexOf("a020")>-1||TMU.indexOf("a021")>-1||
				TMU.indexOf("a023")>-1||TMU.indexOf("a024")>-1||TMU.indexOf("a025")>-1||TMU.indexOf("a026")>-1||TMU.indexOf("a027")>-1||TMU.indexOf("a028")>-1||TMU.indexOf("a029")>-1||
				TMU.indexOf("a030")>-1||TMU.indexOf("a031")>-1||TMU.indexOf("a032")>-1||TMU.indexOf("a033")>-1||TMU.indexOf("a034")>-1||TMU.indexOf("a035")>-1||TMU.indexOf("a036")>-1||
				TMU.indexOf("b003")>-1||TMU.indexOf("b004")>-1||TMU.indexOf("b009")>-1||TMU.indexOf("b010")>-1||TMU.indexOf("b011")>-1||TMU.indexOf("b012")>-1||TMU.indexOf("b013")>-1||
				TMU.indexOf("b014")>-1||TMU.indexOf("b015")>-1||TMU.indexOf("c003")>-1)
			{
				var newDiv=document.createElement("div");
				document.body.appendChild(newDiv);
				newDiv.id="dico";
				newDiv.style.zIndex=0;
				newDiv.style.width="136px";
				newDiv.style.height="25px";
				if (TMU.indexOf("a024")>-1)
				{
					newDiv.style.bottom="2px";
					newDiv.style.right="75px";	
				}
				else
				{
					if (TMU.indexOf("a026")>-1)
					{
						newDiv.style.top="8px";
						newDiv.style.left="8px";	
					}
					else
					{
						if (TMU.indexOf("a029")>-1 || TMU.indexOf("a030")>-1 || TMU.indexOf("a031")>-1 || TMU.indexOf("a032")>-1)
						{
							if (TMU.indexOf("a029")>-1)
							{
								newDiv.style.bottom="1px";
								newDiv.style.right="62px";
							}
							if (TMU.indexOf("a030")>-1)
							{
								newDiv.style.top="3px";
								newDiv.style.right="3px";
							}
							if (TMU.indexOf("a031")>-1)
							{
								newDiv.style.top="9px";
								newDiv.style.right="62px";
							}
							if (TMU.indexOf("a032")>-1)
							{
								newDiv.style.bottom="2px";
								newDiv.style.right="2px";
							}
						}
						else
						{
							newDiv.style.top="0px";
							newDiv.style.right="20px";	
						}	
					}
				}
				newDiv.style.position="absolute";
				if (TMU.indexOf("a003")>-1)
				{newDiv.style.top="12px"; newDiv.style.right="46px";}
				if (TMU.indexOf("a011")>-1)
				{newDiv.style.right="190px";}
				if (TMU.indexOf("a012")>-1)
				{newDiv.style.right="38px";}

				var ImS = "http://www.baidubaidubaidu.com/Shop/other/demo.gif";
				if (TMU.indexOf("/shop/")>-1)
				{
					TMU=TMU.replace("shop/", "");				
				}
				else
				{
					if (TMU.indexOf("a021")>-1)
						{newDiv.style.right="190px";}
					ImS = "http://www.baidubaidubaidu.com/Shop/other/app.gif";		
					TMU=TMU.replace("style/", "style/shop/");
					TMU=TMU.replace("/main.htm", "");
					TMU=TMU.replace("white/", "");
					TMU=TMU.replace("green/", "");
					TMU=TMU.replace("Green/", "");
				}
				newDiv.innerHTML+="<html><body><table cellpadding=0 cellspacing=0 width=100% height=100%><tr><td><a href=\""+TMU+"\" onclick=\"$('dico').style.display='none';\"><img src=\""+ImS+"\" border=0></a></td><td><a href=\"javascript:void(0);\" onclick=\"$('dico').style.display='none';\"><img border=0 src=\"http://www.baidubaidubaidu.com/Shop/other/close.gif\"></a></td></tr></table></body></html>";
			}
		}
		
		function Clicking(teve)
		{
			IsD = true;
			var ptls = fGetXY($("ScrollbarHandle"));
			Temptop = parseInt(teve.clientY) - parseInt(ptls.y);
			if (IsIE)
				$("ScrollbarHandle").setCapture();  
			else 
				window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP); 
		}
		
		function Moveing(teve,joj)
		{
			if (IsD==true || joj!="")
			{
				var ptld = fGetXY($("liScrollbar"));
				var tt;
				if (joj!="")
				{
					if ($("ScrollbarHandle").style.top=="")
					{
						if (joj=="+")
							tt = MouseLen
						else
							tt = 0;
					}
					else
					{
						if (joj=="+")
							tt = parseInt($("ScrollbarHandle").style.top)+parseInt(MouseLen);
						else
							tt = parseInt($("ScrollbarHandle").style.top)-parseInt(MouseLen);
					}		
				}
				else
					tt = teve.clientY - MinTop - Temptop;
					
				$("ScrollbarHandle").style.top =  tt + "px";
				if (parseInt($("ScrollbarHandle").style.top)<0)
				{
					$("ScrollbarHandle").style.top = "0px";
					$('liinfo').scrollTop = 0;
					return;
				}
				if (parseInt($("ScrollbarHandle").style.top)>MaxTop)
				{
					$("ScrollbarHandle").style.top = MaxTop +"px";
					if ($('liinfo').scrollHeight>$('liinfo').offsetHeight)
						$('liinfo').scrollTop = MaxScroll;
						
					return;	
				}
				$('liinfo').scrollTop = MaxScroll*(tt/MaxTop);
			}
		}
			

		function handle(delta) 
		{
			if (delta < 0)
				Moveing(window.event,"+");
			else
				Moveing(window.event,"-");
		}

		/** 事件句柄*/
		function wheel(event)
		{
			var delta = 0;
			if (!event) 
			event = window.event;
			if (event.wheelDelta) 
			{
			 
				delta = event.wheelDelta/120;
				if (window.opera)
					delta = -delta;
			} 
			else 
				if (event.detail) 
					delta = -event.detail/3;
		 
			if (delta)
				handle(delta);
		}

		if (window.addEventListener)
			window.addEventListener('DOMMouseScroll', wheel, false);
		else
			window.onmousewheel = document.onmousewheel = wheel;
		
		var IsIE = false;	
		if(navigator.appName.indexOf('Explorer') > -1)
			IsIE = true;


	function URLEncode(strInput)
	{
		strTmp = encodeURI(strInput);
		strTmp = strTmp.replace(/\ /g, '+');
		strTmp = strTmp.replace(/\@/g, '%40');
		strTmp = strTmp.replace(/\#/g, '%23');
		strTmp = strTmp.replace(/\$/g, '%24');
		strTmp = strTmp.replace(/\&/g, '%26');
		strTmp = strTmp.replace(/\+/g, '%2B');
		strTmp = strTmp.replace(/\=/g, '%3D');
		strTmp = strTmp.replace(/\;/g, '%3B');
		strTmp = strTmp.replace(/\:/g, '%3A');
		strTmp = strTmp.replace(/\//g, '%2F');
		strTmp = strTmp.replace(/\?/g, '%3F');
		strTmp = strTmp.replace(/\,/g, '%2C');
		return strTmp;
	}

	function LQQ(Mes)
	{
		var tempSrc="http://sighttp.qq.com/wpa.js?rantime="+Math.random()+"&sigkey="+Mes;
		var oldscript=document.getElementById("testJs");
		var newscript=document.createElement("script");
		newscript.setAttribute("type","text/javascript");
		newscript.setAttribute("id", "testJs");
		newscript.setAttribute("src",tempSrc);
		if(oldscript == null)
			document.body.appendChild(newscript);
		else
			oldscript.parentNode.replaceChild(newscript, oldscript);
	}

	function thisMovie(movieName) {
		if (navigator.appName.indexOf("Microsoft")!=-1) {
			return window[movieName]
		}else{
		return $(movieName+"_ff")//return document[movieName+"_ff"]
		}
	}