(function($) {
    'use strict';
    var thisSlider;
    $(document).ready(function () {
        var body = $('body'),
            containerEl = $('.carousel-container'),
            carouselEl = containerEl.children('.carousel'),
            navigationEl = $('.navigation-container').children('.navigation'),
            textEl = containerEl.find('.carousel-text'),
            startStopEl,
            autoPlay = true,
            embedElsExistBool = false,
            visualEquipmentItemBool = false,
            initialEmbedEl;

        function adjustCarouselHeight () {
//            var maxHeight = 1;
//            carouselEl.find('img').each(function () {
//                var height = $(this).height();
//                if (height > 0 && height > maxHeight) {
//                    maxHeight = height;
//                }
//            });
//
//            containerEl.height(maxHeight);
        }

        if (carouselEl.children('li').length <= 1) { // if carousel only has 0 or 1 images, return
            adjustCarouselHeight();
            return;
        }

        function setupCircles (length) {
            var i = 1;
            while (--length >= 0) {
                $('<div data-num="' + i + '" class="circle">').appendTo(navigationEl);
                i += 1;
            }
        }

        function setupArrows () {
            if (navigationEl.hasClass('no-arrows')) {
                return;
            }
            $('<div class="arrow left"></div>').prependTo(navigationEl);
            $('<div class="arrow right"></div>').appendTo(navigationEl);
            navigationEl.children('.left').click(function () {
                containerEl.find('.arrow.back').trigger('click');
            });
            navigationEl.children('.right').click(function () {
                containerEl.find('.arrow.forward').trigger('click');
            });
        }

        function setupPausePlay() {
            var anythingStartStopEl,
                myStartStopEl = containerEl.find('.play-pause-container'),
                classStr;
            if (myStartStopEl.length === 0) {
                return;
            }

            classStr = (true === autoPlay)
                ? 'pause'
                : 'play';

            $('<div class="' + classStr + '"></div>').appendTo(myStartStopEl);
            startStopEl = myStartStopEl.children('.' + classStr);
            startStopEl.click(function () {
                // TODO - not working sometimes - DM - 101612
                if (typeof anythingStartStopEl === 'undefined') {
                    anythingStartStopEl = containerEl.find('.start-stop');
                }

                if (thisSlider.playing) { // then pause
                    thisSlider.startStop(false, true);
                    // set button to play icon
                    $(this).attr('class', 'play');
                } else {    // then play
                    thisSlider.startStop(true, false);
                    // set button to pause icon
                    $(this).attr('class', 'pause');
                }
//                anythingStartStopEl.trigger('click');
            });
        }

        function changeCircles (currentPage) {
            navigationEl.children().removeClass('active');
            navigationEl.find('[data-num="' + currentPage + '"]').eq(0).addClass('active');
        }

        function populateText (currentPage) {
//            var text = carouselEl.find('[data-num="' + currentPage + '"]').eq(0).attr('data-text');
//            textEl.html(text);
        }
        setTimeout(function () {
            adjustCarouselHeight();

            var sliderOptions = {
                buildArrows: true,
                buildStartStop: true,
                startPanel: 0,
                enableKeyboard: false,
                hashTags: false,
                autoPlay: true,
                autoPlayLocked: true,
                resumeDelay: -999,
//                width: 1651,
//                height: 779,
                pauseOnHover: true,
                stopAtEnd: false,
                delay: 5000,
                autoPlayDelayed: false,
                onBeforeInitialize:  function (e, slider) { // Callback before the plugin initializes
                    setupCircles(slider.$el[0].children.length);
                    setupArrows();
                    setupPausePlay();
                },
                onInitialized:       function (e, slider) { // Callback when the plugin finished initializing
                    carouselEl.find('embed').show();
                    thisSlider = slider;

                    changeCircles(slider.currentPage);
                    navigationEl.children().click(function () {
                        var num = $(this).attr('data-num');
                        $('.anythingControls').find('.panel' + num).trigger('click');
                    });
                },
                onShowStart:         function (e, slider) { // Callback on slideshow start
                    populateText(slider.currentPage);
                },
                onSlideComplete:     function (slider) {
                    populateText(slider.currentPage);
                    changeCircles(slider.currentPage);

                    if (true === visualEquipmentItemBool) {
                        window[namespaceStr].visualEquipment.onSlideComplete(slider);
                    }

                    if (!startStopEl) {
                        return;
                    }

                    // slider automatically starts assuming user wants to play slideshow again while it is paused after clicking a circle.. DUMB!
                    // NO combo of settings can rectify this
                    if (startStopEl.hasClass('play') && thisSlider.options.autoPlayLocked) { // if is paused
                        setTimeout(function () {
                            if (startStopEl.hasClass('pause')) { // if slideshow is playing, return
                                return;
                            }
                            thisSlider.startStop(false, true);
                        }, 100);
                    } else if (startStopEl.hasClass('pause') && !thisSlider.options.autoPlayLocked) {
                        setTimeout(function () {
                            if (startStopEl.hasClass('play')) { // if slideshow is paused, return
                                return;
                            }
                            thisSlider.startStop(true, false);
                        }, 100);
                    }
                }
            };

            carouselEl.anythingSlider(sliderOptions);
        }, 1000);
    });
}(jQuery));