<?php  $request->setAttribute("active_tab", "multiphoto"); ?>
<?= usetemplate("nodes/_edit_header"); ?>

<script src="/admin/js/jquery.js" type="text/javascript"></script>
<script src="/admin/js/html5upload.js" type="text/javascript" charset="utf-8"></script>

<link rel="stylesheet" href="/admin/js/prettyPhoto/css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
<script src="/admin/js/prettyPhoto/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>

<style type="text/css">
    #custom-queue { border: 1px solid #E5E5E5; max-height: 300px; overflow: auto; margin: 10px 0; width: 370px; }
    #custom-queue:empty { display: none; }
    .h5u-item { font: 11px Verdana, Geneva, sans-serif; padding: 6px 10px; border-bottom: 1px solid #E5E5E5; }
    .h5u-bar { background: #E5E5E5; height: 3px; margin-top: 4px; }
    .h5u-fill { background: #0099FF; height: 3px; width: 0; }
    .h5u-done { color: #999; }
    .h5u-error { background: #FDE5DD; }
</style>

<table width=100% border=0 cellpadding=0 cellspacing=0>
    <tr><td id=pad18 style="padding-right:0;">

            <table width=100% border=0 cellpadding=0 cellspacing=0>
                <tr class=top4>
                    <td id=pad1818 style="padding:10px 0px 10px 0;">

                        <?php  if(!isset($class->fieldDefs['multiphoto'])){?>
                        <table width=100% border=0 cellpadding=0 cellspacing=0>
                            <tr><td id=pad18 style="padding-right:0; color:red; font-weight: bold">
                                    <?=$AdminTrnsl["Multiphoto_NotConfigured"]?>
                                </td><tr></table>
                        <?php  }else{

                        $config = $class->fieldDefs['multiphoto']->fieldParameters['default_value'];
                        $exp=explode(":", $config);
                        $fclass = NodeClass::findById($exp[0]);
                        $field=$fclass->getFieldDef((int)trim($exp[1]));
                        $params = $field->fieldParameters['width'].':'.$field->fieldParameters['height'].':'.$field->fieldParameters['resize_strategy'];
                        $tparams = $field->fieldParameters['thumbnail_width'].':'.$field->fieldParameters['thumbnail_height'].':'.$field->fieldParameters['thumb_resize_strategy'];
                        ?> 
                        <script type="text/javascript">
                            function refreshPhotos(){
                                $.post('/admin/js/uploadify/get.php',{'node_id':'<?=$node_id?>','type':'<?=$field->fieldTypeId?>'}, function(data){
                                        $('#uploaded_photos').html(data); 
                                        $("a[rel^='prettyPhoto']").prettyPhoto();
                                 });
                                
                            }
                            function delPhoto(id){
                                if(confirm('<?=$AdminTrnsl["Confirm_DeletePhoto"]?>')){
                                     $.post('/admin/js/uploadify/delete.php',{'node_id':'<?=$node_id?>','id':id}, function(data){
                                        refreshPhotos();   
                                 });
                                }
                            }
                            function savePhoto(){
                                var use_watermark=0;
                                if(typeof($('#use_watermak:checkbox').get(0))!='undefined' && $('#use_watermak:checkbox').get(0).checked){
                                    use_watermark=1;
                                }
                               location.href='/admin/nodes.php?do=multiphoto_save&node_id=<?=$node_id?>&use_watermak='+use_watermark;
                            }
                            $(function() {
                                refreshPhotos();
                           });
                        </script>
                        <div class="demo-box">
                            <div id="status-message"><?=$AdminTrnsl["Multiphoto_SelectFiles"]?></div>
                            <input id="custom_file_upload" type="file" multiple accept="image/jpeg,image/gif,image/png" />
                            <div id="custom-queue"></div>
                            <div id="uploaded_photos"></div>
                        </div>
                        <script type="text/javascript">
                            html5Upload({
                                input: document.getElementById('custom_file_upload'),
                                queue: document.getElementById('custom-queue'),
                                url: '/admin/js/uploadify/uploadify.php',
                                data: {node_id: <?= json_encode((string)$node_id) ?>, params: <?= json_encode($params) ?>, tparams: <?= json_encode($tparams) ?>},
                                maxFiles: 100,
                                concurrency: 3,
                                accept: function (file) {
                                    return /\.(jpe?g|gif|png)$/i.test(file.name) ? '' : 'JPG, GIF, PNG';
                                },
                                onDone: function (ok, errors) {
                                    $('#status-message').text(ok + <?= json_encode((string)($AdminTrnsl["Multiphoto_FilesUploaded"] ?? '')) ?> + errors + <?= json_encode((string)($AdminTrnsl["Multiphoto_Errors"] ?? '')) ?>);
                                    refreshPhotos();
                                    $('#custom-queue .h5u-done').remove();
                                }
                            });
                        </script>

                        <?php  }?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>


</body>
</html>