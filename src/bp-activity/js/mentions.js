(function( $, undefined ) {
	/**
	 * Adds BuddyPress @mentions to form inputs.
	 *
	 * @param {array|object} options If array, becomes the suggestions' data source. If object, passed as config to $.atwho().
	 */
	$.fn.bp_mentions = function( options ) {
		if ( $.isArray( options ) ) {
			options = { data: options };
		}

		/**
		 * Default options for at.js; see https://github.com/ichord/At.js/.
		 */
		var suggestionsDefaults = {
			hide_without_suffix: true,
			limit:               10,
			start_with_space:    false,
			suffix:              '',

			callbacks: {
				/**
				 * Custom filter to only match the start of spaced words.
				 * Based on the core/default one.
				 *
				 * @param {string} query
				 * @param {array} data
				 * @param {string} search_key
				 * @return {array}
				 */
				filter: function( query, data, search_key ) {
					var item, _i, _len, _results = [],
					regxp = new RegExp( '^' + query + '| ' + query, 'ig' ); // start of string, or preceded by a space.

					for ( _i = 0, _len = data.length; _i < _len; _i++ ) {
						item = data[ _i ];
						if ( item[ search_key ].toLowerCase().match( regxp ) ) {
							_results.push( item );
						}
					}

					return _results;
				},

				/**
				 * Removes some spaces around highlighted string and tweaks regex to allow spaces
				 * (to match display_name). Based on the core default.
				 *
				 * @param {unknown} li
				 * @param {string} query
				 * @return {string}
				 */
				highlighter: function( li, query ) {
					if ( ! query ) {
						return li;
					}

					var regexp = new RegExp( '>(\\s*|[\\w\\s]*)(' + this.at.replace( '+', '\\+') + '?' + query.replace( '+', '\\+' ) + ')([\\w ]*)\\s*<', 'ig' );
					return li.replace( regexp, function( str, $1, $2, $3 ) {
						return '>' + $1 + '<strong>' + $2 + '</strong>' + $3 + '<';
					});
				},

				/**
				 * Reposition the suggestion list dynamically.
				 *
				 * @param {unknown} offset
				 */
				before_reposition: function( offset ) {
					var $view = $( '#atwho-ground-' + this.id + ' .atwho-view' ),
					caret     = this.$inputor.caret( 'offset' ).left,
					move;

					// If the caret is past horizontal half, then flip it, yo.
					if ( caret > ( $( 'body' ).width() / 2 ) ) {
						$view.addClass( 'flip' );
						move = caret - offset.left - this.view.$el.width();
					} else {
						$view.removeClass( 'flip' );
						move = caret - offset.left + 1;
					}

					offset.top  += 1;
					offset.left += move;
				}
			}
		},

		/**
		 * Default options for our @mentions; see https://github.com/ichord/At.js/.
		 */
		mentionsDefaults = {
			data: $.map( options.data,
				/**
				 * Create a composite index to search against of nicename + display name.
				 * This will also determine ordering of results, so nicename matches will appear on top.
				 *
				 * @param {array} suggestion An individual suggestion's original data.
				 * @return {array}
				 */
				function( suggestion ) {
					suggestion.search = suggestion.search || suggestion.ID + ' ' + suggestion.name;
					return suggestion;
				}
			),

			at:         '@',
			search_key: 'search',
			tpl:        '<li data-value="@${ID}"><img src="${image}" /><span class="username">@${ID}</span><small>${name}</small></li>'
		},

		opts = $.extend( true, {}, suggestionsDefaults, mentionsDefaults, options );
		return $.fn.atwho.call( this, opts );
	};

	$( document ).ready(function() {
		$( '.bp-suggestions, #comments form textarea, .wp-editor-area' ).bp_mentions( [
			{ 'ID': 'tessa', 'image': 'https://gravatar.com/avatar/3bc9ab796299d67ce83dceb9554f75df?d=retro', 'name': 'Tessa Name'   },
			{ 'ID': 'test',  'image': 'https://gravatar.com/avatar/3bc9ab796299d67ce83dceb9554f75df?d=retro', 'name': 'Test Surname' },
			{ 'ID': 'testtest',  'image': 'https://gravatar.com/avatar/3bc9ab796299d67ce83dceb9554f75df?d=retro', 'name': 'TestTest Surname' }
		] );
	});
})( jQuery );