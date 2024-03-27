<?php

class SpecialGoogleCustomWikiSearch extends SpecialPage {

	public function __construct() {
		parent::__construct( 'GoogleCustomWikiSearch' );
	}

	/**
	 * Entry point
	 *
	 * @param ?string $subPage
	 */
	public function execute( $subPage ) {
		// Shamelessly borrowed from class SpecialSearch
		$this->setHeaders();
		$this->outputHeader();
		$out = $this->getOutput();
		if ( method_exists( $out, 'allowClickjacking' ) ) {
			// Up to MW 1.41
			// @phan-suppress-next-line PhanUndeclaredMethod
			$out->allowClickjacking();
		} else {
			// MW 1.41+
			$out->setPreventClickjacking( false );
		}

		// Fetch the search term
		$request = $this->getRequest();
		$term = GoogleCustomWikiSearch::getSearchTerm( $request->getText( 'term' ), $subPage );

		$this->showResults( $term );
	}

	/**
	 * @param string $term
	 */
	public function showResults( $term ) {
		$this->setupPage( $term );

		$googleCustomWikiSearch = new GoogleCustomWikiSearch( $this->getContext() );
		$googleCustomWikiSearch->doSearch( $term );
	}

	/**
	 * @param string $term
	 */
	protected function setupPage( $term ) {
		$out = $this->getOutput();
		if ( strval( $term ) !== '' ) {
			$out->setPageTitle( $this->msg( 'searchresults' ) );
			$out->setHTMLTitle( $this->msg( 'pagetitle' )
					->plaintextParams( $this->msg( 'searchresults-title' )->plaintextParams( $term )->text() )
					->inContentLanguage()->text()
			);
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function getGroupName() {
		return 'redirects';
	}
}
