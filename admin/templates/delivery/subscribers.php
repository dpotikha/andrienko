<?php
?>

<table id="lista1" style="width: 800px"></table> <div id="pagera1"></div>
<script type="text/javascript">
    jQuery().ready(function (){
        jQuery("#lista1").jqGrid({
            url:'/admin/delivery.php?do=subscribe_list',
            datatype: "json",
            colNames:['<?=$AdminTrnsl["Delivery_id"]?>','<?=$AdminTrnsl["Delivery_name"]?>', '<?=$AdminTrnsl["Delivery_email"]?>', '<?=$AdminTrnsl["Delivery_category"]?>', '<?=$AdminTrnsl["Delivery_active"]?>','<?=$AdminTrnsl["Delivery_subscribe_date"]?>'],
            colModel:[  {name:'id',width:70,index:'id'},
                {name:'name',index:'name'},
                {
                    name:'email',
                    editable:true,
                    index:'email',
                    align:"right",
                    editoptions:{size:12,
                        dataInit:function(el){
                            //$(el).datepicker({dateFormat:'yy-mm-dd'});
                        },
                        defaultValue: function(){
                            return '';
                        }
                    },
                    formoptions:{ rowpos:2, elmprefix:"(*)",elmsuffix:"  " },
                    editrules:{required:true}
                },
                {name:'category',index:'category_id', align:"right"},
                {name:'active',index:'active', align:"right"},
                {name:'sdate',index:'sdate',align:"right"}
            ],
            rowNum:25,
            autowidth: false,
            rownumbers: true,
            multiselect: true,
            rowList:[25,50,100,200],
            pager: '#pagera1',
            sortname: 'id',
            viewrecords: true,
            editurl:"/admin/delivery.php?do=operations",
            sortorder: "desc",
            jsonReader: { repeatitems : false, id: "0" },
            caption:"<?=$AdminTrnsl["Delivery_subscribers"]?>",
            height: '600px'
        });


        jQuery("#lista1").jqGrid('navGrid','#pagera1',
        {view:true}, //options
        {jqModal:true,
            checkOnUpdate:true,
            savekey: [true,13],
            navkeys: [true,38,40],
            checkOnSubmit : true,
            reloadAfterSubmit:true,
            closeOnEscape:true,
            bottominfo:"Fields marked with (*) are required!"
        }, // edit options
        {jqModal:true,checkOnUpdate:true,
            savekey: [true,13],
            navkeys: [true,38,40],
            checkOnSubmit : true,
            reloadAfterSubmit:false,
            closeOnEscape:true,
            bottominfo:"Fields marked with (*) are required?"}, // add options
        {reloadAfterSubmit:true,
            jqModal:true,
            closeOnEscape:true}, // del options
        {closeOnEscape:true}, // search options
        {navkeys: [true,38,40],
            height:250,
            jqModal:true,
            closeOnEscape:true} // view options
    );

    });

    //jQuery("#cm1").click( function() { var s; s = jQuery("#list13").jqGrid('getGridParam','selarrrow'); alert(s); });
</script>
<?php 
$MDB->Query('SELECT * FROM dbm_mails_categories ORDER BY id ASC');
$res=$MDB->getRecords();
?>
<form action="" enctype="multipart/form-data" method="post">
    <input type="hidden" name="do" value="import_s">
    <table cellpadding="5" border="0">
        <tr>
            <td><strong><?=$AdminTrnsl["Delivery_import_file"]?>:</strong></td>
            <td><select name="category">
                  <?php  foreach($res as $k=>$v){?>
                    <option value="<?=$v['id']?>"><?=$v['name']?></option>
                  <?php  }?>
                </select></td>
            <td><input type="file" name="file"></td>
            <td><input type="submit" value="<?=$AdminTrnsl["Delivery_import"]?>"></td>
        </tr>
        <tr>
            <td colspan="4"><?=$AdminTrnsl["Delivery_csv_format"]?></td>
            <td colspan="4"><a href="/admin/delivery.php?do=export"><?=$AdminTrnsl["Delivery_export"]?></td>
        </tr>
    </table>
</form>