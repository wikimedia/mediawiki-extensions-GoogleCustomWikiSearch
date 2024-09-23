<?php

namespace MediaWiki\Extension\GoogleCustomWikiSearch;

use Html;
use MediaWiki\SpecialPage\Hook\SpecialPageAfterExecuteHook;
use MediaWiki\SpecialPage\Hook\SpecialPageBeforeExecuteHook;
use MediaWiki\Specials\SpecialSearch;
use SpecialPage;

class Hooks implements SpecialPageAfterExecuteHook, SpecialPageBeforeExecuteHook {

	/**
	 * Possibly replace the built-in search
	 * @param SpecialPage $special
	 * @param string $subPage
	 * @global boolean $wgGoogleCustomWikiSearchReplaceSearch
	 * @global boolean $wgDisableTextSearch
	 * @global string $wgSearchForwardUrl
	 */
	public function onSpecialPageBeforeExecute( $special, $subPage ) {
		if ( $special instanceof SpecialSearch ) {
			global $wgGoogleCustomWikiSearchReplaceSearch, $wgDisableTextSearch, $wgSearchForwardUrl;

			if ( !$wgGoogleCustomWikiSearchReplaceSearch ) {
				return;
			}

			$wgDisableTextSearch = true;
			$specialPageTitle = SpecialPage::getTitleFor( 'GoogleCustomWikiSearch' );
			$wgSearchForwardUrl = $specialPageTitle->getFullURL( 'term=$1' );
		}
	}

	/**
	 * Possibly append Google search results to Special:Search
	 *
	 * @global boolean $wgGoogleCustomWikiSearchAppendToSearch
	 * @param SpecialPage $special
	 * @param ?string $subPage
	 * @return bool
	 */
	public function onSpecialPageAfterExecute( $special, $subPage ) {
		global $wgGoogleCustomWikiSearchAppendToSearch;

		// Only modify Special:Search
		if ( $special->getName() != 'Search' || !$wgGoogleCustomWikiSearchAppendToSearch ) {
			return true;
		}

		// Add gcws header to output
		$out = $special->getOutput();
		$out->addModuleStyles( 'ext.googleCustomWikiSearch' );
		$out->addHTML( Html::element( 'h1', [ 'id' => 'gcws_header' ],
				$special->msg( 'googlecustomwikisearch' )->text() ) );

		// Set up the Google search
		$googleCustomWikiSearch = new GoogleCustomWikiSearch( $special->getContext() );

		$request = $special->getRequest();
		$term = GoogleCustomWikiSearch::getSearchTerm( $request->getText( 'search' ), $subPage );
		$googleCustomWikiSearch->doSearch( $term );

		return true;
	}
}
