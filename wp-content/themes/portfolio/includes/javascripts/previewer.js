function portfolioThemePreviewPrimaryColor(newColor) {
	if ( newColor.indexOf('#') != 0 ) {
		newColor = '#' + newColor;
	}
	var colorCSS = portfolioFontColorSelectors + ' { color: ' + newColor + '; } ';
	colorCSS += portfolioBackgroundColorSelectors + ' { background-color: ' + newColor + '; } ';
	colorCSS += portfolioBorderBottomColorSelectors + ' { border-bottom: 2px solid ' + newColor + '; } ';

	jQuery('#portfolio-color-styles').text(colorCSS);
}

function portfolioThemePreviewStickyText(newText) {
	jQuery('span.sticky-text').text(newText);
}