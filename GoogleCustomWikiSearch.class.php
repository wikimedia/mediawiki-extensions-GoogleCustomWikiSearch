<?php

class GoogleCustomWikiSearch {
	/**
	 * The term to search for
	 *
	 * @var string
	 */
	private $term;

	/**
	 * This site's Google Custom Search ID
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Internationalisation code to be passed to Google.
	 *
	 * @todo Handle special languages like Simple English
	 * @var Language
	 */
	private $lang;

	/**
	 * Name of a Google search theme
	 *
	 * @var string
	 */
	private $theme;

	/**
	 * Advanced search options to be inserted into js
	 *
	 * @var string
	 */
	private $options;

	/**
	 * Version number of Google js desired
	 *
	 * @var int
	 */
	private $codeVersion;

	/**
	 * Is this output being inserted into Special:Search?
	 *
	 * @var Boolean
	 */
	private $isOnSpecialSearch = false;

	/**
	 * To be used for rendering the search
	 *
	 * @var OutputPage
	 */
	private $out;

	public function getTerm() {
		return $this->term;
	}

	public function getId() {
		return $this->id;
	}

	public function getLang() {
		return $this->lang;
	}

	public function getTheme() {
		return $this->theme;
	}

	public function getOptions() {
		return $this->options;
	}

	public function getCodeVersion() {
		return $this->codeVersion;
	}

	public function getIsOnSpecialSearch() {
		return $this->isOnSpecialSearch;
	}

	public function getOut() {
		return $this->out;
	}

	public function setTerm( $term ) {
		return wfSetVar( $this->term, $term );
	}

	public function setId( $id ) {
		return wfSetVar( $this->id, $id );
	}

	public function setLang( Language $lang ) {
		return wfSetVar( $this->lang, $lang );
	}

	public function setTheme( $theme ) {
		return wfSetVar( $this->theme, $theme );
	}

	public function setOptions( $options ) {
		return wfSetVar( $this->options, $options );
	}

	public function setCodeVersion( $googleVersion ) {
		return wfSetVar( $this->codeVersion, $googleVersion );
	}

	public function setIsOnSpecialSearch( Boolean $isOnSpecialSearch ) {
		return wfSetVar( $this->isOnSpecialSearch, $isOnSpecialSearch );
	}

	public function setOut( OutputPage $out ) {
		return wfSetVar( $this->out, $out );
	}

	/**
	 * Set up all the inclusions needed for the search
	 * Inspired by ParserOptions
	 *
	 * @global OutputPage $wgOut
	 * @param OutputPage $out
	 * @param Language $lang
	 */
	public function __construct( OutputPage $out = null, Language $lang = null ) {
		if ( $out === null ) {
			global $wgOut;
			if ( $wgOut === null ) {
				$out = new OutputPage;
			} else {
				$out = $wgOut;
			}
		}
		if ( $lang === null ) {
			$lang = $out->getLanguage();
		}

		$this->initializeFromOutputPageAndLanguage( $out, $lang );
	}

	/**
	 * Get a GoogleCustomWikiSearch object from a IContextSource object
	 *
	 * @param IContextSource $context
	 * @return GoogleCustomWikiSearch
	 */
	public static function newFromContext( IContextSource $context ) {
		return new GoogleCustomWikiSearch( $context->getOutput(), $context->getLanguage() );
	}

	/**
	 * Get a GoogleCustomWikiSearch object from a given OutputPage
	 *
	 * @param OutputPage $out
	 * @return GoogleCustomWikiSearch
	 */
	public static function newFromOutputPage( OutputPage $out ) {
		return new GoogleCustomWikiSearch( $out, $out->getLanguage() );
	}

	/**
	 * Get a GoogleCustomWikiSearch object from a given OutputPage and Language
	 *
	 * @param OutputPage $out
	 * @param Language $lang
	 * @return GoogleCustomWikiSearch
	 */
	public static function newFromOutputPageAndLanguage( OutputPage $out, Language $lang ) {
		return new GoogleCustomWikiSearch( $out, $lang );
	}

	/**
	 * Set up the parameters to be inserted into the js
	 *
	 * @global string $wgGoogleCustomWikiSearchId
	 * @global string $wgGoogleCustomWikiSearchTheme
	 * @global string $wgGoogleCustomWikiSearchOptions
	 * @global int $wgGoogleCustomWikiSearchCodeVersion
	 */
	private function initializeFromOutputPageAndLanguage( OutputPage $out, Language $lang ) {
		global $wgGoogleCustomWikiSearchId, $wgGoogleCustomWikiSearchTheme,
		$wgGoogleCustomWikiSearchOptions, $wgGoogleCustomWikiSearchCodeVersion;

		$this->out = $out;
		$this->lang = $lang;
		$this->id = $wgGoogleCustomWikiSearchId;
		$this->theme = $wgGoogleCustomWikiSearchTheme;
		$this->options = $wgGoogleCustomWikiSearchOptions == '' ? $this->getDefaultOptions() :
			$wgGoogleCustomWikiSearchOptions;
		$this->codeVersion = $wgGoogleCustomWikiSearchCodeVersion;
		if ( $out->getTitle()->equals( SpecialPage::getTitleFor( 'Search' ) ) ) {
			$this->isOnSpecialSearch = true;
		}
	}

	/**
	 * Set up a no-frills search based on the GCS id, if set
	 *
	 * @return string
	 */
	private function getDefaultOptions() {
		return "var customSearchControl = new google.search.CustomSearchControl( '$this->id' );";
	}

	/**
	 * The HTML element for the js to use
	 *
	 * @return string
	 */
	protected function getHtml() {
		$html = "<!-- Google Custom Search Element -->\n";
		$html .= '<div id="cse" style="width: 100%;"></div>' . "\n";

		return $html;
	}

	/**
	 * @return string
	 */
	private function getScriptVersion1() {
		return <<<END
	google.load('search', '1', {language : '{$this->lang->getCode()}', style : google.loader.themes.$this->theme});
	google.setOnLoadCallback(function() {
		var options = new google.search.DrawOptions();
		{$this->getSearchDisplayOption()}
		$this->options
		customSearchControl.draw('cse', options);
		customSearchControl.execute("$this->term");
	}, true);
END;
	}

	/**
	 * @return string
	 */
	private function getScriptVersion2() {
		return <<<END
function gcseCallback() {
	if (document.readyState != 'complete')
		return google.setOnLoadCallback(gcseCallback, true);
	google.search.cse.element.render({gname:'gcws', div:'cse', {$this->getSearchDisplayOption()}});
	var element = google.search.cse.element.getElement('gcws');
	element.execute('$this->term');
};
window.__gcse = {
	parsetags: 'explicit',
	callback: gcseCallback
};

(function() {
	var cx = '$this->id';
	var gcse = document.createElement('script'); gcse.type = 'text/javascript';
	gcse.async = true;
	gcse.src = (document.location.protocol == 'https' ? 'https:' : 'http:') +
		'//www.google.com/cse/cse.js?theme={$this->theme}&language={$this->lang->getCode()}&cx=' + cx;
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(gcse, s);
})();
END;
	}

	/**
	 * The name of the external Google script for version 1
	 *
	 * @return string
	 */
	public function getScriptFileName() {
		return 'http://www.google.com/jsapi';
	}

	/**
	 * Add all necessary output
	 *
	 * @param string $term
	 */
	public function doSearch( $term ) {
		$this->term = $term;
		$out = $this->out;
		$out->addHtml( $this->getHtml() );
		if ( $this->codeVersion == 1 ) {
			$out->addScriptFile( $this->getScriptFileName() );
			$out->addInlineScript( $this->getScriptVersion1() );
		} else {
			$out->addInlineScript( $this->getScriptVersion2() );
		}
	}

	/**
	 * Determine and format the correct search term to use
	 * If $term is not set, use $default.
	 *
	 * @param string $term
	 * @param string $default
	 * @return string
	 */
	public static function getSearchTerm( $term, $default ) {
		// from SpecialSearch
		// Strip underscores from title parameter; most of the time we'll want
		// text from here. But don't strip underscores from actual text params!
		$titleParam = str_replace( '_', ' ', $default );
		$correctTerm = isset( $term ) ? $term : $titleParam;

		return str_replace( "\n", " ", $correctTerm );
	}

	/**
	 * Option to hide search box and show only results. Useful if this is
	 * SpecialSearch.
	 *
	 * @return string
	 */
	protected function getSearchDisplayOption() {
		if ( !$this->isOnSpecialSearch ) {
			return '';
		}
		if ( $this->codeVersion == 1 ) {
			return 'options.enableSearchResultsOnly();';
		} else {
			return "tag:'searchresults-only'";
		}
	}
}
