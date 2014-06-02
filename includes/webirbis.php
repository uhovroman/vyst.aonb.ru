<?php

class WebIrbis {

    public static $DBurl;
    public static $DBurlVEX;
    public static $AppUrl;
    public static $count;
    public static $listVariables;
    public static $filesAttr;
    public static $snippetsAttr;
    private $strQueryDBUrl;
    private $strQueryAppUrl;
    private $strJson;
    private $obj;
    private $currentPage;
    private $regSnippetTag;
    private $regChunkTag;
    private $regTmplValueTag;
    private $nameTmpl;
    private $curEx;
    private $snippetsObj;
    private $DBName;
    private static $strCurEx = '';
    
    
    
//=========================================================================================================================================
    public function __construct($partIni, $arr_query=array()) {
     
        if (!count($arr_query))
        {
	    $this->nameTmpl = 'index';
            $this->strQueryDBUrl = self::$DBurl . '&P21DBN=VYST&I21DBN=outJson&S21P03=STATUS=&S21STR=1&S21STN=1&S21CNR='.self::$count.'&S21SRW=sort&S21SRD=DOWN';
	    $this->strQueryAppUrl = self::$AppUrl.'?tmpl=index&P21DBN=VYST&S21P03=STATUS=&S21STR=1';
        }
        else
        {
	    $this->nameTmpl = $arr_query['tmpl'];
            $str = '';
	    $attrHref = '';
            foreach ($arr_query as $key => $val)
	    {
	     
	      if($key === 'tmpl')
	       continue;
	      if($key === 'curLink')
	      {
	       $this->currentPage = $val;
	       continue;
	      }
	      if($key === 'S21CNR')
	       continue;
	      if($key === 'curEx')
	      {
	       if(iconv_strlen($val) )
	       {
		 $this->makeArrEx(urldecode($arr_query['curEx']));
		 self::$strCurEx = '&curEx='.urlencode($arr_query['curEx']);
	       }
	       continue;
	      }
	      
                $str.='&' . $key . '=' . $val;
		$attrHref.='&' . $key . '=' . $val;
                
            }

            $this->strQueryAppUrl = self::$AppUrl.'?tmpl='.$arr_query['tmpl'].$attrHref;
	    $str .= '&S21CNR=' . self::$count;
	    
	    if(strtoupper($arr_query['P21DBN']) === 'VEX')
	    {
	       $this->DBName = 'VEX';
	       $this->strQueryDBUrl = self::$DBurlVEX . '&I21DBN=' . $partIni[$arr_query['P21DBN']].$str.'&S21STN='.$this->currentPage.'&S21SRW=sort&S21SRD=DOWN';
	    }
	    else
               $this->strQueryDBUrl = self::$DBurl . '&I21DBN=' . $partIni[$arr_query['P21DBN']].$str.'&S21STN='.$this->currentPage.'&S21SRW=sort&S21SRD=DOWN';
        }
	
//-------------------------------------------------------------------------------------------------------------------------------------------      
        
	$this->regSnippetTag = '/\[\[[\s]*([\w]+)[\s]*\??'
			     . '((?:[\s]*\&[a-z][\w]*[\s]*=[\s]*[`][\s]*'
		             . '[^`]*'
		             . '[\s]*[`][\s]*)*)'
			     . '\]\]/is';
	
        $this->regChunkTag = '/\[\[\$[\s]*([-\w]+)[\s]*\]\]/is';
	
	$this->regTmplValueTag = '/\[\[\*\s*(\w+)(?:\:(\w{2}))?\s*\*\]\]/im';
	
	$this->snippetsObj = array();
	
	
	
    }//end __construct()

//=========================================================================================================================================
private function makeArrEx($str) {

 $this->curEx = array();
 
 preg_match_all('/\$(\w{2})\:\:([^\:\$]*)/iu', $str, $array , PREG_SET_ORDER);
 if(count($array))
 for($i=0;$i<count($array);$i++)
 
 $this->curEx[$array[$i][1]] = $array[$i][2];
 
}//end function makeArrEx


//=========================================================================================================================================

public function connectDB($strError){
 
    $strResult = @file_get_contents($this->strQueryDBUrl);
    if (!$strResult)
    {
       $strError = 'Ошибка подключения к Webirbis';
       return false;
    }
//-----------------------------------------------------------------
//В этом блоке удаляем последнюю запятую перед закрывающими скобками, если есть. Иначе получается не валидный Json. Убрать форматом ИРБИСА - ни хрена не получилось	
        $strLen = trim(substr($strResult, strripos($strResult, ",") + 1));           
										     
    if ($strLen === ']}')                                                        
	$strResult = substr($strResult, 0, strripos($strResult, ',')) . $strLen;  
//-----------------------------------------------------------------                                                                                    
        $this->strJson = $strResult;
        $this->obj = json_decode($strResult);
	if($this->obj === null)
	{
	   $strError = 'Резульат запроса не может быть преобразован к формату Json.<br />Результат равен: '.$strResult;
	   return false;
	}
        else if($this->DBName === 'VEX')
	   for ($i = 0; $i < count($this->obj->arrObjects); $i++)
	     for ($j = 0; $j < count($this->obj->arrObjects[$i]->allFields); $j++)
	     {
		  $this->obj->arrObjects[$i]->allFields[$j]->val = urldecode($this->obj->arrObjects[$i]->allFields[$j]->val);
		  $this->obj->arrObjects[$i]->allFields[$j]->val = $this->parseFieldsObjectJson ($this->obj->arrObjects[$i]->allFields[$j]->val);
	     }
 return true;
 
}//end function connectDB


//=========================================================================================================================================
    public function printObject() {
        $str = $this->obj->serchRes . '<br />';

        for ($i = 0; $i < count($this->obj->arrObjects); $i++)
        {
            for ($j = 0; $j < count($this->obj->arrObjects[$i]->allFields); $j++)
            {
                $str .= $this->obj->arrObjects[$i]->allFields[$j]->name;
                $str .= ' : ';
                $str .= $this->obj->arrObjects[$i]->allFields[$j]->val;
                $str .= '<br />';
            }
            $str .= '-------------------------<br />';
        }

        return $str;
        
    }//end function printObject()


//====================================================================================================================================
    public function parsHtml($fileName) {
     
    $pathFile = self::$filesAttr['html']['folder'].$fileName.self::$filesAttr['html']['exten'];
     
        if (file_exists($pathFile))
            $html = @file_get_contents($pathFile);
        else
            return 'File '.$pathFile. ' not found';

	if(!$html)
	    return 'Ошибка чтения файла: '.$pathFile;
	
	
	while(preg_match($this->regChunkTag, $html, $buffer) || preg_match($this->regTmplValueTag, $html, $buffer) || preg_match($this->regSnippetTag, $html, $buffer))
	{
	 
	    if(stripos($buffer[0],'[[$') === 0)
	       $html = str_replace($buffer[0], $this->insertChunk($buffer[1]),$html);

	    else if(stripos($buffer[0],'[[*') === 0)
	       $html = str_replace($buffer[0], $this->insertVariables($buffer),$html);

	    else
	       $html = str_replace($buffer[0], $this->runSnippet($buffer[1],$buffer[2]),$html);
	    

	}//end while
	
        return $html;
	
    }//end function parsHtml()

//====================================================================================================================================

 private function insertChunk($fileName) {  
  
  $pathFile = self::$filesAttr['chunk']['folder'].$fileName.self::$filesAttr['chunk']['exten'];
  if(!file_exists($pathFile))
     return 'File '.$pathFile.'not found';
  
  $strFile = @file_get_contents($pathFile);
  if(!$strFile)
     return 'Ошибка чтения файла '.$pathFile;
  
  return $strFile;
  
 }//end function parsChunk()   
    

//==========================================================================================================================================================

     
    
 private function insertVariables($buffer){
 
     if(array_key_exists($buffer[1],self::$listVariables) && array_key_exists($this->nameTmpl, self::$listVariables[$buffer[1]]))
        return self::$listVariables[$buffer[1]][$this->nameTmpl];
     else if($buffer[1] === 'curExhibition')
	if(array_key_exists($buffer[2],$this->curEx))
           return $this->curEx[$buffer[2]];
	else
	   return '';
     else 
	return ''; 
	  

 
} //end function parseVariables
 
  
 
//====================================================================================================================================
    private function runSnippet($nameSnip, $paramList) {
     
        if(iconv_strlen($paramList))//if(1)
	{
           $arrParam = preg_split('/`[\s]*&/', $paramList, -1, PREG_SPLIT_NO_EMPTY);
        
	   for($i = 0; $i < count($arrParam); $i++)
	   {
	      $arrParam[$i] = str_replace('`', '', $arrParam[$i]);
	      $arrParam[$i] = array(trim(substr($arrParam[$i], 0, stripos($arrParam[$i], '='))),substr($arrParam[$i], stripos($arrParam[$i], '=')+1));

	   }

	   $arrParam[0][0] = substr($arrParam[0][0],1);
	
	}//end if(1)
	else
	   $arrParam = '';
//---------------------------------------------------------------------------------------------------------------------------------------------------------
	
	$snippetPath = self::$filesAttr['snippets']['folder'];
            
	foreach(self::$snippetsAttr as $key => $val)
	{
	 if($key === $nameSnip)
	 {
	    $defParam = array();
	    if(count(self::$snippetsAttr[$key])>3)
	      $defParam = array_slice(self::$snippetsAttr[$key], 3);
	     
	    if(self::$snippetsAttr[$key][2] === 1)
	       array_unshift($defParam, &$this->obj->arrObjects, &$this->currentPage);
	    else if(self::$snippetsAttr[$key][2] === 2)
	       array_unshift($defParam, &$this->obj);
	    else if(self::$snippetsAttr[$key][2] === 3)
               array_unshift($defParam, &$this->obj->serchRes,&$this->currentPage,&self::$strCurEx,&$this->strQueryAppUrl);
	     
	    if(!array_key_exists(self::$snippetsAttr[$key][1], $this->snippetsObj) || $this->snippetsObj[self::$snippetsAttr[$key][1]] === null)
	       $this->snippetsObj[self::$snippetsAttr[$key][1]] = new self::$snippetsAttr[$key][1]($defParam);
	    
	    return $this->snippetsObj[self::$snippetsAttr[$key][1]]->runSnippet(&$arrParam);
	  
	 }//end if($key === $nameSnip)   
	    
	}//end foreach
	
    }//end function parsChunk()



//=================================================================================================================================================

    public function __get($name) {
        
        switch ($name) {
            case 'getJsonStr':
	        return $this->strJson; //выдаем наружу строку в формате json
                break;
            case 'getJsonObj':
	        return $this->obj; //выдаем наружу объект, полученный из строки json
                break;
	      
            default:

                break;
        }
        
    } //end function __get()


//=================================================================================================================================================
    
private function deleteUnpairedClosingTag($strTmpl, $strTagName){
 
$strLenTmpl = strlen($strTmpl);
$curPos = 0;
$counter = 0;
$strLenTagName = strlen($strTagName);
$openTagL = '<'.strtolower($strTagName).'>';
$closeTagL = '</'.strtolower($strTagName).'>';
$openTagU = '<'.strtoupper($strTagName).'>';
$closeTagU = '</'.strtoupper($strTagName).'>';

    while($curPos < $strLenTmpl)
    {
         if(substr($strTmpl,$curPos,$strLenTagName+2) === $openTagL || substr($strTmpl,$curPos,$strLenTagName+2) === $openTagU)
	 {
	    $counter++;
	    $curPos += $strLenTagName+2;
	    continue;
	 }

         if(substr($strTmpl,$curPos,$strLenTagName+3) === $closeTagL || substr($strTmpl,$curPos,$strLenTagName+3) === $closeTagU)
	 {
	    if($counter === 0)
	    {
	       $strTmpl = substr_replace($strTmpl, '',$curPos ,$strLenTagName+3);
	       $strLenTmpl = strlen($strTmpl);
	       $curPos += $strLenTagName+3;
	       continue;
	    }
	    else if($counter > 0)
	    {
	       $counter--;
	       $curPos += $strLenTagName+3;
	       continue;
	    }
	 }
	 
	 $curPos++;
	 
    }//end while
 return $strTmpl;
}//end function deleteTag

//=================================================================================================================================================
private function parseFieldsObjectJson(&$fieldVal){
          $fieldVal = trim($fieldVal);
	  
	  $startPos = stripos($fieldVal,'[[~');
	  $endPos = stripos($fieldVal,'~]]');
	  if($startPos !== false && $endPos !== false)
	     $fieldVal = substr_replace($fieldVal,'',$startPos,$endPos+3);
    
	  $fieldVal = preg_replace('/\<([^a-z\>]+)\>/iu','$1',$fieldVal);
          $fieldVal = strip_tags($fieldVal,'<b>,<br>,<tr>');
	  $fieldVal = preg_replace('/\<\s*b\s*\>\s*\<\s*\/b\s*\>/iu','',$fieldVal);
	  
	   while(stripos($fieldVal,'. - . - '))
	     $fieldVal = str_ireplace('. - . - ','. - ', $fieldVal); 
	   while(stripos($fieldVal,'.. - '))
	     $fieldVal = str_ireplace('.. - ','. - ', $fieldVal);
	   while(stripos($fieldVal,'&nbsp;'))
	     $fieldVal = str_ireplace('&nbsp;','', $fieldVal);
	   
	   while(stripos($fieldVal,'<br>') === 0)
	      $fieldVal = substr_replace($fieldVal,'',0,4);
	   while(stripos($fieldVal,'<br>') === strlen($fieldVal)-5)
	      $fieldVal = substr_replace($fieldVal,'',strlen($fieldVal)-5,4);
	   
	  $fieldVal = $this->deleteUnpairedClosingTag($fieldVal, 'b');
	  $fieldVal = $this->deleteUnpairedClosingTag($fieldVal, 'tr');
	  $fieldVal = str_ireplace('<tr>','', $fieldVal);
	  $fieldVal = str_ireplace('</tr>','<br />', $fieldVal);
	  $fieldVal = str_ireplace('<br>','<br />', $fieldVal);
	  $fieldVal = str_ireplace('<br /><br />','<br />', $fieldVal);
	  $fieldVal = str_ireplace('<br /> <br />','<br />', $fieldVal);
	  
	  return $fieldVal;
}//end function 




}//end class WebIrbis


?>
