window.jTour = function (tourdata, options) {

	"use strict"

	// required to init the plugin with tour = jTour(...)
	if (this === window) return new jTour(tourdata, options);

	// short validation
	if (typeof jQuery !== 'function' || jQuery.fn.jquery.replace(/\./g, '') < 171) {
		alert('jQuery >=1.7.1 is required for jTour!');
		return false;
	}
	if (!jQuery.isArray(tourdata)) jQuery.error('tourdata must be a valid array');

	// some variables we need
	var version = '2.0.0',
		$ = jQuery,
		box, content, progress, arrow, navigation, overlay, prefix = 'jTour-',
		// chrome and safari are different
		animateDOM = $('body, html'),
		$window = $(window),
		$document = $(document),
		current = 0,
		last, total = tourdata.length,
		timeout, steps, stepfunction, steppercentage = 0,
		isPaused = false,
		isStopped = false,
		manualskip = false,
		busy = false,
		scrollTop, scrollLeft, offsetX, offsetY, overlayZ = 20000,
		offsetFactor = 2.5,
		classStr, cssNames = {
			x: "scrollLeft",
			y: "scrollTop"
		},

		// the defaults which get overwritten with the options
		defaults = {
			speed: 1,
			width: null,
			height: null,
			startAt: 0,
			debug: false,
			axis: 'xy',
			autostart: false,
			autoplay: true,
			pauseOnHover: true,
			keyboardNav: true,
			showProgress: true,
			showControls: true,
			scrollBack: false,
			scrollDuration: 300,
			easing: 'swing',
			onStart: function () {},
			onStop: function () {},
			onPause: function () {},
			onPlay: function () {},
			onChange: function () {},
			onFinish: function () {},

			// these are the defaults for each step which can get overwritten in each step
			position: 'c',
			location: null,
			live: 'auto',
			offset: 0,
			wait: 0,
			expose: false,
			exposeOffset: 0,
			overlayOpacity: 0.2,
			delayIn: 200,
			delayOut: 100,
			animationIn: 'fadeIn',
			animationOut: 'fadeOut',
			buttons: null,
			onBeforeShow: function () {},
			onShow: function () {},
			onBeforeHide: function () {},
			onHide: function () {},
			onStep: function () {}
		},
		strings = $.extend({}, {
			'previous': 'Previous',
			'play': 'Play',
			'pause': 'Pause',
			'stop': 'Stop',
			'next': 'Next',
			'slower': 'Slower',
			'faster': 'Faster',
		}, options.strings || {});

	var settings = $.extend({}, defaults, options),

		// the defaults for each step
		stepdefaults = {
			html: '',
			text: '',
			position: settings.position,
			width: settings.width,
			height: settings.height,
			location: settings.location,
			live: settings.live,
			offset: settings.offset,
			wait: settings.wait,
			expose: settings.expose,
			exposeOffset: settings.exposeOffset,
			overlayOpacity: settings.overlayOpacity,
			delayIn: settings.delayIn,
			delayOut: settings.delayOut,
			animationIn: settings.animationIn,
			animationOut: settings.animationOut,
			onBeforeShow: settings.onBeforeShow,
			onShow: settings.onShow,
			onBeforeHide: settings.onBeforeHide,
			onHide: settings.onHide,
			onStep: settings.onStep,
			element: 'body',
			buttons: settings.buttons,
			steps: {},
		};

	function init() {

		if (getCookie(prefix + 'skip_' + api.id)) {
			_debug('Tour ' + api.id + ' will be skipped', 'warning');
			return false;
		}

		_debug('Initialize Tour');

		// Create new container
		box = $('<div/>', {
			'class': prefix + 'box'
		}).hide();

		// the content
		content = $('<div/>', {
			'class': prefix + 'content'
		}).appendTo(box);

		// the progressbar wrapper
		$('<div/>', {
			'class': prefix + 'progress'
		}).html('<div class="' + prefix + 'progress_bar"></div>').appendTo(box);

		// save the progressbar for later
		progress = box.find('.' + prefix + 'progress_bar');

		// we need control
		if (settings.showControls) {

			// the DOM
			navigation = $('<nav/>', {
					'class': prefix + 'nav'
				}).html('<a class="' + prefix + 'nav-btn icon-prev" title="' + strings.previous + '" data-role="prev">&nbsp;</a><a class="' + prefix + 'nav-btn icon-play" title="' + strings.play + '" data-role="play">&nbsp;</a><a class="' + prefix + 'nav-btn icon-pause" title="' + strings.pause + '" data-role="pause">&nbsp;</a><a class="' + prefix + 'nav-btn icon-stop" title="' + strings.stop + '" data-role="stop">&nbsp;</a><a class="' + prefix + 'nav-btn icon-next" title="' + strings.next + '" data-role="next">&nbsp;</a><a class="' + prefix + 'nav-btn icon-slower" title="' + strings.slower + '" data-role="slower">&nbsp;</a><a class="' + prefix + 'nav_btn icon-faster" title="' + strings.faster + '" data-role="faster">&nbsp;</a>').appendTo(box)
				// with some event delegation
				.on('click.jTour', 'a', function () {

					manualskip = true;
					// do stuff depending on which button was clicked
					switch ($(this).data('role')) {
					case 'next':
						next();
						break;
					case 'prev':
						prev();
						break;
					case 'slower':
						manualskip = false;
						changeSpeed(-0.25);
						break;
					case 'faster':
						manualskip = false;
						changeSpeed(0.25);
						break;
					case 'pause':
						pauseTour();
						break;
					case 'play':
						manualskip = false;
						continueTour();
						box.trigger('mouseleave');
						break;
					case 'stop':
						stop();
						break;
					}
				});

			// for styling we add a class to the box
			box.addClass('has-controls');

		}

		// the DOM for the overlay
		overlay = $('<div/>', {
			'class': prefix + 'overlay'
		}).hide();

		if (!settings.showProgress) box.find('.' + prefix + 'progress').hide();

		// append the overlay to the body and set its height to the document height
		if (settings.overlayOpacity !== false) {
			overlay.appendTo('body').css({
				'height': document.documentElement.scrollHeight - 100,
				'opacity': settings.overlayOpacity,
			});
		} else {
			overlay.remove();
		}

		// and append it to the body
		box.appendTo('body');

		// save the classes for later
		classStr = box.attr('class');

		// set the browser dimesions on resize
		$window.off('resize.jTour').on('resize.jTour', setClientDimesions).resize();

		// to access with the api we save it
		api.initialized = true;
		api.box = box;
		api.content = content;
		api.overlay = overlay;

		// set the axis as array
		if(typeof settings.axis == 'string') settings.axis = settings.axis.split('');

		// check the has for jTour (multipages)
		if (current = parseInt(getCookie(prefix + 'current', 0), 10)) {
			eraseCookie(prefix + 'current');
			start(current);
			return false;
		}

		// start immediately if required
		if (settings.autostart) start(settings.startAt);

	}

	function setClientDimesions() {
		_debug('Setting Client Dimensions');
		offsetX = (window.innerWidth || document.documentElement.clientWidth) / offsetFactor;
		offsetY = (window.innerHeight || document.documentElement.clientHeight) / offsetFactor;
	}

	function bindEvents() {
		// pause on mouseover
		settings.pauseOnHover && box.on({
			'mouseenter.jTour': pauseTour,
			'mouseleave.jTour': continueTour
		});

		// bind keyevents to the document
		settings.keyboardNav && $document.on({
			'keyup.jTour': keyEvent,
		});

	}

	function unbindEvents() {
		// unbind all events
		settings.pauseOnHover && box.off('.jTour');
		settings.keyboardNav && $document.off('.jTour');
	}

	function keyEvent(event) {

		_debug('KeyPress ' + event.keyCode + ' ' + event.which);
		// do some stuff when keys are pressed
		switch (event.keyCode) {
		case 37:
			// left arrow => previous step
			(current > 0) ? prev(): event.preventDefault();
			break;
		case 39:
			// right arrow => next step
			next();
			break;
		case 38:
			// up arrow => faster
			changeSpeed(0.25);
			break;
		case 40:
			// down arrow => slower
			changeSpeed(-0.25);
			break;
		case 32:
			// space => play/pause tour
			manualskip = true;
			pauseTour(true);
			break;
		case 27:
			// ESC => stop tour
			stop();
			break;
		}

	}

	function start(step) {

		if (!api.initialized) {
			init();
		}

		_debug('Start Tour ' + (step ? 'at step ' + (step + 1) : 'from the beginning'));

		// if no step is set use the current
		if (!step) step = current;

		// save starting position for later
		scrollTop = scrollTop || $window.scrollTop();
		scrollLeft = scrollLeft || $window.scrollLeft();

		// show the overlay and start the tour
		overlay.show().fadeIn(function () {
			bindEvents();
			isPaused = false;
			current = step;
			settings.onStart.call(api, current);
			show(step);
		});

		// toggle play/pause button
		if (settings.showControls) {
			navigation.find('.play').hide();
			navigation.find('.pause').show();
		}
	}

	function stop() {

		// prevent if tour is busy
		if (busy || isStopped) return;

		// clear the timout to prevent any further action
		clearTimeout(timeout);
		// stop the progress
		progress.clearQueue().stop();
		// hide the overlay
		overlay.hide();

		unbindEvents();

		// reset the CSS from the last element if it was exposed
		if (last && last.exposeElement) last.exposeElement.css(last.exposeElement.data('jTour')).removeData('jTour').removeClass(prefix + 'exposed');

		// hide box and set current to 0
		hide();
		current = 0;

		// toggle play/pause button
		if (settings.showControls) {
			navigation.find('.play').show();
			navigation.find('.pause').hide();
		}

		isStopped = true;

		// scroll back to the starting position
		if (settings.scrollBack) {
			scroll(scrollLeft, scrollTop, settings.scrollDuration, function () {
				settings.onStop.call(api, current);
			});
		} else {
			settings.onStop.call(api, current);
		}

		_debug('Stop Tour ' + (current ? 'at step ' + (current + 1) : 'on first step'));
	}

	function pauseTour(toggle, firecb) {

		// if toggle is true use this as play or pause function
		if (isPaused) {
			if (toggle === true) {
				manualskip = !manualskip;
				continueTour();
				return;
			}
			if (!manualskip) return;
		}

		// callback
		if (firecb !== false) settings.onPause.call(api, current);

		// clear the timout to prevent any further action
		clearTimeout(timeout);
		// stop the progress
		progress.clearQueue().stop();

		// toggle play/pause button
		if (settings.showControls && manualskip) {
			navigation.find('.play').show();
			navigation.find('.pause').hide();
		}

		isPaused = true;
		_debug('Pause Tour ' + (current ? 'at step ' + (current + 1) : 'from the beginning') + ' at ' + steppercentage.toFixed(2) + '%');

	}

	function continueTour(firecb) {

		// only continue if no manual pause action was called
		if (isPaused && !manualskip) {

			// callback
			if (firecb !== false) settings.onPlay.call(api, current);

			// clear the timout to prevent any further action
			clearTimeout(timeout);

			// reset the progress from it's paused position
			var percentage = progress.width() / (content.width() / 100) / 100;
			var time = tourdata[current].live;
			if(!time){
				return;
			}

			// get the current time of the step
			time -= time * percentage;
			// stop the progress
			progress.clearQueue().stop().animate({
				width: '100%'
			}, {
				duration: time * (1 / settings.speed),
				easing: 'linear',
				step: stepfunction,
				complete: next,
			});

			if (settings.showControls) {
				navigation.find('.play').hide();
				navigation.find('.pause').show();
			}

			isPaused = false;
			_debug('Continue Tour ' + (current ? 'at step ' + (current + 1) : 'from the beginning'));
		}
	}

	function skip(delay) {
		setCookie(prefix + 'skip_' + api.id, true, delay || 60);
		stop();
	}

	function restart(step) {

		// hide the box
		hide();
		// set current step to step or to zero if not set
		current = step || 0;
		// start again
		start(step);
	}

	function next() {

		// prevent if tour is busy
		if (busy) return;

		// some steps haven't been executed
		if (steps && tourdata[current].steps) {
			if (tourdata[current].onStep) tourdata[current].onStep.call(api, last, 100);
			$.each(steps, function (i, e) {
				tourdata[current].steps[steps[i]].call(api, last, e);
			});
			steps = null;
		}

		// if it's not the last step
		if (current + 1 < total) {
			// clear the timout to prevent any further action
			clearTimeout(timeout);
			progress.clearQueue().stop();

			// show next step
			show(++current);

			continueTour(false);

			// we have the last step (no step left)
		} else {
			// and stop the tour
			stop();
			// call the callback
			settings.onFinish.call(api, current);
		}
	}

	function prev() {

		// prevent if tour is busy
		if (busy) return;

		// if it's not the first step
		if (current > 0) {
			// clear the timout to prevent any further action
			clearTimeout(timeout);
			progress.clearQueue().stop();
			// show previous step
			show(--current);
		}
	}

	function _compareURL(url1, url2) {
		var url = document.createElement('a');
		url.href = url1;
		url1 = url.href.replace(/\/+$/, '');
		url.href = url2;
		url2 = url.href.replace(/\/+$/, '');
		return url1 == url2;

	}

	function show(step) {

		// check for multipage feature
		if (tourdata[step] && tourdata[step].location) {
			if (!_compareURL(tourdata[step].location, window.location.href)) {
				setCookie(prefix + 'current', step || 0);
				window.location.href = tourdata[step].location;
				return false;
			}
		}

		if (settings.debug) {
			_debug(tourdata[step]);
			for (var data in tourdata[step]) {
				if (typeof stepdefaults[data] === 'undefined') {
					_debug('Property "' + data + '" is not allowed in a step!', 'error');
				}
			}
		}

		// get options for this step
		var options = $.extend({}, stepdefaults, tourdata[step]),
			// get the jQuery DOM element
			$element;

		if('body' == options.element || 'html' == options.element){
			options.position = 'c';
		}

		$element = options.element ? ((typeof options.element == 'string') ? $(options.element) : options.element) : 0;

		if ($.isFunction($element)) {
			// $element = $element.call(api, $element);
			$element = $($element());
		}

		// the jQuery DOM element doesn't exist
		if (!$element.length) {
			// throw an info in the console
			_debug('Element doesn\'t exist!', 'error');
			// reduce total with one
			total--;
			// open next step
			next();
			return;
		}

		var message = (options.text) ?
			($.isFunction(options.text) ? options.text.apply(api) : options.text) :
			($.isFunction(options.html) ? options.html.apply(api) : options.html);


		// live time must be calculated
		if (options.live === 'auto') {

			// need temp DOM object to calculate length without any HTML tags
			var temp = $('<div>').html(message),
				length = temp.text().length;

			// calculate live time depending on content length, but at least 2500 ms
			options.live = tourdata[step].live = Math.max(2500, Math.log(length / 10) * 2500 + 1000);
		// no live at all
		}else if(!options.live){
			options.live = false;
		}else{
			// make sure it's always positive;
			options.live = Math.abs(options.live);

		}


		// this is a image map
		options.isArea = $element[0].nodeName.toLowerCase() == 'area';

		// we have no last step (it's the first one)
		if (!last) {
			// we don't need a duration for the fadeOut
			options.delayOut = 0;
			options.animationOut = 'hide';
		} else {
			// callback for the last step
			last.onBeforeHide.call(api, last.element);
		}

		busy = true;

		// hide the last step with special function
		box[options.animationOut](options.delayOut, function () {

			// temporary position the invisible box for calculation
			box.css({
				left: 0,
				top: 0,
				minWidth: 0,
				width: options.width || 'auto',
				height: options.height || 'auto',
			});
			// callback if we have a previous step
			if (last) last.onHide.call(api, last.element);

			// save timeout so we can clear it
			timeout = setTimeout(function () {

				var dimensions;

				// set the content of the step
				if (options.text) {
					content.text(message);
				} else {
					content.html(message);
				}

				if (options.buttons) {
					var buttonarea = $('<div>', {
						'class': prefix + 'button-area'
					})
					options.buttons.map(function (button, i) {
						var a = $('<' + (button.tag || 'a') + '>', {
							'class': prefix + 'button' + (button.className ? ' ' + button.className : '')
						}).text(button.label);
						button.click && a.on('click', $.proxy(button.click, api));
						a.appendTo(buttonarea);
					});
					buttonarea.appendTo(content);
				}

				// callback with current step id
				settings.onChange.call(api, step);

				if ('body' == options.element || 'html' == options.element) {
					// save the dimensions of the target element
					dimensions = {
						width: offsetX * offsetFactor,
						height: (offsetY * offsetFactor / 2) - box.outerHeight() / 2,
						x: 0,
						y: 0
					};

					// target is an area
				} else if (options.isArea) {

					// get the coordinates
					var coords = $element[0].coords.split(',');

					// get postion of image including padding and border width
					var img = options.exposeElement = $('img[usemap=#' + $element.parent().attr('name') + ']'),
						imgoffset = img.offset(),
						imgpadding = {
							top: parseInt(img.css('paddingTop'), 10),
							left: parseInt(img.css('paddingLeft'), 10)
						},
						imgborder = {
							top: parseInt(img.css('borderTopWidth'), 10),
							left: parseInt(img.css('borderLeftWidth'), 10)
						};

					// save the dimensions of the target element
					dimensions = {
						width: coords[2] - coords[0],
						height: coords[3] - coords[1],
						x: parseInt(coords[0], 10) + imgoffset.left + imgpadding.left + imgborder.left,
						y: parseInt(coords[1], 10) + imgoffset.top + imgpadding.top + imgborder.top
					};

					// target is an another DOM element
				} else {
					// save the dimensions of the target element
					dimensions = {
						width: $element.outerWidth(),
						height: $element.outerHeight(),
						x: $element.offset().left,
						y: $element.offset().top
					};
				}


				// raw postion of the box element
				var position = {
					left: dimensions.x,
					top: dimensions.y
				};

				// correction is required for large elements
				var scrollcorrection = {
					x: 0,
					y: 0
				};

				// modify postion depending on the box position and add the offset to the element
				switch (options.position) {

				case 'ne':
					if (!isNaN(options.offset)) options.offset = {
						x: 0,
						y: options.offset
					};
					position.top -= box.outerHeight() + options.offset.y;
					position.left = dimensions.x + dimensions.width - box.outerWidth() + options.offset.x;
					scrollcorrection.x = dimensions.width / 2 - +box.outerWidth() / 2 + options.offset.x;
					// scrollcorrection.y = -dimensions.height / 2 - box.outerHeight() / 2 - options.offset.y;
					scrollcorrection.y = -options.offset.y + 10;
					break;

				case 'nw':
					if (!isNaN(options.offset)) options.offset = {
						x: 0,
						y: options.offset
					};
					position.top -= box.outerHeight() + options.offset.y;
					position.left = dimensions.x - options.offset.x;
					scrollcorrection.x = -dimensions.width / 2 + box.outerWidth() / 2 - options.offset.x;
					// scrollcorrection.y = -dimensions.height / 2 - box.outerHeight() / 2 - options.offset.y;
					scrollcorrection.y = -options.offset.y + 10;
					break;

				case 'n':
					if (!isNaN(options.offset)) options.offset = {
						x: 0,
						y: options.offset
					};
					position.top -= box.outerHeight() + options.offset.y;
					position.left += (dimensions.width - box.outerWidth()) / 2 + options.offset.x;
					scrollcorrection.x = options.offset.x;
					scrollcorrection.y = -dimensions.height / 2 - box.outerHeight() / 2 - options.offset.y;
					scrollcorrection.y = -options.offset.y + 10;
					break;

				case 'se':
					if (!isNaN(options.offset)) options.offset = {
						x: 0,
						y: options.offset
					};
					position.top += dimensions.height + options.offset.y;
					position.left = dimensions.x + dimensions.width - box.outerWidth() + options.offset.x;
					scrollcorrection.x = dimensions.width / 2 - box.outerWidth() / 2 + options.offset.x;
					// scrollcorrection.y = dimensions.height / 2 + box.outerHeight() / 2 + options.offset.y;
					scrollcorrection.y = -options.offset.y + 10;
					break;

				case 'sw':
					if (!isNaN(options.offset)) options.offset = {
						x: 0,
						y: options.offset
					};
					position.top += dimensions.height + options.offset.y;
					position.left = dimensions.x - options.offset.x;
					scrollcorrection.x = -dimensions.width / 2 + box.outerWidth() / 2 - options.offset.x;
					// scrollcorrection.y = dimensions.height / 2 + box.outerHeight() / 2 + options.offset.y;
					scrollcorrection.y = -options.offset.y + 10;
					break;

				case 's':
					if (!isNaN(options.offset)) options.offset = {
						x: 0,
						y: options.offset
					};
					position.top += dimensions.height + options.offset.y;
					position.left += (dimensions.width - box.outerWidth()) / 2 + options.offset.x;
					scrollcorrection.x = options.offset.x;
					// scrollcorrection.y = dimensions.height / 2 + box.outerHeight() / 2 + options.offset.y;
					scrollcorrection.y = -options.offset.y + 10;
					break;

				case 'w':
					if (!isNaN(options.offset)) options.offset = {
						x: options.offset,
						y: 0
					};
					position.top -= box.outerHeight() / 2 - dimensions.height / 2 - options.offset.y;
					position.left -= box.outerWidth() + options.offset.x;
					scrollcorrection.x = -dimensions.width / 2 + box.outerWidth() / 2 - options.offset.x;
					scrollcorrection.y = -options.offset.y;
					break;

				case 'e':
					if (!isNaN(options.offset)) options.offset = {
						x: options.offset,
						y: 0
					};
					position.top -= box.outerHeight() / 2 - dimensions.height / 2 - options.offset.y;
					position.left += dimensions.width + options.offset.x;
					scrollcorrection.x = dimensions.width / 2 - box.outerWidth() / 2 + options.offset.x;
					scrollcorrection.y = -options.offset.y;
					break;

				case 'c':
					if (!isNaN(options.offset)) options.offset = {
						x: 0,
						y: options.offset
					};
					position.top -= box.outerHeight() / 2 - dimensions.height / 2 - options.offset.y;
					position.left += (dimensions.width - box.outerWidth()) / 2 + options.offset.x;
					scrollcorrection.x = options.offset.x;
					scrollcorrection.y = options.offset.y;
					break;

				}

				// we need the position of our element
				var scrolltopos = {
					x: Math.max(0, dimensions.x - (offsetX * offsetFactor / 2 - dimensions.width / 2) + scrollcorrection.x),
					y: Math.max(0, dimensions.y - (offsetY * offsetFactor / 2 - dimensions.height / 2) + scrollcorrection.y)
				};

				// reset the style for the last element
				if (last && last.exposeElement && !options.isArea) last.exposeElement.css(last.exposeElement.data('jTour')).removeData('jTour').removeClass(prefix + 'exposed');

				// scroll to the position
				scroll(scrolltopos.x, scrolltopos.y, settings.scrollDuration, function () {
					// add the postion as class
					box.addClass(prefix + 'arrow ' + options.position);

					// if steps where defined
					if (options.steps) {
						// get all steps as array
						steps = [];
						$.each(options.steps, function (k) {
							steps.push(k);
						});

						// execute all steps that are lower or equal percentage (percentage = 0 - 100)
						stepfunction = function (percentage) {
							steppercentage = percentage;
							options.onStep.call(api, $element, percentage);
							var steplength = steps.length;
							if (!steplength) return;
							for (var i = 0; i < steplength; i++) {
								if (percentage >= steps[i]) {
									options.steps[steps[i]].call(api, $element);
									steps.shift();
								}
							}
						};

					} else {
						// no function required
						stepfunction = function (percentage) {
							steppercentage = percentage;
							options.onStep.call(api, $element, percentage);
						};
					}

					// we have controls
					if (settings.showControls) {

						// hide previous or next if it is the first or last step
						if (step === 0) {
							navigation.find('a.prev').hide();
						} else if (step == total - 1) {
							navigation.find('a.next').hide();
						} else {
							navigation.find('a.next, a.prev').show();
						}
					}

					// callback
					options.onBeforeShow.call(api, $element);

					// reset the progressbar
					progress.clearQueue().stop().css({
						width: '0%'
					});

					// add a minwidth to prevent smaller box outside of the viewport
					position['min-width'] = box.width();

					// set the element realtive and a zindex higher than the overlay
					if (options.overlayOpacity) {
						var polygonpoints = '0,0 0,0 0,0 0,0';
						if (options.expose && $element != animateDOM) {

							var exposeelement = $element[0],
								bound, exposeoffset;

							if (typeof options.expose == 'string') {
								exposeelement = $(options.expose)[0];
							}

							if (typeof options.expose == 'object') {
								bound = {
									top: options.expose[0],
									right: options.expose[1],
									bottom: options.expose[2],
									left: options.expose[3],
								};
							} else {
								bound = exposeelement.getBoundingClientRect();
							}

							exposeoffset = !isNaN(options.exposeOffset) ? [options.exposeOffset, options.exposeOffset, options.exposeOffset, options.exposeOffset] : options.exposeOffset;

							polygonpoints = (bound.left - exposeoffset[3]) + ',' + (bound.top + $window.scrollTop() - exposeoffset[0]) + ' ' + (bound.left - exposeoffset[3]) + ',' + (bound.bottom + $window.scrollTop() + exposeoffset[2]) + ' ' + (bound.right + exposeoffset[1]) + ',' + (bound.bottom + $window.scrollTop() + exposeoffset[2]) + ' ' + (bound.right + exposeoffset[1]) + ',' + (bound.top + $window.scrollTop() - exposeoffset[0]);

							overlay.addClass('is-exposed');

						} else {

							overlay.removeClass('is-exposed');
						}

						overlay.html('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' + (document.documentElement.scrollWidth) + ' ' + (document.documentElement.scrollHeight) + '" preserveAspectRatio="none"><defs><mask id="' + prefix + 'overlay_exposure" ><rect width="100%" height="100%" fill="#ffffff"/> <polygon points="' + polygonpoints + '" fill="#000000"/></mask></defs><rect class="fill" width="100%" height="100%" mask="url(#' + prefix + 'overlay_exposure)" /></svg>');

					}

					if (last) {
						// change overlay opacity if required
						if (last.overlayOpacity != options.overlayOpacity) overlay.fadeTo(options.delayIn * 2, options.overlayOpacity);

						// and set it explicitly on the first step
					} else {
						overlay.css({
							'opacity': options.overlayOpacity
						});
					}

					arrow = prefix + 'arrow ' + options.position;

					// apply the postion and the class, show the box
					box.css(position).attr('class', classStr + ' ' + arrow + ' step-' + step)[options.animationIn](options.delayIn, function () {

						busy = false;
						// callback
						options.onShow.call(api, $element);
						// the current step is our next last one
						last = options;

						steppercentage = 0;

						// if autoplay is active and the tour isn't paused
						if (options.live && settings.autoplay && !isPaused) {
							manualskip = false;
							// animate the progress bar and goto the next step on finish
							progress.stop().animate({
								width: '100%'
							}, {
								duration: options.live * (1 / settings.speed),
								easing: 'linear',
								step: stepfunction,
								complete: next
							});
						}
					});
				});

			}, options.wait);
		});

	}

	function hide() {

		// if no last is set we have nothing to hide
		if (!last) return;

		// callback
		last.onBeforeHide.call(api, last.element);

		// stop every animation
		progress.clearQueue().stop();
		box.stop();

		// and hide the box
		box[last.animationOut](last.delayOut, function () {
			// remove content
			content.empty();
			// callback
			last.onHide.call(api, last.element);
			last = null;

		});
	}

	function changeSpeed(offset) {
		// change the general speed of steps
		settings.speed = Math.max(0.1, settings.speed + offset);

		// prevent if tour is busy
		if (busy) return;

		_debug('Change speed to ' + settings.speed);

		pauseTour(null, false);
		continueTour(false);
	}

	function moveTo(x, y, duration, callback) {
		if (typeof x == 'object') {
			callback = ($.isFunction(y)) ? y : ($.isFunction(duration)) ? duration : false;
			duration = (!isNaN(y)) ? y : 0;
			y = x.offset().top;
			x = x.offset().left;
		}
		if ($.isFunction(duration)) {
			callback = duration;
			duration = false;
		}
		var to = {};
		if (x !== null) to.left = x;
		if (y !== null) to.top = y;

		box.animate(to, {
			duration: (duration === false) ? 0 : duration,
			complete: callback &&
				function () {
					callback.call(api);
				}
		});
	}

	function offset(x, y, duration, callback) {
		moveTo('+=' + x, '+=' + y, duration, callback);
	}

	function scroll(x, y, duration, callback) {

		// no need to scroll
		if (animateDOM.scrollTop() == y && animateDOM.scrollLeft() == x) duration = 1;

		var scrollto = {};

		$.each(settings.axis, function (i, ax) {
			scrollto[cssNames[ax]] = (ax == 'x') ? x : y;
		});

		// animate the html or body
		animateDOM.animate(scrollto, {
			duration: duration || settings.scrollDuration,
			complete: function () {
				callback && callback.call(api);
			},
			queue: true,
			easing: settings.easing
		});
	}

	function setCookie(name, value, seconds) {
		var expires = "";
		if (seconds) {
			var date = new Date();
			date.setTime(date.getTime() + (seconds * 1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value) + expires + "; path=/";
	}

	function getCookie(name, fallback) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
		}
		return fallback !== null ? fallback : null;
	}

	function eraseCookie(name) {
		setCookie(name, false, -1);
	}

	function _debug(message, type) {
		if (!options.debug) return;
		if (typeof message != 'string') {
			settings.id && console.log(settings.id);
			console.table(message);
			return;
		}
		switch (type) {
		case 'error':
			console.error(settings.id, message);
			break;
		case 'warning':
			console.warn(settings.id, message);
			break;
		default:
			console.warn(settings.id, message);
			break;
		}
	}

	function _hash(s) {
		var h = 0,
			l = s.length,
			i = 0;
		if (l > 0)
			while (i < l)
				h = (h << 5) - h + s.charCodeAt(i++) | 0;

		return Math.abs(h);
	}

	function _is_url(str) {
		var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
			'((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
			'((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
			'(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
			'(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
			'(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
		return !!pattern.test(str);
	}

	// our API with public methods
	var api = {
		start: function (step) {
			start(step);
		},
		restart: function (step) {
			restart(step);
		},
		pause: function (toggle) {
			manualskip = true;
			pauseTour(toggle);
		},
		play: function () {
			manualskip = false;
			continueTour();
		},
		stop: function () {
			stop();
		},
		skip: function () {
			skip();
		},
		next: function () {
			next();
		},
		prev: function () {
			prev();
		},
		faster: function (value) {
			changeSpeed(value || 0.25);
		},
		slower: function (value) {
			changeSpeed(value || -0.25);
		},
		moveTo: function (x, y, duration, callback) {
			moveTo(x, y, duration, callback);
		},
		offset: function (x, y, duration, callback) {
			offset(x, y, duration, callback);
		},
		scroll: function (x, y, duration, callback) {
			scroll(x, y, duration, callback);
		}
	};


	api.initialized = false;
	api.box = api.content = api.overlay = null;
	api.tourdata = tourdata;
	api.id = options.id || _hash(JSON.stringify(tourdata));

	// return the api for access
	return api;
};