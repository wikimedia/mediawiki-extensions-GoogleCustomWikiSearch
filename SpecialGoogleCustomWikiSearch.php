<?php
class SpecialGoogleCustomWikiSearch extends SpecialPage {
	function __construct() {
		parent::__construct( 'GoogleCustomWikiSearch' );
	}

	function execute( $par ) {
		//Shamelessly borrowed from class SpecialSearch
		$this->setHeaders();
		$this->outputHeader();
		$out = $this->getOutput();
		$out->allowClickjacking();
		$out->addModuleStyles( 'mediawiki.special' );

		// Strip underscores from title parameter; most of the time we'll want
		// text from here. But don't strip underscores from actual text params!
		$titleParam = str_replace( '_', ' ', $par );

		$request = $this->getRequest();

		// Fetch the search term
		$search = str_replace( "\n", " ", $request->getText( 'term', $titleParam ) );

		$this->showResults( $search );
	}
	
	/**
	 * @param $term String
	 */
	public function showResults( $term ) {
		global $wgLang, $gcwsTheme;
		wfProfileIn( __METHOD__ );

		$this->setupPage( $term );

		$out = $this->getOutput();
		$languageCode = $wgLang->getCode();
		$theme = strtoupper( $gcwsTheme );
		$allOptions = $this->getOptions();

		// start rendering the page
		$out->addHtml(<<<END
<!-- Google Custom Search Element -->
<div id="cse" style="width: 100%;">Loading</div>
END
		);
		
		$out->addScript(<<<END
<script src="http://www.google.com/jsapi" type="text/javascript"></script>
<script type="text/javascript"> 
  google.load('search', '1', {language : '$languageCode', style : google.loader.themes.$theme});
  google.setOnLoadCallback(function() {
	var options = new google.search.DrawOptions();
    $allOptions
    customSearchControl.draw('cse', options);
    customSearchControl.execute("$term");
  }, true);
</script>
END
			);

		$out->parserOptions()->setEditSection( false );
		wfProfileOut( __METHOD__ );
	}
	
	/**
	 * @param $term string
	 */
	protected function setupPage( $term ) {
		$out = $this->getOutput();
		if( strval( $term ) !== ''  ) {
			$out->setPageTitle( $this->msg( 'searchresults' ) );
			$out->setHTMLTitle( $this->msg( 'pagetitle', $this->msg( 'searchresults-title', $term )->plain() ) );
		}
	}
	
	/**
	 * 
	 */
	protected function getOptions() {
		global $gcwsCustomSearchOptions, $gcwsID;
		
		if ( $gcwsCustomSearchOptions == '' ) {
		    return "var customSearchControl = new google.search.CustomSearchControl( '$gcwsID' )";
		}
		else {
			return $gcwsCustomSearchOptions;
		}
	}

}