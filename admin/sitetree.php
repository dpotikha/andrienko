<?php include("prepend.php")?>
<html>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">

<title>Deeptree</title>
<link rel="stylesheet" type="text/css" href="/admin/css.css">
<link rel="stylesheet" type="text/css" href="/admin/mstree/deeptree.css">



<link rel="stylesheet" type="text/css" href="/admin/yui/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="/admin/yui/build/treeview/assets/skins/sam/treeview.css" />
<script type="text/javascript" src="/admin/yui/build/yahoo/yahoo.js"></script>
<script type="text/javascript" src="/admin/yui/build/event/event.js"></script>
<script type="text/javascript" src="/admin/yui/build/connection/connection.js"></script>
<script type="text/javascript" src="/admin/yui/build/treeview/treeview.js"></script>

</head>


<?php $root = Node::findRoot();?>

<?php 
$fieldLocale = '';
if (isset ($_SERVER['HTTP_REFERER'])) {
    preg_match('/.*fieldName=.*_(.*)$/', $_SERVER['HTTP_REFERER'], $m);
    if (isset($m[1]) && sizeof($SETUP_LANGUAGES) > 1 && in_array($m[1], $SETUP_LANGUAGES))
        $fieldLocale = "/{$m[1]}";
}
?>
<script type="text/javascript">

var treeExample = function() {

	var tree, currentIconMode;

    function changeIconMode() {
        var newVal = parseInt(this.value);
        if (newVal != currentIconMode) {
            currentIconMode = newVal;
        }
        buildTree();
    };
		function loadNodeData(node, fnLoadComplete)  {
			var sUrl = "/admin/req4.php?node_id=" + node.id+"&page="+node.page;
			var callback = {
				success: function(oResponse) {
					var oResults = eval("(" + oResponse.responseText + ")");

					if((oResults.ResultSet.Result) && (oResults.ResultSet.Result.length)) {
						if(YAHOO.lang.isArray(oResults.ResultSet.Result)) {
							for (var i=0, j=oResults.ResultSet.Result.length; i<j; i++) {
								var adminURL=oResults.ResultSet.Result[i].adminURL;
								var url="";
								if(adminURL!=""){
									url=adminURL;
								}else{
									url="/admin/nodes.php?do=main&id="+oResults.ResultSet.Result[i].id;
								}
								var tempNode = new YAHOO.widget.TextNode(oResults.ResultSet.Result[i].title, node, false);
								if(i!=0)
									tempNode.href="javascript:selectNode('"+oResults.ResultSet.Result[i].absolutePath+"')";
								else{
									var aname="<a name='#top"+oResults.ResultSet.Result[i].id+"'></a>";
									//var tempNode = new YAHOO.widget.TextNode(aname+"<a href='"+url+"' target='main' class='clear_tree' onclick='ChangeStyle(this,"+oResults.ResultSet.Result[i].id+")'>"+ (oResults.ResultSet.Result[i].title)+"</a>", node, false);
									tempNode.href="javascript:selectNode('"+oResults.ResultSet.Result[i].absolutePath+"')";
								}
								tempNode.id=oResults.ResultSet.Result[i].id;
								tempNode.page=oResults.ResultSet.Result[i].page;
								tempNode.nowrap=true;
								if(oResults.ResultSet.Result[i].ch==0)
								tempNode.dynamicLoadComplete=true;
							}
						} else {
							var tempNode = new YAHOO.widget.TextNode(oResults.ResultSet.Result, node, false)
						}
					}else{
					}
					oResponse.argument.fnLoadComplete();
					
				},
				
				failure: function(oResponse) {
					YAHOO.log("Failed to process XHR transaction.", "info", "example");
					oResponse.argument.fnLoadComplete();
				},
				
				argument: {
					"node": node,
					"fnLoadComplete": fnLoadComplete
				},
				
				timeout: 15000
			};
			
			YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
		};

        function buildTree() {
		   tree = new YAHOO.widget.TreeView("treeDiv1");
		   
		   tree.setDynamicLoad(loadNodeData, currentIconMode);
		   
		   var root = tree.getRoot();
		   
	   		var tempNode = new YAHOO.widget.TextNode("<?=$root->name?>", root, false);
			tempNode.href="javascript:selectNode('<?=$root->getHref()?>')";
			tempNode.id=<?=$root->id?>;
			tempNode.page=-1;
		    tree.draw();
		    tempNode.expand();
		};


	return {
		init: function() {
			currentIconMode = 1;
            buildTree();
        },
		getTree:function()
		{
			return tree;
		}

	};
} ();

//once the DOM has loaded, we can go ahead and set up our tree:
YAHOO.util.Event.onDOMReady(treeExample.init, treeExample,true)

</script>
<style>
#treeDiv1 {background: #fff;}
hr.Line	{padding: 0px; height:1px; width:100%; background-color:#dddddd; border:0px; }
div.SeparatorLine{
	padding: 0px 5px 0px 5px;
}
#topMenu{
	padding-left:5px;
}
#topMenu div{
	height:17px;
	background:url(/admin/yui/build/treeview/assets/skins/sam/square.gif) no-repeat;
	background-position:left 3px;
	padding-left:15px;
}
#topMenu div a.square{
	height:16px;
}
</style>
<body  leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=tree>

<nobr>
<div id="treeDiv1"></div>
</nobr>
<script>
	var old_o="";
	function ReloadNode(node_id,page,node_title,top)
	{
		var tree=treeExample.getTree();
		var node=tree.getNodeById(node_id);
		if(node!=null){
			if(page!=null) node.page=page;
			if(node_title!=null){
				node.label=node_title;
				node.href="/admin/nodes.php?do=main&id="+node_id;
				node.target='main'
				node.labelStyle ="tree";
				node.clickFunction="ChangeStyle(this,"+node_id+")";
				node.getEl().innerHTML=node.getHtml();
			}
			if(top!=null)
			{
				document.location.href="#top"+node_id;
			}else
			old_o=node.getLabelEl();
			node.reload();
		}
	}
	
	function ChangeStyle(o,id)
	{
		if(old_o!="")old_o.className="clear_tree";
		if(id!=null)
		{
			var tree=treeExample.getTree();
			var node=tree.getNodeById(id);
			if(node!=null){
				if(node.expanded==false){
					node.expand();
				}
				node.getLabelEl().focus();
			}
		}
		o.className="tree";
		old_o=o;
	}

</script>

</body>
</html>

<script language="javascript">
	function selectNode(val){
		window.top.opener.document.forms[0].elements['href'].value = '<?= $fieldLocale ?>' + val;
		window.close();  
		window.opener.focus();  	
	}
</script>