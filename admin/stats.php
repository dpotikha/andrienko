<?php
	require_once("prepend.php");
	if(!isset($_POST['stats_data_from'])){
		if(isset($_GET['datestart']))
			$datestart=$_GET['datestart'];
		else
			$datestart=date("d.m.Y 00:00:00",mktime(0,0,0,11,10,2008));
	}else
		$datestart=$_POST['stats_data_from'];
	if(!isset($_POST['stats_data_to'])){
		if(isset($_GET['dateend']))
			$dateend=$_GET['dateend'];
		else
		$dateend=date("d.m.Y 00:00:00",mktime()+3600*24);
	}else
		$dateend=$_POST['stats_data_to'];

	$datestart_t=strtotime($datestart);
	$dateend_t=strtotime($dateend);
	$t=($dateend_t-$datestart_t)/24/3600;
	
	$where=' WHERE nn.id=n.node_id';
	$dbmQL = "SELECT count(n.node_id) as scount FROM {individual_work} n,[nodes] as nn ".$where;
	
	$MDB->execute($dbmQL);
	$res=$MDB->NextResult();
	$all_i_Count=$res["scount"];
	
	$dbmQL = "SELECT count(n.node_id) as scount FROM {collective_work} n,[nodes] as nn".$where;
	$MDB->execute($dbmQL);
	$res=$MDB->NextResult();
	$all_g_Count=$res["scount"];

	$where=" WHERE UNIX_TIMESTAMP(n.date) >=".$datestart_t." AND UNIX_TIMESTAMP(n.date)<=".$dateend_t.' AND nn.id=n.node_id';
	$dbmQL = "SELECT count(n.node_id) as scount FROM {individual_work} n,[nodes] as nn".$where;
	$MDB->execute($dbmQL);
	$res=$MDB->NextResult();
	$per_i_Count=$res["scount"];
	
	$dbmQL = "SELECT count(n.node_id) as scount FROM {collective_work} n ,[nodes] as nn ".$where;
	$MDB->execute($dbmQL);
	$res=$MDB->NextResult();
	$per_g_Count=$res["scount"];


	$i=0;
	$days=array();
	while($i<=$t){
		$date=date('Y-m-d',$datestart_t+$i*24*3600);
		if(!isset($days[$date])){
			$days[$date]=array();
			$days[$date]['g']=array();
			$days[$date]['i']=array();
			$days[$date]['i']['scount']=0;
			$days[$date]['g']['scount']=0;
		}
		$i=$i+1;
	}


	
	$where=" WHERE UNIX_TIMESTAMP(n.date) >=".$datestart_t." AND UNIX_TIMESTAMP(n.date)<=".$dateend_t." AND nn.id=n.node_id ";
	$dbmQL = "SELECT DATE_FORMAT(n.date,'%Y-%m-%d') as dt, count(n.node_id) as scount from {individual_work} n,[nodes] as nn".$where." GROUP BY dt ORDER BY n.date ASC";
	$MDB->execute($dbmQL);
	while($res=$MDB->NextResult()){
		$days[$res['dt']]['i']=$res;
	}
	
	$dbmQL = "SELECT DATE_FORMAT(n.date,'%Y-%m-%d') as dt, count(n.node_id) as scount from {collective_work} n,[nodes] as nn ".$where." GROUP BY dt ORDER BY n.date ASC";
	$MDB->execute($dbmQL);
	while($res=$MDB->NextResult()){
		$days[$res['dt']]['g']=$res;
	}


	$request->setAttribute('datestart',$datestart);
	$request->setAttribute('dateend',$dateend);
	
	$request->setAttribute('all_i_Count',$all_i_Count);
	$request->setAttribute('all_g_Count',$all_g_Count);
	$request->setAttribute('per_i_Count',$per_i_Count);
	$request->setAttribute('per_g_Count',$per_g_Count);
	$request->setAttribute('days',$days);
	if(isset($_GET['do'])&&$_GET['do']=='download'){
	   define("SYSTEM_PEAR_XLS_PATH",$_SERVER['DOCUMENT_ROOT']."/admin/pear_xls/");
	  // require_once (SYSTEM_PEAR_XLS_PATH."PEAR.php"); 
	   require_once (SYSTEM_PEAR_XLS_PATH."Writer.php"); 
	  error_reporting(E_ALL);
	   $xls = new Spreadsheet_Excel_Writer(); 
	   $xls->send("stats_".$datestart."-".$dateend.".xls"); 
	   $sheet =& $xls->addWorksheet('stats'); 
	   $h2Format =& $xls->addFormat();
	   $h2Format->setBold();
	   $h2Format->setSize('10');
	   


	   $xls->setCustomColor(12, 220, 220, 220);//left
	   $xls->setCustomColor(13, 237, 237, 237);//left
	   $xls->setCustomColor(14, 188, 188, 188);//border
	   $xls->setCustomColor(15, 255, 255, 255);//white
	   $xls->setCustomColor(16, 255, 0, 0);//red
	   $xls->setCustomColor(17, 164, 164, 164);//border table head bottom
	   
	   $whiteFormat =& $xls->addFormat();
	   $whiteFormat->setFgColor(15);
	   for($i=0;$i<=50;$i++){
		   
		   for($j=0;$j<=50;$j++){
				$sheet->write($i,$j,"",$whiteFormat); 	   
			}
		}

	   $titleFormat =& $xls->addFormat();
	   $titleFormat->setBold();
	   $titleFormat->setSize('8');

	   $titleFormatTable =& $xls->addFormat();
	   $titleFormatTable->setBold();
	   $titleFormatTable->setSize('8');
//	   $titleFormatTable->setBorderColor(16);
	   $titleFormatTable->setFgColor(12);
	   $titleFormatTable->setBottomColor(17);
	   $titleFormatTable->setLeftColor(14);
	   $titleFormatTable->setBottom(2);
	   $titleFormatTable->setLeft(1);

	   $titleFormat->setFgColor(12);
	   $titleFormat->setBorderColor(14);
	   $titleFormat->setBottom(1);
	   $titleFormat->setTop(1);
	   
	   $textFormatBold =& $xls->addFormat();
	   $textFormatBold->setSize('8');
	   $textFormatBold->setBold();
	   $textFormatBold->setFgColor(13);
	   $textFormatBold->setBorderColor(14);
	   $textFormatBold->setBottom(1);
	   $textFormatBold->setTop(1);
	   $textFormatBold->setAlign('left');


	   $textFormat =& $xls->addFormat();
	   $textFormat->setSize('8');
	   $textFormat->setFgColor(13);
	   $textFormat->setBorderColor(14);
	   $textFormat->setBottom(1);
	   $textFormat->setTop(1);
	   $textFormat->setAlign('left');

	   $textFormatTable =& $xls->addFormat();
	   $textFormatTable->setSize('8');
	   $textFormatTable->setFgColor(13);
	   $textFormatTable->setBorderColor(14);
	   $textFormatTable->setBottom(1);
	   $textFormatTable->setLeft(1);
	   $textFormatTable->setTop(1);
	   $textFormatTable->setAlign('left');

	   $cellLeftFormat =& $xls->addFormat();
	   $cellLeftFormat->setAlign('left');
		$sheet->setColumn(1,1,70);
		$sheet->setColumn(1,2,10);
		$sheet->setColumn(1,3,0);
		$sheet->setColumn(1,5,10);
		$sheet->setColumn(1,6,25);
		$sheet->setColumn(1,7,30);
		$sheet->setColumn(1,8,30);
		
		$sheet->write(1,1,"Статистика подачи заявок на Конкурс c ".$_GET['datestart'].' по '.$_GET['dateend'],$textFormatBold); 
		$sheet->write(1,2," ",$titleFormat); 
		
		$sheet->write(3,1,"Общая статистика за весь период:",$h2Format); 
		$sheet->write(4,1,"Всего заявок:",$titleFormat); 
		$sheet->write(5,1,"Всего индивидуальных заявок:",$titleFormat); 
		$sheet->write(6,1,"Всего заявок коллективных заявок:",$titleFormat); 

		$sheet->write(4,2,$all_i_Count+$all_g_Count,$textFormat); 
		$sheet->write(5,2,$all_i_Count,$textFormat); 
		$sheet->write(6,2,$all_g_Count,$textFormat); 

		$sheet->write(8,1,"Общая статистика за выбранный период:",$h2Format); 

		$sheet->write(9,1,"Всего заявок:",$titleFormat); 
		$sheet->write(10,1,"Всего индивидуальных заявок:",$titleFormat); 
		$sheet->write(11,1,"Всего заявок коллективных заявок:",$titleFormat); 

		$sheet->write(9,2,$per_i_Count+$per_g_Count,$textFormat); 
		$sheet->write(10,2,$per_i_Count,$textFormat); 
		$sheet->write(11,2,$per_g_Count,$textFormat); 


		$sheet->write(3,5,"Статистика по дням:",$h2Format); 


		$sheet->write(4,5,"Дата",$titleFormatTable); 
		$sheet->write(4,6,"Общее количество заявок",$titleFormatTable); 
		$sheet->write(4,7,"Количество индивидуальных заявок",$titleFormatTable); 
		$sheet->write(4,8,"Количество коллективных заявок",$titleFormatTable); 




		$i=4;
		
		foreach($days as $k=>$v){
			$i++;


			$sheet->write($i,5,$k,$textFormat); 
			$sheet->write($i,6,$v['g']['scount']+$v['i']['scount'],$textFormatTable); 
			$sheet->write($i,7,$v['i']['scount'],$textFormatTable); 
			$sheet->write($i,8,$v['g']['scount'],$textFormatTable); 


      }
		   
	   //unlink($_SERVER["DOCUMENT_ROOT"]."/cache/tmp.xls");
	   $res=$xls->close();
	  // print_r($res);
	   //print_r($fh);
	   //fclose($fh);
	 //  unlink($tmpfname);
	  exit();

	}
	usetemplate("stats/_head");
	usetemplate("stats/stats");
?>