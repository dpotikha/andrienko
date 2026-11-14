<?
$action = $request->getParameter("do");

switch($action) {
	case "send" :
		$form=new phpObject();	
		$form->name = $request->getParameter("name");
		$form->phone = $request->getParameter("phone");
		$form->email = $request->getParameter("email");
		$form->text = $request->getParameter("text");

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

$adminEmailText = <<<EOT
<html>
<body>
Вопрос с сайта Виктора Андриенка:<br>
Имя: $form->name<br>
Телефон: $form->phone<br>
E-Mail: $form->email<br>
Сообщение<pre>$form->text</pre>
</body>
</html>
EOT;
			//$from='fail_angel@ua.fm';
			//$admin_email='fail_angel@ua.fm';
			//sendMail($from, $admin_email, "Вопрос с сайта Виктора Андриенка", $adminEmailText);
					$config['charset'] = 'UTF-8';
					$config['mailtype'] = 'html';
					$config['wordwrap'] = FALSE;
					$email=new CI_Email($config);
					$email->from($from);
					$email->to($admin_email);
					$email->subject("Вопрос с сайта Виктора Андриенка");
					$email->message($adminEmailText);
					$email->send();
					
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