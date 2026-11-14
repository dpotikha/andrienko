var QCode={};
var CONFIG={
		skin_path:'/admin/js/'
	};
	CONFIG.windows={
		move:{w:500,h:400,minW:400,minH:300,maxW:800,maxH:600}
	};
QCode.windows={
	windows:new Array(),
	init:function(c){
			var windowObj=document.createElement('DIV');
			windowObj.id=c.id;
			windowObj.className='window';
			windowObj.style.overflow='hidden'; 
			windowObj.style.display='none'; 
			windowObj.style.zIndex=10000+(c.level-1)*2; 
			windowObj.innerHTML='<div id="windowTop_'+c.id+'" class="windowTop"><div id="windowTopContent">'+c.title+'</div><img src="'+CONFIG.skin_path+'/popup_window/window_close.gif" id="windowClose"></div><div id="windowBottom"><div id="windowBottomContent">&nbsp;</div></div><div id="windowContent"   ><div style="margin:10px;"  id="windowContentBody" ></div></div><img style="display: block;" src="'+CONFIG.skin_path+'/popup_window/window_resize.gif" id="windowResize_'+c.id+'" class="windowResize">';
			
			document.body.appendChild(windowObj);
			$('#windowContentBody','#'+c.id).css('width', c.size.w - 65 + 'px');

			$('#windowClose','#'+c.id).bind(
					'click',
					function()
					{
						$("#modalOverlay"+c.level).css("display","none");	
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
						minWidth: c.size.minW,
						minHeight: c.size.minH,
						maxWidth: c.size.maxW,
						maxHeight: c.size.maxH,
						dragHandle: '#windowTop_'+c.id,
						handlers: {
							se: '#windowResize_'+c.id
						},
						onResize : function(size, position) {
							$('#windowBottom, #windowBottomContent','#'+c.id).css('height', size.height-33 + 'px');
							$('#windowContentBody','#'+c.id).css('width', size.width - 65 + 'px');
							var windowContentEl = $('#windowContent','#'+c.id).css('width', size.width - 25 + 'px');
							if (!document.getElementById(c.id).isMinimized) {
								windowContentEl.css('height', size.height - 48 + 'px');
							}
						}
					}
			);
	},
	open:function(target_el,config){
		config.id='window_'+target_el;
		config.target=target_el;
		if(typeof(config.level)=='undefined') config.level=1;
		this.windows[config.level]=config;
		
		if(!document.getElementById(config.id))this.init(config);
		$('#windowContentBody','#'+config.id).html('');
		var scrollObj=QCode.getScrollXY();
		var clientSizeObj=QCode.getClientSize();
		var top=Math.floor(scrollObj.scrollTop+(clientSizeObj.clientHeight-config.size.h)/2);
		var left=Math.floor(scrollObj.scrollLeft+(clientSizeObj.clientWidth-config.size.w)/2);
		document.getElementById(config.id).style.left=left+'px';
		document.getElementById(config.id).style.top=top+'px';
		document.getElementById(config.id).style.width=config.size.w+'px';
		document.getElementById(config.id).style.height=config.size.h+'px';
		$("#modalOverlay"+config.level).css("display","block");	
		$('#'+config.target).TransferTo(
			{
				to:config.id,
				className:'transferer2', 
				duration: 300,
				complete: function()
				{
					$('#'+config.id).show();
					$('#windowBottom, #windowBottomContent','#'+config.id).css('height', config.size.h-33 + 'px');
					$('#windowContent','#'+config.id).css('width', config.size.w - 25 + 'px');
					$('#windowContent','#'+config.id).css('height', config.size.h - 48 + 'px');
					if(typeof(config.onload)=='function'){
						if(typeof(config.params)!="undefined"){
							config.onload(config.params);
						}else{
							config  .onload();
						}
					}
				}
			}
		);
		
		
		this.setContent(config.level,QCode.getIndicator(config.size.h));
	},
	getByLevel:function(level){
		return this.windows[level]; 
	},
	setContent:function(level,content){
		$('#windowContentBody','#'+this.getByLevel(level).id).html(content);
	},
	close:function(level){
		this.setContent(level,'');
		closeWindow(this.getByLevel(level).id,this.getByLevel(level).target,level);	
	}
};
QCode.getScrollXY=function() {
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
};
QCode.getClientSize=function() {
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
};
QCode.getIndicator=function(h){
	return '<div align=center style="margin-top:'+((h-100)/2)+'px"><img src="'+CONFIG.skin_path+'loading.gif"></div>';
};