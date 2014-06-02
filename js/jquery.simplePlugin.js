
$.fn.simplePlugin = function(){

 var previewElement = $('<div id="preview">').appendTo('body').css({
	 'position':'absolute',
	 'visibility':'hidden',
	 'z-index':101
	 });
  
    return this.each(function(){
	 
	var $thisImg = $('img',this);
	var $cloneThisImg = $thisImg.clone();
	var flag = false;
	 
    $(this).hover(function(){
		  
        if ($thisImg.width() > 100) {
	
	      var scrollTop = $(document).scrollTop();
		  var thisOffset = $(this).offset();
		  var thisHeight = $(this).outerHeight();
		  var thisWidth = $(this).outerWidth();
		  var thisImgHeight = $thisImg.outerHeight();
		  var thisImgWidth = $thisImg.outerWidth();
	 
	      flag = true;
	 
	      if(scrollTop < thisOffset.top - thisImgHeight/4 && scrollTop + $(window).height() > thisOffset.top + thisImgHeight/4)
		  {
			  previewElement.css({
			  'top':thisOffset.top-6,
			  'left':thisOffset.left+thisWidth-3,
			  'visibility':'visible',
			  'border':'none',
			  'background':'none',
			  'box-shadow':'none'
			  }).html('<img src="img/2.png" />');
		  }
		  
		  var top = thisOffset.top-thisImgHeight/2;
		  if(top < scrollTop)
		     top = scrollTop;
		  else if(top + thisImgHeight > scrollTop + $(window).height())
		     top = 	scrollTop + $(window).height() - (thisImgHeight + 8);
		  
		  $cloneThisImg.appendTo('body').css({
		  'position':'absolute',
		  'z-index':100,
		  'padding':3,
		  'border':'1px solid #999',
		  'border-radius':5,
		  'background':'#fff',
		  'box-shadow':'1px 1px 7px rgba(0,0,0,0.3),-10px 10px 7px rgba(0,0,0,0.8)',
		  'filter': 'progid:DXImageTransform.Microsoft.shadow(direction=120, color=#333333, strength=6)',
		  'top':top+thisImgHeight/4,		  
		  'left':thisOffset.left+thisWidth+20,
		  'width':thisImgWidth*0.5,
		  'opacity':0.2

		  }).animate({'width':thisImgWidth,'top':top, 'opacity':1},150, function(){
		  
		  
		  if(scrollTop + $(window).height() <= thisOffset.top + thisImgHeight/4 && flag)
		  {
			  previewElement.css({
			  'top':thisOffset.top-20,
			  'left':thisOffset.left+thisWidth-3,
			  'visibility':'visible',
			  'border':'none',
			  'background':'none',
			  'box-shadow':'none'
			  }).html('<img src="img/2.png" />');
		  }
		  
		  if(scrollTop >= thisOffset.top - thisImgHeight/4 && flag)
		  {
			  previewElement.css({
			  'top':thisOffset.top+12,
			  'left':thisOffset.left+thisWidth-3,
			  'visibility':'visible',
			  'border':'none',
			  'background':'none',
			  'box-shadow':'none'
			  }).html('<img src="img/3.png" />');
		  }
		  
		  flag = false;
		  
		  });
		  
		  
		  
	    }	  
		  
	},
	function(){
		flag = false;
		previewElement.css({'visibility':'hidden'});
		$cloneThisImg.remove();
		  
		}); 

    $(this).click(function(){
	    flag = false;
		previewElement.css({'visibility':'hidden'});
		$cloneThisImg.remove();
		});
		
		
    });//end this.each





};//end $.fn.simplePlugin





