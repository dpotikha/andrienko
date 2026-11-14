<input type="hidden" value="<?=prepareStringForXML($CM_PARAMS['strvalue'])?>" name="<?=$CM_CONTROL_NAME?>">
<?php 
$order = unserialize($CM_PARAMS['strvalue']);
?>

<table width="100%"  border="0" cellpadding="5" cellspacing="1">
<tr bgcolor="#FFCC00">
	<td width="50%" bgcolor="#FFCC00"><strong>Наименование</strong></td>
	<td width="70" bgcolor="#FFCC00"><strong>Размер</strong></td>
	<td width="70" nowrap bgcolor="#FFCC00"><strong>Цена</strong></td>
	<td width="70" bgcolor="#FFCC00"><strong>Количество</strong></td>
	<td width="70" bgcolor="#FFCC00"><strong>Сумма</strong></td>
</tr>
<?php 
foreach ($order->items as $item) {
	$itemPath = & Node::findAbsolutePathById($item->id);
	$key = $item->getKey();
?>
<input type="hidden" name="ids[]" value="<?=$item->id?>">
<input type="hidden" name="keys[]" value="<?=$key?>">
<tr bgcolor="#FFF3C6">
	<td bgcolor="#FFF3C6"><a href="<?= $itemPath ?>"><?= $item->manufacturer ?> <?= $item->model ?></a></td>
	<td nowrap><?= $item->size ?></td>
	<td><?= $item->price ?>р</td>
	<td bgcolor="#FFF3C6"><?= $item->count ?></td>
	<td bgcolor="#FFF3C6"><?= $item->price * $item->count ?>р.</td>
</tr>
<?php  } ?>
<tr bgcolor="#FFF3C6">
	<td colspan="4" align="right" bgcolor="#FFF3C6"><strong>Общая сумма:</strong></td>
	<td bgcolor="#FFCC00"><strong><?= $order->amount ?>р.</strong></td>
</tr>
</table>

<h5>Заказчик:</h5>
<b>Контактное лицо:</b> <?= $order->address->name ?><br>
<b>Телефон:</b> <?= $order->address->phone ?><br>
<b>Email:</b> <?= $order->address->email ?><br>
<b>Адрес:</b><br><?= nl2br($order->address->address) ?>
