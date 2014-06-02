
$.fn.singleAccordion = function(options){

    var $headers = $(options.header,this);
    var $body = $(options.body,this);
    var $container = $(this);
	
	var versionIE = /MSIE (\d+(?:\.\d)*)/i;
		versionIE = versionIE.exec(navigator.userAgent);
	if(versionIE != null)
		versionIE = parseFloat(versionIE[1]);
	else
		versionIE = 1000;

	$body.css('position','relative').prepend('<div class="line"></div>');
	$('.line',$body).css({position:'absolute',top:0,left:6,width:10,borderLeft:'1px dotted #999',borderBottom:'1px dotted #999'}).each(function(){
	$(this).height($(this).parent().outerHeight());
	});

	if(versionIE <= 8)
	{
	  $headers.each(function(){if($(this).text() === '') $(this).remove();});
	  $('.line',$body).css({top:-3}).each(function(){
	  $(this).height($(this).parent().outerHeight()+1);
	  });
	}

	$headers.css({'color':'#999','margin-top':'5px'}).prepend('<span class="plus">+</span>').show().hover(
	function(){$(this).css('color','#5390b9'); $('.plus',this).css('border-color','#5390b9');},
	function(){$(this).css('color','#999'); $('.plus',this).css('border-color','#999');}
	);

	if(navigator.userAgent.indexOf('Safari') > -1)
	  $('.plus',$headers).css({'width':10,'padding-right':1});

	$body.hide().css('padding-left',18);


    return this.each(function(){
	 
            $headers.each(function(){
			
					$(this).click(
					
					function(){
						var $container = $(this).parent();
						var index = $headers.index(this);
						var $thisBody = $body.eq(index);
						if($thisBody.css('display') === 'none')
						{
							$(options.child,this).html('&ndash;').css('line-height',0.6);
							if(navigator.userAgent.indexOf('Safari') > -1)
							   $(options.child,this).css({'line-height':0.7});
							var heightBody = $thisBody.height();
								$thisBody.height(0).show().animate(
								{height:heightBody},200
								);
						}
						else if($thisBody.css('display') === 'block')
						{
							$(options.child,this).html('+').css('line-height',0.8);
							var heightBody = $thisBody.height();
								$thisBody.animate(
								{height:0},200,
								function(){$thisBody.hide().height(heightBody); }
								);
						}
					}//end function
					
					);

           	        
				
	        });//end $headers.each
			
        
       
		
		
    });//end this.each





};//end $.fn.accordion





