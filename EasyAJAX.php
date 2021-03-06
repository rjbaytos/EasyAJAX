<?Php
// EASY AJAX LIBRARY BY RHYAN JILL
// GitHub: https://github.com/rjbaytos/EasyAJAX
namespace { class EasyAJAX {} }
namespace EasyAJAX {
	class PageNavigator {
		public	$ContentDirectory = '',			// DIRECTORY OF PAGES
				$homepage = 'index.html',		// DEFAULT HOMEPAGE
				$type = 'GET',					// 'REQUEST', 'GET', 'POST'
				$LibraryRequestName = 'script',	// i.e. ?script=ajax
				$PageRequestName = 'page',		// i.e. ?page=index.html
				$AJAXRequestName = 'ajax',		// i.e. ?page=index.html&ajax
				$html = '';						// RESULTING HTML STRING
		private	$AcceptedRequestTypes = ['REQUEST','GET','POST'];
		
		final public function __construct (
				bool $run = false,
				string $ContentDirectory = '',
				string $homepage = '',
				string $type = 'GET'
		){
			$this->ContentDirectory = $ContentDirectory != '' ? $ContentDirectory : $this->ContentDirectory;
			$this->homepage = $homepage != '' ? $homepage : $this->homepage;
			$this->type = in_array( strtoupper($type), $this->AcceptedRequestTypes ) ? strtoupper($type) : 'GET';
			if ( $run ) {
				$this->html = $this->request ( $this->ContentDirectory, $this->homepage, $this->type );
			}
		}
		
		final public function __invoke (
				string $ContentDirectory = '',
				string $homepage = '',
				string $type = 'GET'
		):string {
			$this->ContentDirectory = $ContentDirectory != '' ? $ContentDirectory : $this->ContentDirectory;
			$this->homepage = $homepage != '' ? $homepage : $this->homepage;
			$this->type = in_array( strtoupper($type), $this->AcceptedRequestTypes ) ? strtoupper($type) : 'GET';
			return $this->request ( $this->ContentDirectory, $this->homepage, $this->type );
		}
		
		final public function request (
				string $ContentDirectory = '',
				string $homepage = '',
				string $type = 'GET'
		):string {
			$this->ContentDirectory = $ContentDirectory != '' ? $ContentDirectory : $this->ContentDirectory;
			$this->homepage = $homepage != '' ? $homepage : $this->homepage;
			$this->type = in_array( strtoupper($type), $this->AcceptedRequestTypes ) ? strtoupper($type) : 'GET';
			if ( $this->type != 'REQUEST' && $this->type != $_SERVER['REQUEST_METHOD'] ){
				http_response_code(501);
				die ( "<script>alert('501 error. Unknown request.');</script>" );
			}
			$HTMLcontent = '';
			if ( isset($_REQUEST[$this->LibraryRequestName]) ) {
				$js = new js();
				die($js($_REQUEST[$this->LibraryRequestName]));
			} elseif ( isset($_REQUEST[$this->PageRequestName]) && isset($_REQUEST[$this->AJAXRequestName]) ) {
				if ( $_REQUEST[$this->PageRequestName]=='' && file_exists ($this->ContentDirectory . $this->homepage) ) {
					die (file_get_contents($this->ContentDirectory . $this->homepage));
				} elseif (	$_REQUEST[$this->PageRequestName]!='' &&
							file_exists ($file = $this->ContentDirectory . $_REQUEST[$this->PageRequestName]) ) {
					die (file_get_contents($file));
				} else {
					if (file_exists($this->ContentDirectory . $this->homepage)) {
						die ( file_get_contents($this->ContentDirectory . $this->homepage));
					} else {
						die ( '<center class="alert">page not found</center>' );
					}
				}
			} elseif (isset($_REQUEST[$this->PageRequestName])) {
				if ( $_REQUEST[$this->PageRequestName]=='' && file_exists ($this->ContentDirectory . $this->homepage) ) {
					$HTMLcontent = file_get_contents($this->ContentDirectory . $this->homepage);
				} elseif (	$_REQUEST[$this->PageRequestName]!='' &&
							file_exists ($file = $this->ContentDirectory . $_REQUEST[$this->PageRequestName]) ) {
					$HTMLcontent = file_get_contents($file);
				} else {
					http_response_code(404);
					if (file_exists($this->ContentDirectory . $this->homepage)) {
						$HTMLcontent =	"<script>alert('page not found');</script>\n" .
										file_get_contents($this->ContentDirectory . $this->homepage);
					} else {
						$HTMLcontent = '<center class="alert">page not found</center>';
					}
				}
			} elseif ( isset($_REQUEST[$this->AJAXRequestName]) ) {
				if (file_exists($this->ContentDirectory . $this->homepage)) {
					die ( file_get_contents ( $this->ContentDirectory . $this->homepage ) );
				} else {
					die ( '<center class="alert">page not found</center>' );
				}
			} else {
				if (file_exists($this->ContentDirectory . $this->homepage)) {
					$HTMLcontent = file_get_contents($this->ContentDirectory . $this->homepage);
				}
			}
			return $HTMLcontent;
		}
	}
	
	class js {
		final public function __invoke ( string $name = '', array $parameters = [] ): string {
			if (	is_string ($name) && $name != '' &&		// CHECK IF STRING IS VALID
					method_exists ( __CLASS__, $name ) &&	// CHECK IF THE METHOD NAME EXISTS AS A METHOD OF THIS CLASS
					is_callable ( __CLASS__, $name )		// CHECK IF THE METHOD NAME CAN BE CALLED
			) {
				return $this->$name(...$parameters);
			}
		}
		
		final public function ajax (): string {
			header ( "Content-Type: text/javascript" );
			return <<<ajaxscript
// EASY AJAX LIBRARY BY RHYAN JILL

var	autoBindAJAXLinks = false,
	AJAXLinkTarget = 'body',
	AJAXLinkSource = 'a',
	AJAXLinkInsert = '',
	AJAXLinkAttrib = 'href',
	autoBindAJAXForms = false,
	AJAXFormTarget = 'body',
	AJAXFormSource = 'form',
	AJAXFormAppend = '',
	AJAXFormPushData = null,
	AJAXFormPushAppendedData = false;

// TURN A FORM INTO AN AJAX FORM AUTOMATICALLY
function autoAJAXForm( target, form, appendedData, pushData, pushAppendedData ) {
	autoBindAJAXForms = true;
	AJAXFormTarget = (typeof target !== 'undefined') ? target : 'body';
	AJAXFormSource = (typeof form !== 'undefined') ? form : 'form';
	AJAXFormAppend = (typeof appendedData !== 'undefined') ? appendedData : '';
	AJAXFormPushData = (pushData !== true && pushData !== false) ? null : pushData;
	AJAXFormPushAppendedData = (pushAppendedData !== true) ? false : pushAppendedData;
	AJAXForm ( AJAXFormTarget, AJAXFormSource, AJAXFormAppend, AJAXFormPushData, AJAXFormPushAppendedData );
}

// TURN A SOURCE ELEMENT INTO AN AJAX LINK AUTOMATICALLY
function autoAJAXLink ( target, source, insert, attrib ){
	autoBindAJAXLinks = true;
	AJAXLinkTarget = ( typeof target !== 'undefined' ) ? target : 'body';				// AJAX OUTPUT
	AJAXLinkSource = ( typeof source !== 'undefined' ) ? source : 'a';					// ELEMENT PAGE ADDRESS
	AJAXLinkInsert = ( typeof insert !== 'undefined' ) ? insert : '';					// ADDITIONAL DATA TO ADD TO REQUEST
	AJAXLinkAttrib = ( typeof attrib !== 'undefined' ) ? attrib.toLowerCase() : 'href';	// ATTRIBUTE NAME OF PAGE ADDRESS
	AJAXLink ( AJAXLinkTarget, AJAXLinkSource, AJAXLinkInsert, AJAXLinkAttrib );
}

// TURN A SOURCE ELEMENT INTO AN AJAX LINK
function AJAXLink ( target, source, insert, attrib ){
	target = ( typeof target !== 'undefined' ) ? target : 'body';				// AJAX OUTPUT
	source = ( typeof source !== 'undefined' ) ? source : 'a';					// ELEMENT PAGE ADDRESS
	insert = ( typeof insert !== 'undefined' ) ? insert : '';					// ADDITIONAL DATA TO ADD TO THE REQUEST STRING
	attrib = ( typeof attrib !== 'undefined' ) ? attrib.toLowerCase() : 'href';	// ATTRIBUTE NAME OF PAGE ADDRESS
	defaultTarget = target;
	$(function(){
		$(source).click(function(){
			var url = $(this).attr(attrib);
			if (	$(this).attr('target') !== undefined &&
					$(this).attr('target').toLowerCase() == '_top' ){
				window.open(url, '_self');
			} else if (	$(this).attr('target') !== undefined &&
					$(this).attr('target').toLowerCase() == '_blank' ){
				window.open(url, '_blank');
			} else {
				if ( $(this).attr('data-nopush') === undefined ) {
					history.pushState({}, '', url);
				}
				if (	$(this).attr('target') !== undefined &&
						$(this).attr('target').toLowerCase() !== '_blank' ){
					target = $(this).attr('target');
				} else if ( $(this).attr('target') === undefined ){
					target = defaultTarget;
				}
				
				getpage(url, target, insert, function(data){
					target = defaultTarget;
				});
			}
			return false;
		});
	});
}

// LOAD A WEBPAGE INTO A TARGET ELEMENT
function getpage ( url, target, insert, anonfn ){
	if ( typeof url === 'undefined' ) { return false; }
	if ( url === '' || url === './' && insert !== '' ) { url = '?' };
	target = ( typeof target !== 'undefined' ) ? target : 'body';
	insert = ( typeof insert !== 'undefined' ) ? insert : '';
	anonfn = ( typeof anonfn !== 'undefined' ) ? anonfn : function(){};
	
	loader(target, function(){
		
		$.get
		(	url + insert,
			function(data)
			{
				$(target).children().fadeOut(500).promise().done(function(){
					$(target).empty().html(data).promise().done(function(){
						if ( $(target + ' img').length ) {
							var w =	window.innerWidth
									|| document.documentElement.clientWidth
									|| document.body.clientWidth;
							var h =	window.innerHeight
									|| document.documentElement.clientHeight
									|| document.body.clientHeight;
							var dimensions = 50;
							var thickness = 10;
							var divisor = 7;
							if ( w > h ) {
								dimensions = h / divisor;
							} else {
								dimensions = w / divisor;
							}
							thickness = dimensions / 5;
							$(this).append("<div class='cssloadercontainer'><div class='cssloader'></div></div>");
							$('div.cssloadercontainer').hide();
							$('div.cssloader').width ( dimensions + 'px' );
							$('div.cssloader').height ( dimensions + 'px' );
							$('div.cssloader').css('border-width', thickness + 'px');
							$('div.cssloadercontainer').fadeIn(500);
							$(target + ' img').hide().on("load", function(){
								$('div.cssloadercontainer').fadeOut(500).remove();
								$(target + ' img').fadeIn(500);
							});
						}
						anonfn(data);
						if ( autoBindAJAXLinks ){
							AJAXLink ( AJAXLinkTarget, AJAXLinkSource, AJAXLinkInsert, AJAXLinkAttrib );
						}
						if ( autoBindAJAXForms ){
							AJAXForm (	AJAXFormTarget,
										AJAXFormSource,
										AJAXFormAppend,
										AJAXFormPushData,
										AJAXFormPushAppendedData
									);
						}
					});
				});
			}
		);
	});
}

// DISPLAY LOADING INDICATOR
function loader ( targetElement, doAfter ){
	if ( typeof targetElement === 'undefined' ) return;
	$(targetElement).stop().promise().done(function(){
		var w =	window.innerWidth
				|| document.documentElement.clientWidth
				|| document.body.clientWidth;
		var h =	window.innerHeight
				|| document.documentElement.clientHeight
				|| document.body.clientHeight;
		var dimensions = 50;
		var thickness = 10;
		var divisor = 7;
		if ( w > h ) {
			dimensions = h / divisor;
		} else {
			dimensions = w / divisor;
		}
		thickness = dimensions / 5;
		$(this).stop().fadeOut(100, function(){
			if ( $(this).attr('data-display') !== undefined ) {
				if ( $(this).attr('data-display') == '' ) {
					$(this).css({'display':'grid'});
				} else {
					$(this).css({'display':$(this).attr('data-display')});
				}
			}
			$(this).empty().append("<div class='cssloadercontainer'><div class='cssloader'></div></div>");
			$('div.cssloader').width ( dimensions + 'px' );
			$('div.cssloader').height ( dimensions + 'px' );
			$('div.cssloader').css('border-width', thickness + 'px');
			$(this).fadeIn(100).promise().done(function(){
				if ( typeof doAfter === 'function' ) {
					doAfter();
				}
			});
		});
	});
}

// PROGRAMMATICALLY LOAD URL
function loadURL ( destURL, targetObj, urlAdditions, trimAfter ){
	if ( typeof urlAdditions === 'undefined' ) urlAdditions = '';
	if ( typeof targetObj === 'undefined' ) targetObj = 'body';
	if ( destURL === '' || destURL === './' && urlAdditions !== '' ) { destURL = '?'; }
	loader(targetObj, function(){
		if ( window.location.search == '' || destURL == window.location.search + urlAdditions ){
			loadList ( destURL, targetObj );
		} else {
			loadList ( window.location.search + urlAdditions, targetObj );
		}
		var trimmedURL = destURL;
		if ( typeof trimAfter !== 'undefined' ) {
			trimmedURL = destURL.replace ( trimAfter, '' );
		}
		history.pushState({}, '', trimmedURL);
	});
}

// LOAD A WEBPAGE INTO A TARGET ELEMENT - CONVERT IF RESULT IS JSON
function loadList ( url, target, anonfn ){
	if ( typeof url === 'undefined' ) return false;
	target = ( typeof target !== 'undefined' ) ? target : 'body';
	anonfn = ( typeof anonfn !== 'undefined' ) ? anonfn : function(data){
		return implode_recursive ( data, ['<br>', ',', '-'] );
	};
	
	$.get(url, function(result){
		if ( typeof result == 'string' ) {
			try {
				var data = jQuery.parseJSON(result);
				$(target).empty().html(anonfn(data));
			} catch(e) {
				$(target).empty().html(result);
			}
		} else {
			alert('Result is not a string.');
		}
	});
}

// TURN PARSED JSON INTO HTML
function implode_recursive ( data, separator, counter ){
	var HTMLContent = '';
	var separatorIsArray = Array.isArray ( separator );
	if ( typeof counter != 'number' ) counter = 0;
	if ( typeof separator === 'undefined' ) separator = '';
	if ( separator[counter] === undefined && separatorIsArray ) separator = '';
	var curr_separator = !separatorIsArray ? separator : separator[counter];
	for ( content in data ) {
		if ( HTMLContent != '' ) HTMLContent += curr_separator;
		if ( typeof data[content] == 'string' || typeof data[content] == 'number' ) {
			HTMLContent += data[content];
		} else if ( typeof data[content] == 'boolean' ) {
			if ( data[content] )
				HTMLContent += 'true';
			else
				HTMLContent += 'false';
		} else if ( data[content] === null ) {
			HTMLContent += 'null';
		} else if ( Array.isArray ( data[content] ) ) {
			var nextKey = separator[ k = counter + 1 ] !== undefined ? k : counter;
			HTMLContent += implode_recursive ( data[content], separator, nextKey );
		} else
			HTMLContent += 'unknown datatype';
	}
	return HTMLContent;
}

// MAKE FORMS ASYNCHRONOUS
function AJAXForm ( target, form, appendedData, pushData, pushAppendedData ){
	if (typeof target === 'undefined') target = 'body';
	if (typeof form === 'undefined') form = 'form';
	if (typeof appendedData === 'undefined') appendedData = '';
	if (pushData !== true && pushData !== false) pushData = null;
	if (pushAppendedData !== true) pushAppendedData = false;
	
	$(function(){
		$(form).submit(function (event){
			//PREVENT DEFAULT FORM SUBMISSION
			event.preventDefault();
			
			// GET FORM DATA BEFORE ERASING THE FORM
			var formMethod = $(form).attr('method').toLowerCase(),
				formAction = $(form).attr('action'),
				formData = $(form).serialize();
			
			//DISPLAY LOADER & SEND DATA
			loader(target,function(){
				if ( formAction == 'undefined' ) { formAction = window.location.href.split("?")[0]; }
				$.ajax(
				{
					type:		formMethod,
					url:		formAction,
					data:		formData + appendedData,
					success:	function(data){
									$(target).stop().empty().html(data).promise().done(function(){
										if ( pushData === true || ( pushData === null && formMethod == 'get' ) ) {
											var urlPush = pushAppendedData ? formData + appendedData : formData;
											history.pushState({}, '', '?' + urlPush);
										}
									});
								},
					error:		function(error){
									alert( 'Error! ' + error.responseText );
								}
				});
			});
		});
	});
}

class slideshow {
	constructor(slides, dots, timer, slideIndex){
		// bind 'this' for recursion to work
		this.playSlides = this.playSlides.bind(this);
		if ( typeof slides === 'string' ){
			if ( $(slides + ' > div.mySlides').length ){
				this.slides = $(slides + ' > div.mySlides');
			} else if ( $(slides).length ){
				this.slides = $(slides);
			}
		}
		
		if ( typeof dots === 'string' && $(dots).length ){
			this.dots = $(dots);
		} else if (	typeof slides === 'string' &&
					$(slides.split('>')[0].trim() + ' > div.slidecontroller > span.dot').length ){
			this.dots = $(slides.split('>')[0].trim() + ' > div.slidecontroller > span.dot');
		}
		
		this.timer = (timer !== undefined) ? timer : 5000;
		this.slideIndex = (slideIndex !== undefined) ? slideIndex : 1;
		var myself = this;
		$(slides.split('>')[0].trim() + ' > a.prev').click(function(){
			myself.plusSlides(-1);
		});
		$(slides.split('>')[0].trim() + ' > a.next').click(function(){
			myself.plusSlides(1);
		});
		$(slides.split('>')[0].trim() + ' > div.slidecontroller > span.dot').click(function(){
			myself.currentSlide($(this).attr('value'));
		});
		myself.displaySlide();
	}
	
	// Next/previous controls
	plusSlides(n) {
		if ( this.slides === undefined ) return 'fail';
		this.displaySlide(this.slideIndex += n);
	}
	
	// Thumbnail image controls
	currentSlide(n) {
		if ( this.slides === undefined ) return 'fail';
		this.displaySlide(this.slideIndex = n);
	}
	
	displaySlide(n) {
		if ( this.slides === undefined ) return 'fail';
		if (typeof n === undefined) n = this.slideIndex;
		if (typeof this.activeSlide !== undefined) clearTimeout(this.activeSlide);
		var i;
		
		var slides = this.slides;
		if (n > slides.length) {this.slideIndex = 1}
		if (n < 1) {this.slideIndex = slides.length}
		for (i = 0; i < slides.length; i++) {
			slides[i].style.display = 'none';
		}
		slides[this.slideIndex-1].style.display = 'block';
		
		if ( this.dots !== undefined ){
			var dots = this.dots;
			for (i = 0; i < dots.length; i++) {
				dots[i].className = dots[i].className.replace(' active', '');
			}
			dots[this.slideIndex-1].className += ' active';
		}
		
		this.activeSlide = setTimeout(this.playSlides, this.timer);
	}
	
	playSlides() {
		if ( this.slides === undefined ) return 'fail';
		var i;
		var slides = this.slides;
		for (i = 0; i < slides.length; i++) {
			slides[i].style.display = 'none';
		}
		if ( this.dots !== undefined ){
			var dots = this.dots;
			for (i = 0; i < dots.length; i++) {
				dots[i].className = dots[i].className.replace(' active', '');
			}
		}
		this.slideIndex++;
		if (this.slideIndex > slides.length) {this.slideIndex = 1}
		slides[this.slideIndex-1].style.display = 'block';
		if ( this.dots !== undefined ){
			dots[this.slideIndex-1].className += ' active';
		}
		this.activeSlide = setTimeout(this.playSlides, this.timer);
	}
}
ajaxscript;
		}
	}
}
?>