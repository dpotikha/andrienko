<?php
require_once("prepend.php");
$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
if (!$currentLoggedUser->group->canManageSettings)
    die('No rights');
$superadmin = $CurrentAdminUser->group->id == 1 ? 1 : 0;

if (isset($_GET['remove']) && isset($_POST['toremove'])) {
    $toremove = explode('|', $request->getParameter("toremove"));
    foreach ($toremove as $k => $v)
        $toremove[$k] = (int)$v;
    if (sizeof($toremove))
        $MDB->QDel("dbm_settings", 'id=' . implode(" OR id=", $toremove));
    die ('true');
}
if (!$superadmin) {
    $MDB->Query("SELECT id FROM dbm_settings WHERE rights = 2");
    $c = $MDB->getRecords();
    $editableForManager = array();
    if ($c)
    foreach ($c as $k => $v)
        $editableForManager[] = $v['id'];
}

if (isset($_POST['settingsForm'])) {
    $ids = $_POST['ids'];
    $shortnames = $_POST['shortnames'];
    $values = $_POST['values'];
    $info = $_POST['info'];
    for ($i = 0; $i < sizeof($ids); $i ++) {
        if ($superadmin && $ids[$i] == 0 && strlen(db_escape($shortnames[$i])) && strlen(db_escape($values[$i])))
            $MDB->QIns("dbm_settings", array('shortname' => db_escape($shortnames[$i]), 'value' => db_escape($values[$i]), 'rights' => (int)($_POST['rightsNew_' . $info[$i]])));
        if ($superadmin && $ids[$i] != 0 && strlen(db_escape($shortnames[$i])) && strlen(db_escape($values[$i])))
            $MDB->QUpdate("dbm_settings", array('shortname' => db_escape($shortnames[$i]), 'value' => db_escape($values[$i]), 'rights' => (int)($_POST['rights' . $ids[$i]])), "id = " . (int)$ids[$i]);
        if (!$superadmin && $ids[$i] != 0 && in_array($ids[$i], $editableForManager) && strlen(db_escape($values[$i])))
            $MDB->QUpdate("dbm_settings", array('value' => db_escape($values[$i])), " id = " . (int)$ids[$i]);
    }
}

$MDB->Query("SELECT * FROM dbm_settings ORDER BY id");
$settings = $MDB->getRecords();
?>
<html>
    <head>
        <meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
        <meta http-equiv="Expires" content="0">
        <meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
        <meta http-equiv="pragma" content="no-cache">
        <title>Admin Module</title>
        <link rel=stylesheet href="/admin/css.css" type="text/css" />
        <script type="text/javascript" src="/admin/jquery/js/jquery-1.3.2.min.js"></script>
        <link rel="stylesheet" href="/admin/js/prettyLoader/css/prettyLoader.css" type="text/css" media="screen" charset="utf-8" />
        <script src="/admin/js/prettyLoader/js/jquery.prettyLoader.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript">
            <?php  if ($superadmin) { ?>
            function remove(toremove) {
                $('#bigButtons').hide();
                $.prettyLoader.show(); freeze = true;
                $.post("/admin/settings.php?remove",
                    {
                        'toremove' : toremove.join('|')
                    },
                    function(data){
                        for (i in toremove)
                            $('#row' + toremove[i]).remove();
                        $.prettyLoader.hide(); freeze = false;
                    }
                );
            }

            function in_array (needle, haystack, argStrict) {
                // http://kevin.vanzonneveld.net
                // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
                // +   improved by: vlado houba
                // +   input by: Billy
                // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
                // *     example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);
                // *     returns 1: true
                // *     example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
                // *     returns 2: false
                // *     example 3: in_array(1, ['1', '2', '3']);
                // *     returns 3: true
                // *     example 3: in_array(1, ['1', '2', '3'], false);
                // *     returns 3: true
                // *     example 4: in_array(1, ['1', '2', '3'], true);
                // *     returns 4: false
                var key = '',
                    strict = !! argStrict;

                if (strict) {
                    for (key in haystack) {
                        if (haystack[key] === needle) {
                            return true;
                        }
                    }
                } else {
                    for (key in haystack) {
                        if (haystack[key] == needle) {
                            return true;
                        }
                    }
                }

                return false;
            }

                <?php  } ?>
            n = 0;
            freeze = false;
            $(document).ready(function(){
                $('#save').click(function(event){
                    var submit = true;
                    $("input[name='values[]']<?= $superadmin ? ", input[name='shortnames[]']" : '' ?>").each(function() {
                        if ($(this).val().length == 0 && submit) {
                            alert('<?=$AdminTrnsl["Msg_FillAllFields"]?>');
                            submit = false;
                            return;
                        }
                    });
                    <?php  if ($superadmin) { ?>
                    var shortnames = new Array;
                    $("input[name='shortnames[]']").each(function() {
                        if (in_array($(this).val(), shortnames) && submit) {
                            alert('<?=$AdminTrnsl["Msg_ShortnamesDuplicate"]?>');
                            submit = false;
                            return;
                        }
                        shortnames.push($(this).val())
                    });
                    <?php  } ?>
                    if (submit) $('#settingsForm').submit();
                });
                <?php  if ($superadmin) { ?>
                $.prettyLoader();
                $('#add').click(function(event){
                    $('#settingsTable').append('<tr id="newrow' + n + '"><td>\n\
                        <input type="hidden" value="' + n + '" name="info[]">\n\
                        <input type="hidden" value="0" name="ids[]">\n\
                        <input type="text" style="width: 100%;" value="" name="shortnames[]"></td>\n\
                        <td><input type="text" style="width: 100%;" value="" name="values[]"></td>\n\
                        <td><label><input type="radio" value="0" checked="checked" name="rightsNew_' + n + '"> <?=$AdminTrnsl["Rights_None"]?></label><label><input type="radio" value="1" name="rightsNew_' + n + '"> <?=$AdminTrnsl["Rights_View"]?></label><label><input type="radio" value="2" name="rightsNew_' + n + '"> <?=$AdminTrnsl["Rights_Edit"]?></label></td>\n\
                        <td>&nbsp;&nbsp;&nbsp;<a class="removeNewItem" rel="' + n + '" href="javascript:;"><img src="/admin/_img/ico/delete.jpg"></a></td>\n\
                        <td><input class="rowCheckbox rowCheckboxNew" type="checkbox" value="' + n + '"></td></tr>');
                        n ++;
                });        
                $('.removeExistingItem').live('click', function() {
                    if (!freeze) {
                        var id = $(this).attr('rel');
                        if (confirm('<?=$AdminTrnsl["Confirm_Delete"]?>')) {
                            var toremove = new Array();
                            toremove.push(id);
                            remove(toremove)
                        }
                    }
                });
                $('.removeNewItem').live('click', function() {
                    if (confirm('<?=$AdminTrnsl["Confirm_Delete"]?>'))
                        $('#newrow' + $(this).attr('rel')).remove();
                });
                $('.rowCheckbox').live('click', function() {
                    if (!freeze) {
                        var selected = new Array;
                        $(".rowCheckbox:checked").each(function() {
                            selected.push($(this).val())
                        });
                        if (selected.length)
                            $('#bigButtons').show();
                        else $('#bigButtons').hide();
                    }
                });
                 $('#allCheckbox').live('change', function() {
                    if (!freeze) {
                        if ($('#allCheckbox:checked').val()) {
                            $(".rowCheckbox").attr('checked', 'checked');
                            $('#bigButtons').show();
                        } else {
                            $(".rowCheckbox").attr('checked', '');
                            $('#bigButtons').hide();
                        }
                    }
                });
                $('#removeSelected').live('click', function() {
                    if (!freeze) {
                        if (confirm('<?=$AdminTrnsl["Confirm_Delete"]?>')) {
                            var selected = new Array;
                            $(".rowCheckbox.rowCheckboxExisting:checked").each(function() {
                                selected.push($(this).val())
                            });
                            $(".rowCheckbox.rowCheckboxNew:checked").each(function() {
                                $('#newrow' + $(this).val()).remove();
                            });
                            if (selected.length)
                                remove(selected)
                        }
                    }
                });
                <?php  } ?>
            });
        </script>

    </head>
    <body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

        <table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src="/admin/_img/s.gif" width="1" height="1"></td></table>
        <table width=100% border=0 cellpadding=4 cellspacing=0>
            <tr class=top41>
                <td height=30 id=pad18 colspan=2 nowrap class=head><b><?=$AdminTrnsl["Menu_Settings"]?></b></td>
            </tr>
        </table>
        <div style="padding:10px;">
        <form action="" id="settingsForm" method="post">
        <input type="hidden" value="1" name="settingsForm">
        <table width="100%" cellspacing="0" cellpadding="5" border="1" id="settingsTable" >
            <tbody>
                <tr>
                    <td style="width: 30%;"><b><?=$AdminTrnsl["Shortname_Label"]?></b></td>
                    <td><b><?=$AdminTrnsl["Value"]?></b></td>
                    <?php  if ($superadmin) { ?>
                        <td><b><?=$AdminTrnsl["Access"]?></b></td>
                        <td style="width: 50px;"><b><?=$AdminTrnsl["Action"]?></b></td>
                        <td style="width: 22px;"><input id="allCheckbox" type="checkbox" ></td>
                    <?php  } ?>
                </tr>
                <?php  if ($settings) { ?>
                    <?php  foreach ($settings as $k => $v) {
                        if ($superadmin || $v['rights'] != 0) { ?>
                            <tr id="row<?= $v['id']?>">
                                <td>
                                    <?php  if ($superadmin || $v['rights'] == 2) { ?>
                                        <input type="hidden" value="0" name="info[]">
                                        <input type="hidden" value="<?= $v['id']?>" name="ids[]">
                                    <?php  } ?>
                                    <?php  if ($superadmin) { ?>
                                        <input type="text" style="width: 100%;" value="<?= $v['shortname']?>" name="shortnames[]">
                                    <?php  } else { ?>
                                        <?= $v['shortname']?>
                                    <?php  } ?>
                                </td>
                                <td>
                                    <?php  if ($superadmin || $v['rights'] == 2) { ?>
                                        <input type="text" style="width: 100%;" value="<?= $v['value']?>" name="values[]">
                                    <?php  } else { ?>
                                        <?= $v['value']?>
                                    <?php  } ?>
                                </td>
                                <?php  if ($superadmin) { ?>
                                    <td>
                                        <label><input type="radio" value="0" <?= $v['rights'] == 0 ? 'checked="checked"' : '' ?> name="rights<?= $v['id']?>"> <?=$AdminTrnsl["Rights_None"]?></label>
                                        <label><input type="radio" value="1" <?= $v['rights'] == 1 ? 'checked="checked"' : '' ?> name="rights<?= $v['id']?>"> <?=$AdminTrnsl["Rights_View"]?></label>
                                        <label><input type="radio" value="2" <?= $v['rights'] == 2 ? 'checked="checked"' : '' ?> name="rights<?= $v['id']?>"> <?=$AdminTrnsl["Rights_Edit"]?></label>
                                    </td>
                                    <td>&nbsp;&nbsp;&nbsp;<a class="removeExistingItem" rel="<?= $v['id']?>" href="javascript:;"><img src="/admin/_img/ico/delete.jpg"></a></td>
                                    <td><input class="rowCheckbox rowCheckboxExisting" type="checkbox" value="<?= $v['id'] ?>"></td>
                                <?php  } ?>
                            </tr>
                    <?php  }} ?>
                <?php  } ?>
            </tbody>
        </table>
            </form>
            <a href="javascript:;" id="save"><img src="/admin/_img/ico/save.png"></a>
            <?php  if ($superadmin) { ?>
                <a href="javascript:;" id="add"><img src="/admin/_img/ico/plus-big.png"></a>
                <span id="bigButtons" style="display:none; float:right;"><a href="javascript:;" id="removeSelected"><img src="/admin/_img/ico/delete-big.png"></a></span>
            <?php  } ?>

        </div>
    </body>
</html>