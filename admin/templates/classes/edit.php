<?php 
$fieldTypes = FieldType::getAllTypes();

$MSG = null;
$rmsg = $request->getParameter("MSG");
if ($rmsg) {
	$MSG = $AdminTrnsl[$rmsg];
}
?>
<?php  usetemplate("classes/header") ?>
<script type="text/javascript" src="/admin/js/jquery.js"></script>
<script type="text/javascript" src="/admin/js/php.js"></script>
<script type="text/javascript" src="/admin/js/jquery.tablednd_0_5.js"></script>
<link rel="stylesheet" href="/admin/js/prettyLoader/css/prettyLoader.css" type="text/css" media="screen" charset="utf-8" />
<script src="/admin/js/prettyLoader/js/jquery.prettyLoader.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="/admin/js/prettyPhoto/css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
<script src="/admin/js/prettyPhoto/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><a href="/admin/classes.php" id=black><?=$AdminTrnsl["Classes_Management"]?></a>: <b><?=$AdminTrnsl["Edit_Class"]?></b></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<form action='/admin/classes.php' method='post' id="Paramsform">
<input type="hidden" name="do" value="update">
<input type=hidden name=class_id value='<?=$class->id?>'>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<?php if ($MSG != null) {?><tr>
<td id=pad18><span id="red"><?=$MSG?></span></td>
</tr><?php }?>
<tr>
<td id=pad18>
<br>
<table border=0 cellpadding=0 cellspacing=0 class=backtab3>
<tr id=back2>
<td width=250>ID:&nbsp;</td>
<td id=back3><?= $class->id ?></td>
</tr>
<tr id=back2>
<td width=250><?=$AdminTrnsl["Name"]?>:&nbsp;</td>
<td id=back3><input type=text name=name value="<?=prepareStringForXML($class->name)?>" size=45>
<?php if ($ERRORS['name'] != null) {?><div class=errmsg><?= $AdminTrnsl["CLASS_EDIT_ERROR_".$ERRORS['name']]?></div><?php }?></td>
</tr>
<tr id=back2>
<td><?=$AdminTrnsl["Shortname"]?> <span id=red>*</span></td>
<td id=back3><input type=text name=shortname value='<?=prepareStringForXML($class->shortname)?>' size=45>
<?php if ($ERRORS['shortname'] != null) {?><div class=errmsg><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$ERRORS['shortname']]?></div><?php }?></td>
</tr>
<tr id=back2>
<td><?=$AdminTrnsl["Default_template"]?>:&nbsp;</td>
<td id=back3><input type="text"  name=default_template value='<?=$class->default_template?>' size="45"></td>
</tr>
<tr id=back2>
<td><?=$AdminTrnsl["Nodename_label"]?>:&nbsp;</td>
<td id=back3><input type="text"  name=nodename_label value='<?=$class->nodeNameLabel?>' size="45"></td>
</tr>
<tr id=back2>
<td><?=$AdminTrnsl["Nodeclass_orderbyfield"]?>:&nbsp;</td>
<td id=back3><input type="text"  name="orderby" value='<?=$class->orderBy?>' size="45"></td>
</tr>
<tr id=back2>
<td><?=$AdminTrnsl["ShowNodesAtAdminInterface"]?>:&nbsp;</td>
<td id=back3><input type=checkbox name=show_at_adt value=1 <?php if ($class->showAtAdminTree) {?> CHECKED<?php }?>></td>
</tr>
<?php  global $NODECLASS_FLAG_NAMES; ?>
<?php  foreach ($NODECLASS_FLAG_NAMES as $flagName) { ?>
<tr id=back2>
<td><?= $AdminTrnsl["NODECLASS_FLAG_" . $flagName] ?>:&nbsp;</td>
<td id=back3><input type=checkbox name=flag_<?= $flagName ?> value=1 <?php if ($class->checkFlag($flagName)) {?> CHECKED<?php }?>></td>
</tr>
<?php  } ?>
<tr id=back2>
<td>&nbsp;</td>
<td id=back3><input type=submit value="<?=$AdminTrnsl["Submit_changes"]?>" class=button5></td>
</tr>
</table>

</td>
</tr>

<?php  if (1 || sizeof($fieldDefs) > 0) { ?>
<tr id="fieldDefsRow" <?= sizeof($fieldDefs) == 0 ? 'style="display:none;"' : ''?> >
<td id=pad18>

<div id=pad5><b class=h3><?=$AdminTrnsl["Field_list"]?>:</b></div>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2 id="parametersList">
<thead>
<tr align=center class="header nodrop nodrag" id=back2>
<td width=5%>ID</td>
<td width=2%><?php /* <?=$AdminTrnsl["Sort"]?> */?>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td width=20%><?=$AdminTrnsl["Shortname"]?></td>
<td width=25%><?=$AdminTrnsl["Name"]?></td>
<td width=20%><?=$AdminTrnsl["Type"]?></td>
<td width=10%><?=$AdminTrnsl["Required"]?></td>
<td width=10%><?=$AdminTrnsl["Shown_in_Nodes_List"]?></td>
<td width=10%><?=$AdminTrnsl["Hide_From_Users"]?></td>
<td width=10%><?=$AdminTrnsl["parameters"]?></td>
<td width=10%><?=$AdminTrnsl["Delete"]?></td>
</tr>
</thead>
<tbody>
<?php 
$k = 0;
if (sizeof($fieldDefs) > 0) forEach ($fieldDefs as $fd) { //edump($fd);?>
<tr id=back3 class="fdRow<?= $fd->id ?>">
<td width=5% nowrap><?= $fd->id ?><input type="hidden" value="<?= ++$k ?>" id="df_orders_<?= $fd->id ?>" name="df_orders_<?= $fd->id ?>" /></td>
<td nowrap><img border="0" onclick="remove(<?= $fd->id ?>)" src="/admin/_img/ico/delete.jpg" tooltip="<?=$AdminTrnsl["Delete"]?>"></td>
<td width=4% nowrap class="dragHandle">&nbsp;DRAG</td>
<td width=5% nowrap>&nbsp;<input type=button value="" style="width:21px" class=button41 onClick="moveDown(<?= $fd->id ?>)"><input type=button value="" style="width:21px" class=button42 onClick="moveUp(<?= $fd->id ?>)">&nbsp;</td>
<td width=20%><input type="text" name="df_shortnames_<?= $fd->id ?>" value="<?= prepareStringForHtml($fd->shortname)?>" style="width:100%">
<?php if ($FERRORS[$fd->id]['shortname'] != null) {?><div class=errmsg><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$FERRORS[$fd->id]['shortname']]?></div><?php }?></td>
<td width=20%><input type=text name="df_names_<?=$fd->id?>" style="width:100%" value="<?= prepareStringForHTML($fd->name) ?>">
<?php if ($FERRORS[$fd->id]['name'] != null) {?><div class=errmsg><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$FERRORS[$fd->id]['name']]?></div><?php }?></td>
<td width=20%><select name="df_types_<?=$fd->id?>" style="width:100%"><?php forEach ($fieldTypes as $ft) {?><option value="<?=$ft->id?>"<?php if ($ft->id == $fd->field_type) {?> SELECTED<?php }?>><?=$ft->name?><?php }?></select></td>
<td width=10% align=center><input type=checkbox name="df_required_<?=$fd->id?>" value=1<?php if ($fd->required) {?> CHECKED<?php }?>></td>
<td width=10% align=center><input type=checkbox name="df_shown_<?=$fd->id?>" value=1 <?php if ($fd->shown) {?> CHECKED<?php }?>></td>
<td width=10% align=center><input type=checkbox name="df_hide_<?=$fd->id?>" value=1 <?php if ($fd->hide) {?> CHECKED<?php }?>></td>
<td width=10% align=center nowrap>
    <input type=button value="<?=$AdminTrnsl["parameters"]?>" class="button1" onClick="params(<?= $fd->id ?>)">
<?php /*<input type=button value="<?=$AdminTrnsl["parameters"]?>" class="button1"
	onClick="location.href='/admin/classes.php?do=field_params&class_id=<?=$class->id?>&field_id=<?= $fd->id ?>';">*/?>
<input type=button value="<?=$AdminTrnsl["Clone"]?>" class="button1" onClick="clone(<?= $fd->id ?>);">
</td>
<td width=10% align=center nowrap>
<input type=checkbox name="df_delete_<?=$fd->id?>" value=1 <?php if ($fd->todelete) {?> CHECKED<?php }?>></td>
</tr>
<?php }?>
</tbody>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr id=back3 class="lastRow">
<td colspan=9 id=pad5>
<input type=submit value="<?=$AdminTrnsl["Submit_changes"]?>" class=button5>
</td>
</tr>
</table>

</td>
</tr>
<?php  } ?>
</table>
</form>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18>

<div id=pad5><b class=h3><?=$AdminTrnsl["Add_new_field"]?>:</b></div>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<form id="newFieldsForm" action=/admin/classes.php method=post>
<input type="hidden" name="do" value="add_field">
<input type=hidden name=class_id value='<?=$class->id?>'>
<tr align=center class=header id=back2>
<td width=15% height=22><?=$AdminTrnsl["Shortname"]?></td>
<td><?=$AdminTrnsl["Name"]?></td>
<td width=20%><?=$AdminTrnsl["Type"]?></td>
<td width=10%><?=$AdminTrnsl["Required"]?></td>
<td width=10%><?=$AdminTrnsl["Shown_in_Nodes_List"]?></td>
<td width=10%><?=$AdminTrnsl["Hide_From_Users"]?></td>
</tr>
<tr id=back3>
<td><input type=text style="width:100%" name=nfshortname value="<?=$nfparams->shortname?>"><div class=errmsg id="errorShortname">
<?php if ($AFERRORS['shortname'] != null) {?><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$AFERRORS['shortname']]?><?php }?></div></td>
<td><input type=text style="width:100%" name=nfname value="<?=$nfparams->name?>"><div class=errmsg id="errorName">
<?php if ($AFERRORS['name'] != null) {?><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$AFERRORS['name']]?><?php }?></div></td>
<td><select id="nffieldtype" name=nffieldtype style="width:100%"><?php forEach ($fieldTypes as $ft) {?><option value="<?=$ft->id?>"<?php if ($ft->id == $nfparams->field_type) {?> SELECTED<?php }?>><?=$ft->name?><?php }?></select></td>
<td align=center><input id="nfrequired" type=checkbox name=nfrequired value=1 <?php if ($nfparams->required == 1) {?> CHECKED<?php }?>></td>
<td align=center><input id="nfshown" type=checkbox name=nfshown value=1 <?php if ($nfparams->shown == 1) {?> CHECKED<?php }?>></td>
<td align=center><input id="nfhide" type=checkbox name=nfhide value=1 <?php if ($nfparams->hide == 1) {?> CHECKED<?php }?>></td>
</tr>

<tr id=back3>
<td colspan=6 id=pad5>
<input type=button onclick="add()" value="<?=$AdminTrnsl["Add"]?>" style="width:120px" class=button5>
</td>
</tr>
</form>
</table>

</td>
</tr>
</table>
<script type="text/javascript">
    jQuery.create = function() {
    if (arguments.length == 0) return [];
    var args = arguments[0] || {}, elem = null, elements = null;
    var siblings = null;

    // In case someone passes in a null object,
    // assume that they want an empty string.
    if (args == null) args = "";
    if (args.constructor == String) {
        if (arguments.length > 1) {
            var attributes = arguments[1];
                if (attributes.constructor == String) {
                            elem = document.createTextNode(args);
                            elements = [];
                            elements.push(elem);
                            siblings =
        jQuery.create.apply(null, Array.prototype.slice.call(arguments, 1));
                            elements = elements.concat(siblings);
                            return elements;

                    } else {
                            elem = document.createElement(args);

                            // Set element attributes.
                            var attributes = arguments[1];
                            for (var attr in attributes)
                                jQuery(elem).attr(attr, attributes[attr]);

                            // Add children of this element.
                            var children = arguments[2];
                            children = jQuery.create.apply(null, children);
                            jQuery(elem).append(children);

                            // If there are more siblings, render those too.
                            if (arguments.length > 3) {
                                    siblings =
        jQuery.create.apply(null, Array.prototype.slice.call(arguments, 3));
                                    return [elem].concat(siblings);
                            }
                            return elem;
                    }
            } else return document.createTextNode(args);
      } else {
              elements = [];
              elements.push(args);
              siblings =
        jQuery.create.apply(null, (Array.prototype.slice.call(arguments, 1)));
              elements = elements.concat(siblings);
              return elements;
      }
};

    fdCount = <?= sizeof($fieldDefs) ?>;
    blockAddition = false;

    function add() {
        if (blockAddition)
            return;
        blockAddition = true;
        $.prettyLoader.show();
        var nfname = $("input[name=nfname]").val();
        var nfshortname = $("input[name=nfshortname]").val();
        var nffieldtype = $("#nffieldtype").val();
        var nfrequired = $("#nfrequired:checked").val() != null ? 1 : 0;
        var nfshown = $("#nfshown:checked").val() != null ? 1 : 0;
        var nfhide = $("#nfhide:checked").val() != null ? 1 : 0;

        $.post("/admin/classes.php",
            {
                'do' : 'add_field',
                'nfname' : nfname,
                'nfshortname' : nfshortname,
                'nffieldtype' : nffieldtype,
                'nfrequired' : nfrequired,
                'nfshown' : nfshown,
                'nfhide' : nfhide,
                'class_id' : <?=$class->id?>
            },
            function(data){
                $("#errorName").html('');
                $("#errorShortname").html('');
                data = json_decode(data);
                if (data['result']) {
                    $.post("/admin/classes.php",
                        {
                            'do' : 'get_fdstring_html',
                            'nfshortname' : nfshortname,
                            'class_id' : <?=$class->id?>
                        },
                        function(data){
                            $('#fieldDefsRow').show();
                            fdCount ++;
                            $("#parametersList tbody").append(data);
                            $("input[name=nfname]").val('');
                            $("input[name=nfshortname]").val('');
                            $("#nfrequired").attr('checked', false);
                            $("#nfshown").attr('checked', false);
                            $("#nfhide").attr('checked', false);
                            $("#nffieldtype").val(0);
                            initializeDnD();
                            blockAddition = false;
                            $.prettyLoader.hide();
                        }
                    );
                } else {
                    $("#errorName").html(data['errors']['name']);
                    $("#errorShortname").html(data['errors']['shortname']);
                    blockAddition = false;
                    $.prettyLoader.hide();
                }
            }
        );
    }

    function clone(id) {
        if (blockAddition)
            return;
        if (confirm('<?=$AdminTrnsl["Are_you_sure"]?>')) {
            blockAddition = true;
            $.prettyLoader.show();
            $.post("/admin/classes.php",
                {
                    'do' : 'cloneField',
                    'field_id' : id,
                    'class_id' : <?=$class->id?>
                },
                function(data){
                    data = json_decode(data);
                    if (data['result']) {
                        $.post("/admin/classes.php",
                            {
                                'do' : 'get_fdstring_html',
                                'nfshortname' : data['result'],
                                'class_id' : <?=$class->id?>
                            },
                            function(data){
                                $('#fieldDefsRow').show();
                                fdCount ++;
                                $("#parametersList tbody").append(data);
                                $("input[name=nfname]").val('');
                                $("input[name=nfshortname]").val('');
                                $("#nfrequired").attr('checked', false);
                                $("#nfshown").attr('checked', false);
                                $("#nfhide").attr('checked', false);
                                $("#nffieldtype").val(0);
                                initializeDnD();
                                blockAddition = false;
                                $.prettyLoader.hide();
                            }
                        );
                    } else {
                        alert('Error')
                        blockAddition = false;
                        $.prettyLoader.hide();
                    }
                }
            );
        }
    }

    function remove(id) {
        if (blockAddition)
            return;
        if (confirm('<?=$AdminTrnsl["Are_you_sure"]?>')) {
            blockAddition = true;
            $.prettyLoader.show();
            $.post("/admin/classes.php",
                {
                    'do' : 'removeField',
                    'field_id' : id,
                    'class_id' : <?=$class->id?>
                },
                function(data){
                    data = json_decode(data);
                    if (data['result']) {
                        $('.fdRow' + id).remove();
                        fdCount --;
                        initializeDnD();
                        blockAddition = false;
                        $.prettyLoader.hide();
                        if (fdCount < 1)
                            $('#fieldDefsRow').hide();
                    } else {
                        alert('Error')
                        blockAddition = false;
                        $.prettyLoader.hide();
                    }
                }
            );
        }
    }

    function params(id) {
        $.prettyPhoto.open('/admin/classes.php?do=field_params&class_id=<?=$class->id?>&field_id=' + id +'?iframe=true&width=500&height=500','','');
    }
    function moveUp(id) {
        $('#df_orders_' + id).val();
        var row = $('.fdRow' + id);
        if ($('#df_orders_' + id).val() > 1) {
            $('#df_orders_' + id).val(Number($('#df_orders_' + id).val()) - 1);
            var prev = row.prev();
            var prevId = prev.attr('class').substr(5, 10);
            $('#df_orders_' + prevId).val(Number($('#df_orders_' + prevId).val()) + 1);
            row.insertBefore(prev);
        }
    }

    function moveDown(id) {
        $('#df_orders_' + id).val();
        var row = $('.fdRow' + id);
        if ($('#df_orders_' + id).val() < fdCount) {
            $('#df_orders_' + id).val(Number($('#df_orders_' + id).val()) + 1);
            var next = row.next();
            var nextId = next.attr('class').substr(5, 10);
            $('#df_orders_' + nextId).val(Number($('#df_orders_' + nextId).val()) - 1);
            row.insertAfter(next);
        }
    }

    function initializeDnD() {
        $("#parametersList").tableDnD({
	    onDragClass: "myDragClass",
	    onDrop: function(table, row) {
                var rows = table.tBodies[0].rows;
                for (var i=0; i<rows.length; i++) {
                    var id = $(rows[i]).attr('class').substr(5, 10);
                    $('#df_orders_' + id).val(i + 1);
                }
	    },
            dragHandle: "dragHandle"
	});
    }

    $(document).ready(function(){
        $.prettyLoader();
        initializeDnD();
        $("a[rel^='prettyPhoto']").prettyPhoto();
    });
</script>
</body>
</html>
