<?php
if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'GoogleCustomWikiSearch' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['GoogleCustomWikiSearch'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['GoogleCustomWikiSearchAlias'] = __DIR__ . '/includes/specials/SpecialGoogleCustomWikiSearch.alias.php';
	wfWarn(
		'Deprecated PHP entry point used for the GoogleCustomWikiSearch extension. ' .
		'Please use wfLoadExtension() instead, ' .
		'see https://www.mediawiki.org/wiki/Special:MyLanguage/Manual:Extension_registration for more details.'
	);
	return;
} else {
	die( 'This version of the GoogleCustomWikiSearch extension requires MediaWiki 1.31+' );
}
