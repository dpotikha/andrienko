  function animatePopupWindow()
	{
		this.title="&nbsp;";
		this.oCanvas='';
		this.duration=0.3;
		this.opacity=0.5;
		this.width=480;
		this.height=300;
		this.toLeft=0;
		this.toTop=0;
		this.init=function(options)
		{
			if(typeof(options)!="undefined"){
				if(options.width!=null)
					this.width=options.width;
				if(options.height!=null)
					this.height=options.height;
				if(options.opacity!=null)
					this.opacity=options.opacity;
				if(options.duration!=null)
					this.duration=options.duartion;
				if(options.title!=null)
					this.title=options.title;
				if(options.afterShow!=null)
					this.afterShow=options.afterShow;
			}	
			if(window.navigator.appName=="Opera")
				this.oCanvas=document.getElementsByTagName("BODY" )[0]; 
			else
				this.oCanvas=document.getElementsByTagName((document.compatMode && document.compatMode == "CSS1Compat") ? "HTML" : "BODY" )[0]; 

			if(!$("popup_l")){
				var popup_l = document.createElement('DIV');
				popup_l.id = "popup_l";
				document.body.appendChild(popup_l);
			}
			if(!$("lock_layer")){
				var lock_layer = document.createElement('DIV');
				lock_layer.id = "lock_layer";
				document.body.appendChild(lock_layer);
				$('lock_layer').style.height=document.body.offsetHeight+"px";
				$('lock_layer').setOpacity(0.1);
			}
			if(!$("bottom_element")){
				var bottom_element=document.createElement('DIV');
				bottom_element.id="bottom_element";
				document.body.appendChild(bottom_element);
			}
			};
			this.initPopupWindow=function()
			{
				if(!$("popupElement")){
				var popupElement = document.createElement('DIV');
				popupElement.id = "popupElement";
				popupElement.style.display="none";
				var popupLayer =document.createElement('DIV');
				popupLayer.id="popup_layer";
				popupLayer.style.width=this.width+"px";
				popupLayer.style.height=this.height+"px";
				popupElement.appendChild(popupLayer);
	
				var popupLayerContent =document.createElement('DIV');
				popupLayerContent.id="popup_layer_content";
				popupLayerContent.style.width=this.width;
				popupLayerContent.style.height=this.height;
				popupLayer.appendChild(popupLayerContent);
	
				var popupElementTitleLeft =document.createElement('DIV');
				popupElementTitleLeft.id="popupElementTitleLeft";
				popupLayerContent.appendChild(popupElementTitleLeft);
	
				var popupElementTitleRight =document.createElement('DIV');
				popupElementTitleRight.id="popupElementTitleRight";
				popupElementTitleRight.style.width=(this.width-18)+"px";

				popupElementTitleLeft.appendChild(popupElementTitleRight);
	
				var popupElementTitleCenter =document.createElement('DIV');
				popupElementTitleCenter.id="popupElementTitleCenter";
				popupElementTitleRight.appendChild(popupElementTitleCenter);
	
				var popupElementClose =document.createElement('DIV');
				popupElementClose.id="popupElementClose";
				var A=document.createElement('A');
				A.href="javascript:popupWindow.hide()";
				var IMG=document.createElement('IMG');
				IMG.src="/admin/js/scriptaculous/pW/close.gif";
				IMG.style.border="0";
				A.appendChild(IMG);
				popupElementClose.appendChild(A);
				
				popupElementTitleCenter.appendChild(popupElementClose);
	
				var popupWindowTitle =document.createElement('DIV');
				popupWindowTitle.id="popupWindowTitle";
				popupWindowTitle.innerHTML=this.title;
				popupElementTitleCenter.appendChild(popupWindowTitle);
	
				var popupWindowBody =document.createElement('DIV');
				popupWindowBody.id="popupWindowBody";
				popupWindowBody.style.width=(this.width-24)+"px";
				popupWindowBody.style.height=(this.height-27)+"px";
				popupLayerContent.appendChild(popupWindowBody);
	
				var popupWindowBodyContent =document.createElement('DIV');
				popupWindowBodyContent.id="popupWindowBodyContent";
				popupWindowBodyContent.style.width=(this.width-27)+"px";
				popupWindowBodyContent.style.height=(this.height-27)+"px";
				popupWindowBody.appendChild(popupWindowBodyContent);
	
				var popupWindowContent =document.createElement('DIV');
				popupWindowContent.id="popupWindowContent";
				
				popupWindowBodyContent.appendChild(popupWindowContent);
	
				var popupLayerShadow =document.createElement('DIV');
				popupLayerShadow.id="popupLayerShadow";
				popupLayerShadow.style.height=this.height+"px";
				popupLayerShadow.style.width=(this.width-3)+"px";
	
				var popupLayerShadowTL =document.createElement('DIV');
				popupLayerShadowTL.id="popupLayerShadowTL";
				popupLayerShadow.appendChild(popupLayerShadowTL);
				var popupLayerShadowTR =document.createElement('DIV');
				popupLayerShadowTR.id="popupLayerShadowTR";
				popupLayerShadowTL.appendChild(popupLayerShadowTR);
				var popupLayerShadowTC =document.createElement('DIV');
				popupLayerShadowTC.id="popupLayerShadowTC";
				popupLayerShadowTR.appendChild(popupLayerShadowTC);
				popupLayerShadow.appendChild(popupLayerShadowTL);
	
				var popupLayerShadowML =document.createElement('DIV');
				popupLayerShadowML.id="popupLayerShadowML";
				popupLayerShadow.appendChild(popupLayerShadowML);
				var popupLayerShadowMR =document.createElement('DIV');
				popupLayerShadowMR.id="popupLayerShadowMR";

				popupLayerShadowML.appendChild(popupLayerShadowMR);
				var popupLayerShadowMC =document.createElement('DIV');
				popupLayerShadowMC.id="popupLayerShadowMC";

				popupLayerShadowMR.appendChild(popupLayerShadowMC);
				popupLayerShadow.appendChild(popupLayerShadowML);
	
				var popupLayerShadowBL =document.createElement('DIV');
				popupLayerShadowBL.id="popupLayerShadowBL";
				popupLayerShadow.appendChild(popupLayerShadowML);
				var popupLayerShadowBR =document.createElement('DIV');
				popupLayerShadowBR.id="popupLayerShadowBR";
				popupLayerShadowBL.appendChild(popupLayerShadowBR);
				var popupLayerShadowBC =document.createElement('DIV');
				popupLayerShadowBC.id="popupLayerShadowBC";
				popupLayerShadowBR.appendChild(popupLayerShadowBC);
				popupLayerShadow.appendChild(popupLayerShadowBL);
	
				
				popupLayer.appendChild(popupLayerShadow);
				document.body.appendChild(popupElement);
				new Draggable('popupElement',{handle:"popupElementTitleLeft",starteffect:null,endeffect:null});
				}

			};
			this.setTitle=function(title){
				this.title=title;
				if($("popupWindowTitle"))
				$(popupWindowTitle).innerHTML=title;
			};
			this.show=function(parent_element){
				$('lock_layer').style.display="block";
				var r=findHtmlElementInfo($("bottom_element"));
				if(r.top<this.oCanvas.clientHeight)r.top=this.oCanvas.clientHeight;
				
//				alert(this.oCanvas.clientHeight);
				$('lock_layer').style.height=r.top+"px";
			
				$('popup_l').style.display="block";
				$('popup_l').style.position="absolute";
				var el_info=findHtmlElementInfo(parent_element);
				$('popup_l').style.height=el_info.height+"px";
				$('popup_l').style.width=el_info.width+"px";
				$('popup_l').style.top=el_info.top+"px";
				$('popup_l').style.left=el_info.left+"px";
				$('popup_l').setOpacity(this.opacity);
				this.toTop=this.oCanvas.scrollTop+(this.oCanvas.clientHeight-this.height)/2;
				this.toLeft=this.oCanvas.scrollLeft+(this.oCanvas.clientWidth-this.width)/2;
				resize=new Effect.MoveAndReSize('popup_l',{resize : ['fixed:'+this.width+':'+this.height],move : ['fixed:'+this.toLeft+':'+ this.toTop], toggleIndex:1,duration:this.duration,goAfterFinish:"popupWindow.showPopup()"});
			

			};
			this.showPopup=function(){
				if(!$("popupElement")){
					this.initPopupWindow();
				}
				$("popupElement").style.top=this.toTop+"px";
				$("popupElement").style.left=this.toLeft+"px";
				$('popup_l').style.display="none";
				$("popupElement").style.display="block";
				this.afterShow();
			};
			this.afterShow=function()
			{
				
				};
			this.hidePopupLayer=function(){
				$('popup_l').style.display="none";
				$('lock_layer').style.display="none";
			};
			
			this.hide=function(){
			
				$("popupElement").style.display="none";
				$('popup_l').style.top=$("popupElement").style.top;
				$('popup_l').style.left=$("popupElement").style.left;
				$('popup_l').style.display="block";
				resize=new Effect.MoveAndReSize('popup_l',{resize : ['y:600','x:800'],move : ['x:200','y:100'], toggleIndex:1,duration:this.duration,goAfterFinish:"popupWindow.hidePopupLayer()"});
			};
		
	}
	/*
	
	function createShowEl(parent_element)
	{
		var title="Hello1";
		if(!$("popupElement")){
			popupWindow=new animatePopupWindow();
			popupWindow.init();
			popupWindow.setTitle(title);
		}
		popupWindow.show(parent_element);
	}
	*/