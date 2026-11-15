<?php 
$action = $request->getParameter("do");

switch($action) {
	case "send" :
		$form=new phpObject();	
		$form->name = $request->getParameter("name");
		$form->phone = $request->getParameter("phone");
		$form->email = $request->getParameter("email");
		$form->text = $request->getParameter("text");

		$errors = array(); // sizeof() of an unset variable is a TypeError in PHP 8
		$requiredStringFields = array("name", "email", "text");
		foreach($requiredStringFields as $fieldShortname) {
			if (strlen($form->$fieldShortname) == 0 || $form->$fieldShortname == "Обязательное поле!") {
				$errors[$fieldShortname] = "Обязательное поле!";
			}
		}
		
		$rCode = $_POST['g-recaptcha-response'];
        $rUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $rSecret = '6LcnXmQUAAAAAL3BaGDCuXZ0jgIKmwbuz3vPgqn8';
        $ip = $_SERVER['REMOTE_ADDR'];


        $curl = curl_init($rUrl);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'secret=' . $rSecret . '&response=' . $rCode . '&remoteip=' . $ip);
        curl_setopt($curl, CURLINFO_HEADER_OUT, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $res = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($res);

        if ($res->success) {
            //Капча введена верно
        } else {
            //Это бот или человек который не умеет пользоваться капчой
			$errors['captcha'] = 'Проверочный код введен неверно';
            //$feedbackForm->setError('captcha', 'Проверочный код введен неверно');
        }


		if (sizeof($errors) == 0) {
			$admin_email = ADMIN_EMAIL;
			$from = ADMIN_EMAIL_FROM;

			$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); // visitor input goes into an HTML mail
			$adminEmailText = "<html><body>Вопрос с сайта Виктора Андриенка:<br>"
				. "Имя: " . $h($form->name) . "<br>"
				. "Телефон: " . $h($form->phone) . "<br>"
				. "E-Mail: " . $h($form->email) . "<br>"
				. "Сообщение<pre>" . $h($form->text) . "</pre></body></html>";
			// PHPMailer from the gady CMS core (the old core's CI_Email is gone); transport: PHP mail(), as before
			try {
				$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
				$mail->CharSet = \PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
				$mail->setFrom($from);
				foreach (explode(',', $admin_email) as $to) {
					if (trim($to) !== '') $mail->addAddress(trim($to));
				}
				if (filter_var($form->email, FILTER_VALIDATE_EMAIL)) $mail->addReplyTo($form->email, (string)$form->name);
				$mail->Subject = "Вопрос с сайта Виктора Андриенка";
				$mail->msgHTML($adminEmailText);
				$mail->send();
			} catch (\PHPMailer\PHPMailer\Exception $e) {
				error_log("contact form mail failed: " . $e->getMessage());
			}

			Header("Location: " . $CurrentNode->getHref() . "?do=sent");
		} else {
			$request->setAttribute("form", $form);
			$request->setAttribute("errors", $errors);

			tinclude("contacts_form");
		}
		break;
	case "sent":
		tinclude("contacts_sent");
		break;
	default :
		tinclude("contacts_form");
		break;
}
?>