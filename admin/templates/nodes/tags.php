<?php 
global $SETUP_LANGUAGES;
global $SETUP_LANGUAGES_TITLE;
$request->setAttribute("active_tab", "tags");
?>
<?= usetemplate("nodes/_edit_header"); ?>
<?php 
$node_id = (int) $request->getParameter("node_id");
$MDB->Query("SELECT * FROM dbm_seo WHERE node_id = $node_id");
$results = $MDB->getRecords();
$seo = array();
foreach ($results as $k => $v) {
    $seo['lang'] = $v;
}
$cats = QNode::getInstance('/tags')->getChildren('n.name');
?>
<link rel="stylesheet" type="text/css" href="/_css/autocomplete.css" media="screen" />
<link rel="stylesheet" href="/qskins/mint/jquery/tablesorter/style.css" type="text/css" media="all" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery.autocomplete2.min.js"></script>
<script src="/js/js.min.js"></script>

<table width=100% border=0 cellpadding=0 cellspacing=0>
    <tr><td id=pad18>

            <table width=100% border=0 cellpadding=0 cellspacing=0>
                <tr class=top4>
                    <td id=pad1818 style="padding:10px 0px 10px 0;">

                        <div style="padding-left: 10px; padding-right: 10px">
                            <table style="background-color: white">
                                <tr>
                                    <td><input type="radio" id="search_tag_type0" name="search_tag_type" value="0" checked=""><label for="search_tag_type0"><?=$AdminTrnsl["Tags_SearchFirstLetter"]?></label> <input type="radio" id="search_tag_type1" name="search_tag_type" value="1"><label for="search_tag_type1"><?=$AdminTrnsl["Tags_SearchWhole"]?></label></td>

                                </tr>
                                <tr>
                                    <td>
                                        <table><tr><td><input style="width: 800px" type="text" id="tagForm_tags"></td><td><select id="tagCategory"><option value="0"><?=$AdminTrnsl["Tags_SelectCategory"]?></option><?php  foreach ($cats as $k => $v) { ?><option value="<?= $v->id ?>"><?= $v->name ?></option><?php  } ?></select></td><td><div style="width:200px; height:26px"><img src="/img/ajax-loader.gif" id="searching1" style="display: none"><input type="button" id="addTagBtn" value="<?=$AdminTrnsl["Add"]?>" class="btn search__submit"></div></td></tr></table>
                                        <div id="suggestions-container"></div>
                                        <div id="ideaTags"></div>

                                        <div id="tablesorter"></div>
                                    </td>

                                </tr>
                            </table>


                            <style>
                                #tablesorter *{
                                    font-size: 11px;
                                }
                                .tablesorter input{
                                    font-size: 12px!important;
                                }
                                /*tipsy.css*/
                                .tipsy{padding:5px;font-size:10px;position:absolute;z-index:100000}.tipsy-inner{padding:5px 8px 4px 8px;background-color:black;color:white;max-width:200px;text-align:center}.tipsy-inner{border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px}.tipsy-arrow{position:absolute;background:url('/_img/tipsy.gif') no-repeat top left;width:9px;height:5px}.tipsy-n .tipsy-arrow{top:0;left:50%;margin-left:-4px}.tipsy-nw .tipsy-arrow{top:0;left:10px}.tipsy-ne .tipsy-arrow{top:0;right:10px}.tipsy-s .tipsy-arrow{bottom:0;left:50%;margin-left:-4px;background-position:bottom left}.tipsy-sw .tipsy-arrow{bottom:0;left:10px;background-position:bottom left}.tipsy-se .tipsy-arrow{bottom:0;right:10px;background-position:bottom left}.tipsy-e .tipsy-arrow{top:50%;margin-top:-4px;right:0;width:5px;height:9px;background-position:top right}.tipsy-w .tipsy-arrow{top:50%;margin-top:-4px;left:0;width:5px;height:9px}#gravity{width:100%;margin:5px 0;border-spacing:5px}#gravity td{text-align:center;vertical-align:middle;padding:5px 0;background-color:#d0d0d0;width:33%}#gravity a:hover{color:#505050;background:none}

                            </style>
                            <script type="text/javascript">

                                function decodeHTMLEntities(str) {
                                    if (str && typeof str === 'string') {
                                        // strip script/html tags
                                        str = str.replace(/&quot;/gmi, '"');
                                    }

                                    return str;
                                }
                                function in_array(needle, haystack, strict) {

                                    var found = false, key, strict = !!strict;

                                    for (key in haystack) {
                                        if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
                                            found = true;
                                            break;
                                        }
                                    }

                                    return found;
                                }
                                var dataURL = '/admin/nodes.php?do=tags&node_id=<?= $node->id ?>&getlist';

                                var ts_config = [
                                    {title: 'ID', sort: true, width: '5%'},
                                    {title: '<?=$AdminTrnsl["Tags_Tag"]?>', sort: true, width: '50%'},
                                    {title: '<?=$AdminTrnsl["Tags_Category"]?>', sort: true, width: '40%'},
                                    {title: '<?=$AdminTrnsl["Action"]?>', sort: false, width: '5%'}
                                ];


                                registerBasicTCDE(ts_config, function() {
                                    $('a.row_delete').click(function() {
                                        var id = $(this).attr('rel');
                                        var info = $(this).attr('rev');
                                        $('.tipsy').remove();
                                        if (confirm("<?=$AdminTrnsl["Tags_ConfirmDelete"]?>:\r\n\r\n" + info)) {
                                            $.post('/admin/nodes.php?do=tags&node_id=<?= $node->id ?>', {'section': 'delete_tag', 'id': id}, function() {
                                                tablesorter.trigger('refresh');
                                            });
                                        }
                                    }).attr('alt', '<?=$AdminTrnsl["Delete"]?>').addClass('tooltipsy');
                                    $(".tooltipsy").tipsy({gravity: 'e', title: 'alt'});
                                });

                                var searching = false;
                                function decodeHTMLEntities(str) {
                                    if (str && typeof str === 'string') {
                                        // strip script/html tags
                                        str = str.replace(/&quot;/gmi, '"');
                                    }

                                    return str;
                                }

                                function addTags(tag_id, val) {
                                    if(tag_id==0 && $('#tagCategory').val()==0){
                                        alert('<?=$AdminTrnsl["Tags_SelectCategory"]?>');
                                        return false;
                                    }
                                    jQuery.post('/admin/nodes.php?do=tags&node_id=<?= $node->id ?>', {'section': 'add_tag', 'node_id': '<?= $node->id ?>', 'tag_id': tag_id, 'val': val, 'category':$('#tagCategory').val()}, function(data) {
                                        setTimeout(function() {
                                            tablesorter.trigger('refresh');
                                            $('#tagForm_tags').val('');
                                        }, 1);
                                    });
                                }
                                jQuery(document).ready(function() {
                                    //addTags(0, '');
                                    jQuery('#addTagBtn').click(function() {
                                        addTags(0, jQuery('#tagForm_tags').val());
                                    });
                                    var search_keyword = '';
                                    jQuery('#tagForm_tags').autocomplete({
                                        serviceUrl: '/admin/nodes.php?do=tags&node_id=<?= $node->id ?>&autocomplete',
                                        dataType: 'json',
                                        width: 500,
                                        params: {'category': function() {
                                                return $('#tagCategory').val()
                                            }, 'search_type': function() {
                                                return $('#search_tag_type1').get(0).checked ? 1 : 0
                                            }},
                                        minChars: 2,
                                        noCache: true,
                                        tabDisabled: true,
                                        deferRequestBy: 100,
                                        appendTo: '#suggestions-container',
                                        onSearchStart: function(q) {
                                            jQuery('.search__submit').hide();
                                            jQuery('#searching1').show();
                                            searching = true;
                                        },
                                        onSearchComplete: function(q) {
                                            jQuery('.search__submit').show();
                                            jQuery('#searching1').hide();
                                            searching = false;
                                            search_keyword = jQuery('#tagForm_tags').val();
                                        },
                                        onSelect: function(suggestion) {
                                            jQuery('.search__submit').show();
                                            jQuery('#searching1').hide();
                                            var val = search_keyword;
                                            var tag_id = 0;
                                            if (suggestion.data == 'x') {
                                                jQuery('#tagForm_tags').val('');
                                            } else {
                                                jQuery('#tagForm_tags').val('');
                                                tag_id = suggestion.data[0];
                                            }
                                            addTags(tag_id, val);
                                        },
                                        formatResult: function(suggestion, currentValue) {


                                            var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g'),
                                                    pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
                                            var pcount = '';
                                            if (suggestion.data[0] != 'x') {
                                                pcount = ' - <span>[' + suggestion.data[1] + ']</span>';
                                            } else if (suggestion.data[0] == 'x') {
                                                return '<strong>' + suggestion.value + '</strong>';
                                            }
                                            //alert(suggestion.value);
                                            return suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>') + pcount;

                                            var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g'),
                                                    pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
                                            if (typeof(suggestion.value) == 'function')
                                                return '';
                                            return suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
                                        },
                                        transformResult: function(response) {

                                            var s = {};
                                            for (var k in response.suggestions) {
                                                s[k] = {value: decodeHTMLEntities(response.suggestions[k]), data: response.data[k]};
                                            }
                                            return s;
                                        }
                                    });
                                    jQuery('#tagForm_tags').keypress(function(e) {
                                        if (e.which == 13) {
                                            if (jQuery('#tagForm_tags').val() != '')
                                                addTags(0, jQuery('#tagForm_tags').val());
                                            e.stopPropagation();
                                            e.preventDefault();
                                        }
                                    });
                                });
                            </script>





                        </div>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
</body>
</html>