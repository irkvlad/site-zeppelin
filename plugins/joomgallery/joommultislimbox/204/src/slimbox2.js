/*!
	Slimbox v2.04 - The ultimate lightweight Lightbox clone for jQuery
	(c) 2007-2010 Christophe Beyls <http://www.digitalia.be>
	MIT-style license.
*/

// Added by JoomGallery team
jQuery.noConflict();

(function($) {

	// Global variables, accessible to Slimbox only
	var win = $(window), options, images, activeImage = -1, activeURL, prevImage, nextImage, compatibleOverlay, middle, centerWidth, centerHeight,
		ie6 = !window.XMLHttpRequest, hiddenElements = [], documentElement = document.documentElement,

	// Preload images
	preload = {}, preloadPrev = new Image(), preloadNext = new Image(),

	// DOM elements
	overlay, center, image, sizer, prevLink, nextLink, bottomContainer, bottom, caption, number;

	/*
		Initialization
	*/

	$(function() {
		// Append the Slimbox HTML code at the bottom of the document
		$("body").append(
			$([
				overlay = $('<div id="lbOverlay" />')[0],
				center = $('<div id="lbCenter" />')[0],
				bottomContainer = $('<div id="lbBottomContainer" />')[0]
			]).css("display", "none")
		);

		image = $('<div id="lbImage" />').appendTo(center).append(
			sizer = $('<div style="position: relative;" />').append([
				prevLink = $('<a id="lbPrevLink" href="#" />').click(previous)[0],
				nextLink = $('<a id="lbNextLink" href="#" />').click(next)[0]
			])[0]
		)[0];

		bottom = $('<div id="lbBottom" />').appendTo(bottomContainer).append([
			$('<a id="lbCloseLink" href="#" />').add(overlay).click(close)[0],
			caption = $('<div id="lbCaption" />')[0],
			number = $('<div id="lbNumber" />')[0],
			$('<div style="clear: both;" />')[0]
		])[0];
	});


	/*
		API
	*/

	// Open Slimbox with the specified parameters
	$.slimbox = function(_images, startImage, _options) {
    // Edit JoomGallery team flexible resize duration
    resizeduration = (11 - resizeSpeed) * 150;
    // Edit JoomGallery team

		options = $.extend({
			loop: false,				// Allows to navigate between first and last images
			overlayOpacity: 0.8,			// 1 is opaque, 0 is completely transparent (change the color in the CSS file)
			overlayFadeDuration: 400,		// Duration of the overlay fade-in and fade-out animations (in milliseconds)
      //resizeDuration: 400,      // Duration of each of the box resize animations (in milliseconds)
      resizeDuration: resizeduration,
			resizeEasing: "swing",			// "swing" is jQuery's default easing
			initialWidth: 250,			// Initial width of the box (in pixels)
			initialHeight: 250,			// Initial height of the box (in pixels)
			imageFadeDuration: 400,			// Duration of the image fade-in animation (in milliseconds)
			captionAnimationDuration: 400,		// Duration of the caption animation (in milliseconds)
      //counterText: "Image {x} of {y}",  // Translate or change as you wish
      // Edit JoomGallery team flexible language
      counterText: joomgallery_image+" {x} "+joomgallery_of+ "  {y}",
			closeKeys: [27, 88, 67],		// Array of keycodes to close Slimbox, default: Esc (27), 'x' (88), 'c' (67)
			previousKeys: [37, 80],			// Array of keycodes to navigate to the previous image, default: Left arrow (37), 'p' (80)
			nextKeys: [39, 78],			// Array of keycodes to navigate to the next image, default: Right arrow (39), 'n' (78)
      // Edit Joomgallery team, get viewport of browser for later resizing
      winWidth: (getWidth() > 0) ? getWidth() : 1024,
      winHeight: (getHeight() > 0) ? getHeight() : 800
      // End edit JoomGallery team

		}, _options);

		// The function is called for a single image, with URL and Title as first two arguments
		if (typeof _images == "string") {
			_images = [[_images, startImage]];
			startImage = 0;
		}

		middle = win.scrollTop() + (win.height() / 2);
		centerWidth = options.initialWidth;
		centerHeight = options.initialHeight;
		$(center).css({top: Math.max(0, middle - (centerHeight / 2)), width: centerWidth, height: centerHeight, marginLeft: -centerWidth/2}).show();
		compatibleOverlay = ie6 || (overlay.currentStyle && (overlay.currentStyle.position != "fixed"));
		if (compatibleOverlay) overlay.style.position = "absolute";
		$(overlay).css("opacity", options.overlayOpacity).fadeIn(options.overlayFadeDuration);
		position();
		setup(1);

		images = _images;
		options.loop = options.loop && (images.length > 1);
		return changeImage(startImage);
	};

	/*
		options:	Optional options object, see jQuery.slimbox()
		linkMapper:	Optional function taking a link DOM element and an index as arguments and returning an array containing 2 elements:
				the image URL and the image caption (may contain HTML)
		linksFilter:	Optional function taking a link DOM element and an index as arguments and returning true if the element is part of
				the image collection that will be shown on click, false if not. "this" refers to the element that was clicked.
				This function must always return true when the DOM element argument is "this".
	*/
	$.fn.slimbox = function(_options, linkMapper, linksFilter) {
		linkMapper = linkMapper || function(el) {
			return [el.href, el.title];
		};

		linksFilter = linksFilter || function() {
			return true;
		};

		var links = this;

		return links.unbind("click").click(function() {
			// Build the list of images that will be displayed
			var link = this, startIndex = 0, filteredLinks, i = 0, length;
			filteredLinks = $.grep(links, function(el, i) {
				return linksFilter.call(link, el, i);
			});

			// We cannot use jQuery.map() because it flattens the returned array
			for (length = filteredLinks.length; i < length; ++i) {
				if (filteredLinks[i] == link) startIndex = i;
				filteredLinks[i] = linkMapper(filteredLinks[i], i);
			}

			return $.slimbox(filteredLinks, startIndex, _options);
		});
	};


	/*
		Internal functions
	*/

	function position() {
		var l = win.scrollLeft(), w = win.width();
		$([center, bottomContainer]).css("left", l + (w / 2));
		if (compatibleOverlay) $(overlay).css({left: l, top: win.scrollTop(), width: w, height: win.height()});
	}

	function setup(open) {
		if (open) {
			$("object").add(ie6 ? "select" : "embed").each(function(index, el) {
				hiddenElements[index] = [el, el.style.visibility];
				el.style.visibility = "hidden";
			});
		} else {
			$.each(hiddenElements, function(index, el) {
				el[0].style.visibility = el[1];
			});
			hiddenElements = [];
		}
		var fn = open ? "bind" : "unbind";
		win[fn]("scroll resize", position);
		$(document)[fn]("keydown", keyDown);
	}

	function keyDown(event) {
		var code = event.keyCode, fn = $.inArray;
		// Prevent default keyboard action (like navigating inside the page)
		return (fn(code, options.closeKeys) >= 0) ? close()
			: (fn(code, options.nextKeys) >= 0) ? next()
			: (fn(code, options.previousKeys) >= 0) ? previous()
			: false;
	}

	function previous() {
		return changeImage(prevImage);
	}

	function next() {
		return changeImage(nextImage);
	}

	function changeImage(imageIndex) {
		if (imageIndex >= 0) {
			activeImage = imageIndex;
			activeURL = images[activeImage][0];
			prevImage = (activeImage || (options.loop ? images.length : 0)) - 1;
			nextImage = ((activeImage + 1) % images.length) || (options.loop ? 0 : -1);

			stop();
			center.className = "lbLoading";

			preload = new Image();
			preload.onload = animateBox;
			preload.src = activeURL;
		}

		return false;
	}

  function animateBox() {
    center.className = "";

    // Get size of viewport
    // http://andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
    if (typeof window.innerWidth != 'undefined')
    {
      winWidth = window.innerWidth;
      winHeight = window.innerHeight;
    }
    // IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
    else if(typeof document.documentElement != 'undefined'
            && typeof document.documentElement.clientWidth != 'undefined'
            && document.documentElement.clientWidth != 0)
    {
      winWidth = document.documentElement.clientWidth;
      winHeight = document.documentElement.clientHeight;
    }
    // older versions of IE
    else
    {
      winWidth = document.getElementsByTagName('body')[0].clientWidth;
      winHeight = document.getElementsByTagName('body')[0].clientHeight;
    }

    // Edit JoomGallery team resize adopted from modified v1.41
    if(resizeJsImage == 1)
    {
      //innerHTML = image.innerHTML;
      if(preload.width > (winWidth * 0.99))
      {
        preload.height = (preload.height * (winWidth * 0.99)) / preload.width;
        preload.width  = winWidth * 0.99;
      }
      if(preload.height>(winHeight * 0.88))
      {
        preload.width  = (preload.width * (winHeight * 0.88)) / preload.height;
        preload.height = winHeight * 0.88;
      }
      innerImageHtml = "<img src=\""+images[activeImage][0]+"\" width=\""+preload.width+"\" height=\""+preload.height+"\" />";
      // If image not exists create it
      if($("#sbimg").length < 1)
      {
        imgelem  = new Element('img',{id: 'sbimg'});
        imgelem.inject(image);
      }
      imgelem.setProperty('src', images[activeImage][0]);
      imgelem.setProperty('width', preload.width);
      imgelem.setProperty('height', preload.height);
    }
    else
    {
      $(image).style.backgroundImage = 'url('+images[activeImage][0]+')';
    }
    //End Edit JoomGallery team resize

		$(image).css({width: preload.width, visibility: "hidden", display: ""});
		$(sizer).width(preload.width);
		$([prevLink, nextLink]).height(preload.height);

		$(caption).html(images[activeImage][1] || "");

    // Edit JoomGallery team
    // check multiple links for correction of the counter
    // return an array with unique object keys
    uniquearr   = new Array();
    uniquearr   = joomcheckmulti(images);
    uniquecount = joomuniquelength(uniquearr);
    uniquemaxid = joomidmax(uniquearr, images.length);

    // Check if a double deleted image and jump to the right one
    changed = false;
    while(!uniquearr[activeImage])
    {
      activeImage++;
      changed=true;
      nextImage++;
    }
    while(!uniquearr[prevImage] && prevImage >= 0)
    {
      prevImage--;
    }
    if(changed)
    {
      while(!uniquearr[nextImage] && nextImage <= uniquemaxid)
      {
        nextImage++;
      }
      if (nextImage > uniquemaxid)
      {
        nextImage = -1;
      }
    }
    // Get the right counter of actual image
    if(prevImage < 0)
    {
      imageactcounter = 1;
    }
    else
    {
      imageactcounter=joomgetactcount(uniquearr,images.length,activeImage);
    }

    $(number).html((((images.length > 1) && options.counterText) || "").replace(/{x}/, activeImage + 1).replace(/{y}/, images.length));
    // End edit JoomGalleryteam


		if (prevImage >= 0) preloadPrev.src = images[prevImage][0];
		if (nextImage >= 0) preloadNext.src = images[nextImage][0];

		centerWidth = image.offsetWidth;
		centerHeight = image.offsetHeight;
		var top = Math.max(0, middle - (centerHeight / 2))-35;
		if (center.offsetHeight != centerHeight) {
			$(center).animate({height: centerHeight, top: top}, options.resizeDuration, options.resizeEasing);
		}
		if (center.offsetWidth != centerWidth) {
			$(center).animate({width: centerWidth, marginLeft: -centerWidth/2}, options.resizeDuration, options.resizeEasing);
		}
		$(center).queue(function() {
			$(bottomContainer).css({width: centerWidth, top: top + centerHeight, marginLeft: -centerWidth/2, visibility: "hidden", display: ""});
			$(image).css({display: "none", visibility: "", opacity: ""}).fadeIn(options.imageFadeDuration, animateCaption);
		});
	}

	function animateCaption() {
		if (prevImage >= 0) $(prevLink).show();
		if (nextImage >= 0) $(nextLink).show();
		$(bottom).css("marginTop", -bottom.offsetHeight).animate({marginTop: 0}, options.captionAnimationDuration);
		bottomContainer.style.visibility = "";
	}

	function stop() {
		preload.onload = null;
		preload.src = preloadPrev.src = preloadNext.src = activeURL;
		$([center, image, bottom]).stop(true);
		$([prevLink, nextLink, image, bottomContainer]).hide();
	}

	function close() {
		if (activeImage >= 0) {
			stop();
			activeImage = prevImage = nextImage = -1;
			$(center).hide();
			$(overlay).stop().fadeOut(options.overlayFadeDuration, setup);
		}

		return false;
	}
  // Internal functions for JoomGallery
  // needed to avoid displaying the same picture multiple
  // and the right counter in the slimbox
  // JoomGallery team October 2010, adapted from code of JoomGallery

  // analyzes the images array and construct
  // an array with unique numbers
  function joomcheckmulti(images)
  {
    o = {};
    ilength = images.length;
    for(i = 0; i < ilength; i++)
    {
      // Create an array with unique URL
      // and number of object in images
      o[images[i]["0"]] = i;
    }
    // Create an array with the object numbers from o
    p = new Array();
    for (i in o)
    {
      p[o[i]] = true;
    }
    return p;
  }

  // Returns the count of all unique pictures
  function joomuniquelength(uniarr)
  {
    arrlength    = uniarr.length;
    uniquelength = arrlength;
    for (i=0; i < arrlength; i++)
    {
      if(!uniarr[i])
      {
        uniquelength--;
      }
    }
    return uniquelength;
  }

  // Returns the max. object id of picture in the array
  function joomidmax(uniarr,imlength)
  {
    maxid = 0;
    for (i=0; i<=imlength; i++)
    {
      if(uniarr[i])
      {
        maxid=Math.max(maxid,i);
      }
    }
    return maxid;
  }

  // Returns the count of actual picture showing in the box
  function joomgetactcount (uniarr, imlength, aktcounter)
  {
    actcount=0;
    for (i=0; i<=imlength; i++)
    {
      if(uniarr[i])
      {
        actcount++;
        if (i==aktcounter)
        {
          break;
        }
      }
    }
    return actcount;
  }
  // End of internal functions for JoomGallery

})(jQuery);
//AUTOLOAD CODE BLOCK (MAY BE CHANGED OR REMOVED)
if (!/android|iphone|ipod|series60|symbian|windows ce|blackberry/i.test(navigator.userAgent)) {
  jQuery(function($) {
    $("a[rel^='lightbox']").slimbox({/* Put custom options here */}, null, function(el) {
      return (this == el) || ((this.rel.length > 8) && (this.rel == el.rel));
    });
  });
}