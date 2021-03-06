/*
 * Tagging input element
 * @module Ink.UI.TagField_1
 * @version 1
 */
Ink.createModule(
	"Ink.UI.TagField",
	"1",
	["Ink.Dom.Element_1", "Ink.Dom.Event_1", "Ink.Dom.Css_1", "Ink.Dom.Browser_1", "Ink.UI.Droppable_1", "Ink.Util.Array_1", "Ink.Dom.Selector_1", "Ink.UI.Common_1"],
	function( InkElement, InkEvent, Css, Browser, Droppable, InkArray, Selector, Common) {
	'use strict';

	var enterKey     = 13;
	var backspaceKey = 8;
	var isTruthy     = function (val) {return ! ! val;};

	// Old IE (< 9) would split this into ['s'], but the correct behaviour is ['s', '']
	// We get around this.
	var buggySplit = 's,'.split( /,/g ).length === 1;

	var splitFunction = (function () {
			// Solves the above problem in old IE. Taken from:
			// http://blog.stevenlevithan.com/archives/cross-browser-split
			// (slightly adapted so as to not touch String.prototype)
			var nativeSplit   = String.prototype.split,
			compliantExecNpcg = /()??/.exec( "" )[1] === undefined, // NPCG: nonparticipating capturing group
			self;

			self = function (str, separator, limit) {
				/*jshint -W038 */
				/*jshint -W004 */
				/*jshint boss:true */
				/*jshint loopfunc:true */
				// If `separator` is not a regex, use `nativeSplit`
				if (Object.prototype.toString.call( separator ) !== "[object RegExp]") {
					return nativeSplit.call( str, separator, limit );
				}
				var output    = [],
				flags         = (separator.ignoreCase ? "i" : "") +
						(separator.multiline ? "m" : "") +
						(separator.extended ? "x" : "") + // Proposed for ES6
						(separator.sticky ? "y" : ""), // Firefox 3+
				lastLastIndex = 0,
				// Make `global` and avoid `lastIndex` issues by working with a copy
				separator = new RegExp( separator.source, flags + "g" ),
				separator2, match, lastIndex, lastLength;
				str      += ""; // Type-convert
				if ( ! compliantExecNpcg) {
					// Doesn't need flags gy, but they don't hurt
					separator2 = new RegExp( "^" + separator.source + "$(?!\\s)", flags );
				}
				/* Values for `limit`, per the spec:
				 * If undefined: 4294967295 // Math.pow(2, 32) - 1
				 * If 0, Infinity, or NaN: 0
				 * If positive number: limit = Math.floor(limit); if (limit > 4294967295) limit -= 4294967296;
				 * If negative number: 4294967296 - Math.floor(Math.abs(limit))
				 * If other: Type-convert, then use the above rules
				 */
				limit = limit === undefined ?
				-1 >>> 0 : // Math.pow(2, 32) - 1
				limit >>> 0; // ToUint32(limit)
				while (match = separator.exec( str )) {
					// `separator.lastIndex` is not reliable cross-browser
					lastIndex = match.index + match[0].length;
					if (lastIndex > lastLastIndex) {
						output.push( str.slice( lastLastIndex, match.index ) );
						// Fix browsers whose `exec` methods don't consistently return `undefined` for
						// nonparticipating capturing groups
						if ( ! compliantExecNpcg && match.length > 1) {
							match[0].replace(
							separator2,
							function () {
							for (var i = 1; i < arguments.length - 2; i++) {
									if (arguments[i] === undefined) {
										match[i] = undefined;
										}
							}
							}
								);
						}
						if (match.length > 1 && match.index < str.length) {
							Array.prototype.push.apply( output, match.slice( 1 ) );
						}
						lastLength    = match[0].length;
						lastLastIndex = lastIndex;
						if (output.length >= limit) {
							break;
						}
					}
					if (separator.lastIndex === match.index) {
						separator.lastIndex++; // Avoid an infinite loop
					}
				}
				if (lastLastIndex === str.length) {
					if (lastLength || ! separator.test( "" )) {
						output.push( "" );
					}
				} else {
					output.push( str.slice( lastLastIndex ) );
				}
				return output.length > limit ? output.slice( 0, limit ) : output;
				};

		// For convenience
		/* We don't override prototypes in Ink
		String.prototype.split = function (separator, limit) {
			return self(this, separator, limit);
		};
		*/

		return self;
	}());

	/**
	 * Use this class to have a field where a user can input several tags into a single text field. A good example is allowing the user to describe a blog post or a picture through tags, for later searching.
	 *
	 * The markup is as follows:
	 *
	 *           <input class="ink-tagfield" type="text" value="initial,value">
	 *
	 * By applying this UI class to the above input, you get a tag field with the tags "initial" and "value". The class preserves the original input element. It remains hidden and is updated with new tag information dynamically, so regular HTML form logic still applies.
	 *
	 * Below "input" refers to the current value of the input tag (updated as the user enters text, of course), and "output" refers to the value which this class writes back to said input tag.
	 *
	 * @class Ink.UI.TagField
	 * @version 1
	 * @constructor
	 * @param {String|Element}      element                         Selector or DOM Input Element.
	 * @param {Object}              [options]                       Options object
	 * @param {String|Array}        [options.tags]                  Initial tags in the input
	 * @param {Boolean}             [options.allowRepeated]         Flag to allow user to input several tags. Defaults to true.
	 * @param {RegExp}              [options.separator]             Split the input by this RegExp. Defaults to /[,;(space)]+/g (spaces, commas and semicolons)
	 * @param {String}              [options.outSeparator]          Use this string to separate each tag from the next in the output. Defaults to ','.
	 * @param {Boolean}             [options.autoSplit]             Flag to activate tag creation when the user types a separator. Defaults to true.
	 * @param {Integer}             [options.maxTags]               Maximum number of tags allowed. Set to -1 for no limit. Defaults to -1.
	 * @example
	 */
	function TagField() {
			Common.BaseUIComponent.apply( this, arguments );
	}

	TagField._name = 'TagField_1';

	TagField._optionDefinition = {
			tags: ['String', []],
			allowRepeated: ['Boolean', false],
			maxTags: ['Integer', -1],
			outSeparator: ['String', ','],
			separator: ['String', /[,; ]+/g],
			autoSplit: ['Boolean', true]
	};

	TagField.prototype = {
			/**
			 * Init function called by the constructor
			 *
			 * @method _init
			 * @private
			 */
			_init: function() {
				var o = this._options;
				if (typeof o.separator === 'string') {
					o.separator = new RegExp( o.separator, 'g' );
				}

				if (typeof o.tags === 'string') {
					// coerce to array using the separator
					o.tags = this._readInput( o.tags );
				}

				Css.addClassName( this._element, 'hide-all' );

				this._viewElm = InkElement.create(
				'div',
				{
				className: 'ink-tagfield',
				insertAfter: this._element
				}
				);

				this._input = InkElement.create(
				'input',
				{
				type: 'text',
				className: 'new-tag-input',
				insertBottom: this._viewElm
				}
				);

				var tags = [].concat( o.tags, this._tagsFromMarkup( this._element ) );

				this._tags = [];

				InkArray.each( tags, Ink.bindMethod( this, '_addTag' ) );

				InkEvent.observe( this._input, 'keyup', Ink.bindEvent( this._onKeyUp, this ) );
				InkEvent.observe( this._input, 'change', Ink.bindEvent( this._onKeyUp, this ) );
				InkEvent.observe( this._input, 'keydown', Ink.bindEvent( this._onKeyDown, this ) );
				InkEvent.observe( this._input, 'blur', Ink.bindEvent( this._onBlur, this ) );
				InkEvent.observe( this._viewElm, 'click', Ink.bindEvent( this._refocus, this ) );
				},

		destroy: function () {
			InkElement.remove( this._viewElm );
			Css.removeClassName( this._element, 'hide-all' );
			},

		_tagsFromMarkup: function (element) {
			var tagname = element.tagName.toLowerCase();
			if (tagname === 'input') {
				return this._readInput( element.value );
			} else if (tagname === 'select') {
				return InkArray.map(
					element.getElementsByTagName( 'option' ),
					function (option) {
					return InkElement.textContent( option );
				}
					);
			} else {
				throw new Error( 'Cannot read tags from a ' + tagname + ' tag. Unknown tag' );
			}
			},

		_tagsToMarkup: function (tags, element) {
			var tagname = element.tagName.toLowerCase();
			if (tagname === 'input') {
				if (this._options.separator) {
					element.value = tags.join( this._options.outSeparator );
				}
			} else if (tagname === 'select') {
				element.innerHTML = '';
				InkArray.each(
					tags,
					function (tag) {
					var opt = InkElement.create( 'option', {selected: 'selected'} );
					InkElement.setTextContent( opt, tag );
					element.appendChild( opt );
				}
					);
			} else {
				throw new Error( 'TagField: Cannot read tags from a ' + tagname + ' tag. Unknown tag' );
			}
			},

		_addTag: function (tag) {
			if (this._options.maxTags !== -1 &&
					this._tags.length >= this._options.maxTags) {
				return;
			}
			if (( ! this._options.allowRepeated &&
					InkArray.inArray( tag, this._tags, tag )) || ! tag) {
				return false;
			}
			var elm = InkElement.create(
				'span',
				{
				className: 'ink-tag',
				setTextContent: tag + ' '
			}
				);

			var remove = InkElement.create(
				'span',
				{
				className: 'remove fa fa-times',
				insertBottom: elm
			}
				);
			InkEvent.observe( remove, 'click', Ink.bindEvent( this._removeTag, this, null ) );

			var spc = document.createTextNode( ' ' );

			this._tags.push( tag );
			this._viewElm.insertBefore( elm, this._input );
			this._viewElm.insertBefore( spc, this._input );
			this._tagsToMarkup( this._tags, this._element );
			},

		_readInput: function (text) {
			if (this._options.separator) {
				return InkArray.filter( text.split( this._options.separator ), isTruthy );
			} else {
				return [text];
			}
			},

		_onKeyUp: function () {  // TODO control input box size
			if ( ! this._options.autoSplit) {
				return;
			}

			var split;
			if ( ! buggySplit) {
				split = this._input.value.split( this._options.separator );
			} else {
				split = splitFunction( this._input.value, this._options.separator );
			}

			if (split.length <= 1) {
				return;
			}
			var last = split[split.length - 1];
			split    = split.splice( 0, split.length - 1 );
			split    = InkArray.filter( split, isTruthy );

			InkArray.each( split, Ink.bind( this._addTag, this ) );
			this._input.value = last;
			},

		_onKeyDown: function (event) {
			if (event.which === enterKey) {
				return this._onEnterKeyDown( event );
			} else if (event.which === backspaceKey) {
				return this._onBackspaceKeyDown();
			} else if (this._removeConfirm) {
				// user pressed another key, cancel removal from a backspace key
				this._unsetRemovingVisual( this._tags.length - 1 );
			}
			},

		/**
		 * When the user presses backspace twice on the empty input, we delete the last tag on the field.
		 *
		 * @method onBackspaceKeyDown
		 * @return {void}
		 * @private
		 */
		_onBackspaceKeyDown: function () {
			if (this._input.value) {
return; }

			if (this._removeConfirm) {
				this._unsetRemovingVisual( this._tags.length - 1 );
				this._removeTag( this._tags.length - 1 );
				this._removeConfirm = null;
			} else {
				this._setRemovingVisual( this._tags.length - 1 );
			}
			},

		_onEnterKeyDown: function (event) {
			var tag = this._input.value;
			if (tag) {
				this._addTag( tag );
				this._input.value = '';
			}
			InkEvent.stopDefault( event );
			},

		_onBlur: function () {
			this._addTag( this._input.value );
			this._input.value = '';
			},

		/* For when the user presses backspace.
		 * Set the style of the tag so that it seems like it's going to be removed
		 * if they press backspace again. */
		_setRemovingVisual: function (tagIndex) {
			var elm = this._viewElm.children[tagIndex];
			if ( ! elm) {
return; }

			Css.addClassName( elm, 'tag-deleting' );

			this._removeRemovingVisualTimeout = setTimeout( Ink.bindMethod( this, '_unsetRemovingVisual', tagIndex ), 4000 );
			InkEvent.observe( this._input, 'blur', Ink.bindMethod( this, '_unsetRemovingVisual', tagIndex ) );
			this._removeConfirm = true;
			},
		_unsetRemovingVisual: function (tagIndex) {
			var elm = this._viewElm.children[tagIndex];
			if (elm) {
				Css.removeClassName( elm, 'tag-deleting' );
				clearTimeout( this._removeRemovingVisualTimeout );
			}
			this._removeConfirm = null;
			},

		_removeTag: function (event) {
			var index;
			if (typeof event === 'object') {  // click event on close button
				var elm = InkEvent.element( event ).parentNode;
				index   = InkElement.parentIndexOf( this._viewElm, elm );
			} else if (typeof event === 'number') {  // manual removal
				index = event;
			}
			this._tags = InkArray.remove( this._tags, index, 1 );
			InkElement.remove( this._viewElm.children[index] );
			this._tagsToMarkup( this._tags, this._element );
			},

		_refocus: function (event) {
			this._input.focus();
			InkEvent.stop( event );
			return false;
			}
	};

	Common.createUIComponent( TagField );

	return TagField;
}
	);
