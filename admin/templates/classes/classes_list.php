<?php 
$MSG = null;
$rmsg = $request->getParameter("MSG");
if ($rmsg) {
	$MSG = $AdminTrnsl[$rmsg];
}
?>
<?php  usetemplate("classes/header") ?>
<script type="text/javascript" src="/admin/js/jquery.js"></script>
<script type="text/javascript" src="/admin/js/php.js"></script>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><b><?=$AdminTrnsl["Classes_Management"]?></b></td>
</tr>
<?php if ($MSG != null) {?><tr>
<td id=pad18><span id="red"><?=$MSG?></span></td>
</tr><?php }?>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top4>
<td height=36 id=pad18 nowrap><input type=submit value="<?=$AdminTrnsl["Create_New_Class"]?>" title="" style="width:150px" class=button1 onClick="location.href='/admin/classes.php?do=create_form';">
</tr>
<tr>
<td id=pad18>

<br>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr id=back2 class=header>
<td width=1%>ID</td>
<td width=70%><a href="/admin/classes.php?sortby=name"><?=$AdminTrnsl["CClass"]?></a></td>
<td width=1%><?=$AdminTrnsl["NODECLASS_FLAG_CACHE"]?></td>
<td width=1%><a href="/admin/classes.php?sortby=shortname"><?=$AdminTrnsl["Shortname"]?></a></td>
<td><?=$AdminTrnsl["Actions"]?></td>
<td><input type="checkbox" id="markAll"></td>
</tr>
<?php  foreach ($classes as $nc) { ?>
<tr id=back3>
<td><?= $nc->id ?></td>
<td ><a href=/admin/classes.php?do=edit&class_id=<?=$nc->id?>><?= $nc->name ?></a></td>
<td><?= $nc->checkFlag("CACHE") ? $AdminTrnsl["Yes"] : $AdminTrnsl["No"] ?></td>
<td><?= $nc->shortname ?></td>
<td nowrap align=center>
	<input type=button value="<?=$AdminTrnsl["Class_Dependences"]?>" onclick="document.location.href='/admin/classes.php?do=depends&class_id=<?=$nc->id?>'" class=button1>
        	<input type=button value="<?=$AdminTrnsl["Clone"]?>" onclick="document.location.href='/admin/classes.php?do=cloneClass&class_id=<?=$nc->id?>'" class=button1>
	<input type=button value="<?=$AdminTrnsl["Delete"]?>" onclick='if(confirm("<?=$AdminTrnsl["Warning_All_children_of_this_class_will_be_removed_to"]?>")) document.location.href="/admin/classes.php?do=delete&class_id=<?=$nc->id?>"' class=button3>
</td>
<td>
        <input type="checkbox" class="deleteCheckbox"  value="<?=$nc->id?>" />
</td>
</tr><?php }?>
</table>
<tr colspan=7>
    <td>
        <div style='float:right; margin-right:10px'><input type="button" id="deleteFields" value="<?=$AdminTrnsl["Delete"]?>" style="display:none" class=button4 ></div>
    </td>
</tr>

<br>
</td>
</tr>
<form action="/admin/classes.php" method=POST>
<input type="hidden" name="do" value="create_form">
<tr class=top4>
<td height=36 id=pad18 nowrap><input type=submit value="<?=$AdminTrnsl["Create_New_Class"]?>" title="" style="width:150px" class=button1>
</tr>
</form>
</table>
<script type="text/javascript">
    $(document).ready(function(){
        function updateDeleteButton() {
             if ($(".deleteCheckbox:checked").length > 0)
                 $('#deleteFields').show();
             else $('#deleteFields').hide();
        }
        $('.deleteCheckbox').change(function(){
            updateDeleteButton();
        });
        $('#markAll').change(function(){
            if ($('#markAll').is(':checked'))
                $(".deleteCheckbox").attr('checked', true);
            else $(".deleteCheckbox").attr('checked', false);
            updateDeleteButton();
        });
        $('#deleteFields').click(function(event){
            if (confirm('<?=$AdminTrnsl["Warning_All_children_of_this_classes_will_be_removed_to"]?>')) {
                var classes = new Array();
                $(".deleteCheckbox:checked").each(function(index) {
                    classes.push($(this).val());
                });
                document.location.href="/admin/classes.php?do=delete&class_id=" + implode(',', classes);
            }
        });
    });
</script>
</body>
</html>