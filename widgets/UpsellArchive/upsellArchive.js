(function ($, window, document) {
    "use strict";
    if('container_selector' in window && $(container_selector).length > 0) {
        var list_selector_full = container_selector + ' ' + list_selector;
        var item_selector_full = list_selector_full + ' ' + item_selector;
        var add_to_cart_selector_full = item_selector_full + ' ' + add_to_cart_selector;
        var nav_selector_full = container_selector + ' ' + nav_selector;
        var scroll_selector_full = nav_selector + ' ' + scroll_selector;
        var options = $.extend({
                product_archive_container_selector: false,
                product_archive_list_selector: false,
                product_archive_item_selector   : false,               
                product_archive_add_to_cart_selector   : false,
                pagination_scroll_button_selector   : false,
                pagination_nav_selector   : false,
                is_shop        : false,
                loader         : false,
                columns         : false  
            }, {
                'product_archive_container_selector'      : container_selector,	           
                'product_archive_list_selector'   : list_selector_full,
                'product_archive_item_selector'      : item_selector_full,	           
                'product_archive_add_to_cart_selector'      : add_to_cart_selector_full,	           
                'pagination_scroll_button_selector'      : scroll_selector_full,
                'pagination_nav_selector'      : nav_selector_full,
                'is_shop'           : true,  
                'loader'            : image_loader,
                'columns'            : products_list_columns
            }
        );

        var productArchiveElement = $( options.product_archive_container_selector );
        var productListElement = $( options.product_archive_list_selector );
        var productItemElement = $( options.product_archive_item_selector );
        var paginationElement = $( options.pagination_nav_selector );
        
        $(options.product_archive_add_to_cart_selector).click((element) => {
            var addToCartButtonContainer = $(element.currentTarget);
            var product_id = addToCartButtonContainer.parents( options.product_archive_item_selector ).data('product-id');
            var data = {
                action: 'woocommerce_ajax_add_to_cart',
                product_id: product_id,
                product_sku: '',
                quantity: 1,
                variation_id: product_id,
            };
            
            addToCart(data, addToCartButtonContainer);
        });
        
        productArchiveElement.find(options.pagination_scroll_button_selector).click(function (e) {
            e.preventDefault();
            productListElement = $( options.product_archive_list_selector );
            productItemElement = $( options.product_archive_item_selector );
            var columns = options.columns;
            var isNext = $(this).data('scroll') == 'next';
            var columnIndexes = {
                first: 0,
                second: columns,
                last: productItemElement.length-columns
            };
            var oldShown = [];
            var newShown = [];
            var containerWidth = productListElement.width() / columns;
            
            if (isNext) {
                oldShown = $(productItemElement.splice(columnIndexes.first, columns));
                oldShown.fadeOut(500).promise().done(function() {
                    newShown = $(productItemElement.slice(columnIndexes.first, columns)).hide();
                    $(oldShown).removeClass('product_shown');
                    productListElement.append(oldShown);
                    newShown.addClass('product_shown').fadeIn(500);
                });
            } else {
                oldShown = $(productItemElement.slice(columnIndexes.first, columns));
                oldShown.fadeOut(500).promise().done(function() {
                    newShown = $(productItemElement.splice(columnIndexes.last, columns)).hide();
                    $(oldShown).removeClass('product_shown');
                    productListElement.prepend(newShown);
                    newShown.addClass('product_shown').fadeIn(500);
                });
            }
        });

        $(window).on('resize', function() {
            var oldColumnSize = options.columns;
            var newColumnSize;
            var isReduce;
            var shown_products;
            var breakPointesColumns = {
                one: {number: 1, width: 320},
                two: {number: 2, width: 580},
                three: {number: 3, width: 768},
                four: {number: 4, width: 1200},
            };

            // breakPointesColumns.forEach((size, index) => {
            //     if(breakPointesColumns.length < index) {
            //         if(index = 0 && screen.width < size.width)

            //         if(screen.width < size.width && screen.width > twoColumnsBreakPoint && oldColumnSize == 4) {
            //             newColumnSize = 3;
            //         }
            //     }
                
            // });
            if (screen.width > breakPointesColumns.three.width && oldColumnSize != 4) {
                newColumnSize = options.columns = 4;
            }
            else if (screen.width < breakPointesColumns.three.width && screen.width > breakPointesColumns.two.width && oldColumnSize != 3) {
                newColumnSize = options.columns = 3;
            }
            else if (screen.width < breakPointesColumns.two.width && oldColumnSize != 2) {
                newColumnSize = options.columns = 2;
            }
            else 
                return;

            isReduce = newColumnSize < oldColumnSize;

            productListElement.removeClass("columns-" + oldColumnSize).addClass("columns-" + newColumnSize);
            
            if(isReduce) {
                for (let i = newColumnSize; i < oldColumnSize; i++) {
                    shown_products = productListElement.find('.product_shown').last();
                    shown_products.removeClass("product_shown");
                }
            }
            else {
                for (let i = oldColumnSize; i < newColumnSize; i++) {
                    shown_products = productListElement.find('.product_shown').last().next();
                    shown_products.addClass("product_shown");    
                }
            }
        });

        function addToCart(data, addToCartButtonContainer) {
            $.ajax({
                type: 'post',
                url: wc_add_to_cart_params.ajax_url,
                data: data,
                beforeSend: function (response) {
                    addToCartButtonContainer.css('background-color', '#ff0046');
                },
                complete: function (response) {
                    addToCartButtonContainer.css('background-color', '#ff0046');
                },
                success: function (response) {
                    console.log(response);
                    if (response.error && response.product_url) {
                        // window.location = response.product_url;
                        addToCartButtonContainer.find('i').removeClass('fa-plus').addClass('fa-exclamation-triangle')
                            .css('margin-top', '-1px');
                        
                        return;
                    } else {
                        addToCartButtonContainer.find('i').css('color', '#4BB543');
                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, addToCartButtonContainer]);
                    }
                },
            });
        }
    }
})( jQuery, window, document );