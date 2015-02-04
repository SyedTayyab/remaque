jQuery(document).ready(function($){
    // **********************************************************************// 
    // ! Main Navigation plugin
    // **********************************************************************//

    $.fn.et_menu = function ( options ) {
        var methods = {
            showChildren: function(el) {
                el.fadeIn(100).css({
                    display: 'list-item',
                    listStyle: 'none'
                }).find('li').css({listStyle: 'none'});
            },
            calculateColumns: function(el) {
                // calculate columns count
                var columnsCount = el.find('.container > ul > li.menu-item-has-children').length;
                var dropdownWidth = el.find('.container > ul > li').outerWidth();
                var padding = 20;
                if(columnsCount > 1) {
                    dropdownWidth = dropdownWidth*columnsCount + padding;
                    el.css({
                        'width':dropdownWidth
                    });
                }

                // calculate right offset of the  dropdown
                var headerWidth = $('.menu-wrapper').outerWidth();
                var headerLeft = $('.menu-wrapper').offset().left;
                var dropdownOffset = el.offset().left - headerLeft;
                var dropdownRight = headerWidth - (dropdownOffset + dropdownWidth);

                if(dropdownRight < 0) {
                    el.css({
                        'left':'auto',
                        'right':0
                    });
                } 
            },
            openOnClick: function(el,e) {
                var timeOutTime = 0;
                var openedClass = "current";
                var header = $('.header-wrapper');
                var $this = el;


                if($this.parent().hasClass(openedClass)) {
                    e.preventDefault();
                    $this.parent().removeClass(openedClass);
                    $this.next().stop().slideUp(settings.animTime);
                    header.stop().animate({'paddingBottom': 0}, settings.animTime);
                } else {

                    if($this.parent().find('>div').length < 1) {
                        return;
                    }

                    e.preventDefault();

                    if($this.parent().parent().find('.' + openedClass).length > 0) {
                        timeOutTime = settings.animTime;
                        header.stop().animate({'paddingBottom': 0}, settings.animTime);
                    }

                    $this.parent().parent().find('.' + openedClass).removeClass(openedClass).find('>div').stop().slideUp(settings.animTime);

                    setTimeout(function(){
                        $this.parent().addClass(openedClass);
                        header.stop().animate({'paddingBottom': $this.next().height()+50},settings.animTime);
                        $this.next().stop().slideDown(settings.animTime);
                    },timeOutTime);
                }
            }
        };

        var settings = $.extend({
            type: "default", // can be columns, default, mega, combined
            animTime: 250,
            openByClick: true
        }, options );

        if(settings.type == 'mega') {
            this.find('>li>a').click(function(e) {
                methods.openOnClick($(this),e);
            });
            return this;
        }

        this.find('>li').hover(function (){
            if(!$(this).hasClass('open-by-click') || (!settings.openByClick && $(this).hasClass('open-by-click'))) {
                if(settings.openByClick) {
                    $('.open-by-click.current').find('>a').click();
                    $(this).find('>a').unbind('click');
                }
                var dropdown = $(this).find('> .nav-sublist-dropdown');
                methods.showChildren(dropdown);

                if(settings.type == 'columns') {
                    methods.calculateColumns(dropdown);
                }
            } else {
                $(this).find('>a').unbind('click');
                $(this).find('>a').bind('click', function(e) {
                    methods.openOnClick($(this),e);
                });
            }
        }, function () {
            if(!$(this).hasClass('open-by-click') || (!settings.openByClick && $(this).hasClass('open-by-click'))) {
                $(this).find('> .nav-sublist-dropdown').fadeOut(100).attr('style', '');
            }
        });

        return this;
    }

    // First Type of column Menu
    $('.main-nav .menu').et_menu({
        type: "default"
    });

    $('.fixed-header .menu').et_menu({
        openByClick: false
    });
    


    function et_equalize_height(elements, removeHeight) {
        var heights = [];

        if(removeHeight) {
            elements.attr('style', '');
        }

        elements.each(function(){
            heights.push($(this).height());
        });

        var maxHeight = Math.max.apply( Math, heights );
        if($(window).width() > 767) {
            elements.height(maxHeight);
        }
    }

    $(window).resize(function(){
        //et_equalize_height($('.product-category'), true);
    });

     // **********************************************************************// 
    // ! Mobile navigation
    // **********************************************************************// 

    var navList = $('.mobile-nav div > ul');
    var etOpener = '<span class="open-child">(open)</span>';
    navList.addClass('et-mobile-menu');
    
    navList.find('li:has(ul)',this).each(function() {
        $(this).prepend(etOpener);
    })
    
    navList.find('.open-child').click(function(){
        if ($(this).parent().hasClass('over')) {
            $(this).parent().removeClass('over').find('>ul').slideUp(200);
        }else{
            $(this).parent().parent().find('>li.over').removeClass('over').find('>ul').slideUp(200);
            $(this).parent().addClass('over').find('>ul').slideDown(200);
        }
    });
    
    $('.menu-icon, .close-mobile-nav').click(function(event) {
        if(!$('body').hasClass('mobile-nav-shown')) {
            $('body').addClass('mobile-nav-shown', function() {
                // Hide search input on click
                setTimeout(function(){
                    $(document).one("click",function(e) {
                        var target = e.target;
                        if (!$(target).is('.mobile-nav') && !$(target).parents().is('.mobile-nav')) {

                                    $('body').removeClass('mobile-nav-shown');
                        }
                    });  
                }, 111);
            });



        } else{
            $('body').removeClass('mobile-nav-shown');
        }
    });

    // **********************************************************************// 
    // ! Side Block
    // **********************************************************************// 

    $('.side-area-icon, .close-side-area').click(function(event) {
        if(!$('body').hasClass('shown-side-area')) {
            $('body').addClass('shown-side-area', function() {
                // Hide search input on click
                setTimeout(function(){
                    $(document).one("click",function(e) {
                        var target = e.target;
                        if (!$(target).is('.side-area') && !$(target).parents().is('.side-area')) {
                            $('body').removeClass('shown-side-area');
                        }
                    });  
                }, 111);
            });
        } else{
            $('body').removeClass('shown-side-area');
        }
    });

    // **********************************************************************// 
    // ! Full width section
    // **********************************************************************//

    function et_sections(){
        $('.et_section').each(function(){
            $(this).css({
                'left': - ($(window).width() - $('.header > .container').width())/2,
                'width': $(window).width(),
                'visibility': 'visible'
            });
            var videoTag = $(this).find('.section-back-video video');
            videoTag.css({
                'width': $(window).width(),
                //'height': $(window).width() * videoTag.height() / videoTag.width() 
            });
        });
    }

    et_sections()

    $(window).resize(function(){
        et_sections();
    })
    

    // **********************************************************************// 
    // ! Hidden Top Panel
    // **********************************************************************//

    $(function(){
        var topPanel = $('.top-panel');
        var pageWrapper = $('.page-wrapper');
        var showPanel = $('.show-top-panel');
        var panelHeight = topPanel.outerHeight();
        showPanel.toggle(function(){
            $(this).addClass('show-panel');
            pageWrapper.attr('style','transform: translateY('+panelHeight+'px);-ms-transform: translateY('+panelHeight+'px);-webkit-transform: translateY('+panelHeight+'px);');
            topPanel.addClass('show-panel');
        },function(){
            pageWrapper.attr('style','')
            topPanel.removeClass('show-panel');
            $(this).removeClass('show-panel');
        });
    });

    // **********************************************************************// 
    // ! Images lightbox
    // **********************************************************************//
    $("a[rel^='lightboxGall']").magnificPopup({
        type:'image',
        gallery:{
            enabled:true
        }
    });

    $("a[rel='lightbox']").magnificPopup({
        type:'image'
    });

    // **********************************************************************// 
    // ! Fixed header
    // **********************************************************************// 
    
    $(window).scroll(function(){
        if (!$('body').hasClass('fixNav-enabled')) {return false; }
        var fixedHeader = $('.fixed-header-area');
        var scrollTop = $(this).scrollTop();
        var headerHeight = $('.header-wrapper').height() + 20;
        
        if(scrollTop > headerHeight){
            if(!fixedHeader.hasClass('fixed-already')) {
                fixedHeader.stop().addClass('fixed-already');
            }
        }else{
            if(fixedHeader.hasClass('fixed-already')) {
                fixedHeader.stop().removeClass('fixed-already');
            }
        }
    });

    // **********************************************************************// 
    // ! Progress bars
    // **********************************************************************//

        var progressBars = $('.progress-bars');
        progressBars.waypoint(function() {
            i = 0;
            $(this).find('.progress-bar').each(function () {
                i++;
                
                var el = $(this);
                var width = $(this).data('width');
                setTimeout(function(){
                    el.find('div').animate({
                        'width' : width + '%'
                    },400);
                    el.find('span').css({
                        'opacity' : 1
                    });
                },i*300, "easeOutCirc");
            
            });
        }, { offset: '85%' });

        // **********************************************************************// 
    // ! Tabs
    // **********************************************************************// 

    var tabs = $('.tabs');
    $('.tabs > p > a').unwrap('p');
    
    var leftTabs = $('.left-bar, .right-bar');
    var newTitles;
    
    leftTabs.each(function(){
        var currTab = $(this);
        //currTab.find('> a.tab-title').each(function(){
            newTitles = currTab.find('> a.tab-title').clone().removeClass('tab-title').addClass('tab-title-left');
        //});

        newTitles.first().addClass('opened');

        
        var tabNewTitles = $('<div class="left-titles"></div>').prependTo(currTab);
        tabNewTitles.html(newTitles);

        currTab.find('.tab-content').css({
            'minHeight' : tabNewTitles.height()
        });
    });
    
    
    tabs.each(function(){
        var currTab = $(this);

        currTab.find('.tab-title').first().addClass('opened').next().show();

        currTab.find('.tab-title, .tab-title-left').click(function(e){
            
            e.preventDefault();
            
            var tabId = $(this).attr('id');
        
            if($(this).hasClass('opened')){
                if(currTab.hasClass('accordion') || $(window).width() < 767){
                    $(this).removeClass('opened');
                    $('#content_'+tabId).hide();
                }
            }else{
                currTab.find('.tab-title, .tab-title-left').each(function(){
                    var tabId = $(this).attr('id');
                    $(this).removeClass('opened');
                    $('#content_'+tabId).hide();
                });


                if(currTab.hasClass('accordion') || $(window).width() < 767){
                    $('#content_'+tabId).removeClass('tab-content').show();
                    setTimeout(function(){
                        $('#content_'+tabId).addClass('tab-content').show(); // Fix it
                    },1);
                } else {
                    $('#content_'+tabId).show();
                }
                $(this).addClass('opened');
            }
        });
    });


    // **********************************************************************// 
    // ! Products grid images slider
    // **********************************************************************//

    function contentProdImages() {
        $('.hover-effect-slider').each(function() {
            var slider = $(this);
            var index = 0;
            var autoSlide;
            var imageLink = slider.find('.product-content-image');
            var imagesList = imageLink.data('images-list');
            imagesList = imagesList.split(",");
            var arrowsHTML = '<div class="small-slider-arrow arrow-left">left</div><div class="small-slider-arrow arrow-right">right</div>';
            var counterHTML = '<div class="slider-counter"><span class="current-index">1</span>/<span class="slides-count">' + imagesList.length + '</span></div>';

            if(imagesList.length > 1) {
                slider.prepend(arrowsHTML);
                slider.prepend(counterHTML);

                // Previous image on click on left arrow
                slider.find('.arrow-left').click(function(event) {
                    if(index > 0) {
                        index--; 
                    } else {
                        index = imagesList.length-1; // if the first item set it to last
                    }
                    imageLink.find('img').attr('src', imagesList[index]); // change image src
                    slider.find('.current-index').text(index + 1); // update slider counter
                });

                // Next image on click on left arrow
                slider.find('.arrow-right').click(function(event) {
                    if(index < imagesList.length - 1) {
                        index++;
                    } else {
                        index = 0; // if the last image set it to first
                    }
                    imageLink.find('img').attr('src', imagesList[index]);// change image src
                    slider.find('.current-index').text(index + 1);// update slider counter
                });


            }

        });
    }



    contentProdImages();

}); // document ready

