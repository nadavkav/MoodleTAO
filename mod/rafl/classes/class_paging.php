<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Provides a whole paging solution with which any given database query can interact
	// Depd: Can only print page numbers as HTML text, not using images
	//----------------------------------------------------------------------------------------------

	class paging { 
		var $from;
		var $to;
		var $pageTotal;
		var $thisPage;
		var $previousText;
		var $nextText;
		var $divider;
		var $queryString;
		var $extraQuerystring;
		var $linkCssClass;
		var $activeCssClass;

		//----------------------------------------------------------------------------------------------
		// Desc: Works out the neccessary paging variables
		// Depd: When using this you must have determined the total record count already
		//----------------------------------------------------------------------------------------------

		function paging($recordTotal, $pageSize, $thisPage) {
			$recordTotal = (int) $recordTotal;
			$pageSize = max((int) $pageSize, 1);
			$thisPage = (int) $thisPage;
			$pageTotal = ceil($recordTotal / $pageSize);

			// First protect against out of range
			$thisPage = min($thisPage, $pageTotal);

			// Then set this page to 1, if we havent got any
			$thisPage = max($thisPage, 1);

			$this->from = (($thisPage - 1) * $pageSize) + 1;
			$this->to = min($thisPage * $pageSize, $recordTotal);
			$this->pageTotal = $pageTotal;
			$this->thisPage = $thisPage;
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Prints a paging bar, which is pretty customisable using any of the public variables from above
		// Depd: -
		//----------------------------------------------------------------------------------------------

		function pagingBar() {
			// Paging bar might get called with no records returned
			if ($this->pageTotal > 0) {
				// Output paging system
				if ($this->thisPage == 1) {
					// This is the first page - there is no previous page
					$bar  = "<span class=\"" . $this->activeCssClass . "\">" . $this->previousText . "</span>\n";
				} else {
					// Not the first page, link to the previous page
					$bar  = "<a class=\"" . $this->linkCssClass . "\" href=\"?" . $this->queryString . "=" . ($this->thisPage - 1) . $this->extraQuerystring . "\">" . $this->previousText . "</a>\n";
				}
	
				for ($i = 1; $i <= $this->pageTotal; $i++) {
					if ($i == $this->thisPage) {
						$bar .= $i;
					} else {
						$bar .= "<a class=\"" . $this->linkCssClass . "\" href=\"?" . $this->queryString . "=" . $i . $this->extraQuerystring . "\">$i</a>\n";
					}
	
					// Avoid the last divider, cos there is nothingto divide anymore
					if ($i < $this->pageTotal) {
						$bar .= $this->divider;
					}
				}
	
				if ($this->thisPage == $this->pageTotal) {
					// This is the last page - there is no next page
					$bar .= "<span class=\"" . $this->activeCssClass . "\">" . $this->nextText . "</span>\n";
				} else {
					// Not the last page, link to the next page
					$bar .= "<a class=\"" . $this->linkCssClass . "\" href=\"?" . $this->queryString . "=" . ($this->thisPage + 1) . $this->extraQuerystring . "\">" . $this->nextText . "</a>\n";
				}
			}

			return $bar;
		}
	}
?>
