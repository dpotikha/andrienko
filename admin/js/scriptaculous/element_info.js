function findHtmlElementInfo(obj) {
	var curLeft = curTop =curHeight=curWidth= 0;
	
	if (obj.offsetParent) {
		curLeft = obj.offsetLeft
		curTop = obj.offsetTop
		curHeight=obj.offsetHeight
		curWidth=obj.offsetWidth
		while (obj = obj.offsetParent) {
			curLeft += obj.offsetLeft
			curTop += obj.offsetTop
		}
	}
	return {left:curLeft,top:curTop,height:curHeight,width:curWidth};//[curLeft,curTop,curHeight,curWidth];
}
