<?php 

require_once (SYSTEM_PEAR_XLS_PATH."Writer.php"); 



// Создание случая 

$xls =& new Spreadsheet_Excel_Writer(); 



// Отправка HTTP заголовков для сообщения обозревателю о типе вxодимыx //данныx  

$xls->send("test.xls"); 



// Добавление листа к файлу, возвращение объекта для добавления данныx

$sheet =& $xls->addWorksheet('Binary Count'); 



// Пишем несколько цифр  

for ( $i=0;$i<11;$i++ ) { 

 // Использование функции PHP decbin()для преобразования целого числа в //бинарные данные

 $sheet->write($i,0,"Вася"); 

 $sheet->write($i,1,"Вася"); 

} 

// Конец листа, отправка обозревателю

$xls->close(); 

?>