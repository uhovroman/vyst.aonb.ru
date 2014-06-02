


$.fn.simpletooltip = function(options){
      
   
//====================================================================================================================================================
      
  var toolCoords = function($this,objToolTip){
      
      var coords = {left:0,top:0,direction:'up'};
      var thisOffset = $this.offset();
      var thisHeight = $this.outerHeight();
      var thisWidth = $this.outerWidth();
      var scrollLeft = $(document).scrollLeft();
      var scrollTop = $(document).scrollTop();
      var marginTop = parseInt(objToolTip.css('margin-top'));
      var marginBottom = parseInt(objToolTip.css('margin-bottom'));
          objToolTip.css('white-space','nowrap');
//---------------------------------------------------------------------------------------------    
      
          if(thisOffset.top - scrollTop - (objToolTip.outerHeight() + marginBottom) < 0){
              
             coords.top = thisOffset.top + thisHeight + marginTop;
             coords.direction = 'down';
          }
          else{
              
             coords.top = thisOffset.top - (objToolTip.outerHeight() + marginBottom);
              
          }
          
//---------------------------------------------------------------------------------------------      
      var leftPos = thisOffset.left + thisWidth/2 - objToolTip.outerWidth()/2;
      
          if($(window).width() < objToolTip.outerWidth()){
             coords.left = leftPos;
             return coords;
          }
      
      var rightPos = leftPos + objToolTip.outerWidth();
      
          if(leftPos < 0){
              
             coords.left = scrollLeft;
              
          }
          else if(rightPos > $(window).width() + scrollLeft){
              
             coords.left = $(window).width() + scrollLeft - objToolTip.outerWidth();
              
          }
          else{
              
             coords.left = leftPos;
              
          }
      
      
//---------------------------------------------------------------------------------------------      
      
      return coords;
  } //end function toolCoords 
  
  
  
//==================================================================================================================================================== 
  
  
 var displayTooltip = function($this,resultSettingsCss,options,mytooltipElement){
    
     var el;
        if(options.innerTag)
           el = $(options.innerTag,$this);
        else
           el = $this; 
       
     var offset = el.offset();   
     
               $('.mytooltip').removeAttr('style');                  
               $('.mytooltip').css(resultSettingsCss);
               $('.mytooltip').text($this.data('tooltext'));

               var leftPos;
	       var topPos;
                
               if(options.location && options.location == 'user'){
		      leftPos = offset.left+parseInt(options.css.left);
		      topPos = offset.top+parseInt(options.css.top);

	       }
	       else{

		      var coords = toolCoords($this,mytooltipElement);

		      leftPos = coords.left;
		      topPos = coords.top;

	       }



               if(options.effect){
		 
		  switch (options.effect)
		  {
		   case 'fall': resultSettingsCss.left = leftPos;
				if(coords.direction == 'up')
				resultSettingsCss.top = topPos-mytooltipElement.outerHeight()*2;
				else if(coords.direction == 'down')
				resultSettingsCss.top = topPos+mytooltipElement.outerHeight()*2;

				mytooltipElement.css({'left':resultSettingsCss.left,'top':resultSettingsCss.top});
				mytooltipElement.css({display:'block'}).animate({opacity:1,top:topPos},150,function(){
				mytooltipElement.effect('bounce',{direction:coords.direction,distance:5,times:1});
				});
				break;
				
		   case 'scale': mytooltipElement.css({
				  'left':leftPos,
				  'top':topPos,
				  'opacity':1,
				  'display':'block'
				 });
				 mytooltipElement.effect('scale',{from:{height:0,width:0},percent:100},300);
				 break;
			
                   case 'puff':  mytooltipElement.css({
				  'left':leftPos,
				  'top':topPos
				 });
				 mytooltipElement.show('puff',{percent:130},200,function(){mytooltipElement.css({display:'block',opacity:1});});
				 break;			
				 
		  }//end switch
		
	       }
	       else{
		
		    resultSettingsCss.left = leftPos;
		    resultSettingsCss.top = topPos;

		    mytooltipElement.css({'left':resultSettingsCss.left,'top':resultSettingsCss.top});
		    mytooltipElement.css({display:'block'}).animate({opacity:1},300);
	       }
     
 } //end function displayTooltip
  


//====================================================================================================================================================
        var idTimeout;
	var settings = {
                        location:'auto',
                        css:{
                            'position':'absolute',
                            'display':'none',
                            'z-index':'5000000',
                            'opacity': '0'
                            }

                       };
	
        var mytooltipElement;
        var versionIE = /MSIE (\d+(?:\.\d)*)/i;
	    versionIE = versionIE.exec(navigator.userAgent);
	if(versionIE != null)
	    versionIE = parseFloat(versionIE[1]);
	else
	    versionIE = 1000;   

	if(!$('.mytooltip').length )	
           mytooltipElement = $('<div class="mytooltip">').appendTo('body');
	else
           mytooltipElement = $('.mytooltip');
            
        return this.each(function(){	  
	  
           var resultSettingsCss = $.extend({}, options.css, settings.css);
	   var flagIE8 = false;	
		
		$(this).mousemove(function(){flagIE8 = true;});
		
		$(this).click(function(){
		            lagIE8 = false;
                    clearTimeout(idTimeout);
					mytooltipElement.css({display:'none',opacity:0});
					});
		
                $(this).hover(function(){
                  
		  if((flagIE8 && versionIE <= 8) || versionIE > 8)
		  {
                    var $this = $(this);
                    if($this.attr('title')!='')
		    {
                        $(this).data('tooltext',$(this).attr('title'));
                        $(this).attr('title','');
                    }
                    idTimeout = setTimeout(function(){displayTooltip($this,resultSettingsCss,options,mytooltipElement);},350);
		  }
                },
                function(){
                    flagIE8 = false;
                    clearTimeout(idTimeout);
		    if(options.effect && options.effect == 'scale')
		    //mytooltipElement.effect('scale',{percent:0},300,function(){mytooltipElement.css({display:'none',opacity:0})});
		    mytooltipElement.effect('puff',{percent:50,mode:'hide'},300,function(){mytooltipElement.css({display:'none',opacity:0})});
		    else if(options.effect && options.effect == 'puff')
		    mytooltipElement.effect('puff',{percent:130,mode:'hide'},300,function(){mytooltipElement.css({display:'none',opacity:0})}); 
		    else
                    mytooltipElement.animate({opacity:0},300,function(){$(this).css({display:'none'})});  

                });//end this.hover
		
		
	        $(document).mousewheel(function(){
		        flagIE8 = false;
		        clearTimeout(idTimeout);
		        $('.mytooltip').css({opacity:0,display:'none'});
			
		});
                
		$(document).keydown(function(event){

                        switch(event.keyCode){
                                case 38:    
                                case 39:
                                case 37:    
                                case 40: flagIE8 = false;
				         clearTimeout(idTimeout);
				         $('.mytooltip').css({opacity:0,display:'none'}); 
				         break;
                        }

                });//end $(document).keydown
		
                
                
                
	});//end this.each








};//end $.fn.mytooltip


