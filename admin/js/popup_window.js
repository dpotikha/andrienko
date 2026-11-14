// JavaScript Document
function getScrollXY() {
  var scrOfX = 0, scrOfY = 0;
  if( typeof( window.pageYOffset ) == 'number' ) {
    //Netscape compliant
    scrOfY = window.pageYOffset;
    scrOfX = window.pageXOffset;
  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
    //DOM compliant
    scrOfY = document.body.scrollTop;
    scrOfX = document.body.scrollLeft;
  } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
    //IE6 standards compliant mode
    scrOfY = document.documentElement.scrollTop;
    scrOfX = document.documentElement.scrollLeft;
  }
  return { scrollLeft:scrOfX, scrollTop:scrOfY };
}
function getClientSize() {
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myWidth = window.innerWidth;
    myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
    myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
    myHeight = document.body.clientHeight;
  }
  return { clientWidth:myWidth, clientHeight:myHeight };
}
function openWindow(configObj){
	//configObj={id,target,size:{w,h,minW,minH,maxW,maxH},title}
	if(!document.getElementById(configObj.id))initWindow(configObj);
	var scrollObj=getScrollXY();
	var clientSizeObj=getClientSize();
	var top=Math.floor(scrollObj.scrollTop+(clientSizeObj.clientHeight-configObj.size.h)/2);
	var left=Math.floor(scrollObj.scrollLeft+(clientSizeObj.clientWidth-configObj.size.w)/2);
	document.getElementById(configObj.id).style.left=left+'px';
	document.getElementById(configObj.id).style.top=top+'px';
	document.getElementById(configObj.id).style.width=configObj.size.w+'px';
	document.getElementById(configObj.id).style.height=configObj.size.h+'px';
	if(typeof(configObj.level)!='undefined'){
		$("#modalOverlay"+configObj.level).css("display","block");	
	}else{
		$("#modalOverlay1").css("display","block");	
	}
	$('#'+configObj.target).TransferTo(
		{
			to:configObj.id,
			className:'transferer2', 
			duration: 300,
			complete: function()
			{
				$('#'+configObj.id).show();
				$('#windowBottom, #windowBottomContent','#'+configObj.id).css('height', configObj.size.h-33 + 'px');
				$('#windowContent','#'+configObj.id).css('width', configObj.size.w - 25 + 'px');
				$('#windowContent','#'+configObj.id).css('height', configObj.size.h - 48 + 'px');
				if(typeof(configObj.onload)=='function'){
					if(typeof(configObj.params)!="undefined"){
						configObj.onload(configObj.params);
					}else{
						configObj.onload();
					}
				}
			}
		}
	);
	
}
function closeWindow(id,target,level){
	if(typeof(level)=='undefined'){
		$("#modalOverlay1").css("display","none");	
	}else{
		$("#modalOverlay"+level).css("display","none");	
	}
		$('#'+id).TransferTo(
			{
				to:target,
				className:'transferer2', 
				duration: 300
			}
		).hide();
	}
function initWindow(c){
	var windowObj=document.createElement('DIV');
	windowObj.id=c.id;
	windowObj.className='window';
	windowObj.style.overflow='hidden'; 
	windowObj.style.display='none'; 
	if(typeof(c.level)!='undefined'){
		windowObj.style.zIndex=10000+c.level; 
	}
	windowObj.innerHTML='<div id="windowTop_'+c.id+'" class="windowTop"><div id="windowTopContent">'+c.title+'</div><img src="'+CONFIG.skin_path+'/popup_window/window_close.gif" id="windowClose"></div><div id="windowBottom"><div id="windowBottomContent">&nbsp;</div></div><div id="windowContent"><div style="margin:10px" id="windowContentBody"></div><img style="display: block;" src="'+CONFIG.skin_path+'/popup_window/window_resize.gif" id="windowResize_'+c.id+'" class="windowResize">';
	
	document.body.appendChild(windowObj);
	$('#windowClose','#'+c.id).bind(
			'click',
			function()
			{
				if(typeof(c.level)!='undefined'){
					$("#modalOverlay"+c.level).css("display","none");	
				}else{
					$("#modalOverlay1").css("display","none");	
				}	
//				$("#modalOverlay").css("display","none");	
				$('#'+c.id).TransferTo(
					{
						to:c.target,
						className:'transferer2', 
						duration: 300
					}
				).hide();
			}
		);
		
	$('#'+windowObj.id).Resizable(
			{
				minWidth: 200,
				minHeight: 60,
				maxWidth: 1000,
				maxHeight: 700,
				dragHandle: '#windowTop_'+c.id,
				handlers: {
					se: '#windowResize_'+c.id
				},
				onResize : function(size, position) {
					$('#windowBottom, #windowBottomContent','#'+c.id).css('height', size.height-33 + 'px');
					var windowContentEl = $('#windowContent','#'+c.id).css('width', size.width - 25 + 'px');
					if (!document.getElementById(c.id).isMinimized) {
						windowContentEl.css('height', size.height - 48 + 'px');
					}
				}
			}
	);
}