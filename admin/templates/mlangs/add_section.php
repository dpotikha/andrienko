<?php

?>
<form id="frm_<?=$lang?>" action="/admin/mlangs.php" method="post">
	<input type="hidden" name="do" value="create_section" />
	<input type="hidden" name="lang" value="<?=$lang?>" />
	<table cellpadding="0" cellspacing="5" border="0">
		<tr>
			<td><?=$AdminTrnsl["Mlang_SectionName"]?> </td>
			<td><input type="text" style="width:350px" name="section_name" value=""></td>
		</tr>
		<tr>
			<td><?=$AdminTrnsl["Mlang_SectionShortname"]?> </td>
			<td><input type="text" style="width:350px" name="section_shortname" value=""></td>
		</tr>
	</table>
</form>
<?php 

?>
