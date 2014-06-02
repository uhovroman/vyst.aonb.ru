<?php
$DBurlVEX = 'http://webirbis.aonb.ru/cgi-bin/irbis64r_1/cgiirbis_64.exe?C21COM=S&S21FMT=outJson2';
$DBurl = 'http://webirbis.aonb.ru/cgi-bin/irbis64r_1/cgiirbis_64.exe?C21COM=S&S21FMT=outJson';

$dbNameAssoc = array('VYST'=>'outJson','VEX'=>'outJson2');
$countRec = 5;
$AppUrl = 'http://'.$_SERVER['SERVER_NAME'].'/index.php';

$link = 1;
$currentTag = 'span';
$containerItemPaginator = array('open'=>'<li>','close'=>'</li>');
$stylePaginator = array('first'=>'number','prev'=>'number','next'=>'number','last'=>'number');
$filesAttr = array(
'html'=>array('folder'=>'html/','exten'=>'.html'),   
'snippets'=>array('folder'=>'snippets/','exten'=>'.php'),   
'chunk'=>array('folder'=>'chunk/','exten'=>'.html')   
);

$snippetsAttr = array( 
'formatingRecords'=>array('formatingRecords/','formatingRecords',1,','),
'paginator'=>array('paginator/','paginator',3,$countRec,$link,$currentTag,$containerItemPaginator,$stylePaginator),
'function'=>array('snippetFunction/','snippetFunction',0),
'if'=>array('snippetIf/','snippetIf',0)
);

$listVariables = array(
'defaultURL'=>array('vex'=>$AppUrl.'?tmpl=index&P21DBN=VYST&S21P03=STATUS=&S21STR=1', 'index'=>$AppUrl)
);


function __autoload($class){
   if($class === 'WebIrbis')
    require_once 'includes/webirbis.php';
   else
    require_once "snippets/$class/$class.php";
   
 
}



?>
