$(document).ready(function() {

var versionWIN = /Windows NT (\d+(?:\.\d)*)/i;
    versionWIN = versionWIN.exec(navigator.userAgent); 
if(versionWIN != null)
    versionWIN = parseFloat(versionWIN[1]);
else
    versionWIN = 5.1;//5.1 - Win XP, 6.1 - Win 7
 
 
var versionIE = /MSIE (\d+(?:\.\d)*)/i;
    versionIE = versionIE.exec(navigator.userAgent);
if(versionIE != null)
    versionIE = parseFloat(versionIE[1]);
else
    versionIE = 1000;
  
var obj_css = { 
              'top':'0px',
              'left':'0px',
              'width':$('.posterExhibit img')[0].offsetWidth-10,
              'font-size':'1.2em',
              'font-weight':'bold',
              'color':'#000',
              'text-align':'center',
              'padding':'11px',
              'padding-left':'5px',
              'padding-right':'5px',
              'background': 'url(img/font_21.png)'
              };
 if(versionIE <= 8)
	obj_css.backgroundColor = '#e9edf7';
 if(versionWIN > 5.1){
	obj_css.paddingTop = '13px';
	obj_css.paddingBottom = '13px';
 }
    
 $('.posterExhibit').simpletooltip({
 'location':'user',
 'innerTag':'img',
 'css':obj_css   
  });
 
  
  
  $('.titleExhibit').simpletooltip({
    'effect':'fall',//'puff',//'scale',
    'css':{
	'margin-bottom':'10px',
        'margin-top':'0px',
        'padding': '10px',
        'font-size': '1.5em',
        'color': '#303030',
        'background-color': '#f5f5b5',
        'border': '1px solid #deca7e',
	'box-shadow': '5px 5px 10px rgba(0, 0, 0, .5)',
	'border-radius':'7px 7px 7px 0px', 
        'text-shadow': 'none'
	}	
 });

$('a[href*=".jpg"]').simplelightbox({
'selector':'a[href*=".jpg"]',
'text':'title'
});
 
});