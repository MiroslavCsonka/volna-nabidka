(function(){
   var minHidden = $("#min");
   var maxHidden = $("#max");

   function setSlider(sliderContainerId){
      $('.slider').parent('div').hide();
      if (sliderContainerId !== undefined){
         var slider = $("#" + sliderContainerId).fadeIn(500);
         var amounts = slider.find(".amount").text();
         var values = amounts.split("-");
         minHidden.val(parseInt(values[0]));
         maxHidden.val(parseInt(values[1]));
      }
   }

   function setAmount(containerId, min, max){
      $('#' + containerId).find('.amount').html(min + " - " + max);
   }

   var checked =  $("input[type='radio']:checked").val();
   setSlider(checked);
   var sliders = ['perHour','perProject'];
   $.each(sliders, function(index,value){
      var selector = $('#'+value).children('.slider');
      selector.slider({
         min: selector.data('min'),
         max: selector.data('max'),
         values: [ selector.data('defaultmin'), selector.data('defaultmax') ],
         range:true,
         slide: function (event, ui) {
            var containerId = $(this).parent('div').attr('id');
            minHidden.val(ui.values[ 0 ]);
            maxHidden.val(ui.values[ 1 ]);
            setAmount(containerId,ui.values[ 0 ],ui.values[ 1 ]);
         }
      });
   });
   var sliderDiv = $("#slider");
   $("#amount").html(
      sliderDiv.slider("values", 0) + " - " + sliderDiv.slider("values", 1)
      );
   $('input[type="radio"]').on('change',function(){
      var sliderContainer = $(this).val();
      setSlider(sliderContainer);
   });
})();

