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
			var sUrl = "/admin/req.php?node_id=" + node.id+"&page="+node.page;
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
								if(i!=0)
									var tempNode = new YAHOO.widget.TextNode("<a href='"+url+"' target='main' class='clear_tree' onclick='ChangeStyle(this,"+oResults.ResultSet.Result[i].id+")'>"+ (oResults.ResultSet.Result[i].title)+"</a>", node, false);
								else{
									var aname="<a name='#top"+oResults.ResultSet.Result[i].id+"'></a>";
									var tempNode = new YAHOO.widget.TextNode(aname+"<a href='"+url+"' target='main' class='clear_tree' onclick='ChangeStyle(this,"+oResults.ResultSet.Result[i].id+")'>"+ (oResults.ResultSet.Result[i].title)+"</a>", node, false);
								}
								tempNode.id=oResults.ResultSet.Result[i].id;
								tempNode.page=oResults.ResultSet.Result[i].page;
								tempNode.nowrap=true;
								tempNode.adminURL=adminURL;
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
		   
	   		var tempNode = new YAHOO.widget.TextNode("<a href='/admin/nodes.php?do=main&id=<?=$root->id?>' class='clear_tree' target='main'  onclick='ChangeStyle(this)'><?=addslashes($root->name)?></a>", root, false);
			tempNode.id=<?=$root->id?>;
			tempNode.adminURL='/admin/nodes.php?do=main&id=<?=$root->id?>';
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
<div id="topMenu">

<?php 
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	$showSiteSettings = false;
	if ($currentLoggedUser->group->canManageUsers) {
		?>
			<div><a href="/admin/users.php" target="main" class="clear_tree" onClick="ChangeStyle(this)"><?=$AdminTrnsl["Users_Management"]?></a></div>
        <?php 
			
	} else {
		$showSiteSettings = false;
	}
	if ($currentLoggedUser->group->canManageClasses) {
		?>
			<div><a href="/admin/classes.php" target="main" class="clear_tree" onClick="ChangeStyle(this)"><?=$AdminTrnsl["Classes_Management"]?></a></div>
        <?php 
	} else {
		$showSiteSettings = false;
	}
	if (_MODERATION_FEATURE_ === true && $CurrentAdminUser->group->isModerator) {
		?>
			<div><a href="admin/moderation.php" target="main" class="clear_tree" onClick="ChangeStyle(this)"><?=$AdminTrnsl["Nodes_Moderation"]?></a></div> 
        <?php 
	}
	if ($showSiteSettings) {
		?>
			<div><a href="/admin/settings.php" target="main" class="clear_tree" onClick="ChangeStyle(this)">Site Settings</a></div> 
        <?php 
	}
	if ($currentLoggedUser->group->canManageImgLib) {
		?>
			<div><a href="/admin/filelib.php" target="main" class="clear_tree" onClick="ChangeStyle(this)"><?=$AdminTrnsl["DownloadLib"]?></a></div>
			<div><a href="/admin/imglib.php" target="main" class="clear_tree" onClick="ChangeStyle(this)"><?=$AdminTrnsl["Image_Library"]?></a></div> 
        <?php 
	}
?>
			<div><a href="/admin/mlangs.php" target="main" class="clear_tree" onClick="ChangeStyle(this)"><?=$AdminTrnsl["Menu_Languages"]?></a></div>
			<?php   if ($currentLoggedUser->group->canManageSettings) { ?>
                       <!-- <div><a href="/admin/delivery.php" target="main" class="clear_tree" onClick="ChangeStyle(this)"><?=$AdminTrnsl["Menu_Delivery"]?></a></div>-->
        
			<div><a href="/admin/settings.php" target="main" class="clear_tree" onClick="ChangeStyle(this)"><?=$AdminTrnsl["Menu_Settings"]?></a></div>
        <?php  } ?>
                        <?php   if ($currentLoggedUser->group->id <=2) { ?>
			<div><a href="/admin/logger.php" target="main" class="clear_tree" onClick="ChangeStyle(this)">Logger</a></div>
        <?php  } ?>
</div>


	<div class="SeparatorLine"><hr class="Line"/></div>
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
				var adminURL=node.adminURL;
				var url="";
				if(adminURL!=""){
					url=adminURL;
				}else{
					url="/admin/nodes.php?do=main&id="+node_id;
				}
				node.label=node_title;
				node.href=url;
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
