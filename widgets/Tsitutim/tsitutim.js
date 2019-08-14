var userId;
var itemsPerPage;
var totalItems;
var bookmarkedItemId;
var bookmarkedItem;
var widgetName = getUrlData().page.substring(0, getUrlData().page.length-1);

(($) => {
    var urlData = getUrlData();

    userId = $(`.userId`).text();
    totalItems = $(`.${widgetName}s`).data(`total-${widgetName}s`);
    itemsPerPage = $(`.${widgetName}s`).data(`${widgetName}s-per-page`);

    jQuery.ajax({
        url: admin_ajax.url,
        type: `get`,
        dataType: `json`,
        data: {
            userId,
            itemName: widgetName,
            action: admin_ajax.actions.get_bookmark,
            nonce: admin_ajax.nonce
        },
        success: function(item) {
            if(item)
                setItemAsBookmarked(item[widgetName+"_id"]);
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }, done: function() {        
        }
    });

    if(urlData.hash) {
        goToBookmark(urlData.hash.substring(2));
    }


    $('#nextPage.paginationButton').click(() => {movePage(true)});
    $('#prevPage.paginationButton').click(() => {movePage(false)});
})(jQuery);

function setItemAsBookmarked(itemId) {
    jQuery(`.bookmarked`).removeClass(`bookmarked`);

    bookmarkedItemId = itemId;
    bookmarkedItem = jQuery(`.item[data-${widgetName}-id='` + itemId + `']`);
    bookmarkedItem.addClass(`bookmarked`);
}

function bookmark(itemId) {
    setItemAsBookmarked(itemId);

    var data = { 
        userId,
        itemId,
        itemName: widgetName,
        nonce: admin_ajax.nonce,
        action: admin_ajax.actions.save_bookmark
    };
    
    jQuery.ajax({
        url: admin_ajax.url,
        type: `get`,
        dataType: `text`,
        data,
        success: function(item) {
            console.log(item);
        },
        error: function(xhr) {
            console.log(xhr.itemText);
        }
    });
}

function goToBookmark(bookmarkId) {
    if(bookmarkId) {
        setItemAsBookmarked(bookmarkId);
    }
    
    if(bookmarkedItem) {
        var urlData = getUrlData();

        // FIX RANGES - SWICH METHOD
        var lowRange = urlData.currentPageIndex * itemsPerPage - itemsPerPage;
        var highRange = urlData.currentPageIndex * itemsPerPage;
        if (bookmarkedItemId <= highRange && bookmarkedItemId > lowRange)  {
            jQuery(`html, body`).animate({
                scrollTop: bookmarkedItem.offset().top - 110
            });
        } else {
            var iteration = 1;
            
            while (bookmarkedItemId > itemsPerPage * iteration && bookmarkedItemId <= itemsPerPage * iteration + itemsPerPage && iteration < totalItems / itemsPerPage) {
                ++iteration;
            }

            if (!window.location.hash)
                Object.assign(window.location, {pathname: setCurrentPageIndex(iteration), hash: `#b${bookmarkedItemId}`}).toString();
        }
    }
}

function movePage(isForward) {
    var urlData = getUrlData();

    if (isNaN(urlData.currentPageIndex)) {
        urlData.currentPageIndex = 1;
        urlData.seperatedUrl.push(1);
    }

    var nextIndex = isForward ? urlData.currentPageIndex = ++urlData.currentPageIndex : (urlData.currentPageIndex == 1 ? `0` : --urlData.currentPageIndex);
    urlData.seperatedUrl[urlData.seperatedUrl.length - 1] = nextIndex;

    if (nextIndex * itemsPerPage < totalItems + itemsPerPage && nextIndex != 0)
        window.location = urlData.seperatedUrl.join(`/`); 
}

function getUrlData() {
    var seperatedUrl = window.location.toString().replace(window.location.search, '').split(`/`);

    return {currentPageIndex: getCurrentPageIndex(), seperatedUrl, hash: window.location.hash, page: window.location.pathname.split('/')[1], search: window.location.search};
}

function getCurrentPageIndex() {
    var url = window.location.pathname;
    var lastSlash = url.substr(url.lastIndexOf('/') + 1);

    return parseInt(isNaN(lastSlash) ? '1' : lastSlash, 10);
}

function setCurrentPageIndex(index) {
    var url = window.location.pathname;
    var lastSlash = url.substr(url.lastIndexOf('/') + 1);
    
    return isNaN(lastSlash) ? url + '/' + index : url.replace( lastSlash, index);
}