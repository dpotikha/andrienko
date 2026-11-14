function newImg(i){p=new Image(); p.src=i; return p;}

menu_01 = newImg("/_img/menu_main_01.gif");
menu_01_activ = newImg("/_img/menu_main_01_activ.gif");
menu_01_01 = newImg("/_img/menu_main_01_01.gif");
menu_01_01_activ = newImg("/_img/menu_main_01_01_activ.gif");
menu_01_02 = newImg("/_img/menu_main_01_02.gif");
menu_01_02_activ = newImg("/_img/menu_main_01_02_activ.gif");
menu_01_03 = newImg("/_img/menu_main_01_03.gif");
menu_01_03_activ = newImg("/_img/menu_main_01_03_activ.gif");
menu_02 = newImg("/_img/menu_main_02.gif");
menu_02_activ = newImg("/_img/menu_main_02_activ.gif");
menu_03 = newImg("/_img/menu_main_03.gif");
menu_03_activ = newImg("/_img/menu_main_03_activ.gif");
menu_04 = newImg("/_img/menu_main_04.gif");
menu_04_activ = newImg("/_img/menu_main_04_activ.gif");
menu_05 = newImg("/_img/menu_main_05.gif");
menu_05_activ = newImg("/_img/menu_main_05_activ.gif");


menu_05_01 = newImg("/_img/menu_main_05_01.gif");
menu_05_01_activ = newImg("/_img/menu_main_05_01_activ.gif");
menu_05_02 = newImg("/_img/menu_main_05_02.gif");
menu_05_02_activ = newImg("/_img/menu_main_05_02_activ.gif");
menu_05_03 = newImg("/_img/menu_main_05_03.gif");
menu_05_03_activ = newImg("/_img/menu_main_05_03_activ.gif");
menu_05_04 = newImg("/_img/menu_main_05_04.gif");
menu_05_04_activ = newImg("/_img/menu_main_05_04_activ.gif");
menu_05_05 = newImg("/_img/menu_main_05_05.gif");
menu_05_05_activ = newImg("/_img/menu_main_05_05_activ.gif");

function over(img) {
	document[img].src = eval(img + "_activ.src");
}
function out(img) {
	document[img].src = eval(img + ".src");
}



function dhtml(){
	//Browser Information
	this.User_agent = navigator.userAgent.toLowerCase();
	this.version    = this.User_agent;
	this.Opera    	= ( window.opera );
	this.IE       	= ( ( this.User_agent.indexOf("msie") != -1 ) && ( !this.Opera ) );
	this.MAC      	= ( this.User_agent.indexOf("mac") != -1 );
	this.Gecko    	= ( navigator.product == "Gecko" );
	this.Mozilla  	= this.Gecko;
}
var dhtml = new dhtml();
/** Generate random word **/
function generateRandomWord(){
	cons = new Array();
	vow = new Array();
	consts='bcdgklmnprst';
	vowels='aeiou';
	for (x=0; x < 6; x++) {
		rand1 = Math.random( )*consts.length;
		rand2 = Math.random( )*vowels.length;
		cons[x] = consts.substr(rand1,1);
		vow[x] = vowels.substr(rand2,1);
	}
	return cons[0]+vow[0]+cons[2]+cons[1]+vow[1]+cons[3]+vow[3]+cons[4];
}
function enlarge(oLink,title,width,height,scroll,resize,windowId) {
	title = (title)?title:( (oLink.title)?(oLink.title):('') );
	width = width || 400;
	height = height || 330;
	scroll = scroll || false;
	resize = resize || false;
	windowId = (windowId)?windowId:generateRandomWord(); 
	
	if (oLink.href.match(/(jpg)|(png)|(gif)|(jpeg)|(bmp)$/i)) {
		win = makePopUp(oLink.href,width,height,false,false,windowId);
		win.document.open("text/html");
		html = '<html><head><title>'+title+'</title><script language="JavaScript" type="text/javascript">'
			 + 'function $(id) { return (document.getElementById) ? document.getElementById(id) : ((document.all) ? document.all[id] : eval("document."+id)); };'
			 + 'function doResize() { window.resizeTo($("img").width+30, $("img").height+100); window.moveTo((screen.availWidth - $("img").width)/2, (screen.availHeight - $("img").height)/2 ) };'
			 + '</script><link rel="stylesheet" type="text/css" href="/main.css" ></head><body style="margin:0px;padding:0px;text-align:center">'
			 + '<div class="shadowImgDetailTL_01"><div class="shadowImgDetailBR_01"><a href="Закрыть окно" onclick="window.close(); return false;">'
			 + '<img src="' + oLink.href + '" name="img" id="img" alt="' + title + '" border="0" onload="doResize()" style="display:block"/>'
			 + '</a><div style="padding-top:8px"><a href="Закрыть окно" onClick="window.close(); return false" style="font-size:10px; color:#333">Закрыть окно</a></div></div></div></body></html>';
		win.document.write(html);
		win.focus();
		win.document.close();
	} else {
		makePopUp(oLink.href,width,height,scroll,resize,windowId);
	}		
		
	return false;
}

function makePopUp(sUrl,width,height,scroll,resize,windowId) {
	resize = (resize) ? 'yes' : 'no';
	scroll = (scroll) ? 'yes' : 'no';
	windowId = (windowId)?windowId:generateRandomWord(); 
	
    var leftPos = Math.round((screen.availWidth - width) / 2);
    var topPos  = Math.round((screen.availHeight - height) / 2);

	sOption = 'width='+width+',height='+height+',scrollbars='+scroll+',resizable='+resize+',statusbar=no,location=no,titlebar=0,toolbar=0,status=0';
	
    var Popup = new Object();
	Popup = window.open(sUrl, windowId, sOption+',top=' + topPos + ',left=' + leftPos);
	Popup.focus();
	
	return Popup;
}