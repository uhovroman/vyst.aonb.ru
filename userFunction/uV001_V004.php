<?php

function uV001_V004($thisVal, $thisObj){
 
$strOut = ''; 
$arrH3 = array(); 
 
$v001 = $thisObj->selectFields('v001');
if(count($v001))
{
   $v001 = $v001[0];
   $arrH3[] = 'MeSH';
}
else
   $v001 = '';

$v002 = $thisObj->selectFields('v002');
if(count($v002))
{
   $v002 = $v002[0];
   $arrH3[] = 'рубрики';
}
else
   $v002 = '';

$v003 = $thisObj->selectFields('v003');
if(count($v003))
{
   $v003 = $v003[0];
   $arrH3[] = 'ключевые слова';
}
else
   $v003 = '';

$v004 = $thisObj->selectFields('v004');
if(count($v004))
{
   $v004 = $v004[0];
   $arrH3[] = 'дескрипторы';
}
else
   $v004 = '';

$strOut = ''; 
$countH3 = count($arrH3); 
 
if($countH3)
{
   $str = implode(', ',$arrH3);
   $strOut = '<h3>'.mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8').mb_substr($str, 1, mb_strlen($str, 'UTF-8'), 'UTF-8').'</h3><p class="open">';
   
   if($v001)
      $strOut .= $v001.'<br />';
   if($v002)
      $strOut .= $v002.'<br />';
   if($v003)
      $strOut .= $v003.'<br />';
   if($v004)
      $strOut .= $v004.'<br />';
   
   $strOut .= '</p>';
}//end if

return $strOut;

}//end function uV001-V004
?>
