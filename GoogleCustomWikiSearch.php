<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
        exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
        'path' => __FILE__,
        'name' => 'GoogleCustomWikiSearch',
        'author' => 'Ike Hecht for [http://wikiworks.com/ WikiWorks]',
        'url' => 'https://www.mediawiki.org/wiki/Extension:GoogleCustomWikiSearch',
        'descriptionmsg' => 'gcws-desc',
        'version' => '0.1 alpha',
);
 
$dir = dirname(__FILE__) . '/';
 
$wgAutoloadClasses['SpecialGoogleCustomWikiSearch'] = $dir . 'SpecialGoogleCustomWikiSearch.php';
$wgAutoloadClasses['GoogleCustomWikiSearchSettings'] = $dir . 'GoogleCustomWikiSearch.settings.php';
$wgExtensionMessagesFiles['MyExtensionAlias'] = $dir . 'SpecialGoogleCustomWikiSearch.alias.php';
$wgExtensionMessagesFiles['GoogleCustomWikiSearch'] = $dir . 'GoogleCustomWikiSearch.i18n.php';
$wgSpecialPages['GoogleCustomWikiSearch'] = 'SpecialGoogleCustomWikiSearch';
$wgSpecialPageGroups['GoogleCustomWikiSearch'] = 'redirects';

require_once $dir . 'GoogleCustomWikiSearch.Settings.php';

unset( $dir );