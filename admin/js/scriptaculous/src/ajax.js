// JavaScript Document
function AjaxConnector(){
	this.setParameter=function(post_parameter){
		var foo=this; 
		var opt2 = {
		method: 'post',
		encoding: 'UTF-8',
		parameters: post_parameter,
		onSuccess:function(t) {
			
				foo.onSuccess(t);			
		   },
		on404: function(t) {
			alert('Error 404: location "' + t.statusText + '" was not found.');
		},
		onFailure: function(t) {
			alert('Error ' + t.status + ' -- ' + t.statusText);
		}
		};
		return opt2;
	}
	this.onSuccess=function(t){
			
		}
	this.send=function(url,post_parameters)
	{
    	new Ajax.Request(url,this.setParameter(post_parameters));
	}
}
