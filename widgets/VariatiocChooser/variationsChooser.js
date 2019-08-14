( function( $ ) {
  $(window).on('load', () => {
    selectVariation($('.variation.selected'));
  });
  
  var variation = $('.variation');
  function selectVariation(variation) {
    var form = $(".variations_form");
    var select = $("table.variations select");
    var variationElement = $(variation);
    var variation = {
      name: variationElement.find('.variation-name').text().trim(),
      price: variationElement.find('.variation-price').data("price"),
      id: variationElement.data("variation-id"),
    };

    $('.selected').removeClass('selected');
    variationElement.addClass("selected");

    $('#mainProductPrice .woocommerce-Price-amount').contents().filter(function() { return this.nodeType === 3; }).first().replaceWith(variation.price);

    select.val(variation.name);
    form.trigger("check_variations");
    form.trigger("woocommerce_variation_has_changed");

    $('.woocommerce-variation.single_variation').hide();
    // $('.quantity').hide();
    
    return variation;
  }

  variation.click(function() {
    var variation = selectVariation(this);

    window.location.hash = variation.name + "-" + variation.id;
  });

  variation.hover(function() {
    $('.hovered').removeClass('hovered');
    $(this).addClass("hovered");
  });
  
  variation.mouseleave(function() {
    $('.hovered').removeClass('hovered');
  });
} )( jQuery );

