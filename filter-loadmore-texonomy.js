jQuery(document).ready(function(){
  var ppp = 4; // Post per page
  var pageNumber = 1;

  jQuery(document).on("change", ".cat-list input[name=cat-name]", function() {
    pageNumber = 1;
    var cat_id = '';
    cat_id = jQuery('input[name="cat-name"]:checked').val();
    if($(this).prop("checked") == true){
      jQuery.ajax({
        type: 'POST',
        url: custom_object.ajaxurl,
        data: {
          action: 'dealer_list',
          cat_id : cat_id,
          pageNumber : pageNumber,
          ppp : ppp 
        },
        beforeSend: function(){
          jQuery('.ajax-loader').css('display', 'inline-block');
        },
        success: function(data) {
          console.log(data.is_cat);
          if(data.is_cat == 1){
            jQuery(".outer").html(data.result);
          }else{
            jQuery(".outer").append(data.result);
          }
          if(data.hide_btn){
            jQuery('.load-more').css('display', 'none');
          }else{
            jQuery('.load-more').css('display', 'block');
          }
        },
        error: function(errorThrown){
          console.log('errorThrown');
          console.log(errorThrown);
        },
        complete: function(){
          jQuery('.ajax-loader').hide();
        },
      });
    }
    
  });

  jQuery('.load-more').on('click', function() {
    var cat_id = '';
    cat_id = jQuery('input[name="cat-name"]:checked').val();
    pageNumber++;
    jQuery.ajax({
      type: 'POST',
      url: custom_object.ajaxurl,
      data: {
        action: 'dealer_list',
        cat_id : cat_id,
        pageNumber : pageNumber,
        ppp : ppp 
      },
      beforeSend: function(){
        jQuery('.ajax-loader').css('display', 'inline-block');
      },
      success: function(data) {
        if(data.result != ''){
          jQuery(".outer").append(data.result);
        }
        if(data.hide_btn){
          jQuery('.load-more').css('display', 'none');
        }else{
          jQuery('.load-more').css('display', 'inline-block');
        }
      },
      error: function(errorThrown){
        console.log('errorThrown');
        console.log(errorThrown);
      },
      complete: function(){
        jQuery('.ajax-loader').hide();
      },
    });
  });
});

