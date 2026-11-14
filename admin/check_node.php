<?php
	
	function checkNodeName(&$NodeName,$node){
		$node=Node::findById($node->id);
		if($node->dynamicTemplate=="book"&&$node->tfields["counter"]>0){
			$contactNode=Node::findByPath("/contacts");
			$childs=$node->getChildrenOfClass("notice");
			$book_id=$node->id;
			$bookNode=$node;
			foreach($childs as $k=>$v){
				$member_id=$v->fields["member"]->value;
				$member_node=Node::findById($member_id);
				$orderNode=Node::createWithDefaultValues0("/settings/my_messages", "Книга (".$bookNode->name.") в наличии!", "", "message");
				$orderNode->fields["member"]->value=$member_id;
				$orderNode->fields["message"]->value="Книга <a href='".$bookNode->getHref()."'>".$bookNode->name."</a> в наличии.";
				$orderNode->store();

$admin_email = $contactNode->tfields["email"];
$book_path="http://".$_SERVER['HTTP_HOST'].$bookNode->getHref();
$adminEmailText = <<<EOT
<html>
<body>
Книга <a href="$book_path">$bookNode->name</a> в наличии.
</body>
</html>
EOT;
	$MAIL=new mime_mail();
	$MAIL->setCharset("utf-8");
	$MAIL->setFrom($admin_email);
	$MAIL->setTo($member_node->tfields["email"]);
	$MAIL->setSubject("Книга (".$bookNode->name.") в наличии");
	$MAIL->setBody($adminEmailText);
	$MAIL->send();
//		sendMail($admin_email,$member_node->tfields["email"], "Книга (".$bookNode->name.") в наличии", $adminEmailText);


				Node::remove($v->id);
				
				$records=Node::findByQuery("SELECT n.* FROM {notice_book} n WHERE n.book='".$book_id."' AND n.member='".$member_id."'");
					if(count($records)>0){
						foreach($records as $k1=>$v1)
						Node::remove($v1->id);
					}
				
				
				
				
				
			}
		}
	};
	
	function checkNodeNameByClass(&$NodeName,$nodeClass,$fieldList){
	//echo "+++";
		if($nodeClass->default_template=="menuweek"){
			$NodeName=date("d.m.Y",$fieldList["date1"]->value->time)." - ".date("d.m.Y",$fieldList["date2"]->value->time);
		}elseif($nodeClass->default_template=="weekday"){
			$NodeName=date("d.m.Y",$fieldList["date"]->value->time);
			
		}
	};

?>