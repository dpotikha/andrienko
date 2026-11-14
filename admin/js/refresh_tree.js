// ******
// ***** refresh tree
// ******



function refreshTree(node_id, parent_id,node_title) {
	var treeFrame;
	if (window.opener != null) {
		treeFrame = window.opener.top.frames["treeFrame"];
	} else {
		treeFrame = top.frames["treeFrame"];
	}
	treeFrame.ReloadNode(node_id,null,decodeURIComponent(node_title));
}

function hardRefreshTree(node_id, parent_id) {
	/*var treeFrame;
	if (window.opener != null) {
		treeFrame = window.opener.top.frames["treeFrame"];
	} else {
		treeFrame = top.frames["treeFrame"];
	}
	treeFrame.location = "/admin/tree.php?selected_id=" + node_id;*/
}
