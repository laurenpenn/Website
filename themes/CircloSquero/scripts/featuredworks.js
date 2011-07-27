   var currentDiv = 1;
   
   jQuery(document).ready(function(){
      //hide them all (id startst with "content"
      jQuery("div[id^='featuredworks']").hide();
      //show the initial div
      showDiv(currentDiv);
   
      jQuery("#avaa-prev").click(function(event) {
         if(currentDiv != 1){
            currentDiv--;
            showDiv(currentDiv);
         }
         else {currentDiv = jQuery("div[id^='featuredworks']").length;
                showDiv(currentDiv);}
      });
      
      jQuery("#avaa-next").click(function(event) {
         if(currentDiv != jQuery("div[id^='featuredworks']").length){
            currentDiv++;
            showDiv(currentDiv);
         }
        else {currentDiv = 1;
                showDiv(currentDiv);}
      });
      
      function showDiv(){
         jQuery("div[id^='featuredworks']").hide();
         jQuery("#featuredworks" + currentDiv).fadeIn("slow");
      }
   });
   