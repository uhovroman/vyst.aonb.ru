<?php

class formatingRecords{
 
private $allRecords; 
private $tmpl;
private $regSimpleTag;//регулярное выражение для поиска тэга плейсхолдера'[[+...]]'
private $regOccurTag;//регулярное выражение для поиска тэга '[[#...[[+...]]...[[+...]]...]]'
private $iterObj;
private $fieldsSeparator;
private $arrSelectedFields;
private $uFormat;
private $curRecord;
private static $listUserFunction;
private static $tmplFolder = 'snippets/formatingRecords/tmpl/';
private static $tmplFilesExt = '.html';
private static $formatsFilesExt = '.php';
private static $formatsFolder = 'userFunction/';//путь к каталогу (от index.php), для хранения пользовательских скриптов. 
					        //Имя файла скрипта (без расширения) указываются в тэгах плейсхолдеров в виде вызова функции.
						//К примеру: [[+test_2(v20)]] - файл test_2.php.
						//Эти скрипты предназначаются для форматирования полей/подполей записи из БД, в том случае,
						//если полный вывод значения поля/подполя не устраивает
								     
//==========================================================================================================================================================

public function __construct($defParam) {
 
        $this->allRecords = $defParam[0];
	$this->fieldsSeparator = $defParam[2];
	$this->curRecord = $defParam[1];
	$this->uFormat = array();
	self::$listUserFunction = scandir(self::$formatsFolder);
	
	$this->regSimpleTag =  '(?:\[\[\+[\s]*)'
			       .'(?:'
			       .'([a-z][\w]*)[\s]*[\(][\s]*(?:(?:(v[0-9]+)(\^.)?)'
			       .'|'
			       .'(true|false|[\d]+|(?:\')[^\'\)]*(?:\')|(?:\")[^\"\)]*(?:\"))'
			       .')[\s]*'
			       .'(?<!\()(?<!\,)'
			       .'((?:\,[\s]*(?:true|false|[\d]+|(?:\')[^\'\)]*(?:\')|(?:\")[^\"\)]*(?:\"))+[\s]*)*)[\s]*[\)][\s]*'
			       .'|'
			       .'([a-z][\w]*)[\s]*[\(][\s]*[\)][\s]*'
			       .'|'
			       .'(v[0-9]+)(\^.)?'
			       .')'
			       .'\]\]';
	
	
	$this->regOccurTag =   '\[\[\#'
			       .'(F|L|\d+)*'
                               .'[^\[]*'
			       .'(?:'
			       .'(?:\[\[\+[\s]*'
                               .'(?:'
                               .'[a-z][\w]*[\s]*[\(][\s]*(?:(?:v[0-9]+(?:\^.)?)'
		               .'|'
			       .'(?:true|false|[\d]+|(?:\')[^\'\)]*(?:\')|(?:\")[^\"\)]*(?:\"))'
			       .')[\s]*'
			       .'(?<!\()(?<!\,)'
			       .'(?:\,[\s]*(?:true|false|[\d]+|(?:\')[^\'\)]*(?:\')|(?:\")[^\"\)]*(?:\"))[\s]*)*'
			       .'[\)][\s]*'
			       .'|'
			       .'[a-z][\w]*[\s]*[\(][\s]*[\)][\s]*'
			       .'|'
			       .'v[0-9]+(?:\^.)?'
			       .')'
			       .'\]\]'
		               . '|'
                               . '\[\[[\s]*[\w]+[\s]*\??'
			       . '(?:[\s]*\&[a-z][\w]*[\s]*=[\s]*[`][\s]*'
		               . '[^`]*'
		               . '[\s]*[`][\s]*)*'
			       . '\]\])'
			       .'[^\[\]\|]*'
			       .')+'
			       .'(?:[\s]*\|([^\|]+)\|\+)?\]\]';
	
}//end __construct

//==========================================================================================================================================================




public function runSnippet($arrParam) {
 
 
        $this->nameParam = $arrParam[0][0];
	$this->tmpl = trim($arrParam[0][1]);

        $tmplFileName = self::$tmplFolder . $this->tmpl . self::$tmplFilesExt;
     
        if (!$this->nameParam === 'tmpl')
	    return 'Snippet formatingRecords : invalid name parametr';
	if (!file_exists($tmplFileName))
	    return 'Snippet formatingRecords : invalid value parametr. File '.$tmplFileName.'not exists';
	
	
        $tmpl = @file_get_contents($tmplFileName);
	if(!$tmpl)
	    return 'Ошибка чтения файла '.$tmplFileName;
	
	$tmpl = preg_replace('/(\]\]\s*)(\t|\n)+(\s*\[\[)/im', "\${1}\${3}", $tmpl);//убираем табы и переносы строк ибо браузеры интепретируют их как пробелы
	
	$listFields = array();
	
        //парсим эту строку и находим тэги вставки полей/подполей типа '[[$...[[+...]]...]]' или просто '[[+...]]'
	$strPreg = '/'.$this->regSimpleTag.'|'.$this->regOccurTag.'/im';
	
	preg_match_all($strPreg, $tmpl, $listFields, PREG_SET_ORDER);
		
        $tmplAllRec = '';

        for ($this->iterObj = 0; $this->iterObj < count($this->allRecords); $this->iterObj++) //for(1) Перебор всех имеющихся записей IRBIS в поступившем блоке Json
	{
	     $tmplRec = $tmpl;
	     
	      for ($i = 0; $i < count($listFields); $i++) //for(2) (обработка шаблона)
	      {
	        $result = '';
	       
                  if(stripos(trim($listFields[$i][0]),'[[+') === 0)
                     $result = $this->parseSimleTagInTmpl($listFields[$i]);
	          else if(stripos(trim($listFields[$i][0]),'[[#') === 0)
		     $result = $this->parseOccurTagInTmpl($listFields[$i]);

		  $tmplRec = substr_replace($tmplRec, $result, stripos($tmplRec, $listFields[$i][0]), strlen($listFields[$i][0]));
		  
	      }//end for(2)
	      
	    $tmplRec = preg_replace('/\[\[\+\s*ID\s*\]\]/i', $this->curRecord + $this->iterObj, $tmplRec);  
	      
            $tmplAllRec .= $tmplRec;
	    
	
        }//end for(1)
	

        return $tmplAllRec;
	
	
}//end function runSnippet()

    
//==================================================================================================================================================
    
    private function parseSimleTagInTmpl($array){
    
    $execFunName = ''; //имя пользовательской функции в шаблоне для обработки массива повторений поля/подполя
    $numField = ''; //метка поля - пример 'V910'
    $nameSubField = ''; //метка подполя, - пример '^a' 
    $firstParam = '';//первый аргумент пользовательской функции, но не номер поля
    $strListParam = '';//строка с допольнительными параметрами, перечисленными через запятую
    
       if(!empty($array[1]))//три параметра: функция[1], поле[2] [, подполе[3]]
       {
	    $execFunName = $array[1];
	    $numField = $array[2];
	    if(!empty($array[3]))
	    $nameSubField = $array[3];
       }
       else if(!empty($array[4]))//два(+ доп. аргументы) параметрa: функция[4] + первый аргумент, но не номер поля
       {
	    $execFunName = $array[1];
	    $firstParam = $array[4];
       }
       else if(!empty($array[6]))//один параметр: функция[6] без аргументов
       {    
	    $execFunName = $array[6];
       }
       else if(!empty($array[7]))//два параметра: поле[7] [, подполе[8]]
       {
	    $numField = $array[7];
	    if(!empty($array[8]))
	    $nameSubField = $array[8];
       }//end all if
     
       
       if(!empty($array[5]))//дополнительные аргументы если есть  
	    $strListParam = $firstParam.$array[5];
       
       
       
       if(iconv_strlen($numField))//if(2) если указан номер поля, делаем выборку
       {
	   $this->arrSelectedFields = $this->selectFields($numField, $nameSubField); //находим значение указанного в шаблоне поля/подполя
                                                                                     //результат возвращается в виде массива
           $cnt = count($this->arrSelectedFields);
	   if(!$cnt)//если выборка вернула пустой результат
	      return '';
	    
	    
	      if(iconv_strlen($execFunName))//если указана пользовательская функция
	         for($i=0;$i<$cnt;$i++)
		     $this->arrSelectedFields[$i] = $this->execUserFunction($execFunName, $this->arrSelectedFields[$i], $strListParam);
	   
	   
	   return implode($this->fieldsSeparator, $this->arrSelectedFields);
	   
       }
       else//если номер поля не указан, значит указа пользовательская функция
	   return $this->execUserFunction($execFunName);
	   
    
    }//end function parseSimleTagInTmpl


//==================================================================================================================================================
    
   private function parseOccurTagInTmpl($array){
    
      $occur = 'ALL';
      $lenOccur = 0;
      $literal = '';
      $lenLiteral = 0;
      
      if(isset($array[10]))
      {
         $literal = $array[10];
	 $lenLiteral = iconv_strlen($array[10])+3;
      }
      
      if(isset($array[9]) && !empty($array[9]))
      {
         $occur = strtoupper($array[9]);
	 $lenOccur = iconv_strlen(trim($array[9]));
      }
      
      
      
      $strTmpl = substr($array[0], 3+$lenOccur, iconv_strlen($array[0])-(5+$lenOccur+$lenLiteral));//получаем исходную строку из шаблона, без начальных '[[#' и конечных ']]'
      $strPreg = '/'.$this->regSimpleTag.'/ium';
      
      preg_match_all($strPreg, $strTmpl, $listFields, PREG_SET_ORDER);
      
//-------------------------------------------------------------------------------------------------------------------------------------------------
//Проверяем что бы в тэге повторения, в тэгах полей были указаны одинаковые номера полей или вызов пользовательских функций без параметров

      
      $arrTotal = array();
      $numField = '';
      
      for($i=0;$i<count($listFields);$i++)//for(1)
      {
       
        $arrTotal[$i][0] = $listFields[$i][0];//исходное выражение, например '[[+v70^a]]'
       
        if(!empty($listFields[$i][1]))//три параметра: функция[1], поле[2] [, подполе[3]] [, дополнительные аргументы]
	{
	     $arrTotal[$i][1] = $listFields[$i][1];//пользовательская функция
	     $arrTotal[$i][2] = $listFields[$i][2];//номер поля
	     if(!empty($listFields[$i][3]))
	     $arrTotal[$i][3] = $listFields[$i][3];//метка подполя
	     else
	     $arrTotal[$i][3] = '';
	     if(!empty($listFields[$i][5]))
	     $arrTotal[$i][4] = $listFields[$i][5];//дополнительные аргументы функции
	     else
	     $arrTotal[$i][4] = '';
	}
	else if(!empty($listFields[$i][4]))//два(+ доп. аргументы) параметр: функция[4] + первый аргумент, но не номер поля
	{
	     $arrTotal[$i][1] = $listFields[$i][1];
	     $arrTotal[$i][2] = false;
	     $arrTotal[$i][3] = '';
	     $arrTotal[$i][4] = $listFields[$i][4];
	     if(!empty($listFields[$i][5]))
	     $arrTotal[$i][4] .= $listFields[$i][5];
	}
	else if(!empty($listFields[$i][6]))//один параметр: функция[6] без аргументов
	{
	     $arrTotal[$i][1] = $listFields[$i][6];
	     $arrTotal[$i][2] = false;
	     $arrTotal[$i][3] = '';
	     $arrTotal[$i][4] = '';
	}
	else if(!empty($listFields[$i][7]))//два параметра: поле[7] [, подполе[8]]
	{
	     $arrTotal[$i][1] = '';
	     $arrTotal[$i][2] = $listFields[$i][7];
	     if(!empty($listFields[$i][8]))
	     $arrTotal[$i][3] = $listFields[$i][8];
	     else
	     $arrTotal[$i][3] = '';
	     $arrTotal[$i][4] = '';//дополнительные аргументы функции
	}//end all if
       
       
        if($numField === '')
	   $numField = $arrTotal[$i][2];
        else if($numField !== $arrTotal[$i][2])//если в тэге повторения указаны разные поля, возвращаем тэг повторения и текст ошибки
	   return htmlentities($array[0]).' :: WARNING!!! Provide different numbers fields.';
      
      
      }//end for(1)
      unset($listFields);
      
//-------------------------------------------------------------------------------------------------------------------------------------------------
 
     if(!$numField)//если не указано ни одного поля, но есть вызов функции/функций      
     {
        $sumFunc = '';//общая строка с результатом выполнения всех вызванных функций
	$resFunc = '';//строка с результатом выполнения функции
        for($j=0;$j<count($arrTotal);$j++)
	{
	    $resFunc = $this->execUserFunction($arrTotal[$j][1],'',$arrTotal[$j][4]);
            $strTmpl = substr_replace($strTmpl, $resFunc, stripos($strTmpl, $arrTotal[$j][0]), strlen($arrTotal[$j][0]));
	    $sumFunc .= $resFunc;
	}
     
	if(iconv_strlen($sumFunc))//если функции вернули результат, возвращаем шаблон с вставленным результатом
           return $strTmpl;
	else
	   return '';//иначе возвращаем пустую строку
     }
     
      
//-------------------------------------------------------------------------------------------------------------------------------------------------  
// Все ниже работает с полями
     
      $this->arrSelectedFields = $this->selectFields($numField);
      
      if(!count($this->arrSelectedFields))//если результат выборки пустой, возвращаем пустую строку в шаблон (повторений 0)
          return '';
      
//-------------------------------------------------------------------------------------------------------------------------------------------------      
      $countSelect = count($this->arrSelectedFields);
      $countTotal = count($arrTotal);
       
      if($occur === 'ALL')//если повторения поля не указано, то выводим все повторения поля
      {
       
          $strOuter = '';   
	  
          for($k=0;$k<$countSelect;$k++)//for (2)
          {
	       $strTmplCopy = $strTmpl;
	       $sumStr = '';//общая строка с результатом поиска поля/подполя или функции
	   
	       for($x=0;$x<$countTotal;$x++)//for(2.1)
	       {
		 $result = '';

		 if(!empty($arrTotal[$x][2]))
		    $result = $this->selectSubField($this->arrSelectedFields[$k],$arrTotal[$x][3]);

		 if(!empty($arrTotal[$x][1]))
		    $result = $this->execUserFunction($arrTotal[$x][1],$result,$arrTotal[$x][4]);

                 $sumStr .= $result;
		 $strTmplCopy = substr_replace($strTmplCopy, $result, stripos($strTmplCopy, $arrTotal[$x][0]), iconv_strlen($arrTotal[$x][0]));
                 
	       }//end for(2.1)
	   
	       if(iconv_strlen($sumStr))//если указанные в шаблоне поля/подполя или результат выполнения вызываемых функции в сумме образовали не пустую строку
	       {  
		$strOuter .= $strTmplCopy;//возвращаем обработанный для данного повторения поля шаблон
		if($k < $countSelect-1)
		    $strOuter .= $literal;
	       }
	       else
	         $strOuter .= '';//иначе шаблон повторения не нужен
	       
	  }//end for(2)
       
          return $strOuter;
       
      }
      else // иначе выводим указанное в шаблоне повторение(если такое повторение поля в БД есть)
      {
           $currentRec = ''; //значение поля(целиком со всеми подполями), с указанным повторением
      
	   if(is_numeric($occur))//если повторение поля указано в виде числа
	   {
	      if($occur>0)//и если оно больше нуля, иначе такого повторения просто не существует
	      {
		  if($occur>$countSelect)
		     return '';
		  else
		     $currentRec = $this->arrSelectedFields[$occur-1];
	      }
	      else
	          return '';

	   }//end if($occur>0)
      
	   if($occur === 'F')
              $currentRec = $this->arrSelectedFields[0];

	   if($occur === 'L')
              $currentRec = $this->arrSelectedFields[count($this->arrSelectedFields)-1];
	   
	      $sumStrOne = '';//общая строка с результатом поиска поля/подполя или функции
	      for($j=0;$j<$countTotal;$j++)//for(3)
	      {
		 $result = '';

		 if(!empty($arrTotal[$j][2]))//если указано поле
		    $result = $this->selectSubField($currentRec,$arrTotal[$j][3]);

		 if(!empty($arrTotal[$j][1]))//если указана функция
		    $result = $this->execUserFunction($arrTotal[$j][1],$result,$arrTotal[$j][4]);

                 $sumStrOne .= $result;
		 $strTmpl = substr_replace($strTmpl, $result, stripos($strTmpl, $arrTotal[$j][0]), iconv_strlen($arrTotal[$j][0]));

	      }//end for(3)
	   
	   if(iconv_strlen($sumStrOne))//если указанные в шаблоне поля/подполя или результат выполнения вызываемых функции в сумме образовали не пустую строку 
	      return $strTmpl;//возвращаем обработанный для данного повторения поля шаблон
	   else
	      return '';//иначе шаблон повторения не нужен
	      
      }//end if
      
      
     

   }//end function parseOccurTagInTmpl
   
   
   
 

//=================================================================================================================================================
    private function execUserFunction($execFunName, $thisVal = false, $strListParam = '') {
     
        $resultUF = '';
	$strEval;
	 
	if(in_array($execFunName.self::$formatsFilesExt, self::$listUserFunction))
	   $strEval = "return ".$execFunName."('".$thisVal."', &\$this ".$strListParam.");";
	else
	{
	   if($thisVal === false)  
	      $strEval = "return ".$execFunName."(".$strListParam.");";
	   else
	      $strEval = "return ".$execFunName."('".$thisVal."'".$strListParam.");";
	 
	}
	
        if(function_exists($execFunName))//проверяем, является ли функция встроенной
	   $resultUF = @eval($strEval);
       
        else if (!file_exists(self::$formatsFolder . $execFunName . self::$formatsFilesExt))//проверяем есть ли такой скрипт
           return self::$formatsFolder . $execFunName . self::$formatsFilesExt . ' file not found';
	
	else
	{
	   include_once self::$formatsFolder . $execFunName . self::$formatsFilesExt;
	   $resultUF = @eval($strEval);
	}
	   

	if(!is_string($resultUF) && !is_int($resultUF) && !is_float($resultUF) && !is_bool($resultUF))
	   $resultUF = 'Ошибка выполнения функции '.$execFunName.'. Результат не является строкой';
	
        return $resultUF;
	
	
    }//end function execUserFunction()




//=================================================================================================================================================
    public function selectSubField($value, $nameSubField) {

        if (!$nameSubField || (strlen($nameSubField) !== 2 && stripos($nameSubField, '^') !== 0))//если метка подполя не указана, записываем в результат значение поля целиком
            return $value;
    
        if(is_string($value))
	   return $this->selectSubFieldInString($value, $nameSubField);//возвращаем строку
	else if(is_array($value) && count($value))
	{
	   $countArr = count($value);
	   $resultArray = array();
	   for($i=0;$i<$countArr;$i++)
	   {
	      $str = $this->selectSubFieldInString($value[$i], $nameSubField);
	      if($str)
	         $resultArray[] = $str;
	     
	   }//end for
	    
	   return $resultArray;//возвращаем массив
	   
	}//end elseif
	
    }//end function field()

//=================================================================================================================================================
    private function selectSubFieldInString($value, $nameSubField) {

        $startSubField = stripos($value, $nameSubField); //находим позицию метки подполя

        if ($startSubField === false)
            return ''; //если такая метка не найдена, сохраняем пустую строку в массиве

        $startSubField = $startSubField + 2; // делаем смещение на два символа: ^a или ^h, к примеру

        if (stripos($value, '^', $startSubField) !== false)//если нужное подполе не последнее в строке
            $lenSubField = stripos($value, '^', $startSubField) - $startSubField;
        else
            $lenSubField = strlen($value) - $startSubField;

        return substr($value, $startSubField, $lenSubField);
	
    }//end function field()
    
    
    
    
//=================================================================================================================================================
    public function selectFields($numField, $nameSubField = '') {//делаем поиск и выборку поля/подполя, из массива всех полей конкретной ($this->iterObj) записи
	
        $searchResults = array();

        for ($j = 0; $j < count($this->allRecords[$this->iterObj]->allFields); $j++) //for(1) проходим по всем полям одной конкретной записи
	{
            if (strtoupper($this->allRecords[$this->iterObj]->allFields[$j]->name) === strtoupper($numField))//если метка поля найдена в массиве полей
	        if('' !== $res = $this->selectSubField($this->allRecords[$this->iterObj]->allFields[$j]->val, $nameSubField))
                $searchResults[] = $res;
        }//end for(1)

	
	if(strtoupper($numField) === 'V907' && stripos($numField,'^') === false)//по непонятной причине, 907 поле возвращает на одно повторения больше.
	   array_pop($searchResults);                                           //Этого повторения в базе нет (содержит кашу из времени и каких-то букв)
	                                                                        //поэтому это последнее повторение удаляем из результирующего массива
        return $searchResults; //возвращаем массив. Если повторение поля/подполя одно, массив будет состоят из одного элемента
        
    }//end function selectFieldOrSubfield()


//=================================================================================================================================================
   public function consistOfSubFields(){//проверяем, состоит ли поле (первый параметр) из строго указанных подполей (последующие параметры: например '^a','^b')
    
       $arrArgs = func_get_args();
       $thisVal = mb_convert_case($arrArgs[0], MB_CASE_UPPER,"UTF-8"); 
       $arrArgs = array_slice($arrArgs, 1);
       $countArgs = count($arrArgs);
       
       if(substr_count($thisVal,'^') !== $countArgs)
	  return false;
       for($i=0;$i<$countArgs;$i++)
       {
	
           if(strlen($arrArgs[$i]) !== 2 && stripos($arrArgs[$i], '^') !== 0)
              return false;

           if(stripos($thisVal,mb_convert_case($arrArgs[$i], MB_CASE_UPPER,"UTF-8")) === false)
	      return false;   
		   
		   
		   
       }//end for
    
       return true;
       
       
   }// end function consistOfSubFields

   
   
   
//=================================================================================================================================================
   

    
    
    
 
}//end class formatingRecords



?>
