		var IsD = false;
		var Temptop = 0;
		var MinTop = 0;
		var MaxTop = 0;
		var MaxScroll = 0;
		var MouseLen = 0;
		var Distance = 0;
		var SwfHeight= "100%";
		
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
			var windowWidth = document.body.clientWidth | document.documentElement.clientWidth;
			var windowHeight = document.body.clientHeight | document.documentElement.clientHeight;
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
		
		function LoadDivWZ(fdiv,tdiv,title,date,mes)
		{
			$('lititle').innerHTML = title+"<span> ["+date+"]</span>";
			$('liinfo').innerHTML = mes.replace(/<font size=\"/g,"<font style=\"font-size:");
			
			$(fdiv).style.display = "block";
			$(tdiv).style.display = "block";
			
			var windowWidth = document.body.clientWidth | document.documentElement.clientWidth;
			var windowHeight = document.body.clientHeight | document.documentElement.clientHeight;
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
			$(fdiv).style.display = "none";
			$(tdiv).style.display = "none";
		}
		
		function Loadd(fdiv,tdiv,tt)
		{
			//if (tt=="" || tt==null)
				tt="0";
			Distance = tt;

			$(fdiv).left = 0+"px";
			$(fdiv).style.top = 0+"px";
		
			$(fdiv).style.display = "none";
			$(tdiv).style.display = "none";
		}
		
		function Clicking(teve)
		{
			IsD = true;
			var ptls = fGetXY($("ScrollbarHandle"));
			Temptop = parseInt(teve.clientY) - parseInt(ptls.y);
			$("ScrollbarHandle").setCapture();
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