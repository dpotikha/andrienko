<?php
tinclude("_head", array("extraHead" => "<script src=\"https://www.google.com/recaptcha/api.js\" async defer></script>\n"));
$form = $form ?? null;
$errors = $errors ?? array();
$val = function ($k) use ($form) { return $form ? e($form->$k ?? '') : ''; };
$err = function ($k) use ($errors) { return isset($errors[$k]) ? '<span class="error">' . ($k == 'captcha' ? 'Підтвердьте, що ви не робот' : 'Обов’язкове поле') . '</span>' : ''; };
?>
<section class="wrap split" style="padding-top:48px;padding-bottom:64px">
	<div class="main" style="flex:1 1 420px">
		<h1 class="h1" style="margin-bottom:28px"><?= e($CurrentNode->name) ?></h1>
		<div class="rich"><?= richText($CurrentNode->tfields["text"] ?? '') ?></div>
	</div>
	<form class="form-box" action="<?= e($CurrentNode->getHref()) ?>" method="post" style="flex:1 1 460px;min-width:0">
		<input type="hidden" name="do" value="send">
		<label>Ім’я *<input name="name" type="text" required autocomplete="name" value="<?= $val('name') ?>"><?= $err('name') ?></label>
		<label>E-mail *<input name="email" type="email" required autocomplete="email" value="<?= $val('email') ?>"><?= $err('email') ?></label>
		<label>Контактний телефон<input name="phone" type="tel" autocomplete="tel" value="<?= $val('phone') ?>"></label>
		<label>Текст повідомлення *<textarea name="text" rows="6" required><?= $val('text') ?></textarea><?= $err('text') ?></label>
		<div class="g-recaptcha" data-sitekey="6LcnXmQUAAAAAH5m6ITv_L-4dl1IgYZA_-RRICC8" style="margin-bottom:8px"></div>
		<p style="margin:0 0 18px"><?= $err('captcha') ?></p>
		<button type="submit">НАДІСЛАТИ</button>
	</form>
</section>
<?php tinclude("_foot") ?>
