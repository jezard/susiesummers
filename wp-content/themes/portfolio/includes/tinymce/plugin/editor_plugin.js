/**
 * WordPress plugin.
 */

(function() {

	// Language packs don't seem to work in older versions of WP!
	//tinymce.PluginManager.requireLangPack('ttfsyntax');

	tinymce.create('tinymce.plugins.PortfolioThemePlugin', {
		mceTout : 0,

		init : function(ed, url) {

			var buttons =
				{ "intro": { commandName: "type_intro", title: 'Introduction paragraph' },
				  "title": { commandName: "title_underline", title: 'Title underline' },
				  "pullquote": { commandName: "pullquote", title: 'Pullquote' },
				  "statement": { commandName: "statement", title: 'Bold statement' },
				  "alert": { commandName: "alert", title: 'Alert message box' },
				  "error": { commandName: "error", title: 'Error message box' },
				  "success": { commandName: "success", title: 'Success message box' },
				  "note": { commandName: "note", title: 'Note message box' } };

			// Register custom commands
			for (var key in buttons) {
				var commandName = buttons[key].commandName;
				// Tricky JS callback wrapping to create a new scope
				(function(currentCommand) {
					ed.addCommand(currentCommand, function() {
						ed.focus();
						ed.formatter.toggle(currentCommand);
					});
				})(commandName);
			}

			// Register custom buttons
			for (var key in buttons) {
				var commandName = buttons[key].commandName;
				ed.addButton(commandName, {
					title : buttons[key].title,
					cmd : commandName,
					label : key,
					text : key,
					'class' : 'portfolio-class'
				});
			}

			ed.onInit.add(function(ed) {
				// Hide the 3rd row of buttons if the advanced editor is off
				if ( getUserSetting('hidetb', '0') == '0' ) {
					jQuery( '#' + ed.id + '_toolbar3' ).hide();
				}

				// On click, toggle the 3rd row of buttons with the rest of the advanced editor
				jQuery( '#' + ed.id + '_wp_adv').click(function() {
					if ( jQuery( '#' + ed.id + '_toolbar2' ).is( ':visible' ) ) {
						jQuery( '#' + ed.id + '_toolbar3' ).show();
					} else {
						jQuery( '#' + ed.id + '_toolbar3' ).hide();
					}
				});

				// Register formatting options for easy toggling
				ed.formatter.register('type_intro', {inline : 'span', classes : 'type-intro'});
				ed.formatter.register('title_underline', {inline : 'span', classes : 'title-underline'});
				ed.formatter.register('pullquote', {block : 'div', wrapper : 1, remove : 'all', classes : 'pullquote'});
				ed.formatter.register('statement', {inline : 'span', classes : 'statement'});
				ed.formatter.register('alert', {block : 'div', wrapper : 1, remove : 'all', classes : 'alert'});
				ed.formatter.register('error', {block : 'div', wrapper : 1, remove : 'all', classes : 'alert error'});
				ed.formatter.register('success', {block : 'div', wrapper : 1, remove : 'all', classes : 'alert success'});
				ed.formatter.register('note', {block : 'div', wrapper : 1, remove : 'all', classes : 'alert note'});
			});

			// Here, all of our custom formatting buttons are toggled
			// between active/inactive based on the current selection
			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setActive('type_intro', ed.formatter.match( 'type_intro' ));
				cm.setActive('title_underline', ed.formatter.match( 'title_underline' ));
				cm.setActive('pullquote', jQuery(n).parents('div.pullquote').length);
				cm.setActive('statement', ed.formatter.match( 'statement' ));
				cm.setActive('alert', jQuery(n).parents('.alert').length);
				cm.setActive('error', jQuery(n).parents('.alert.error').length);
				cm.setActive('success', jQuery(n).parents('.alert.success').length);
				cm.setActive('note', jQuery(n).parents('.alert.note').length);
			});
		},

		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
				return {
						longname : 'The Theme Foundry Portfolio Theme plugin',
						author : 'The Theme Foundry',
						authorurl : 'http://thethemefoundry.com',
						version : "1.0"
				};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('portfolio', tinymce.plugins.PortfolioThemePlugin);
})();