<?php
class SkinData { 
	
	var $skinFolderPath;
	var $skinObj;
	var $skinConfigObj;
	var $defaultSkinId;
	
	/**
	 * Constructor for the skin data class.
	 *
	 * @param string $skinFolderPath The path to the skins folder.
	 * @param integer $schoolId The id of the school to get the skin data for.
	 * @return SkinData
	 */
	
	public function SkinData ( $skinFolderPath = "/", $schoolId = 0, $skinId = 0 ) {
		$this -> skinFolderPath = $skinFolderPath;
		if (!$skinId) {
			$skinId = 1;
		}
		$this -> defaultSkinId = $skinId;
		
		// define the possible skin locations
		$school_skin_file = $skinFolderPath."school.".$schoolId.".skins.xml";
		$default_skin_file = $skinFolderPath."default.skins.xml";
		
		// attempt to parse the xml file
		if ( file_exists($school_skin_file) ) {
			$this -> skinObj = new SimpleXMLElement( $school_skin_file, NULL, TRUE );
		} else if ( file_exists($default_skin_file) ) {
			$this -> skinObj = new SimpleXMLElement( $default_skin_file, NULL, TRUE );
		}
		
		// attempt to parse the skin xml
		if ( $this -> defaultSkinId && $this -> skinObj ) {
			// WORKAROUND: Work out skin path in relation to the path of the passed XML folder path
			$relativeSkinPath = $this->skinFolderPath . "/../../../" . $this->getSkinPath();

			$this -> parseSkinConfig ( $relativeSkinPath . "config.xml" );
		}
	}
	
	/**
	 * Gets the path to the skin xml file based on the skin id supplied
	 *
	 * @param integer $skinId 
	 * @return string The path to the skin xml file
	 */
	
	public function getSkinPath ( $skinId = 0 ) {
		if (!$skinId) {
			$skinId = $this -> defaultSkinId;
		}
		if ( isset( $this -> skinObj ) ) {
			// loop through skin config to find id
			foreach ($this -> skinObj -> skin as $skin) {
				$skin_attributes = $skin -> attributes();
				if ( $skin_attributes["id"] == $skinId ) {
					return "main/" . $skin->path;
					break;
				}
			}
		} else {
			return "";
		}
	}
	
	/**
	 * Parses the skin config file
	 *
	 * @param string $skinConfigPath The path to the skin config file. e.g. "main/skins/Snot/config.xml"
	 * @return unknown
	 */
	
	public function parseSkinConfig ( $skinConfigPath = "/skin.config.xml" ) {
		// parse the config file for the specified skin
		if ( file_exists( $skinConfigPath ) ) {
			$this -> skinConfigObj = new SimpleXMLElement( $skinConfigPath, NULL, TRUE );
			return true;
		}
		return false;
	}
	
	/**
	 * Returns the hex value contained within the currently selected skin config file
	 *
	 * @param unknown_type $colourName
	 * @return unknown
	 */
	
	public function getSkinColour ( $colourName ) {
		if ( $this -> skinConfigObj -> colourHexValues -> {$colourName} ) {
			return $this -> skinConfigObj -> colourHexValues -> {$colourName};
		} else {
			return "";
		}
	}
	
	/**
	 * Returns the hex value re-formatted to be used within HTML
	 *
	 * @param unknown_type $colourName
	 * @return unknown
	 */
	
	public function getSkinHtmlColour($colourName) {
		$colourRetrieved = $this->getSkinColour($colourName);

		if (strpos($colourRetrieved, 'x')) {
			$htmlColour = explode('x', $colourRetrieved);
			return $htmlColour[1];
		} else {
			return "";
		}
	}
}
?>