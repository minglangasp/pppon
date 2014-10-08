    function unselectall(thisform){
        if(thisform.chkAll.checked){
            thisform.chkAll.checked = thisform.chkAll.checked&0;
        }   
    }
    function CheckAll(thisform){
        for (var i=0;i<thisform.elements.length;i++){
            var e = thisform.elements[i];
            if (e.Name != "chkAll"&&e.disabled!=true)
                e.checked = thisform.chkAll.checked;
        }
    }
	
	//获取颜色值
function Getcolor(img_val,input_val){
	var arr = showModalDialog("Public/Admin/images/selcolor.html?action=title", "", "dialogWidth:18.5em; dialogHeight:17.5em; status:0; help:0");
	if (arr != null){
		document.getElementById(input_val).value = arr;
		img_val.style.backgroundColor = arr;
		}
}

function myKeyDown()
{
    var    k=window.event.keyCode;   

    if ((k==46)||(k==8)||(k==189)||(k==109)||(k==190)||(k==110)|| (k>=48 && k<=57)||(k>=96 && k<=105)||(k>=37 && k<=40)) 
    {}
    else if(k==13){
         window.event.keyCode = 9;}
    else{
         window.event.returnValue = false;}
}
function nTabs(thisObj,Num){
if(thisObj.className == "active")return;
var tabObj = thisObj.parentNode.id;
var tabList = document.getElementById(tabObj).getElementsByTagName("li");
for(i=0; i <tabList.length; i++)
{
  if (i == Num)
  {
   thisObj.className = "active"; 
      document.getElementById(tabObj+"_Content"+i).style.display = "block";
  }else{
   tabList[i].className = "normal"; 
   document.getElementById(tabObj+"_Content"+i).style.display = "none";
  }
} 
}

var qi;var qt;var qp="parentNode";var qc="className";function ldc(sd,v,l){if(!l){l=1;sd=document.getElementById("ld"+sd);sd.onmouseover=function(e){x6(e)};document.onmouseover=x2;sd.style.zoom=1;}sd.style.zIndex=l;var lsp;var sp=sd.childNodes;for(var i=0;i<sp.length;i++){var b=sp[i];if(b.tagName=="A"){lsp=b;b.onmouseover=x0;if(l==1&&v){b.style.styleFloat="none";b.style.cssFloat="none";}}if(b.tagName=="DIV"){if(window.showHelp&&!window.XMLHttpRequest)sp[i].insertAdjacentHTML("afterBegin","<span style='display:block;font-size:1px;height:0px;width:0px;visibility:hidden;'></span>");x5("ldparent",lsp,1);lsp.cdiv=b;b.idiv=lsp;new ldc(b,null,l+1);}}};function x2(e){if(qi&&!qt)qt=setTimeout("x3()",100);};function x3(){var a;if((a=qi)){do{x1(a);}while((a=a[qp])&&!ld_a(a))}qi=null;};function ld_a(a){if(a[qc].indexOf("ldmc")+1)return 1;};function x1(a){if(window.ldad&&ldad.bhide)eval(ldad.bhide);a.style.visibility="";x5("ldactive",a.idiv);};function x0(e){if(qt){clearTimeout(qt);qt=null;}var a=this;if(a[qp].isrun)return;var go=true;while((a=a[qp])&&!ld_a(a)){if(a==qi)go=false;}if(qi&&go){a=this;if((!a.cdiv)||(a.cdiv&&a.cdiv!=qi))x1(qi);a=qi;while((a=a[qp])&&!ld_a(a)){if(a!=this[qp])x1(a);else break;}}var b=this;if(b.cdiv){var aw=b.offsetWidth;var ah=b.offsetHeight;var ax=b.offsetLeft;var ay=b.offsetTop;if(ld_a(b[qp])&&b.style.styleFloat!="none"&&b.style.cssFloat!="none")aw=0;else ah=0;if(!b.cdiv.ismove){b.cdiv.style.left=(ax+aw)+"px";b.cdiv.style.top=(ay+ah)+"px";}x5("ldactive",this,1);if(window.ldad&&ldad.bvis)eval(ldad.bvis);b.cdiv.style.visibility="inherit";qi=b.cdiv;}else  if(!ld_a(b[qp]))qi=b[qp];else qi=null;x6(e);};function x5(name,b,add){var a=b[qc];if(add){if(a.indexOf(name)==-1)b[qc]+=(a?' ':'')+name;}else {b[qc]=a.replace(" "+name,"");b[qc]=b[qc].replace(name,"");}};function x6(e){if(!e)e=event;e.cancelBubble=true;if(e.stopPropagation)e.stopPropagation();}