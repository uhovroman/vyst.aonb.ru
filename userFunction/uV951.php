<?php

function uV951($thisVal, $thisObj){
 
$v951 = $thisObj->selectFields('v951');
$countV951 = count($v951);
$strOut = ''; 
if($countV951)
{
   if($countV951 > 1)
      $strOut = '<h3 class="links">См. ссылки: </h3><p class="open">';
   else if($countV951 === 1)
      $strOut = '<h3 class="links">См. ссылку </h3><p class="open">';
 
   for($i=0;$i<$countV951;$i++)
   {
       $v951_i = $thisObj->selectSubField($v951[$i],'^i');
       $v951_t = $thisObj->selectSubField($v951[$i],'^t');
       
       if($v951_i)
       {
	  $strImg= '';
	  $h = '';
	  if(preg_match('/^.+\.(jpg|gif|png)$/i',trim($v951_i)))
	  {
	     $strImg = '<img src="'.$v951_i.'?w=200" />';
	     $h = '?h=700';
	  }
	
	  $strOut .= '<a href="'.$v951_i.$h.'" target="_blank">';
	  if($v951_t)
	     $strOut .= $v951_t;
	  else
	     $strOut .= $v951_i;
	  
	  $strOut .= $strImg.'</a>';
	  if($i<$countV951-1)
	     $strOut .= '<br />';
       }
    
   }//end for   
 
   $strOut .= '</p>';
}
 
 
return $strOut;

 
}//end function uV951



?>