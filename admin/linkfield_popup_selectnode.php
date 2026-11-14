<?php include("prepend.php")?>
<?php 
$rn = $request->getParameter("rn");
$cs = $request->getParameter("cs");
$d = $request->getParameter("d");
$cname = $request->getParameter("cname");
?>
<html>
<head><meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<META NAME="Robots" CONTENT="noindex">
  <title>Deeptree</title>
  <link rel="stylesheet" type="text/css" href="/admin/css.css">
  <link rel="stylesheet" type="text/css" href="/admin/mstree/deeptree.css">
  
  
	 <link rel="stylesheet" type="text/css" href="/admin/yui/build/fonts/fonts-min.css" />
    <link rel="stylesheet" type="text/css" href="/admin/yui/build/treeview/assets/skins/sam/treeview_moved.css" />
    <script type="text/javascript" src="/admin/yui/build/yahoo/yahoo.js"></script>
    <script type="text/javascript" src="/admin/yui/build/event/event.js"></script>
    <script type="text/javascript" src="/admin/yui/build/connection/connection.js"></script>
    <script type="text/javascript" src="/admin/yui/build/treeview/treeview.js"></script>

  <?php $root = Node::findByPath($request->getParameter("rn"));?>
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
			//var sUrl = "/req2.php?node_id=" + node.id+"&page="+node.page;
//			var sUrl="/req2.php?node_id="+node.id+"&rn=<?=$rn?>&cs="+node.absolutePath+"&d=<?=$d?>&page="+node.page;
			var sUrl="/admin/req2.php?node_id="+node.id+"&rn="+node.absolutePath+"&cs=<?=$cs?>&d="+node.d+"&page="+node.page;
			
			var callback = {
				success: function(oResponse) {
					var oResults = eval("(" + oResponse.responseText + ")");

					if((oResults.ResultSet.Result) && (oResults.ResultSet.Result.length)) {
						if(YAHOO.lang.isArray(oResults.ResultSet.Result)) {
							for (var i=0, j=oResults.ResultSet.Result.length; i<j; i++) {
								var tempNode = new YAHOO.widget.TextNode(oResults.ResultSet.Result[i].title, node, false);
								if(oResults.ResultSet.Result[i].ch==0){
									tempNode.href="javascript:selectNode('"+oResults.ResultSet.Result[i].absolutePath+"')";
									tempNode.dynamicLoadComplete=true;
								}
								tempNode.href="javascript:selectNode('"+oResults.ResultSet.Result[i].absolutePath+"')";
								tempNode.id=oResults.ResultSet.Result[i].id;
								tempNode.nowrap=true;
								tempNode.d=oResults.ResultSet.Result[i].depth;
								tempNode.page=oResults.ResultSet.Result[i].page;
								tempNode.absolutePath=oResults.ResultSet.Result[i].absolutePath;
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
			tempNode.href="javascript:selectNode('<?=$root->id?>')";
			tempNode.id=<?=$root->id?>;
			tempNode.page=-1;
			tempNode.d="<?=$d?>";
			tempNode.absolutePath="<?=$root->absolutePath?>";
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
	
	function ReloadNode(node_id,page,node_title,top)
	{
		var tree=treeExample.getTree();
		var node=tree.getNodeById(node_id);
		if(node!=null){
			if(page!=null) node.page=page;
			node.reload();
		}
	}

</script>

</head>
<script>

window.name = 'linkfieldwindow';
function selectNode(nodePath) {
    opener.document.forms["node_form"].elements["<?=$cname?>"].value = nodePath;
    window.close();
}
</script>


<body  leftmargin=10 topmargin=10 marginwidth=10 marginheight=10 class=main>
<nobr>
<div id="treeDiv1"></div>
</nobr>


</body>
</html>
