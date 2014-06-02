
$(document).ready(function(){

$('#container').singleAccordion({header:'h3',body:'p.open',child:'.plus'});

$('.links+.open>a').simplePlugin();

$('a[href*=".jpg"]').simplelightbox({'selector':'a[href*=".jpg"]', 'text':'text', 'preview':'part'});




});//end $(document).ready