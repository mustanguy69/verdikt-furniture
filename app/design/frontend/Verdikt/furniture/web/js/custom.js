require(['jquery'], function($) {
  function setSameHeight($items){
    var maxHeight = 0;

    $items.each(function(){
       if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
    });

    $items.height(maxHeight);
  }

  $(document).ready(function() {
      //setSameHeight($('.home-feature-products .product-item-photo'));
      //setSameHeight($('.home-feature-products .product-item-details .product-item-name'));
      // $('.product-item .product-image-photo').each(function(){
      //     $(this).height($(this).width());
      // });
  });

  $(document).ready(function() {
    const storeID = window.checkout.storeId;
    if ( storeID === '1' ) {
      if( $('.product-item-details').length ){
        $('.product-item-details').each(function(){
          if( $(this).find('span[data-role="amhideprice-hide-button"]').length ){
            $(this).find('.show-price-link').css( "display", "block" );
          }
        });
      }

      if ( $('body').hasClass('catalog-product-view') ) {
        if ( $('span[data-role="amhideprice-hide-button"]').length ) {
          $('.show-price-link').css( "display", "block" );
        }
      }

    }
  });

  $(document).ready(function() {
    const storeRedirectURL = 'stores/store/redirect/';
    $('.amlocator-link').each(function(){
      let fullLink = $(this).attr("href");
      let targetStore = fullLink.split("/").reverse().filter(Boolean)[0];
      let data = { "___store": targetStore, "___from_store": "default" };
      let mypostData = { "action": storeRedirectURL, "data": data};
      let attrData = JSON.stringify(mypostData);
      $(this).attr("data-post", attrData);
    });
  });  
});