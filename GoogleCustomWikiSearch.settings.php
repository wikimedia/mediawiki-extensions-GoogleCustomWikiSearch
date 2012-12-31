<?php
###### Default settings for variables - change in LocalSettings #####
$gcwsReplaceSearch = false; #Replace standard search?

# Google Custom Search ID - requires account with Google.
# Note that if this is left blank (not set in LocalSettings), the result will be a site search.
$gcwsID = '';

# The custom search options may leave out customSearchControl.draw, though options will only display then if the variable is named "options"
# If this is set, $gcwsID is ignored
$gcwsCustomSearchOptions = '';

# Possible values are: 'BUBBLEGUM', 'ESPRESSO', 'GREENSKY', 'MINIMALIST', 'SHINY'
$gcwsTheme = 'V2_DEFAULT';
#####################################################################

#Replace the standard search functionality with this extension
$wgHooks['SpecialSearchSetupEngine'][] = 'onSpecialSearchSetupEngine';
function onSpecialSearchSetupEngine() {
	global $gcwsReplaceSearch, $wgDisableTextSearch, $wgSearchForwardUrl;

	if ( $gcwsReplaceSearch ) {
		$wgDisableTextSearch = true;
		$specialPageTitle = Title::newFromText("Special:GoogleCustomWikiSearch");
		$wgSearchForwardUrl = $specialPageTitle->getFullURL('term=$1');
	}

	return true;
}