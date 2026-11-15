<?php
//dump($request);exit;
?>
<html>
    <head>
        <meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
        <meta http-equiv="Expires" content="0">
        <meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
        <meta http-equiv="pragma" content="no-cache">
        <title>Admin Module</title>
        <link rel=stylesheet href="/admin/css.css" type="text/css" />
        <link rel=stylesheet href="/admin/jquery/css/smoothness/jquery-ui-1.7.2.custom.css" type="text/css" />
        <script type="text/javascript" src="/admin/jquery/js/jquery-1.3.2.min.js"></script>
        <script type="text/javascript" src="/admin/jquery/js/jquery-ui-1.7.2.custom.min.js"></script>
        <script type="text/javascript" src="/admin/jquery/js/jquery.validate.js"></script>
        <script type="text/javascript" src="/admin/jquery/js/jquery.form.js"></script>

        <link rel=stylesheet href="/admin/js/calendar/calendar-win2k-cold-2.css" type="text/css">
        <script type="text/javascript" src="/admin/js/calendar/calendar.js"></script>
        <script type="text/javascript" src="/admin/js/calendar/ru.js"></script>
        <script type="text/javascript" src="/admin/js/calendar/calendar-setup.js"></script>





        <link rel="stylesheet" type="text/css" media="screen" href="/admin/jquery/jqgrid/css/ui.jqgrid.css" />
        <script src="/admin/jquery/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
        <script type="text/javascript">
            $.jgrid.no_legacy_api = true;
            $.jgrid.useJSON = true;
        </script>
        <?php /*<script src="/admin/jquery/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
        <script src="/admin/jquery/jqgrid/src/ui.multiselect.js" type="text/javascript"></script>
        <script src="/admin/jquery/jqgrid/src/JsonXml.js" type="text/javascript"></script>*/?>

        <script type="text/javascript" src="/admin/tiny_mce/tiny_mce.js"></script>
        <script type="text/javascript" src="/admin/js/editor2.js"></script>

        <style>
            input.ui-pg-input{
                height: 16px!important;
            }
            .jqgrid-rownum{
                /* width: 35px!important;*/
            }
        </style>


    </head>
    <body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

        <table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src="/admin/_img/s.gif" width="1" height="1"></td></table>
        <table width=100% border=0 cellpadding=4 cellspacing=0>
            <tr class=top41>
                <td height=30 id=pad18 colspan=2 nowrap class=head><b><?=$AdminTrnsl["Post_Management"]?></b></td>
            </tr>
        </table>



        <div id="tabs">
            <ul>
                <li><a href="/admin/delivery.php?do=delivery"><?=$AdminTrnsl["Delivery_delivery"]?></a></li>
                <li><a href="/admin/delivery.php?do=subscribers2&page=<?=isset($_GET['page']) ? $_GET['page'] : 1?>&keyword=<?=isset($_GET['keyword']) ? $_GET['keyword'] : ''?>"><?=$AdminTrnsl["Delivery_subscribers"]?></a></li>
                <li><a href="/admin/delivery.php?do=categories"><?=$AdminTrnsl["Delivery_subscribers_categories"]?></a></li>
                <?php /*<li><a href="/admin/delivery.php?do=subscribers"><?=$AdminTrnsl["Delivery_subscribers"]?></a></li>*/?>
            </ul>

        </div>

        <div id="dialog"></div>
        <script type="text/javascript">
            var USE_FULL_URLS=true;
            function toggleEditor(id) {
                if (!tinyMCE.get(id))
                    tinyMCE.execCommand('mceAddControl', false, id);
                else
                    tinyMCE.execCommand('mceRemoveControl', false, id);
            }
            function checkForm(form){
                return false;
            }
            
            function showCreateDialog()
            {
                //alert( $('#dialog').get(0));
                $('#dialog').show();
                $('#dialog').html('<div class="shadow_right"><div class="backg_white" id="content">Please wait. Loading...</div></div>');
                $('#dialog').dialog({
                    bgiframe: true,
                    resizable:false,
                    close:function(){
                        toggleEditor('mail_body');
                        $(this).dialog('destroy');
                    },
                    open:function(){
                        $('.ui-dialog-buttonpane',$(this).parent()).append('<div class="corner_bottom_right"><div class="corner_bottom_left"></div></div>');
                        $.post('/admin/delivery.php?do=create',{},function(data){
                            $('#content',$('#dialog')).html(data);
                        });

                    },
                    height:800,
                    width:800,
                    modal: true,
                    closeText:'',
                    overlay: {
                        backgroundColor: '#000',
                        opacity: 0.5
                    },
                    title: '<?=$AdminTrnsl["Delivery_create_new"]?>',
                    buttons: {
                        '<?=$AdminTrnsl["Delivery_create_new"]?>': function() {
                            if($('#subject').val()!=''){
                             $('#createForm').get(0).submit();
                            }else{
                                alert('<?=$AdminTrnsl["Delivery_input_subject"]?>');
                            }
                            /*var edit_options = {
                                success			:   successResponse,
                                beforeSubmit 	: 		beforeSubmit
                            };
                            function beforeSubmit(formData, jqForm, options) {
                            }
                            function successResponse(responseText, statusText)  {
                                if(responseText=='success')
                                {
                                    $("#tabs").tabs('load',TAB_INDEX);
                                    $('#dialog').dialog('destroy');
                                }else{
                                    alert(responseText);
                                }
                            }*/




                        },
                        '<?=$AdminTrnsl["Delivery_cancel"]?>': function() {
                            //$('textarea.tinymce').tinymce().hide();
                            toggleEditor('mail_body');
                            $(this).dialog('destroy');
                        }
                    }
                });

            }
            function showEditDialog(id)
            {
                
            }
            var TAB_INDEX=0;
            $(function() {

                $('#tabs').tabs({
                    selected: <?=isset($_GET['selected_tab']) ? $_GET['selected_tab'] : 0?>,
                    select: function(event, ui) {
                        TAB_INDEX=ui.index;
                        return true;
                    }
//                    load: function(event, ui) {
//                        $('a', ui.panel).click(function() {
//                            $(ui.panel).load(this.href);
//                            return false;
//                        });
//                    }
                });

            });
            function DeleteD(id){
                if(confirm('<?=$AdminTrnsl["Delivery_shure"]?>')){
                    location.href='/admin/delivery.php?do=delete&id='+id;
                }
            }
            function DeleteC(id){
                if(confirm('<?=$AdminTrnsl["Delivery_shure2"]?>')){
                    location.href='/admin/delivery.php?do=delete_category&id='+id;
                }
            }
            function DeleteS(id){
                if(confirm('<?=$AdminTrnsl["Delivery_shure"]?>')){
                    location.href='/admin/delivery.php?do=delete_subscriber&id='+id;
                }
            }
            function EditD(id){
                //alert( $('#dialog').get(0));
                $('#dialog').show();
                $('#dialog').html('<div class="shadow_right"><div class="backg_white" id="content">Please wait. Loading...</div></div>');
                $('#dialog').dialog({
                    bgiframe: true,
                    resizable:false,
                    close:function(){
                        toggleEditor('mail_body');
                        $(this).dialog('destroy');
                    },
                    open:function(){
                        $('.ui-dialog-buttonpane',$(this).parent()).append('<div class="corner_bottom_right"><div class="corner_bottom_left"></div></div>');
                        $.post('/admin/delivery.php?do=edit&id='+id,{},function(data){
                            $('#content',$('#dialog')).html(data);
                        });

                    },
                    height:800,
                    width:800,
                    modal: true,
                    closeText:'',
                    overlay: {
                        backgroundColor: '#000',
                        opacity: 0.5
                    },
                    title: '<?=$AdminTrnsl["Delivery_edit"]?>',
                    buttons: {
                        '<?=$AdminTrnsl["Delivery_save_without_send"]?>': function() {
                            if($('#subject').val()!=''){
                             $('#resend').val(0);
                             $('#createForm').get(0).submit();
                            }else{
                                alert('<?=$AdminTrnsl["Delivery_input_subject"]?>');
                            }
                        },
                        '<?=$AdminTrnsl["Delivery_save_resend"]?>': function() {
                            if($('#subject').val()!=''){
                             $('#resend').val(1);
                             $('#createForm').get(0).submit();
                            }else{
                                alert('<?=$AdminTrnsl["Delivery_input_subject"]?>');
                            }
                        },
                        '<?=$AdminTrnsl["Delivery_cancel"]?>': function() {
                            //$('textarea.tinymce').tinymce().hide();
                            toggleEditor('mail_body');
                            $(this).dialog('destroy');
                        }
                    }
                });

            }
            function showCreateCatDialog(){
                $('#dialog').show();
                $('#dialog').html('<div class="shadow_right"><div class="backg_white" id="content">Please wait. Loading...</div></div>');
                $('#dialog').dialog({
                    bgiframe: true,
                    resizable:false,
                    close:function(){
                        $(this).dialog('destroy');
                    },
                    open:function(){
                        $('.ui-dialog-buttonpane',$(this).parent()).append('<div class="corner_bottom_right"><div class="corner_bottom_left"></div></div>');
                        $.post('/admin/delivery.php?do=add_category',{},function(data){
                            $('#content',$('#dialog')).html(data);
                        });

                    },
                    height:300,
                    width:400,
                    modal: true,
                    closeText:'',
                    overlay: {
                        backgroundColor: '#000',
                        opacity: 0.5
                    },
                    title: '<?=$AdminTrnsl["Delivery_add_category"]?>',
                    buttons: {
                        '<?=$AdminTrnsl["Delivery_save"]?>': function() {
                             $('#addForm').get(0).submit();
                        },
                        '<?=$AdminTrnsl["Delivery_cancel"]?>': function() {
                            $(this).dialog('destroy');
                        }
                    }
                });
            }
             function EditC(id){
                //alert( $('#dialog').get(0));
                $('#dialog').show();
                $('#dialog').html('<div class="shadow_right"><div class="backg_white" id="content">Please wait. Loading...</div></div>');
                $('#dialog').dialog({
                    bgiframe: true,
                    resizable:false,
                    close:function(){
                        $(this).dialog('destroy');
                    },
                    open:function(){
                        $('.ui-dialog-buttonpane',$(this).parent()).append('<div class="corner_bottom_right"><div class="corner_bottom_left"></div></div>');
                        $.post('/admin/delivery.php?do=edit_category&id='+id,{},function(data){
                            $('#content',$('#dialog')).html(data);
                        });

                    },
                    height:300,
                    width:400,
                    modal: true,
                    closeText:'',
                    overlay: {
                        backgroundColor: '#000',
                        opacity: 0.5
                    },
                    title: '<?=$AdminTrnsl["Delivery_edit_category"]?>',
                    buttons: {
                        '<?=$AdminTrnsl["Delivery_save"]?>': function() {
                             $('#updateForm').get(0).submit();
                        },
                        '<?=$AdminTrnsl["Delivery_cancel"]?>': function() {
                            $(this).dialog('destroy');
                        }
                    }
                });

            }
            function showCreateSubscriberDialog(){
                $('#dialog').show();
                $('#dialog').html('<div class="shadow_right"><div class="backg_white" id="content">Please wait. Loading...</div></div>');
                $('#dialog').dialog({
                    bgiframe: true,
                    resizable:false,
                    close:function(){
                        $(this).dialog('destroy');
                    },
                    open:function(){
                        $('.ui-dialog-buttonpane',$(this).parent()).append('<div class="corner_bottom_right"><div class="corner_bottom_left"></div></div>');
                        $.post('/admin/delivery.php?do=add_subscriber',{},function(data){
                            $('#content',$('#dialog')).html(data);
                        });

                    },
                    height:300,
                    width:400,
                    modal: true,
                    closeText:'',
                    overlay: {
                        backgroundColor: '#000',
                        opacity: 0.5
                    },
                    title: '<?=$AdminTrnsl["Delivery_add_subscriber"]?>',
                    buttons: {
                        '<?=$AdminTrnsl["Delivery_save"]?>': function() {
                            if($('#email').val().match(/^[0-9a-z_\.-]+@[0-9a-z_^\.-]+\.[a-z]{2,6}$/i))
                                $('#addForm').get(0).submit();
                            else alert('<?=$AdminTrnsl["Delivery_enter_valid_email"]?>');
                        },
                        '<?=$AdminTrnsl["Delivery_cancel"]?>': function() {
                            $(this).dialog('destroy');
                        }
                    }
                });
            }
            function EditS(id){
                //alert( $('#dialog').get(0));
                $('#dialog').show();
                $('#dialog').html('<div class="shadow_right"><div class="backg_white" id="content">Please wait. Loading...</div></div>');
                $('#dialog').dialog({
                    bgiframe: true,
                    resizable:false,
                    close:function(){
                        $(this).dialog('destroy');
                    },
                    open:function(){
                        $('.ui-dialog-buttonpane',$(this).parent()).append('<div class="corner_bottom_right"><div class="corner_bottom_left"></div></div>');
                        $.post('/admin/delivery.php?do=edit_subscriber&id='+id,{},function(data){
                            $('#content',$('#dialog')).html(data);
                        });

                    },
                    height:300,
                    width:400,
                    modal: true,
                    closeText:'',
                    overlay: {
                        backgroundColor: '#000',
                        opacity: 0.5
                    },
                    title: '<?=$AdminTrnsl["Delivery_edit_subscriber"]?>',
                    buttons: {
                        '<?=$AdminTrnsl["Delivery_save"]?>': function() {
                            if($('#email').val().match(/^[0-9a-z_\.-]+@[0-9a-z_^\.-]+\.[a-z]{2,6}$/i))
                             $('#updateForm').get(0).submit();
                            else alert('<?=$AdminTrnsl["Delivery_enter_valid_email"]?>');
                        },
                        '<?=$AdminTrnsl["Delivery_cancel"]?>': function() {
                            $(this).dialog('destroy');
                        }
                    }
                });

            }
        </script>
    </body>
</html>