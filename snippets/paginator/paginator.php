<?php


class paginator{

private $link;
private $currentPage;
private $maxCount;
private $strQueryAppUrl;
private $count;
private $strCurEx;
private $currentTag;
private $containerItem;
private $stylePaginator;
 

//==========================================================================================================================================================


public function __construct($defParam) {
 
 $this->maxCount = $defParam[0];
 $this->currentPage = $defParam[1];
 $this->strCurEx = $defParam[2];
 $this->strQueryAppUrl = $defParam[3];
 $this->count = $defParam[4];
 $this->link = $defParam[5];
 $this->currentTag = $defParam[6];
 $this->containerItem = $defParam[7];
 $this->stylePaginator = $defParam[8];
 
 
}//end function __construct
 
 
 
 
//=================================================================================================================================================    
     
 public function runSnippet($arrParam){
  
  if($this->maxCount > $this->count)//выводим пагинатор,только если количество полученных из БД записей больше количества выводимых на одной странице записей
     {
        $strPaginator = '';
	  if(empty($this->currentPage))
	     $this->currentPage = 1;
	$first=$this->currentPage-$this->link*$this->count;
	  if($first<1)
	     $first=1;
	$last=$this->currentPage+$this->link*$this->count;
	$endPart = (ceil($this->maxCount/$this->count)-1)*$this->count+1;
	$remainder = $this->maxCount - $endPart;
	  if($last>$endPart)
	     $last=$endPart;
//-----------------------------------------------------------------------------------------------------------------------------------------------------
	//начало вывода нумерации
	//выводим первую страницу
 
	if($first>1) 
	{
	   if($this->stylePaginator['first'] === 'number')
	   {
	    
	      if($this->count > 1)
		 $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink=1">1 - '.$this->count.'</a>'.$this->containerItem['close'];
	      else
		 $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink=1">1</a>'.$this->containerItem['close'];
	   
	   }
	   else
	      $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink=1">'.$this->stylePaginator['first'].'</a>'.$this->containerItem['close'];
	    
	   
	}//end if ($first>1)
	
//-----------------------------------------------------------------------------------------------------------------------------------------------------	 
	//если страница, находящаяся перед первой из диапазона выводимых страниц, не является первой или второй, тогда 
	//скрываем троеточием все страницы между первой страницее и страницей, которая является первой в диапазоне ($first)
	$y=$first-$this->count;
	if($y > 1+$this->count)
	{
	  if($this->stylePaginator['prev'] === 'number')
	     $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$y.'">...</a>'.$this->containerItem['close'];
	  else
	     $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$y.'">'.$this->stylePaginator['prev'].'</a>'.$this->containerItem['close'];
	}
	else if($y == 1+$this->count)// иначе, если она является второй, тогда выводим ссылку на неё
	{
	 
	  if($this->stylePaginator['prev'] === 'number')
	  {
	     if($this->count > 1)
	       $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$y.'">'.$y.' - '.($y-1+$this->count).'</a>'.$this->containerItem['close'];
	     else
	       $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$y.'">'.$y.'</a>'.$this->containerItem['close'];
	  }
	  else 
	     $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$y.'">'.$this->stylePaginator['prev'].'</a>'.$this->containerItem['close'];
	  
	}//end if($y > 1+$this->count)
 
//-----------------------------------------------------------------------------------------------------------------------------------------------------
	//отображаем заданный диапазон: текущая страница +-prev
	for($i=$first;$i<=$last;$i+=$this->count)//begin (for 1) 
	{
	    
	    $textLink = $i;
	    if($this->count > 1)
	    {
	     
		 if($i==$last && $last==$endPart)
		 {
		    if($remainder > 0)
		       $textLink .= ' - '.$this->maxCount;
		 }
		 else
		 {
		    $q = $i+$this->count-1;
		    $textLink .= ' - '.$q;
		 }
	    }//end if($this->count > 1)
	    
	    if($i==$this->currentPage) //если выводится текущая страница
	      $strPaginator .= $this->containerItem['open'].'<'.$this->currentTag.'>'.$textLink.'</'.$this->currentTag.'>'.$this->containerItem['close'];
	    else 
	      $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$i.'">'.$textLink.'</a>'.$this->containerItem['close'];
	    
	}//end (for 1)

//---------------------------------------------------------------------------------------------------------------------------------------------------
	$y=$last+$this->count;
       //часть страниц скрываем троеточием. По аналогии с первым троеточием
	if($y < $endPart-$this->count)
	{
	   if($this->stylePaginator['next'] === 'number')
	      $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$y.'">...</a>'.$this->containerItem['close'];
	   else
	      $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$y.'">'.$this->stylePaginator['next'].'</a>'.$this->containerItem['close'];
	}
	else if($y == $endPart-$this->count)
	{
	   if($this->stylePaginator['next'] === 'number')
	   {
	      if($this->count > 1)
		 $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$y.'">'.$y.' - '.($y-1+$this->count).'</a>'.$this->containerItem['close'];
	       else
		 $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$y.'">'.$y.'</a> ';
	   }
	   else
	      $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$y.'">'.$this->stylePaginator['next'].'</a>'.$this->containerItem['close'];
	 
	}
	 
 //-------------------------------------------------------------------------------------------------------------------------------------------------
	//выводим последнюю страницу
	if($last < $endPart)//begin (if $last < $endPart)
	{
	   if($this->stylePaginator['last'] === 'number')
	   {
	      if($this->count > 1)
	      {
		if($endPart < $this->maxCount)
		  $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$endPart.'">'.$endPart.' - '.$this->maxCount.'</a>'.$this->containerItem['close'];
		else if($endPart == $this->maxCount)
		  $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$endPart.'">'.$endPart.'</a>'.$this->containerItem['close'];
	      }    
	      else
		$strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$endPart.'">'.$endPart.'</a>'.$this->containerItem['close'];
	      
	   }
	   else
	      $strPaginator .= $this->containerItem['open'].'<a href="'.$this->strQueryAppUrl.$this->strCurEx.'&curLink='.$endPart.'">'.$this->stylePaginator['last'].'</a>'.$this->containerItem['close'];
	}//end (if $last < $endPart)
	
	
	return $strPaginator;

       }//end if($this->obj->serchRes > $this->count)
  
  
  
 }//end function runSnippet()
 
 
 
 
 
 
 
}//end class paginator


?>