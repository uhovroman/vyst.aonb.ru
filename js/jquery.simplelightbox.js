
$.fn.simplelightbox = function(options){
      
	    var flagResize = false,
		    flagClick = false,
            index = -1,
		    lengthImg = $(options.selector).length,
		    coordX = 0, coordY = 0,
			arrTitle = new Array(),
			srcText = options.text
			minIndex = 0,
			maxIndex = lengthImg - 1;
		
		$(document).mousemove(function(event){coordX = event.pageX; coordY = event.pageY;});  
		   
	    $('body').append('<div id="LB-overlay"></div>');
        $overlay = $('#LB-overlay').css({
		'position':'absolute',
		'top':0,
		'left':0,
		'z-index':200,
		'display':'none',
		'background':'rgb(0, 19, 38)',
		'opacity':0.7
		}).click(function(){
		                    $overlay.css({'display':'none'});
                            $container.css({'display':'none'});
							$('.LB-image').css({'display':'none'});
							$prev.css({'display':'none'});
		                    $next.css({'display':'none'});
							$backTextLink.css({'display':'none'});
				            $textLink.css({'display':'none'});
							flagResize = false;
							if(flagClick)
							   $('.LB-image').eq(index).attr({'src':''});
							flagClick = false;
						   }); 

		$('body').append('<div id="LB-container"></div>');
		$container = $('#LB-container').css({
		'position':'absolute',
		'z-index':201,
		'display':'none',
		'background':'url(img/NFLightBox/loading.gif) no-repeat 50% 50% #fff',
		'border':'1px solid #333',
		'border-radius':15,
		'padding':10,
		'box-shadow':'-10px 10px 7px rgba(0,0,0,0.7)'
		}).hover(function(){
					if(!flagResize)
					{
						$close.css({'display':'block'});
						if(!flagClick)
						{
							if(index > minIndex && index < maxIndex)
							{
							   $prev.css({'display':'block'});
							   $next.css({'display':'block'});
							}
							else if(index == minIndex && lengthImg > 1)
							{
							   $prev.css({'display':'none'});
							   $next.css({'display':'block'});
							}
							else if(index == maxIndex && lengthImg > 1)
							   $prev.css({'display':'block'});
							else if((index == minIndex || index == maxIndex) && lengthImg == 1)
							{
							   $prev.css({'display':'none'});
							   $next.css({'display':'none'});
							}
						}
					}
					
					if(!flagClick && $('.LB-image').eq(index).attr('src') !== '')
					   viewText();
					
				 },
				 function(){
				 $close.css({'display':'none'});
				 $prev.css({'display':'none'});
				 $next.css({'display':'none'});
				 $backTextLink.css({'display':'none'});
				 $textLink.css({'display':'none'});
		});
		
		$container.append('<div id="LB-backTextLink"></div>');
		$backTextLink = $('#LB-backTextLink').css({
		'position':'absolute',
		'top':10,
		'left':10,
		'z-index':202,
		'background':'#fff',
		'height':50,
		'display':'none'
		})
		
		$container.append('<div id="LB-textLink"></div>');
		$textLink = $('#LB-textLink').css({
		'position':'absolute',
		'top':20,
		'font-size':'2.2em',
		'white-space':'nowrap',
		'z-index':203,
		'display':'none'
		});
		
		$container.append('<img id="LB-close" src="img/NFLightBox/close.png" />');
		$close = $('#LB-close').css({
		'position':'absolute',
		'top':0,
		'z-index':203,
		'cursor':'pointer',
		'display':'none'
		}).click(function(){
		                    $overlay.css({'display':'none'});
                            $container.css({'display':'none'});
							$('.LB-image').css({'display':'none'});
							$prev.css({'display':'none'});
		                    $next.css({'display':'none'});
							$backTextLink.css({'display':'none'});
				            $textLink.css({'display':'none'});
							flagResize = false;
							if(flagClick)
							   $('.LB-image').eq(index).attr({'src':''});
							flagClick = false;
						   });
		
		$container.append('<img id="LB-prev" src="img/NFLightBox/prev.png" />');
		$prev = $('#LB-prev').css({
		'position':'absolute',
		'z-index':203,
		'cursor':'pointer',
		'display':'none'
		}).click(function(){
		      $prev.css({'display':'none'});
			  $next.css({'display':'none'});
			  if(index > minIndex && !flagClick)
			  {
				$('.LB-image').css({'display':'none'});
				$backTextLink.css({'display':'none'});
				$textLink.css({'display':'none'});
				index--;
				runAction($(options.selector).eq(index), index);
			  }
		});
		
		$container.append('<img id="LB-next" src="img/NFLightBox/next.png" />');
		$next = $('#LB-next').css({
		'position':'absolute',
		'z-index':203,
		'cursor':'pointer',
		'display':'none'
		}).click(function(){
		      $prev.css({'display':'none'});
			  $next.css({'display':'none'});
			  if(index < maxIndex && !flagClick)
			  {
				$('.LB-image').css({'display':'none'});
				$backTextLink.css({'display':'none'});
				$textLink.css({'display':'none'});
				index++;
				runAction($(options.selector).eq(index), index);
			  }
		});
		
		var defineCursor = function(){
		
			    var offset = $container.offset();
				var width = $container.outerWidth();
				var height = $container.outerHeight();     
			    if(coordY>=offset.top && coordY<=offset.top+height && coordX>=offset.left && coordX<=offset.left+width)
			        return true;
				else
				    return false;
					
		};
		
		var viewText = function(){
		  if($textLink.text() != '')
		  {
		    $backTextLink.css({'opacity':0,'display':'block'});
		    $textLink.css({'opacity':0,'display':'block'});
					   
		    $backTextLink.animate({
			'opacity':0.7
			},
			{
			duration:250,
			complete:function(){$textLink.css({'opacity':1});},
			step:function(now,fx){$textLink.css({'opacity':now});
			}
			});
		  }
		};//end function viewText
		
		var boxAnimate = function($thisImg, $thisLink, index, topCenter, leftCenter){
		    flagResize = true;
		    $close.css({'display':'none'});
		    $prev.css({'display':'none'});
		    $next.css({'display':'none'});
		    $backTextLink.css({'display':'none','width':$thisImg.width()});
			var text = '';
			if(srcText == 'text')
			   text = $thisLink.text();
			else if(srcText == 'title')
			   text = arrTitle[index];
	   
		    $textLink.text(text).css({'display':'none','left':($backTextLink.width()-$textLink.width())/2});
		   
		    if(topCenter + $thisImg.height()/2 + $container.css('padding')*2 > $overlay.height())
		    {
			   $overlay.height(topCenter + $thisImg.outerHeight()/2 + $container.css('padding')*2);
		    }
			
		    $container.animate({
		    'top':topCenter-$thisImg.height()/2,
		    'left':leftCenter-$thisImg.width()/2,
		    'width':$thisImg.width(),
		    'height':$thisImg.height()
		    },
		    600,
		    function(){
		    $close.css({'left':$container.outerWidth()-30});
		    $thisImg.css({'opacity':0.5,'display':'block'}).animate({'opacity':1},500,function(){
		   
			   $prev.css({
			   'top':($container.outerHeight()-$next.outerHeight())/2,
			   'left':0
			   });
	
			   $next.css({
			   'top':($container.outerHeight()-$next.outerHeight())/2,
			   'left':$container.outerWidth() - $next.outerWidth()
			   });
		   
		       flagResize = false;
		       flagClick = false;
		   
			   if(defineCursor())
			   {
					viewText();
					$close.css({'display':'block'});
					if(!flagClick)
					{
						if(index > minIndex && index < maxIndex)
						{
						   $prev.css({'display':'block'});
						   $next.css({'display':'block'});
						}
						else if(index == minIndex && lengthImg > 1)
						{
						   $prev.css({'display':'none'});
						   $next.css({'display':'block'});
						}
						else if(index == maxIndex && lengthImg > 1)
						   $prev.css({'display':'block'});
						else if((index == minIndex || index == maxIndex) && lengthImg == 1)
						{
						   $prev.css({'display':'none'});
						   $next.css({'display':'none'});
						}
					}
			   }
			   
			   
			   
		    });
		   
		   
		    });
		
		};//end function boxAnimate
		
		var runAction = function($this, index){
		
		        var scrollTop = $(document).scrollTop();
				var scrollLeft = $(document).scrollLeft();
				var windowHeight = $(window).height();
				var windowWidth = $(window).width();
				var topCenter = scrollTop + windowHeight/2;
				var leftCenter = scrollLeft + windowWidth/2;
				
				flagClick = true;
				
				$overlay.css({
				'width':$(document).outerWidth(),
				'height':$(document).outerHeight(),
				'display':'block'
				});
				
				if($container.width() == 0 || $container.height() == 0)
				   $container.css({
				   'top':topCenter-150,
				   'left':leftCenter-200,
				   'width':400,
				   'height':300,
				   'background':'url(img/NFLightBox/loading.gif) no-repeat 50% 50% #fff',
				   'display':'block'
				   });
			    else
				   $container.css({
				   'top':topCenter-$container.height()/2,
				   'left':leftCenter-$container.width()/2,
				   'background':'url(img/NFLightBox/loading.gif) no-repeat 50% 50% #fff',
				   'display':'block'
				   });
				
				$close.css({
				'left':$container.outerWidth()-30,
				'display':'block'
				});
				
				
				if($('.LB-image').eq(index).attr('src') == '')
				{
				   var idTime = setTimeout(function(){
				       flagClick = false;
					   $container.css({'background':'url(img/NFLightBox/noprv.gif) no-repeat 50% 50% #fff'});
					   $close.css({
					   'left':$container.outerWidth()-30,
					   'display':'block'
					   });
					   $backTextLink.css({'display':'none'});
				       $textLink.css({'display':'none'});
					   $('.LB-image').eq(index).attr({'src':''});
					   $prev.css({
					   'top':($container.outerHeight()-$next.outerHeight())/2,
					   'left':0
					   });
			
					   $next.css({
					   'top':($container.outerHeight()-$next.outerHeight())/2,
					   'left':$container.outerWidth() - $next.outerWidth()
					   });
				   }, 10000);
				   
				   
				   $('.LB-image').eq(index).attr({'src':$this.attr('href')}).load(function(){
				       clearTimeout(idTime);
					   if(flagClick)
						  boxAnimate($(this), $this, index, topCenter, leftCenter);
				   });//end load 
				   
			    }
				else
				    boxAnimate($('.LB-image').eq(index), $this, index, topCenter, leftCenter);
				
		};//end function runAction
		
		
		
        return this.each(function(){
		
			    $container.append('<img class="LB-image" src="" />');
				$('.LB-image').css({'display':'none'});
				arrTitle[$(options.selector).index(this)] = $(this).attr('title');
				
				$(this).click(function(event){	
					event.preventDefault();			
					index = $(options.selector).index(this);
					
					if(options.preview == 'part')
					{
					$parent = $(this).parent();
					$innerSet = $(options.selector, $parent[0]);
					lengthImg = $innerSet.length;
					innerIndex = $innerSet.index(this);
					minIndex = index - innerIndex;
					maxIndex = minIndex + ($innerSet.length-1);
					}
					
					runAction($(this), index);
				});//end $(this).click  
                
	    });//end this.each



};//end $.fn.mytooltip


