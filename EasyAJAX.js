// EASY AJAX LIBRARY BY RHYAN JILL

// TURN A SOURCE ELEMENT INTO AN AJAX LINK
function AJAXLink ( target, source, insert, attrib )
{
	target = ( typeof target !== 'undefined' ) ? target : 'body';				// AJAX OUTPUT
	source = ( typeof source !== 'undefined' ) ? source : 'a';					// ELEMENT PAGE ADDRESS
	insert = ( typeof insert !== 'undefined' ) ? insert : '';					// ADDITIONAL DATA TO ADD TO THE REQUEST STRING
	attrib = ( typeof attrib !== 'undefined' ) ? attrib.toLowerCase() : 'href';	// ATTRIBUTE NAME OF PAGE ADDRESS
	defaultTarget = target;
	$(function()
	{
		$(source).click(function()
		{
			var url = $(this).attr(attrib);
			if ( $(this).attr('data-nopush') === undefined ) {
				history.pushState({}, '', url);
			}
			if	(	$(this).attr('target') !== undefined &&
					$(this).attr('target').toLowerCase() !== '_blank'
				) {
				target = $(this).attr('target');
			}
			else if ( $(this).attr('target') === undefined ) {
				target = defaultTarget;
			}
			
			getpage(url, target, insert, function(data){
				target = defaultTarget;
			});
			
			return false;
		});
	});
}

// LOAD A WEBPAGE INTO A TARGET ELEMENT
function getpage ( url, target, insert, anonfn )
{
	if ( typeof url === 'undefined' ) { return false; }
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
					});
				});
			}
		);
	});
}

// DISPLAY LOADING INDICATOR
function loader ( targetElement, doAfter )
{
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
		$(this).stop().fadeOut(100, function()
		{
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
function loadURL ( destURL, targetObj, urlAdditions, trimAfter )
{
	if ( typeof urlAdditions === 'undefined' ) urlAdditions = '';
	if ( typeof targetObj === 'undefined' ) targetObj = 'body';
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
function loadList ( url, target, anonfn )
{
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
function implode_recursive ( data, separator, counter )
{
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
function AJAXForm ( target, form, appendedData, pushData, pushAppendedData )
{
	if (typeof target === 'undefined') target = 'body';
	if (typeof form === 'undefined') form = 'form';
	if (typeof appendedData === 'undefined') appendedData = '';
	if (pushData !== true && pushData !== false) pushData = null;
	if (pushAppendedData !== true) pushAppendedData = false;
	
	$(function()
	{
		$(form).submit(function (event)
		{
			//PREVENT DEFAULT FORM SUBMISSION
			event.preventDefault();
			
			//DISPLAY LOADER & SEND DATA
			loader(target,function(){
				var formMethod = $(form).attr('method').toLowerCase(),
					formAction = $(form).attr('action'),
					formData = $(form).serialize();
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