!function(r){"use strict";r(document).ready(function(){function s(){var e=r(".yz-media-small-box .yz-media-group-photos .yz-media-item-img,.yz-media-small-box video, .yz-media-small-box .yz-media-group-videos .yz-media-item-img"),i=r(".yz-media-small-box .yz-media-group-photos .yz-media-item-img,.yz-media-small-box video, .yz-media-small-box .yz-media-group-videos .yz-media-item-img");e.height(e.width()),i.height(i.width())}var i;r(document).on("click",".yz-video-lightbox",function(e){e.preventDefault(),r("body").hasClass("yz-media-lightbox-loaded")||(r("body").addClass("yz-media-lightbox-loaded"),r("<script/>",{rel:"text/javascript",src:Youzer.assets+"js/yz-media-lightbox.min.js"}).appendTo("head"),r(this).trigger("click"))}),s(),r(window).on("resize",function(e){clearTimeout(i),i=setTimeout(function(){s()},250)}),r(document).on("click",".yz-media-filter .yz-filter-content",function(e){var t=r(this);if(!r(".yz-media-filter .yz-filter-content.loading")[0]&&!t.hasClass("yz-current-filter")){var d=t.closest(".yz-media"),n=t.data("type");if(d.find(".yz-media-group-"+n)[0])return d.find(".yz-media-filter .yz-filter-content").removeClass("yz-current-filter"),t.removeClass("loading").addClass("yz-current-filter"),void d.find('div[data-active="true"]').fadeOut(100,function(){r(this).attr("data-active",!1),d.find(".yz-media-group-"+n).attr("data-active",!0).fadeIn()});var o=d.find(".yz-media-widget");r.ajax({url:Youzer.ajax_url,type:"post",data:{action:"yz_media_pagination",data:t.data()},beforeSend:function(){t.addClass("loading")},success:function(e){var i=r('<div class="yz-media-group-'+n+'" data-active="true"></div>').append('<div class="yz-media-widget-content">'+e+"</div>"),a=i.find(".yz-media-view-all").clone();i.find(".yz-media-view-all").remove(),d.find(".yz-media-filter .yz-filter-content").removeClass("yz-current-filter"),t.removeClass("loading").addClass("yz-current-filter"),i.append(a),d.find('div[data-active="true"]').fadeOut(100,function(){r(this).attr("data-active",!1),o.append(i),s()})}})}}),r(document).on("click",".yz-media .yz-pagination a",function(e){var i=r(this);e.preventDefault();i.closest(".yz-pagination");var a,t=i.closest(".yz-media").find(".yz-media-items");r.ajax({url:Youzer.ajax_url,type:"post",data:{action:"yz_media_pagination",data:r(this).closest(".yz-pagination").data(),page:(a=i.clone(),a.find(".yz-page-symbole").remove(),parseInt(a.text()))},beforeSend:function(){var e=i.clone().html('<i class="fas fa-spinner fa-spin"></i>');i.hide(0,function(){e.insertAfter(r(this))})},success:function(e){r("html, body").animate({scrollTop:t.offset().top-150},1e3),t.html(e).fadeIn()}})})})}(jQuery);