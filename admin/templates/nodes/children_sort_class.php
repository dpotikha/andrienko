<?php $request->setAttribute("active_tab", "sorting");?>
<?=usetemplate("nodes/_edit_header_sort_class")?>
 <style type="text/css" media="screen">
  
 .tDnD_whileDrag{
	  background-color:#CCC;	 
  }
  
  .even td{
	background-color:#FFF;	
  }
  .odd td{
	background-color:#E1E1E1;	
  }
  th{
	 font-size:11px;
	 background-color:#999
	  }
  td.dragHandle {
    background-color: #E8A400;
    color:white;
	padding-left:3px;
	padding-right:3px;
    cursor: move;
	width:90px;
  }
  .tDnD_whileDrag td.dragHandle{
	   background-color:#CCC;	 
  }
   .tDnD_whileDrag td{
	  background-color:#CCC; 
  }
  .even .dragHandle{
	background-color:#F90;  
  }
  #tableSort-1 td{
	border:1px solid #ccc;  
	}
 #tableSort-1 th{
	border:1px solid #fff;  
	}
  </style>
  <script src="/admin/js/jquery/jquery.js" type="text/javascript"></script>
  <script src="/admin/js/jquery/jquery.tablednd_0_5.js" type="text/javascript"></script>


<form action=/admin/nodes.php?do=children_sort&reload=1 method=post name=sort_form>
<input type=hidden name=action value='tree_order'>
<input type=hidden name=node_id value='<?=$node->id?>'>
<input type=hidden name=kid_id value='-1'>
<input type=hidden name=kid_act value='none'>
</form>

<?php 

    $shownFieldDefs = $node->shownFields; 
    $showShortname = !$restrict_edit || $node->nodeClass->checkFlag("USE_SHORTNAME");
	$showNodeName = $node->nodeClass->checkFlag("USE_NODENAME");

?>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td id=pad18>
</td>
</tr></table>
<div style="padding-left:20px; padding-right:20px;_widht:100%;">
<input type="checkbox" checked="checked" value="1" onclick="javascript:showHideImages(this)"><?=$AdminTrnsl["Show_Images"]?>
<input type="checkbox" checked="checked" value="1" onclick="javascript:showSmallImages(this)" id="showSmallImages"><?=$AdminTrnsl["Show_Small_Images"]?>
<table cellpadding="3" cellspacing="0" border="0"  id="tableSort-1" width="100%">
<tr class="nodrop nodrag">
<th>&nbsp;</th>
<?php  if ($showShortname) { ?>
<th class=td2><?=$AdminTrnsl["Shortname"]?></th>
<?php  } ?>
<?php  if ($showNodeName) { ?>
<th<?php if (sizeof($shownFieldDefs) !=0 ) {?> width=40%<?php }?>><?=($item->nodeClass->nodeNameLabel ? $item->nodeClass->nodeNameLabel : $AdminTrnsl["Node_name"])?></th>
<?php  } ?>
<?php  foreach($shownFieldDefs as $fd) { ?>
<th><?=$fd->name?></th>
<?php  } ?>

</tr>
<?php 
	$sorts=array();
forEach ($children as $k=> $child) {
    $sorts[]=$child->subtreeOrder;
?>
<tr id="<?=$child->id?>">
	<td class="dragHandle">DRAG HERE</td>
	<?php  if ($showShortname) { ?>
    <td><?=$child->shortname?></td>
    <?php  }?>
    <?php  if ($showNodeName) { ?>
    <td><?=$child->name?></td>
    <?php  }?>
<?php  foreach($shownFieldDefs as $fd) { ?>
<td><?=$child->fields[$fd->shortname]->getHtmlVisualValue()?>&nbsp;</td>
<?php  } ?>

</tr>

<?php  }?>
</table>
</div>
<form action=/admin/nodes.php?do=multi_children_sort_class&reload=1 method=post name=multi_sort_form>
<input type=hidden name=node_id value='<?=$node->id?>'>
<input type=hidden name=class value='<?=$_GET['class']?>'>
<input type=hidden name=sorts value='<?=implode(',',$sorts)?>'>
<input type=hidden  name=action value='multi_tree_order_class'>
<input type=hidden id='node_ids' name='node_ids' value=''>
</form>
<div style="padding-left:20px; padding-bottom:20px;">
<input id="save_btn" style="display:none" type="button" class=button2  onclick="document.forms['multi_sort_form'].submit()" value="<?=$AdminTrnsl["Save"]?>">
</div>
<script type="text/javascript">
function showHideImages(el){
		$('#tableSort-1 img').each(function (){
				if(!el.checked)
				$(this).hide();		
				else
				$(this).show();		
		});
}
function showSmallImages(el){
		$('#tableSort-1 img').each(function (){
				if(el.checked)
				$(this).css({'height':'30px'});		
				else
				$(this).css({'height':'auto'});	
		});
}
$(document).ready(function() {
		$('#tableSort-1 tr:even').addClass('odd');
		$('#tableSort-1 tr:odd').addClass('even');
		showSmallImages($('#showSmallImages')[0]);
		$('#tableSort-1').tableDnD({
				onDrop: function(table, row) {
					$('#tableSort-1 tr:even').removeClass('even');	
					$('#tableSort-1 tr:even').addClass('odd');
					$('#tableSort-1 tr:odd').removeClass('odd');	
					$('#tableSort-1 tr:odd').addClass('even');
					$('#node_ids').val($('#tableSort-1').tableDnDSerialize());
					$('#save_btn').show();
				},
				onDragClass: "tDnD_whileDrag",
				dragHandle: "dragHandle"
		});	
});	

 </script>
</body>
</html>