<?php 
//$prefix = strpos($CM_CONTROL_NAME, 'jsonprogram');
if ($request->getAttribute('nodeClass')->id == 317 && strpos($CM_CONTROL_NAME, 'jsonprogram') !== false) { //Раздел программы саммита 
    $program = json_decode($CM_PARAMS['strvalue']);
    //dump($program, 1, 10);
    ?>
    <style>
        .dateTitle { display: inline-block; margin-left: 25px; width: 85px;}
        textarea { font-size: 13px; width: 600px; height: 120px; }
        #workSpace input { width: 600px; }
        #workSpace td { border: none; }
        .dayBlock {
            background-color: #DDDDDD;
            border-radius: 10px 10px 10px 10px;
            padding: 15px 20px 20px;
            width: 745px;
            margin: 5px 0 25px 10px;
            box-shadow: 5px 5px 5px #888;
        }
        .timeBlock {
            background-color: #EDEDED;
            border-radius: 10px 10px 10px 10px;
            margin-top: 20px;
            padding: 20px;
            width: 700px;
            box-shadow: 5px 5px 5px #888;
        }
        .removeDay, #addDay, .removeTime, .addtime { display: inline-block; }
        #addDay { margin:10px 10px 10px 20px; }
        .addtime { margin-top:20px; }
        .removeDay { float: right; margin-right: 10px; }
        .dayControls {margin: 0 0 -5px;}
    </style>
    <div id="dayPrototype" style="display:none;">
        <div class="dayBlock" id="dayBlockID">
            <div class="dayControls">
                <span class="dateTitle"><b><?=$AdminTrnsl["Date"]?></b></span><input class="day" id="dayID" type="text" style="width: 100px;" />
                <img src="/admin/js/calendar/img.gif" id="datePickerID" style="cursor:pointer; border:1px solid red; margin-left:10px" title="Date selector" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
                <a href="javascript:;" class="removeDay" rel="ID"><?=$AdminTrnsl["Delete"]?></a>
            </div>

            <div id="timeSpaceID"></div>
            <a href="javascript:;" class="addtime" rel="ID"><?=$AdminTrnsl["Program_AddEvent"]?></a>
        </div>
    </div>

    <div id="timePrototype" style="display:none;">
        <div class="timeBlock" id="timeBlockID">
            <table>
                <tr valign="top"><td><?=$AdminTrnsl["Program_Time"]?></td><td><input class="time" type="text" /></td></tr>
                <tr valign="top"><td><?=$AdminTrnsl["Program_Event_UA"]?></td><td><input class="name_ua" type="text" /></td></tr>
                <tr valign="top"><td><?=$AdminTrnsl["Program_Event_EN"]?></td><td><input class="name_en" type="text" /></td></tr>
                <tr valign="top"><td><?=$AdminTrnsl["Program_Desc_UA"]?></td><td><textarea class="text_ua"></textarea></td></tr>
                <tr valign="top"><td><?=$AdminTrnsl["Program_Desc_EN"]?></td><td><textarea class="text_en"></textarea></td></tr>
            </table>
            <a href="javascript:;" rel="ID" class="removeTime"><?=$AdminTrnsl["Delete"]?></a>
        </div>
    </div>

    <div id="workSpace"></div>
    <a href="javascript:;" id="addDay"><?=$AdminTrnsl["Program_AddDay"]?></a>

    <script type="text/javascript" src="/admin/js/jquery-1.6.1.js"></script>
    <script type="text/javascript">
        $.noConflict();
        lastId = 0;
        lastTimeId = 0;
        function addDay(day) {
            lastId ++;
            jQuery('#workSpace').append(jQuery('#dayPrototype').html().replace(/ID/g, lastId));
            //if (day.length)
            jQuery('#day' + lastId).val(day);
            Calendar.setup({
                inputField     :    "day" + lastId,     // id of the input field
                ifFormat       :    "%d.%m.%Y",      // format of the input field
                button         :    "datePicker" + lastId,  // trigger for the calendar (button ID)
                align          :    "Tl",           // alignment (defaults to "Bl")
                singleClick    :    true
            });          
        }
        function addTime(id, time, name_ua, name_en, text_ua, text_en) {
            lastTimeId ++;
            jQuery('#timeSpace' + id).append(jQuery('#timePrototype').html().replace(/ID/g, lastTimeId));
            jQuery('#timeBlock' + lastTimeId).find('.time').val(time);
            jQuery('#timeBlock' + lastTimeId).find('.name_ua').val(name_ua);
            jQuery('#timeBlock' + lastTimeId).find('.name_en').val(name_en);
            jQuery('#timeBlock' + lastTimeId).find('.text_ua').val(text_ua);
            jQuery('#timeBlock' + lastTimeId).find('.text_en').val(text_en);
        }
        jQuery(document).ready(function(){
            // restore program
    <?php  if (strlen($CM_PARAMS['strvalue'])) { ?>
                program = <?= $CM_PARAMS['strvalue'] ?>;
                for (k in program) {
                    addDay(program[k].day);
                    for (v in program[k].times)
                        addTime(lastId, program[k].times[v].time, program[k].times[v].name_ua, program[k].times[v].name_en, program[k].times[v].text_ua, program[k].times[v].text_en);
                }
    <?php  } ?>
                                    
                                    
            // day
            jQuery('#addDay').live('click', function(event){
                addDay()
            });
            jQuery('.removeDay').live('click', function(event){
                jQuery('#dayBlock' + jQuery(this).attr('rel')).remove();
            });
                                    
                                    
            //time
            jQuery('.addTime').live('click', function(event){
                addTime(jQuery(this).attr('rel'))
            });
            jQuery('.removeTime').live('click', function(event){
                jQuery('#timeBlock' + jQuery(this).attr('rel')).remove();
            });
                                  
                                  
            // submit
            jQuery('form[name=node_form]').submit(function(event){
                var program = new Array();
                jQuery('#workSpace .dayBlock').each(function(index){
                    var times = new Array();
                    jQuery(this).find('.timeBlock').each(function(index){
                        times.push({
                            time : jQuery(this).find('.time').val(),
                            name_ua : jQuery(this).find('.name_ua').val(),
                            name_en : jQuery(this).find('.name_en').val(),
                            text_ua : jQuery(this).find('.text_ua').val(),
                            text_en : jQuery(this).find('.text_en').val()
                        });
                    });
                                            
                    program.push({
                        day : jQuery(this).find('.day').val(),
                        times : times
                    })
                });
                jQuery('#jsonprogram').html(json_encode(program))
            });
                                   
        });
                                
        function json_encode(mixed_val){var retVal,json=this.window.JSON;try{if(typeof json==='object'&&typeof json.stringify==='function'){retVal=json.stringify(mixed_val);if(retVal===undefined){throw new SyntaxError('json_encode');}
                    return retVal;}
                var value=mixed_val;var quote=function(string){var escapable=/[\\\"\u0000-\u001f\u007f-\u009f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;var meta={'\b':'\\b','\t':'\\t','\n':'\\n','\f':'\\f','\r':'\\r','"':'\\"','\\':'\\\\'};escapable.lastIndex=0;return escapable.test(string)?'"'+string.replace(escapable,function(a){var c=meta[a];return typeof c==='string'?c:'\\u'+('0000'+a.charCodeAt(0).toString(16)).slice(-4);})+'"':'"'+string+'"';};var str=function(key,holder){var gap='';var indent='    ';var i=0;var k='';var v='';var length=0;var mind=gap;var partial=[];var value=holder[key];if(value&&typeof value==='object'&&typeof value.toJSON==='function'){value=value.toJSON(key);}
                    switch(typeof value){case'string':return quote(value);case'number':return isFinite(value)?String(value):'null';case'boolean':case'null':return String(value);case'object':if(!value){return'null';}
                                            if((this.PHPJS_Resource&&value instanceof this.PHPJS_Resource)||(window.PHPJS_Resource&&value instanceof window.PHPJS_Resource)){throw new SyntaxError('json_encode');}
                                            gap+=indent;partial=[];if(Object.prototype.toString.apply(value)==='[object Array]'){length=value.length;for(i=0;i<length;i+=1){partial[i]=str(i,value)||'null';}
                                                v=partial.length===0?'[]':gap?'[\n'+gap+
                                                    partial.join(',\n'+gap)+'\n'+
                                                    mind+']':'['+partial.join(',')+']';gap=mind;return v;}
                                            for(k in value){if(Object.hasOwnProperty.call(value,k)){v=str(k,value);if(v){partial.push(quote(k)+(gap?': ':':')+v);}}}
                                            v=partial.length===0?'{}':gap?'{\n'+gap+partial.join(',\n'+gap)+'\n'+
                                                mind+'}':'{'+partial.join(',')+'}';gap=mind;return v;case'undefined':case'function':default:throw new SyntaxError('json_encode');}};return str('',{'':value});}catch(err){if(!(err instanceof SyntaxError)){throw new Error('Unexpected error type in json_encode()');}
                                                this.php_js=this.php_js||{};this.php_js.last_error_json=4;return null;}}
    </script>

    <textarea id="jsonprogram" rows="10" style="width:100%; display:none;" name="<?= $CM_CONTROL_NAME ?>"><?= prepareStringForXML($CM_PARAMS['strvalue']) ?></textarea>

<?php  } else { ?><textarea rows="10" style="width:100%" name="<?= $CM_CONTROL_NAME ?>"><?= prepareStringForXML($CM_PARAMS['strvalue']) ?></textarea><?php  } ?>