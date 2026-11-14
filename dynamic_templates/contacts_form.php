<? tinclude("_head") ?>
<?= $CurrentNode->tfields["text"]?>
<style>
.cform .name {padding-top:9px}
INPUT, TEXTAREA {background:D9D9D9; font:normal 11px Tahoma; padding:0px 3px; line-height:15px}
#red {color:#990000}
.oligat {border:none; border-left:2px solid #990000}
.error {border:2px solid #990000;color:#990000}
.submit {font-size:11px; cursor:pointer; line-height:11px; padding:0px}
</style>
<h3 style="margin-top:10px!important">Написать мне письмо</h3>
<form action="<?= $CurrentNode->getHref() ?>" method="post">
<input type="hidden" name="do" value="send">
<table cellspacing="0" cellpadding="4" width="100%" class="cform">
<col width="30%"/>
<col width="70%"/>
<tr>
<td></td>
<td>
<span id="red">* Обязательные поля</span></td></tr>
<? if($errors["name"]){ ?>
<tr>
<td class="name">Имя <span id="red">*</span></td>
<td>
<input name="name" type="text" class="oligat error" style="width:100%" value="<?=$errors["name"]?>" onfocus="if(this.value=='<?=prepareStringForHTML($errors["name"])?>'){this.value='';this.style.color='#000'}" onblur="if(this.value==''){this.value='<?=prepareStringForHTML($errors["name"])?>';this.style.color='#990000'}">
</td>
</tr>
<? } else { ?>
<tr>
<td class="name">Имя <span id="red">*</span></td>
<td>
<input name="name" type="text" class="oligat" style="width:100%" value="<?=prepareStringForHTML($form->name)?>">
</td>
</tr>
<? } ?>

<? if($errors["email"]){ ?>
<tr>
<td class="name">E-Mail <span id="red">*</span></td>
<td>
<input name="email" type="text" class="oligat error" style="width:100%" value="<?=$errors["email"]?>" onfocus="if(this.value=='<?=prepareStringForHTML($errors["email"])?>'){this.value='';this.style.color='#000'}" onblur="if(this.value==''){this.value='<?=prepareStringForHTML($errors["email"])?>';this.style.color='#990000'}">
</td>
</tr>
<? } else { ?>
<tr>
<td class="name">E-Mail <span id="red">*</span></td>
<td>
<input name="email" type="text" class="oligat" style="width:100%" value="<?=prepareStringForHTML($form->email)?>">
</td>
</tr>
<? } ?>

<tr>
<td class="name" nowrap="nowrap">Контактный телефон</td>
<td>
<input name="phone" class="oligat" type="text" style="width:100%" value="<?=prepareStringForHTML($form->phone)?>">
</td>
</tr>

<? if($errors["text"]){ ?>
<tr>
<td class="name">Текст сообщения <span id="red">*</span></td>
<td>
<textarea name="text" class="oligat error" rows="5" style="width:100%" onfocus="if(this.value=='<?=prepareStringForHTML($errors["text"])?>'){this.value='';this.style.color='#000'}" onblur="if(this.value==''){this.value='<?=prepareStringForHTML($errors["text"])?>';this.style.color='#990000'}"><?=$errors["text"]?></textarea>
</td>
</tr>
<? } else { ?>
<tr>
<td class="name">Текст сообщения <span id="red">*</span></td>
<td>
<textarea name="text" class="oligat" rows=5 style="width:100%"><?=prepareStringForHTML($form->text)?></textarea>
</td>
</tr>
<? } ?>
<tr>
<td></td><td><div class="g-recaptcha" data-sitekey="6LcnXmQUAAAAAH5m6ITv_L-4dl1IgYZA_-RRICC8"></div>
<span id="red"><?=$errors["captcha"]?></span>
</td>
</tr>

<td style="border:none"></td>
<td colspan="5" style="border:none">
<input type="submit" value="Отправить" class="submit">
<input type="reset" value="Сброс" class="submit">
</td></tr>
</table>
</form>
<? tinclude("_foot") ?>