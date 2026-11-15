<?php $request->setAttribute("active_tab", "permissions");?>
<?=usetemplate("nodes/_edit_header")?>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td id=pad18>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr class=top4><td id=pad1818>

<form action="/admin/nodes.php" method="POST">
<input type="hidden" name="do" value="permissions">
<input type="hidden" name="node_id" value="<?=$node->id?>">
<input type="hidden" name="action" value="update">

<!-- begin -->
<?php if ($CurrentAdminUser->group->canManageUsers) {?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab>
<tr><td colspan=4><h4><?=$AdminTrnsl["Perm_AccessToSection"]?></h4></td></tr>
<tr align=center id=back2>
    <td height=22 width=75%><?=$AdminTrnsl["Group"]?></td>
    <td><?=$AdminTrnsl["Read"]?></td>
    <td><?=$AdminTrnsl["Write"]?></td>
    <td><?=$AdminTrnsl["Generic_Deletion"]?></td>
</tr>
<?php $i=0;foreach ($groups as $g) { $gr = $rights[$i]; ?>
<?php if ($CurrentAdminUser->group->id != $g->id && $g->id != 1) {?>
<tr id=back3>
	<td><?=$g->name?></td>
	<td align=center><input type=checkbox name='<?=$g->id?>_r' value=1 <?php if ($gr->hasViewRight()) {?>CHECKED<?php }?>></td>
    <td align=center><input type=checkbox name='<?=$g->id?>_w' value=1 <?php if ($gr->hasWriteRight()) {?>CHECKED<?php }?>></td>
    <td align=center><input type=checkbox name='<?=$g->id?>_d' value=1 <?php if ($gr->hasDeleteRight()) {?>CHECKED<?php }?>></td>
</tr><?php }$i++;}?>

<?php /*<tr height=20 id=back2>
<td colspan=3>
<input type=checkbox name=recursive value=1>&nbsp; <?=$AdminTrnsl["Apply_this_rights_to_all_children_recursive"]?></td>
</tr>*/?>

<?php /*--------------*/?>
<tr><td colspan=4 style="padding-top:30px;"><h4><?=$AdminTrnsl["Perm_AccessToChildren"]?></h4></td></tr>
    <tr align=center id=back2>
    <td height=22 width=75%><?=$AdminTrnsl["Group"]?></td>
    <td><?=$AdminTrnsl["Read"]?></td>
    <td><?=$AdminTrnsl["Write"]?></td>
    <td><?=$AdminTrnsl["Generic_Deletion"]?></td>
</tr>
<?php $i=0;foreach ($groups as $g) { $gr = $rights[$i]; ?>
<?php if ($CurrentAdminUser->group->id != $g->id && $g->id != 1) {?>
<tr id=back3>
	<td><?=$g->name?></td>
	<td align=center><input type=checkbox name='<?=$g->id?>_r_c' value=1 <?php if ($gr->hasViewRightForChildren()) {?>CHECKED<?php }?>></td>
    <td align=center><input type=checkbox name='<?=$g->id?>_w_c' value=1 <?php if ($gr->hasWriteRightForChildren()) {?>CHECKED<?php }?>></td>
    <td align=center><input type=checkbox name='<?=$g->id?>_d_c' value=1 <?php if ($gr->hasDeleteRightForChildren()) {?>CHECKED<?php }?>></td>
</tr><?php }$i++;}?>
<?php /*--------------*/?>

<tr id=back2>
<td colspan=3>
<table>
<!--<input type=submit name=submit value="<?=$AdminTrnsl["Submit_changes"]?>" style="width:170px" size=32 class=button5>-->
<tr>
    <td>
<td><input type="checkbox" name="recursive" value="1"> <?=$AdminTrnsl["Perm_RecursiveApply"]?> <select name="run_group">
            <option value="0"><?=$AdminTrnsl["Perm_AllGroups"]?></option>
            <?php $i=0;foreach ($groups as $g) { if($g->id==1)continue; ?>
            <option value="<?=$g->id?>"><?=$g->name?></option>
            <?php  }?>
        </select></td>
<td><input type=submit name=submit value="<?=$AdminTrnsl["Submit_changes"]?>" style="width:170px" size=32 class="button5" /></td></tr>
</table>
</td>
</tr>
</form>
</table>
<?php }?>
<?php if (!$CurrentAdminUser->group->canManageUsers) {?><?=$AdminTrnsl["This_option_is_disabled_for_your_login"]?><?php }?>

</td>
</tr></table>
</td>
</tr></table>

</body>
</html>
