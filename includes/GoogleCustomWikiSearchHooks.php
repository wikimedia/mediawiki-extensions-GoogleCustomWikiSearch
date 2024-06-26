<?php

class GoogleCustomWikiSearchHooks {

	/**
	 * Possibly replace the built-in search
	 *
	 * @global boolean $wgGoogleCustomWikiSearchReplaceSearch
	 * @global boolean $wgDisableTextSearch
	 * @global string $wgSearchForwardUrl
	 * @return bool
	 */
	public static function onSpecialSearchSetupEngine() {
		global $wgGoogleCustomWikiSearchReplaceSearch, $wgDisableTextSearch, $wgSearchForwardUrl;

		if ( !$wgGoogleCustomWikiSearchReplaceSearch ) {
			return true;
		}

		$wgDisableTextSearch = true;
		$specialPageTitle = SpecialPage::getTitleFor( 'GoogleCustomWikiSearch' );
		$wgSearchForwardUrl = $specialPageTitle->getFullURL( 'term=$1' );

		return true;
	}

	/**
	 * Possibly append Google search results to Special:Search
	 *
	 * @global boolean $wgGoogleCustomWikiSearchAppendToSearch
	 * @param SpecialPage $special
	 * @param ?string $subPage
	 * @return bool
	 */
	public static function onSpecialPageAfterExecute( SpecialPage $special, $subPage ) {
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
