<?php  $request->setAttribute("active_tab", "comments"); ?>
<?= usetemplate("nodes/_edit_header"); ?>
<?php 
$node_id = (int) $request->getParameter("node_id");
$MDB->Query("SELECT count(id) as count FROM dbm_comments WHERE node_id = $node_id");
$comments_count = $MDB->NextResult();
$comments_count = $comments_count['count'];
?>
<style>
    .commentsListLeft {float: left;}
    .commentsListRight {float: right;}
    .clear {clear:both;}
    .commentLevel1 {margin-left: 40px;}
    .commentLevel2 {margin-left: 80px;}
    .commentLevel3 {margin-left: 120px;}
    .commentLevel4 {margin-left: 160px;}
    .commentLevel5 {margin-left: 200px;}
    .commentLevel6 {margin-left: 240px;}
    .commentLevel7 {margin-left: 280px;}
    .commentWrap {border-bottom: 1px dotted lightgray; padding: 8px 6px 3px 10px;}
    .nameInput {width: 400px;}
    .textInput {width: 400px; height:130px;}
    .addBlock {display:none; padding-top: 10px;}
    .addBlock td {vertical-align: top;}
    .saveTd {vertical-align: bottom !important;}
    .active0 {background-color: rgb(246, 220, 142);}
    .listingArea {padding:10px;}
    #bigButtons {float:right; display:none; padding: 10px;}
    #allCheckbox {margin-bottom: 8px;}
    .textBackup {display: none;}
    .commentsText {overflow: hidden;}
</style>
<script src="/admin/js/jquery.js" type="text/javascript"></script>
<link rel="stylesheet" href="/admin/js/prettyLoader/css/prettyLoader.css" type="text/css" media="screen" charset="utf-8" />
<script src="/admin/js/prettyLoader/js/jquery.prettyLoader.js" type="text/javascript" charset="utf-8"></script>
<script type='text/javascript' src='/admin/js/autocomplete/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="/admin/js/autocomplete/jquery.autocomplete.css" />


<script type="text/javascript">
     autocompleteOtions = {
            delay : 0,
            cacheLength : 1,
            mustMatch : false,
            minChars: 3,
            width: 260,
            selectFirst: false,
            scroll: true,
            scrollHeight: 350
        }

    freeze = false;
    function getCommentsList(page) {
        $.prettyLoader.show(); freeze = true;
        if (!(typeof(page) !== 'undefined' && page != null))
            page = $('#currentPage')? $('#currentPage').val() : 1;
        $.get("/admin/templates/nodes/comments_list.php",
            {
                'node_id' : <?=$node_id?>,
                'page' : page,
                'view' : $('input:radio[name=view]:checked').val()
            },
            function(data){
                $('#commentsList').html(data);
                $('#commentsList').width($('body').width() - 18);
                $.prettyLoader.hide(); freeze = false;
            }
        );
    }
    function removeComments(comments) {
        $('#bigButtons').hide();
        $.prettyLoader.show(); freeze = true;
        $.post("/admin/nodes.php?do=remove_comments",
            {
                'node_id' : <?=$node_id?>,
                'comments' : comments.join('|')
            },
            function(data){
                getCommentsList();
            }
        );
    }
    function blockComments(comments, active) {
        $.prettyLoader.show(); freeze = true;
        $.post("/admin/nodes.php?do=block_comments",
            {
                'node_id' : <?=$node_id?>,
                'comments' : comments.join('|'),
                'active' : active
            },
            function(data){
                getCommentsList();
            }
        );
    }
    function closeAllFields() {
        $('.commentsEditText').hide();
        $('.addBlock').hide();
        $('.commentsText').show();
    }

    $(document).ready(function(){
        $.prettyLoader();
        $('.addComment').live('click', function() {
            if (!freeze) {
                closeAllFields()
                var id = $(this).attr('rel');
                $('#addBlock' + id).toggle();
                $("#name" + id).unautocomplete()
                $("#name" + id).autocomplete("/admin/templates/nodes/comments_name_autocomplete.php", autocompleteOtions);
                $("#name" + id).result(function(event, data, formatted) {
//                    $('#user_id' + id).val('');
//                    if (data)
//                        $('#user_id' + id).val(data[1]);
//                    alert($('#user_id' + id).val())
                })
            }
        });
        $('.removeComment').live('click', function() {
            if (!freeze) {
                var id = $(this).attr('rel');
                if (confirm('<?=$AdminTrnsl["Confirm_DeleteComment"]?>')) {
                    var comments = new Array();
                    comments.push(id);
                    removeComments(comments)
                }
            }
        });
        $('.editComment').live('click', function() {
            if (!freeze) {
                closeAllFields()
                var id = $(this).attr('rel');
                $('#commentsText' + id).hide();
                $('#commentsTextAreaDiv' + id).show();
            }
        });
        $('.saveComment').live('click', function() {
            if (!freeze) {
                var id = $(this).attr('rel');
                var text = $('#commentsTextArea' + id).val();
                if (text.length == 0) {
                    alert('<?=$AdminTrnsl["Msg_EnterComment"]?>');
                    return;
                }
                $.prettyLoader.show(); freeze = true;
                $.post("/admin/nodes.php?do=save_comment&node_id=<?=$node_id?>",
                            {
                                'text' : text,
                                'id' : id
                            },
                            function(data){
                                getCommentsList();
                            }
                        );
            }
        });
        $('.cancelSubmitComment').live('click', function() {
            if (!freeze) {
                var id = $(this).attr('rel');
                $('#name' + id).val('');
                $('#text' + id).val('');
                $('#addBlock' + id).hide();
            }
        });
        $('.cancelSaveComment').live('click', function() {
            if (!freeze) {
                var id = $(this).attr('rel');
                $('#commentsTextAreaDiv' + id).hide();
                $('#commentsTextArea' + id).val($('#commentsBackupArea' + id).val());
                $('#commentsText' + id).show();
            }
        });
        $('.blockComment').live('click', function() {
            if (!freeze) {
                var id = $(this).attr('rel');
                if (confirm('<?=$AdminTrnsl["Confirm_BlockComment"]?>')) {
                    var comments = new Array();
                    comments.push(id);
                    blockComments(comments, 0)
                }
            }
        });
        $('.unblockComment').live('click', function() {
            if (!freeze) {
                var id = $(this).attr('rel');
                if (confirm('<?=$AdminTrnsl["Confirm_UnblockComment"]?>')) {
                    var comments = new Array();
                    comments.push(id);
                    blockComments(comments, 1)
                }
            }
        });
        $('.page').live('click', function() {
            if (!freeze) {
                getCommentsList($(this).attr('rel'));
            }
        });
        $('.submitComment').live('click', function() {
            if (!freeze) {
                var id = $(this).attr('rel');
                var name = $('#name' + id).val();
                var text = $('#text' + id).val();
                var parent_id = $('#parent' + id).val();
                if (name.length == 0) {
                    alert('<?=$AdminTrnsl["Msg_EnterAuthor"]?>');
                    return;
                }
                if (text.length == 0) {
                    alert('<?=$AdminTrnsl["Msg_EnterComment"]?>');
                    return;
                }
                $.prettyLoader.show(); freeze = true;
                $.post("/admin/nodes.php?do=add_comment&node_id=<?=$node_id?>",
                            {
                                'name' : name,
                                'text' : text,
                                'parent_id' : parent_id
                            },
                            function(data){
                                $('#addBlock' + id).hide();
                                $('#name' + id).val('');
                                $('#text' + id).val('');
                                if ($('input:radio[name=view]:checked').val() == 'list')
                                    data = 100000;
                                getCommentsList(data);
                            }
                        );
            }
        });
        $('.commentCheckbox').live('click', function() {
            if (!freeze) {
                var selected = new Array;
                $(".commentCheckbox:checked").each(function() {
                    selected.push($(this).val())
                });
                if (selected.length)
                    $('#bigButtons').show();
                else $('#bigButtons').hide();
            }
        });
        $('#removeSelected').live('click', function() {
            if (!freeze) {
                if (confirm('<?=$AdminTrnsl["Confirm_DeleteSelectedComments"]?>')) {
                    var selected = new Array;
                    $(".commentCheckbox:checked").each(function() {
                        selected.push($(this).val())
                    });
                    if (selected.length)
                        removeComments(selected)
                }
            }
        });
        $('#blockSelected').live('click', function() {
            if (!freeze) {
                if (confirm('<?=$AdminTrnsl["Confirm_BlockSelectedComments"]?>')) {
                    var selected = new Array;
                    $(".commentCheckbox:checked").each(function() {
                        selected.push($(this).val())
                    });
                    if (selected.length)
                        blockComments(selected, 0)
                }
            }
        });
        $('#unblockSelected').live('click', function() {
            if (!freeze) {
                if (confirm('<?=$AdminTrnsl["Confirm_UnblockSelectedComments"]?>')) {
                    var selected = new Array;
                    $(".commentCheckbox:checked").each(function() {
                        selected.push($(this).val())
                    });
                    if (selected.length)
                        blockComments(selected, 1)
                }
            }
        });
        $('input:radio[name=view]').live('change', function() {
            if (!freeze)
                getCommentsList(0);
        });

        $('#allCheckbox').live('change', function() {
            if (!freeze) {
                if ($('#allCheckbox:checked').val()) {
                    $(".commentCheckbox").attr('checked', 'checked');
                    $('#bigButtons').show();
                } else {
                    $(".commentCheckbox").attr('checked', '');
                    $('#bigButtons').hide();
                }
            }
        });
        getCommentsList();
    });
</script>
<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td id=pad18 style="padding-right:0;">

<table width=100% border=0 cellpadding=0 cellspacing=0>
    <tr class=top4>
        <td id=pad1818 style="padding:10px 0px 10px 0;">
        <table width=100% border=0 cellpadding=0 cellspacing=0 class="" style="padding:10px">
            <tr>
                <td colspan=4>
                    <h4 id="widthMeter">
                        <label><input type="radio" name="view" value="tree" checked="checked" /><?=$AdminTrnsl["Comments_ViewTree"]?></label>
                        <label><input type="radio" name="view" value="list" /><?=$AdminTrnsl["Comments_ViewList"]?></label>
                    </h4>
                </td>
            </tr>
            <tr>
                <td colspan=4>
                    <input type="button" value="<?=$AdminTrnsl["Comments_AddComment"]?>" class="button1 addComment" href="javascript:;" rel="0">
                    <?= showAddBlock(0) ?>
                </td>
            </tr>
        </table>
        <table width=100% border=0 cellpadding=0 cellspacing=0 class="">
            <tr><td colspan=4><div id="commentsList"></div></td></tr>
        </table>
        </td>
    </tr>
</table>
</td>
</tr></table>


</body>
</html>