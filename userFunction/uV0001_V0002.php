<?php 

function uV0001_V0002($thisVal, $thisObj){
 
$strOut = ''; 
$arrH3 = array(); 
 
$v0001 = $thisObj->selectFields('v0001');
if(count($v0001))
{
   $v0001 = $v0001[0];
   $arrH3[] = 'Аннотация';
}
else
   $v0001 = '';

$v0002 = $thisObj->selectFields('v0002');
if(count($v0002))
{
   $v0002 = $v0002[0];
   $arrH3[] = 'приплетено';
}
else
   $v0002 = '';



$strOut = ''; 
$countH3 = count($arrH3); 
 
if($countH3)
{
   $str = implode(', ',$arrH3);
   $strOut = '<h3>'.mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8').mb_substr($str, 1, mb_strlen($str, 'UTF-8'), 'UTF-8').'</h3><p class="open">';
   
   if($v0001)
      $strOut .= $v0001.'<br />';
   if($v0002)
      $strOut .= $v0002.'<br />';
   
   
   $strOut .= '</p>';
}//end if

return $strOut;

}//end function uV0001_V0002

?>
