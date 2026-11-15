<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
    <link rel=stylesheet href=/admin/css.css type="text/css">
    </head>
<body>
<?=$AdminTrnsl["Ok_node_has_been_moved_to_new_location"]?>
<script language="javascript">
    function _close() {
//        window.opener.document.location.href="/admin/nodes.php?do=main&id=<?=$request->getParameter("node_id")?>&reload=1"
 //       window.close();
		var treeFrame;
		if (window.opener != null) {
			treeFrame = window.opener.top.frames["treeFrame"];
		} else {
			treeFrame = top.frames["treeFrame"];
		}
		<?php 
			$node=Node::findById($request->getParameter("node_id"));
			$parentNode=$node->getParent();
			$oldParent=$request->getAttribute("oldParent");
			
		?>
		treeFrame.ReloadNode(<?=$oldParent->id?>,null,null);
		treeFrame.ReloadNode(<?=$parentNode->id?>,null,null);
		 window.opener.document.location.href="/admin/nodes.php?do=main&id=<?=$request->getParameter("node_id")?>&reload=1"
		 window.close();
    }
</script>
<a href="javascript:_close()"><?=$AdminTrnsl["close_window"]?></a>
</body>
</html>