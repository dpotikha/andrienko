<?php 
function showTableHeadItem($text, $nodeClass, $orderBy, $node, $tdParams = '', $tdClass = ''){
    if ($nodeClass->checkFlag("USE_ORDERING")){
        $nodeClassId = $nodeClass->id;
        $currentOrder = $nodeClass->orderBy;
        if ($currentOrder == "n.$orderBy ASC")
            $tdOrderClass = 'actDown';
        if ($currentOrder == "n.$orderBy DESC")
            $tdOrderClass = 'actUp';
        return "<td $tdParams class='$tdClass $tdOrderClass'><a href='/admin/nodes.php?do=changeSortOrder&nodeClass=$nodeClassId&orderBy=$orderBy&returnToNode=$node->id'>$text</a></td>";
    }
    return "<td $tdParams class='$tdClass'>$text</td>";
}




$request->setAttribute("active_tab", "editing");
$restrict_edit = $CurrentAdminUser->group->restrictNodeEdit;

$visibleDepends = array();
foreach($depends as $dep) {
	if (!$restrict_edit || $dep->checkFlag("RU_CAN_CREATE")) {
		$visibleDepends[] = $dep;
	}
}
			$ndMap = new PHPObject();
			$ndMap->node = $node;
			$currentLoggedUser = $_SESSION[SESSION_LOGGED_USER_ATTR];
			$ndGR = new NodeGroupRights($currentLoggedUser->group, $node);
			$ndMap->hasWriteRight = $ndGR->hasWriteRight();
			$ndMap->hasViewRight = $ndGR->hasViewRight();
			$ndMap->hasDeleteRight = $ndGR->hasDeleteRight();
			$request->setAttribute("ndMap",$ndMap);
?>

<?php  usetemplate("nodes/_edit_header") ?>
<style type="text/css">
    td.actDown, td.actUp {background:none no-repeat scroll 0 0 #BCBCBC;}
    .actDown a { background:url("/admin/_img/order_arrow_down.gif") no-repeat scroll 100% 9px; float:left; margin:-4px 0 -5px -5px; padding:4px 11px 5px 5px; }
    .actUp a { background:url("/admin/_img/order_arrow_up.gif") no-repeat scroll 100% 9px; float:left; margin:-4px 0 -5px -5px; padding:4px 11px 5px 5px; }
</style>
<link rel=stylesheet href="/admin/js/calendar/calendar-win2k-cold-2.css" type="text/css">
<script type="text/javascript" src="/admin/js/calendar/calendar.js"></script>
<script type="text/javascript" src="/admin/js/calendar/ru.js"></script>
<script type="text/javascript" src="/admin/js/calendar/calendar-setup.js"></script>
<script type="text/javascript" src="/admin/js/tooltip.js"></script>

<?php  if (!$restrict_edit || $ndMap->hasWriteRight ) { ?>
<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td id=pad18>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<form action="/admin/nodes.php" method="POST">
<input type="hidden" name="parent_id" value="<?=$node->id?>">
<input type="hidden" name="do" value="addnode_form">
<tr class=top4>
<td height=36 id=pad5 nowrap width=50%>
<?php  if ($node->id != 1 && ($CurrentAdminUser->group->id == 1 ||  MANAGERS_CAN_MOVE && $ndMap->hasDeleteRight)) { ?>
    <input type=button class=button1 value="<?=$AdminTrnsl["Move"]?>" onClick="wop('/admin/nodes.php?do=move_node_form&node_id=<?=$node->id?>', 450, 600)"<?=$disabledControlIfUserHasNoWriteRights?>>
<?php  } ?>
<?php  if ($ndMap->hasDeleteRight) { ?>
<input type=button class=button3 value="<?=$AdminTrnsl["Del"]?>" onClick="if(confirm('<?=$AdminTrnsl["This_will_delete_all_subnodes_Proceed"]?>')) location.href='/admin/nodes.php?do=deletenode&id=<?=$node->id?>'">
<?php  } ?>
</td>

<?php  if (sizeof($visibleDepends) != 0) { ?>
<td id=pad5 nowrap>
<?php  if (!$restrict_edit || $ndMap->hasWriteRight) {?>
<table border=0 cellpadding=0 cellspacing=0 width=1%>
<tr>
<td nowrap width=1%><b><?php /*<?=$AdminTrnsl["New_SubNode"]?>:*/?>&nbsp;</b></td>
<td>
	<?php  foreach($visibleDepends as $dep) { ?>
	<input type=button class=button1 onClick="location.href='/admin/nodes.php?parent_id=<?= $node->id ?>&do=addnode_form&class_id=<?= $dep->id ?>'" value="<?=$AdminTrnsl["CreateSubnode"]?> <?= $dep->name ?>"><br>
	<?php  } ?>
</table>
<?php  }?>
</td>
<?php }?>

<?php  if ($node->id != 1 && ($CurrentAdminUser->group->id == 1 ||  MANAGERS_CAN_CLONE)) { ?>
    <td id=pad5 nowrap  width=1%>
        <input type=button class=button1 value="<?= $AdminTrnsl["Clone"] ?>" onClick="if(confirm('<?= $AdminTrnsl["Are_you_sure"] ?>')) location.href='/admin/nodes.php?do=clone_node&id=<?= $node->id ?>&clonesubnodes=' + document.getElementById('cloneSubnodes').checked">
        <label><input type="checkbox" checked="checked" id="cloneSubnodes"> <?= $AdminTrnsl["Including_subnodes"] ?></label>
    </td>
<?php  } ?>

</tr>
</form>
</table>

</td>
</tr>
</table>
<?php  } ?>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18 class=back2>
<?php  usetemplate("nodes/editnode_in") ?>
</td>
</tr>

<?php  if ($childrenCount > 0) { ?>

<tr><td id=pad18>

<?php 
$pageNumMap = $request->getParameter("pagenum");
if ($pageNumMap == null) {
	$pageNumMap = array();
}

foreach($childrenByClasses as $item) {
	if (sizeof($item->nodes) == 0) {
		continue;
	}

	$showShortname = !$restrict_edit || $item->nodeClass->checkFlag("USE_SHORTNAME");
	$showNodeName = $item->nodeClass->checkFlag("USE_NODENAME");
	$nodeNameReadonly = $item->nodeClass->checkFlag("NODENAME_READONLY");
	$shownFieldDefs = $item->shownFields; 
?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr bgcolor=#ffffff>
<td class=td2 colspan=10 id=pad5>

<a name="<?="nodeslist".$item->nodeClass->shortname?>"></a>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<td><b><?=$AdminTrnsl["CClass"]?>: <?=$item->nodeClass->name?></b></td>

<td align=right class=small>
    <?php  if ($CurrentAdminUser->group->id == 1 || $item->nodeClass->checkFlag("SHOW_SORTING")) { ?>
        <a href="/admin/nodes.php?do=children_sort_class&node_id=<?=$node->id?>&class=<?=$item->nodeClass->shortname?>"><?=$AdminTrnsl["Sort"]?></a>
    <?php  } ?>
    <?php  if (!$restrict_edit) { ?><?=$item->nodeClass->shortname?><?php  } ?>
</td>

</table>

</td>
</tr>
</table>
<form action="/admin/nodes.php?do=deleteselectednode" onsubmit="return CheckForm(this)" method="post">
<input type="hidden" name="do1" value="0">
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr id=back2 class=header>
<?php  //if (!$restrict_edit || $nodeClass->checkFlag("RU_CAN_DELETE")) 
{ ?>
<td width=1%  class=td2><input type="checkbox" name="delete_all" value="1" onclick="SelectAllCheckBox(this)"></td>
<?php  }?>

<?php  if ($showShortname) { ?>
<?php /*<td width=1% class=td2><?=$AdminTrnsl["Shortname"]?></td>*/?>
<?=showTableHeadItem($AdminTrnsl["Shortname"], $item->nodeClass, 'shortname', $node, 'width=1%', 'td2')?>
<?php  } ?>
<?php  if ($showNodeName) { ?>
<?php /*<td<?if (sizeof($shownFieldDefs) !=0 ) {?> width=40%<?}?>><?=($item->nodeClass->nodeNameLabel ? $item->nodeClass->nodeNameLabel : $AdminTrnsl["Node_name"])?></td>*/?>
<?=showTableHeadItem(($item->nodeClass->nodeNameLabel ? $item->nodeClass->nodeNameLabel : $AdminTrnsl["Node_name"]), $item->nodeClass, 'name', $node,  (sizeof($shownFieldDefs) !=0 ? 'width=40%' : ''))?>
<?php  } ?>
<?php  foreach($shownFieldDefs as $fd) { ?>
<?php /*<td><?=$fd->name?></td>*/?>
<?=showTableHeadItem($fd->name, $item->nodeClass, $fd->shortname, $node)?>
<?php  } ?>
<td width=1%><?=$AdminTrnsl["Action"]?></td>
</tr>

<?php 
$c=0;
$tot=sizeof($item->nodes);
  
foreach($item->nodes as $child){
	$c++;
	if (!$child->hasWriteRight) {
		$child_disabledControlIfUserHasNoWriteRights = true;
	} else {
		$child_disabledControlIfUserHasNoWriteRights = false;
	}
?>
<tr id=back3>

<td><?php  if ($child->hasDeleteRight) { ?><input type="checkbox" name="delete_node[]" value="<?=$child->node->id?>" onclick="checkNode(this)"><?php  }else{?><input type="checkbox" disabled="disabled"><?php  }?></td>

<?php  if ($showShortname) { ?>
<td class=td2><?=$child->node->shortname?></td>
<?php  } ?>
<?php  if ($showNodeName) { ?>
<td>
<?php /* <a href="/admin/nodes.php?do=main&id=<?=$child->node->id?>"> */?>
<?=$child->node->name?>
<?php /* </a> */?>
</td>
<?php  } ?>
<?php  foreach($shownFieldDefs as $fd) { ?>
<td><?=$child->node->fields[$fd->shortname]->getHtmlVisualValue()?></td>
<?php  } ?>
<?php /* <?if ($item->sort) {?><td nowrap<? if ($c == $tot) { ?> align=right<? } else { ?><input type=button style="width:21px" class=button41><?}?><?if ($c!=1) {?><input type=button style="width:21px" class=button42><?}?></td><?}?> */?>
<td nowrap width=1%>
<!--<input type=button value="<?= $AdminTrnsl["Edit"] ?>" onClick="location.href='/admin/nodes.php?do=editnode&id=<?=$child->node->id?>'" class=button1>
<?php  if (!$restrict_edit || $item->nodeClass->checkFlag("RU_CAN_DELETE")) { ?>
<input type=button value="<?=$AdminTrnsl["Del"]?>" class=button4 onClick="if(confirm('<?=$AdminTrnsl["This_will_delete_all_subnodes_Proceed"]?>')) location.href='/admin/nodes.php?do=deletenode&id=<?=$child->node->id?>'"<?=$child_disabledControlIfUserHasNoWriteRights?>>
<?php  } ?>-->
<?php  if ($child->hasWriteRight) { ?>
<a href="/admin/nodes.php?do=editnode&id=<?=$child->node->id?>"><img src="/admin/_img/ico/edit.jpg" border="0" title='<?=$AdminTrnsl["Generic_Edit_Full"]?>' id='hint' ></a>
<?php  }else{?>
<img src="/admin/_img/ico/edit_d.jpg" border="0">
<?php  }?>
<?php  if ( $child->hasDeleteRight) { ?>
<a href="javascript:if(confirm('<?=$AdminTrnsl["This_will_delete_all_subnodes_Proceed"]?>')) location.href='/admin/nodes.php?do=deletenode&id=<?=$child->node->id?>'"><img src="/admin/_img/ico/delete.jpg" border="0" title='<?=$AdminTrnsl["Delete"]?>' id='hint'></a>
<?php  }else{ ?>
	<img src="/admin/_img/ico/delete_d.jpg" border="0">
<?php  }?>
<?php  if ($CurrentAdminUser->group->id == 1 ||  MANAGERS_CAN_CLONE) { ?>
        <a href="javascript:if(confirm('<?= $AdminTrnsl["Are_you_sure"] ?>')) location.href='/admin/nodes.php?do=clone_node&id=<?=$child->node->id?>&clonesubnodes=true'"><img src="/admin/_img/ico/clone.png" border="0" title='<?= $AdminTrnsl["Clone"] ?>'></a>
<?php  } ?>
</td>
</tr>
<?php  } ?>
</table>
	<div><input type="submit" name="deleted_btn" value="<?=$AdminTrnsl["Del"]?>" style="visibility:hidden" class=button4 onclick="this.form.action='/admin/nodes.php?do=deleteselectednode'; this.form['do1'].value=0;">
            <?php  if ($CurrentAdminUser->group->id == 1 ||  MANAGERS_CAN_MOVE) { ?>
                <input type="submit" name="moved_btn" value="<?=$AdminTrnsl["Move"]?>" style="visibility:hidden" class=button2 onclick="this.form.action='/admin/nodes.php?do=moveselectednode'; this.form['do1'].value=1;">
            <?php  } ?>
        </div>
</form>
<?php  if($item->nodespagesCount > 1) { ?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<tr id=back3>
<td class=td2 colspan=10 id=pad5>
<?php 
$hiddenNames = array();
$hiddenValues = array();
foreach($pageNumMap as $_k=>$_v) {
	if($_k == $item->nodeClass->shortname) continue;
	$hiddenNames[] = "pagenum[".$_k."]";
	$hiddenValues[] = $_v;
}
$hiddenNames[] = "do";
$hiddenValues[] = "main";
$hiddenNames[] = "id";
$hiddenValues[] = $node->id;

HTMLWriter::draw_paging_links($item->nodesOffset, NODES_PER_PAGE, NODELINKS_PER_PAGE, $item->nodesCount,
	$hiddenNames,
	$hiddenValues,
	"_default_draw_paging_link",
	"#nodeslist".$item->nodeClass->shortname,	
	"pagenum[".$item->nodeClass->shortname."]"
	)?>
</td>
</tr>
</table>
<?php  } ?>

<?php  } ?>

</td>
</tr>
<?php  } ?>
</table>

<p>
<script language="javascript">
	function SelectAllCheckBox(ch)
	{
		var form=ch.form;
		
		for(i=0;i<form.length;i++){
			el=form.elements[i];
			if(el.type=="checkbox"&&!el.disabled)
			el.checked=ch.checked;
			//alert(el.type);
		}
		//form["deleted_btn"].style.visibility=(ch.checked)?"visible":"hidden";
		checkNode(ch);
	}
	function CheckForm(form)
	{
		
		var is_move=(form['do1'].value==1)?true:false;
		var res=false;
		var ids=new Array();
		for(i=0;i<form.length;i++){
			el=form.elements[i];
			if(el.type=="checkbox"&&el.name!="delete_all"&&el.checked){
				res=true;
				ids[ids.length]=el.value;
			}
		}
		if(!res){
			if(is_move){
				alert("<?=$AdminTrnsl["Msg_SelectItemsToMove"]?>");
			}else{
				alert("<?=$AdminTrnsl["Msg_SelectItemsToDelete"]?>");
			}
			return false;
		}
		if(is_move){
			if(confirm('<?=$AdminTrnsl["Confirm_MoveSections"]?>')){
				wop('/admin/nodes.php?do=moveselectednode&node_id='+ids[0]+'&node_ids='+ids.join(','), 450, 600);	
			}
			return false;
		}else{
			return (confirm('<?=$AdminTrnsl["This_will_delete_all_subnodes_Proceed"]?>'));
		}
	}
	function checkNode(ch)
	{
		var res=false;
		var form=ch.form;
		if(ch.checked&&ch.name!="delete_all"){
			form["deleted_btn"].style.visibility=(ch.checked)?"visible":"hidden";
			form["moved_btn"].style.visibility=(ch.checked)?"visible":"hidden";
		}else{
			for(i=0;i<form.length;i++){
				el=form.elements[i];
				if(el.type=="checkbox"&&el.name!="delete_all"&&el.checked)
				res=true;
			}
			form["deleted_btn"].style.visibility=(res)?"visible":"hidden";
			form["moved_btn"].style.visibility=(res)?"visible":"hidden";
			if(!res&&form["delete_all"].checked)form["delete_all"].checked=false;
		}
	}
</script>
</body>
</html>
