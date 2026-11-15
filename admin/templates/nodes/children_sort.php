<?php $request->setAttribute("active_tab", "sorting");?>
<?=usetemplate("nodes/_edit_header")?>
 <style type="text/css" media="screen">
  ul.sortabledemo {
    list-style-image:none;
    list-style-type:none;
    margin:0px;
    padding:0px;
	padding:20px;
  }

  ul.sortabledemo li {
    padding:0px;
    margin:0px;
	border:1px solid #cccccc;
  	background-color:#ededed;
	padding-top:2px;
	padding-bottom:2px;
  }

  div.handle {
    background-color: #E8A400;
    color:white;
	padding-left:3px;
	padding-right:3px;
    cursor: move;
  }

  </style>
<script language="javascript">
function move_kid(kid,to){
    document.sort_form.kid_id.value=kid;
    document.sort_form.kid_act.value = to;
    document.sort_form.submit();
}
</script>
  <script src="/admin/js/scriptaculous/prototype.js" type="text/javascript"></script>
  <script src="/admin/js/scriptaculous/scriptaculous.js" type="text/javascript"></script>
  <script src="/admin/js/scriptaculous/unittest.js" type="text/javascript"></script>


<form action=/admin/nodes.php?do=children_sort&reload=1 method=post name=sort_form>
<input type=hidden name=action value='tree_order'>
<input type=hidden name=node_id value='<?=$node->id?>'>
<input type=hidden name=kid_id value='-1'>
<input type=hidden name=kid_act value='none'>
</form>

<form action=/admin/nodes.php?do=multi_children_sort&reload=1 method=post name=multi_sort_form>
<input type=hidden name=node_id value='<?=$node->id?>'>
<input type=hidden  name=action value='multi_tree_order'>
<input type=hidden id='node_ids' name='node_ids' value=''>
</form>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td id=pad18>
</td>
</tr></table>
<div style="padding-left:20px; padding-right:20px;_widht:100%;">
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr id=back2 class=header>
<td width=155 class=td2 style="padding-right:15px" ><b><?=$AdminTrnsl["Action"]?></b></td>
<td width="100%"><b><?=$AdminTrnsl["Name"]?></b></td>
</tr>
</table>
</div>
<ul class="sortabledemo" id="secondlist">
<?php forEach ($children as $k=> $child) {?>
<li id="secondlist_secondlist_<?=$child->id?>">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td><div class="handle" style="float:left">DRAG HERE</div></td>
    <td><div style="padding-left:10px; float:left"><div><div style="width:200px; float:left"><b><?=$child->shortname?></b></div></td>
    <td><div style="float:left"><?=$child->name?></div></td>
</tr>
</table>
</li>
<?php }?>
</ul>
<div style="padding-left:20px;">
<input id="save_btn" style="visibility:hidden" type="button" class=button2  onclick="document.forms['multi_sort_form'].submit()" value="<?=$AdminTrnsl["Save"]?>">
</div>
<script type="text/javascript">
 // <![CDATA[
   Sortable.create("secondlist",
     {/*dropOnEmpty:true,*/handle:'handle',containment:["secondlist"]/*,constraint:true*/
     ,onChange:function(){document.forms["multi_sort_form"]['node_ids'].value = Sortable.serialize('secondlist'); document.getElementById('save_btn').style.visibility="visible"; }});
 // ]]>
 </script>
</body>
</html>