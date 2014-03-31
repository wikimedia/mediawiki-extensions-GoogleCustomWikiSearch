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
        'version' => '0.2.0 alpha',
);

$dir = __DIR__ . '/';

$wgAutoloadClasses['SpecialGoogleCustomWikiSearch'] = $dir . 'SpecialGoogleCustomWikiSearch.php';
$wgExtensionMessagesFiles['GoogleCustomWikiSearchAlias'] = $dir . 'SpecialGoogleCustomWikiSearch.alias.php';
$wgMessagesDirs['GoogleCustomWikiSearch'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['GoogleCustomWikiSearch'] = $dir . 'GoogleCustomWikiSearch.i18n.php';
$wgSpecialPages['GoogleCustomWikiSearch'] = 'SpecialGoogleCustomWikiSearch';
$wgSpecialPageGroups['GoogleCustomWikiSearch'] = 'redirects';

require_once $dir . 'GoogleCustomWikiSearch.settings.php';

unset( $dir );
