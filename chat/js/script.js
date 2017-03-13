/*////////////////////////////////////////////////
	Script made by: ikefi
		http://steamcommunity.com/id/ikefi/
////////////////////////////////////////////////*/ 

var sendMessage

$(document).ready(function(){
	
	// Current message id, used to keep track of what messages we need
	var currentMessageID = 0
	
	// Wait for the twitch emotes to load, so cache the messages we get during that time
	var cachedMessages = [];
	
	// Twitch emotes
	var globalEmotesLoaded = false;
	var subscriberEmotesLoaded = false;
	var betterttvEmotesLoaded = false;
	var globalEmoteURL = '';
	var subscriberEmoteURL = '';
	var globalEmotes = [];
	var subscriberEmotes = [];
	var betterttvEmotes = [];
	
	// Get twitch global emotes
	$.ajax({
		url: 'https://twitchemotes.com/api_cache/v2/global.json',
		dataType: 'json',
		cache: true,
		success: function( data ){
			globalEmoteURL = data.template.small.replace( "//", "https://" );
			for( var emote in data.emotes ){
				globalEmotes[ emote ] = data.emotes[ emote ].image_id;
			}
			globalEmotesLoaded = true;
			emotesLoaded();
		},
		error: function(){
			globalEmotesLoaded = true;
			emotesLoaded();
		}
	});
	
	// Get twitch subscriber emotes
	$.ajax({
		url: 'https://twitchemotes.com/api_cache/v2/subscriber.json',
		dataType: 'json',
		cache: true,
		success: function( data ){
			subscriberEmoteURL = data.template.small.replace( "//", "https://" );
			for( var index in data.channels ){
				var channel = data.channels[ index ];
				for( var emoteIndex in channel.emotes ){
					var emote = channel.emotes[ emoteIndex ];
					subscriberEmotes[ emote.code ] = emote.image_id;
				}
			}
			subscriberEmotesLoaded = true;
			emotesLoaded();
		},
		error: function(){
			subscriberEmotesLoaded = true;
			emotesLoaded();
		}
	});
	
	// Get betterttv emotes
	$.ajax({
		url: 'https://api.betterttv.net/emotes',
		dataType: 'json',
		cache: true,
		success: function( data ){
			for( var index in data.emotes ){
				var emote = data.emotes[ index ];
				betterttvEmotes[ emote.regex ] = emote.url;
			}
			betterttvEmotesLoaded = true;
			emotesLoaded();
		},
		error: function(){
			betterttvEmotesLoaded = true;
			emotesLoaded();
		}
	});
	
	function emotesLoaded(){
		
		if( globalEmotesLoaded && subscriberEmotesLoaded && betterttvEmotesLoaded ){
			
			// Remove the loading
			if( currentMessageID == 0 ){
				$( '.chat-lines' ).html( "" );
			}
			
			// Add all data we gotten during loading the emoticons
			for( var i=0; i<cachedMessages.length; i++ ){
				onMessageReceived( cachedMessages[ i ] )
			}
			
		}
	}
	
	// Apply emoticions
	function applyEmoticons( message ){
		
		// Do nothing if they all failed
		if( !globalEmotesLoaded && !subscriberEmotesLoaded && !betterttvEmotesLoaded ){ return message; }
		
		// Split each space
		var array = message.split( " " );
		
		// Loop through the array, replace it with an image if it's a twitch emote
		var newMsg = "";
		for( var i=0; i<array.length; i++ ){
			var msgPiece = array[ i ];
			if( globalEmotes[ msgPiece ] ){
				msgPiece = "<img class=\"emote\" data-name=\"" + msgPiece + "\" src=\"" + globalEmoteURL.replace( "{image_id}", globalEmotes[ msgPiece ] ) + "\">";
			}
			else if( subscriberEmotes[ msgPiece ] ){
				msgPiece = "<img class=\"emote\" data-name=\"" + msgPiece + "\" src=\"" + subscriberEmoteURL.replace( "{image_id}", subscriberEmotes[ msgPiece ] ) + "\">";
			}
			else if( betterttvEmotes[ msgPiece ] ){
				msgPiece = "<img class=\"emote\" data-name=\"" + msgPiece + "\" src=\"" + betterttvEmotes[ msgPiece ] + "\">";
			}
			else if( i != 0 ){
				msgPiece = " " + msgPiece;
			}
			newMsg += msgPiece;
		}
		
		// Return the new message
		return newMsg;
	}
	
	function onMessageReceived( data ){
		
		var chat_lines = $( '.chat-lines' );
		
		// Remove the loading
		if( currentMessageID == 0 ){
			chat_lines.html( "" );
		}
		
		// No data or no new data, do nothing
		if( !data || data.messageid <= currentMessageID ){ return; }
		
		// Only scroll if the client was scrolled all the way down
		var shouldScroll = chat_lines.prop('scrollHeight') - ( chat_lines.scrollTop() + chat_lines.outerHeight() ) == 0;
		
		var previousMessageID = currentMessageID; // To check if he gotten messages we already got
		currentMessageID = data.messageid; // Set the currentMessageID
		
		for( var i=0; i<data.chat_log.length; i++ ){
			
			var d = data.chat_log[ i ];
			
			// To prevent double messages
			if( d.id <= previousMessageID ){ continue; }
			
			var time = new Date( d.time*1000 ).toLocaleTimeString();
			var message = d.msg;
			var name = d.name;
			var color = d.color;
			var admin = d.admin;
			
			var line = "";
			if( admin && message.charAt( 0 ) == '!' ){
				message = message.substr( 1 );
				line = '<div class="chat-line noticable">';
			}
			else{
				line = '<div class="chat-line">';
			}
			
			// Apply twitch emoticons
			message = applyEmoticons( message );
			
			line += '<span class="time">' + time
				+ ' </span><span class="name" style="color:#' + color + ';">'
				+ name + '</span><span>:&nbsp;</span><span class="text">'
				+ message + '</span></div>';
			
			// Add the line
			chat_lines.append( line );
			
		}
		
		if( shouldScroll ){
			
			// Max amount of messages in the chat
			var child_length = chat_lines.children().length;
			if( child_length > 250 ){
				$( '.chat-line:lt(' + ( child_length-250 ) + ')' ).remove();
			}
			
			// Scroll to bottom
			chat_lines.animate( { scrollTop: chat_lines.prop( 'scrollHeight' ) }, 'fast' );
			
		}
		
	}
	
	// Get new messages
	function getMessages(){
		
		$.ajax({
			url: 'get_messages.php',
			type: 'POST',
			data: {cur_msg_id: currentMessageID},
			dataType: 'json',
			success: function( data ){
				if( !globalEmotesLoaded || !subscriberEmotesLoaded || !betterttvEmotesLoaded ){
					cachedMessages.push( data );
				}
				else{
					onMessageReceived( data );
				}
		  	}
		});
		
	}
	
	// Send a message
	sendMessage = function( message ){
		
		// We don't want our messages to be empty
		if( message == "" ){ return; }
		
		// Post the message
		$.post( 'send_message.php', { message: message }, function(){
			getMessages(); // Get the message right away
		});
		
	}
	
	setInterval( getMessages, 1500 ); // Get new chat messages every 1500 ms
	getMessages(); // Get messages now
	
	// More messages below notification
	var ps = 0
	$( '.chat-lines' ).on( 'scroll', function(){
		
		var st = $(this).scrollTop();
		var tps = ps;
		ps = st;
		
		var chat_lines = $( '.chat-lines' );
		
		// If scrolling down then
		if( st > tps ){
			if( chat_lines.prop('scrollHeight') - ( chat_lines.scrollTop() + chat_lines.outerHeight() ) <= 1 ){
				$( "#more-messages-notify" ).remove();
			}
		}
		else if( !$( "#more-messages-notify" ).length ){
			if( chat_lines.prop('scrollHeight') - ( chat_lines.scrollTop() + chat_lines.outerHeight() ) > 0 ){
				var tooltip = $("<div id=\"more-messages-notify\">More messages below</div>")
				.appendTo( "body" )
				.show()
				.on( 'click', function(){
					$( '.chat-lines' ).animate( { scrollTop: $( '.chat-lines' ).prop( 'scrollHeight' ) }, 'fast' );
				});
			}
		}
		
	});
	
	// Tooltip for the emoticons
	// credits: http://stackoverflow.com/questions/2011142/how-to-change-the-style-of-title-attribute-inside-the-anchor-tag#answer-16462668
	$(document).on( "mouseenter", "*[data-name]", function( e ){
		
		var target = $(e.target);
		var tooltip = $("<div id=\"tooltip\" />")
			.appendTo( "body" )
			.html( target.data( "name" ) )
			.show();
		
		var pos = target.offset();
		var x = pos.top-tooltip.outerHeight()-4, y = pos.left-tooltip.outerWidth()+target.outerWidth()*0.5+8;
		tooltip.css( { top: x, left: y } );
		
	});
	$(document).on( "mouseleave", "*[data-name]", function( e ){
		$( "#tooltip" ).remove();
	});
	
});