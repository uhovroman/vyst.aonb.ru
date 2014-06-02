<?php 

function uV000002_V00002_V000001($thisVal, $thisObj){
 
$strOut = ''; 
$arrH3 = array(); 

$v000002 = $thisObj->selectFields('v000002');
if(count($v000002))
{
   $v000002 = $v000002[0];
   $arrH3[] = 'Держатели документа';
}
else
   $v000002 = '';

$v00002 = $thisObj->selectFields('v00002');
if(count($v00002))
{
   $v00002 = $v00002[0];
   $arrH3[] = 'точки доступа';
}
else
   $v00002 = '';

$v000001 = $thisObj->selectFields('v000001');
if(count($v0001))
{
   $v000001 = $v000001[0];
   $arrH3[] = 'учебная литература';
}
else
   $v000001 = '';

$strOut = ''; 
$countH3 = count($arrH3); 
 
if($countH3)
{
   $str = implode(', ',$arrH3);
   $strOut = '<h3>'.mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8').mb_substr($str, 1, mb_strlen($str, 'UTF-8'), 'UTF-8').'</h3><p class="open">';
   
   if($v000001)
      $strOut .= $v000001.'<br />';
   if($v00002)
      $strOut .= $v00002.'<br />';
   if($v000002)
      $strOut .= $v000002.'<br />';
   
   $strOut .= '</p>';
}//end if

return $strOut;

}//end function uV000002_V00002_V000001

?>
