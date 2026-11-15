<?php /* <input type="hidden" value="<?=$CM_PARAMS['formatted_datetime']?>" name="<?=$CM_CONTROL_NAME?>">
<?=$CM_PARAMS['formatted_date']?>
 */?>

<table border=0 cellpadding=0 cellspacing=0 border="0" class=nobacktab><tr>
 <td><input type="text" size="20" value="<?=$CM_PARAMS['formatted_datetime']?>" id="<?=$CM_CONTROL_NAME?>" name="<?=$CM_CONTROL_NAME?>"></td>
 <td><img src="/admin/js/calendar/img.gif" id="f_<?=$CM_CONTROL_NAME?>" style="cursor: pointer; border: 1px solid red;" title="Date selector"
      onmouseover="this.style.background='red';" onmouseout="this.style.background=''" /></td>
</table>

<script type="text/javascript">
    Calendar.setup({
        inputField     :    "<?=$CM_CONTROL_NAME?>",     // id of the input field
        ifFormat       :    "%d.%m.%Y %H:%M:%S",      // format of the input field
		showsTime      :    true,
		timeFormat     :    "24",
        button         :    "f_<?=$CM_CONTROL_NAME?>",  // trigger for the calendar (button ID)
        align          :    "Tl",           // alignment (defaults to "Bl")
        singleClick    :    true
    });
</script>
