<?php

?>
<form id="frm_<?=$lang?>_<?=$section?>" action="/admin/mlangs.php" method="post">
	<input type="hidden" name="do" value="save_section" />
	<input type="hidden" name="lang" value="<?=$lang?>" />
	<input type="hidden" name="section" value="<?=$section?>" />
	<table cellpadding="0" cellspacing="5" border="0">
		<tr>
			<td><?=$AdminTrnsl["Mlang_SectionName"]?></td>
			<td><input type="text" style="width:350px" name="section_name" value="<?=trim($section_info['title'])?>"></td>
		</tr>
		<tr>
			<td><?=$AdminTrnsl["Mlang_SectionShortname"]?></td>
			<td><input type="text" style="width:350px"  name="section_shortname" readonly value="<?=$section?>"></td>
		</tr>
	</table>
</form>
<?php 

?>
