<?php

class snippetIf{

public function runSnippet($arrParam){
 
 
  $count = count($arrParam);
  $else = '';
  $elseIf = array();
 
  if($count < 2)
     return 'snippet If: Syntax error. Incorrect number of parameters';
  if($arrParam[0][0] !== 'cond')
     return 'snippet If: Syntax error. 1-st parameter should be the name "cond"';
  if($arrParam[1][0] !== 'then')
     return 'snippet If: Syntax error. 2-st parameter should be the name "then"';
  
  if($arrParam[$count-1][0] === 'else')//если задано последнее условие else, запоминаем его и удаляем из массива
  {
     $else = $arrParam[$count-1][1];
     array_pop($arrParam);
     $count = count($arrParam);
  }
  
  
  if($count > 2)//если после удаления else(если оно было) у нас остались параметры сниппета после первого then, тогда обрабатываем их
     for($i=2;$i<$count;$i+=2)
     {
         //если где-то посередине затисалось условие else
         if($arrParam[$i][0] === 'else' || ($i+1 < $count && $arrParam[$i+1][0] === 'else')
					
	   )
            return 'snippet If: Syntax error. If there is an alternative condition, then the last parameter should be the "else"';
	 
	 
	 if($arrParam[$i][0] !== 'elseIf')
	    return 'snippet If: Syntax error. '.$i.'-st parameter should be the name "elseIf"';
	 
	 if($i+1 > $count-1 || $arrParam[$i+1][0] !== 'then')
	    return 'snippet If: Syntax error. '.($i+1).'-st parameter should be the name "then"';
	 
	 //если шаблон валидный, запоминаем значения параметров: elseIf, operator, operand - в указанной последовательности в массив массивов
	 $elseIf[] = array($arrParam[$i][1],$arrParam[$i+1][1]);//,$arrParam[$i+2][1],$arrParam[$i+3][1]);
	 
	 
     }//end for
     $countElseIf = count($elseIf);
     
     
     $execString = 'if('.$arrParam[0][1].'){ return '.$arrParam[1][1].';}';
     if($countElseIf)
     for($j=0;$j<$countElseIf;$j++)
         $execString .= ' else if('.$elseIf[$j][0].')'.'{ return '.$elseIf[$j][1].';}';
     if($else !== '')//если задано последнее условие else
        $execString .= ' else {return '.$else.';}';
     
     return @eval($execString);
     
 
}//end function runSnippet
 
//==========================================================================================================================================================
      
 
 
}//end class snippetIf

?>