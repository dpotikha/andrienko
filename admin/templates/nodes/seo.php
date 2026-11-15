<?php  
global $SETUP_LANGUAGES;
	global $SETUP_LANGUAGES_TITLE;
$request->setAttribute("active_tab", "seo"); ?>
<?= usetemplate("nodes/_edit_header"); ?>
<?php 
$node_id = (int) $request->getParameter("node_id");
$MDB->Query("SELECT * FROM dbm_seo WHERE node_id = $node_id");
$results=$MDB->getRecords();
$seo=array();
foreach($results as $k=>$v){
$seo['lang']=$v;
}

?>

<link rel=stylesheet href="/admin/jquery/css/smoothness/jquery-ui-1.7.2.custom.css" type="text/css" />
<script type="text/javascript" src="/admin/jquery/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/admin/jquery/js/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="/admin/jquery/js/jquery.validate.js"></script>
<script type="text/javascript" src="/admin/jquery/js/jquery.form.js"></script>
<table width=100% border=0 cellpadding=0 cellspacing=0>
    <tr><td id=pad18>

            <table width=100% border=0 cellpadding=0 cellspacing=0>
                <tr class=top4>
                    <td id=pad1818 style="padding:10px 0px 10px 0;">
                        <div id="tabs">
                            <ul>
                                <?php  foreach($SETUP_LANGUAGES as $k=>$lang){?>
                                <li><a href="/admin/nodes.php?do=seo&node_id=<?=$node_id?>&lang=<?=$lang?>"><?=$SETUP_LANGUAGES_TITLE[$k]?></a></li>
                                <?php  }?>
                            </ul>

                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<script>
    var TAB_INDEX=0;
	$(function() {

		$('#tabs').tabs({
			select: function(event, ui) {
				TAB_INDEX=ui.index;
				return true;
			}
		});

	});
    
</script></body>
</html>