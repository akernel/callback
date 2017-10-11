jQuery(function(){
    jQuery(".on-fancybox").fancybox({
        tpl : {
        	error    : '<p class="fancybox-error">Запрошенное содержимое не может быть загружен.<br/>Пожалуста попробуйте поже.</p>',
        	closeBtn : '<a title="Закрыть" class="fancybox-item fancybox-close" href="javascript:;"><span class="mif-cross"></span></a>'
         }
        
    });
});