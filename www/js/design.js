(function(){
   var menu = $('#userPanel'),
   pos = menu.offset();

   $(window).scroll(function () {
      if ($(this).scrollTop() > pos.top + menu.height() && menu.hasClass('default')) {
         menu.fadeOut('fast', function () {
            $(this).removeClass('default').addClass('fixed').fadeIn('fast');
         });
      } else if ($(this).scrollTop() <= pos.top && menu.hasClass('fixed')) {
         menu.fadeOut('fast', function () {
            $(this).removeClass('fixed').addClass('default').fadeIn('fast');
         });
      }
   }); 
   // Hiding subcategory dropdown
   $('.selectSubCategory').hide();
   $('.mainCategory').change(function(){
      $('.selectSubCategory').fadeIn();
   })
   
   /* Fixed Height Script */
   var windowHeight = $(window).height();
   var headerHeight = $('#header').height();
   var footerHeight = $('#footer').height();
   var footerMarginAndPadding = 60;
   var contentHeight = windowHeight - (headerHeight + footerHeight + footerMarginAndPadding);
   $("#main").css("min-height", contentHeight);
})()
