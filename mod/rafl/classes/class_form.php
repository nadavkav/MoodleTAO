<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Print out form elements and their labels in a standardised way
	// Depd: -
	//----------------------------------------------------------------------------------------------

	class form {
		//----------------------------------------------------------------------------------------------
		// Desc: Make input boxes look nice
		// Depd: -
		//----------------------------------------------------------------------------------------------

		function getStylesheet($argExternalStylesheet) {
			if (strlen($argExternalStylesheet)) {
				echo $argExternalStylesheet;
			} else {
				echo "<style type=\"text/css\">\n";
				echo "	div.error {\n";
				echo "		display: none;\n";
				echo "	}\n";
				echo "	input.input {\n";
				echo "		width: 400px;\n";
				echo "	}\n";
				echo "	file.file {\n";
				echo "		width: 405px;\n";
				echo "	}\n";
				echo "	select.select {\n";
				echo "		width: 405px;\n";
				echo "	}\n";
				echo "	select.selectAddRemove {\n";
				echo "		width: 180px;\n";
				echo "	}\n";
				echo "	textarea.textarea {\n";
				echo "		width: 400px;\n";
				echo "		height: 150px;\n";
				echo "	}\n";
				echo "</style>\n";
			}
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Create an input box using a common style
		// Depd: -
		//----------------------------------------------------------------------------------------------
	
		function getInputField($elementId, $labelText='', $defaultValue='', $extraAttribute='') {
 		 	if (strlen($labelText)) {
 		 		$label = '<label id="' . $elementId . 'Label" for="' . $elementId . '" class="label">' . $labelText . '</label>' . "\n";
 		 	}

			return($label . '<div id="' . $elementId . 'Error" class="error"></div>
			                 <div id="' . $elementId . 'Element" class="element">
			                     <input class="input" type="text" name="' . $elementId . '" id="' . $elementId . '" 
			                         value="' . htmlentities($defaultValue) . '" ' . $extraAttribute . ' />
			                 </div>' . "\n");
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Create an password box using a common style
		// Depd: -
		//----------------------------------------------------------------------------------------------
	
		function getInputPassword($elementId, $labelText='', $extraAttribute='') {
 		 	if (strlen($labelText)) {
 		 		$label = '<label id="' . $elementId . 'Label" for="' . $elementId . '" class="label">' . $labelText . '</label>' . "\n";
 		 	}

			return($label . '<div id="' . $elementId . 'Error" class="error"></div>
			                 <div id="' . $elementId . 'Element" class="element">
			                     <input class="input" type="password" name="' . $elementId . '" id="' . $elementId . '" 
			                         value="" ' . $extraAttribute . ' />
			                 </div>' . "\n");
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Create a checkbox using a common style
		// Depd: -
		//----------------------------------------------------------------------------------------------
	
		function getInputCheckbox($elementId, $labelText='', $defaultValue, $isChecked=false, $extraAttribute='') {
			// WORKAROUND: Count checkboxes of the same name within this function
			global $idCheckElementIds, $idCheckCount;
			
			if (is_array($idCheckElementIds) && in_array($elementId, $idCheckElementIds)) {
				$idCheckCount++;
			} else {
				$idCheckCount = 1;
			}
			
			$idCheckElementIds[] = $elementId;

			$checkedAttribute = $isChecked == true ? ' checked="checked" ' : '';

 		 	if (strlen($labelText)) {
 		 		$label = '<label id="' . $elementId . 'Label_' . $idCheckCount . '" for="' . $elementId . '_' . $idCheckCount . '" class="checkboxLabel">' . $labelText . '</label>' . "\n";
 		 	}

			return('<div id="' . $elementId . '_' . $idCheckCount . 'Error" class="error"></div>
			        <div id="' . $elementId . 'Element_' . $idCheckCount . '" class="element">
			            <input class="checkbox" type="checkbox" name="' . $elementId . '[]" id="' . $elementId . '_' . $idCheckCount . '" 
			                value="' . htmlentities($defaultValue) . '" ' . $checkedAttribute . $extraAttribute . ' /> 
			            ' . $label . '
			        </div>' . "\n");
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Create a radio button using a common style
		// Depd: -
		//----------------------------------------------------------------------------------------------
	
		function getInputRadio($elementId, $labelText='', $defaultValue, $isChecked=false, $extraAttribute='') {
			// WORKAROUND: Count radios of the same name within this function
			global $idRadioElementIds, $idRadioCount;
			
			if (is_array($idRadioElementIds) && in_array($elementId, $idRadioElementIds)) {
				$idRadioCount++;
			} else {
				$idRadioCount = 1;
			}
			
			$idRadioElementIds[] = $elementId;
			
			$checkedAttribute = $isChecked == true ? ' checked="checked" ' : '';

 		 	if (strlen($labelText)) {
 		 		$label = '<label id="' . $elementId . 'Label_' . $idRadioCount . '" for="' . $elementId . '_' . $idRadioCount . '" class="radioLabel">' . $labelText . '</label>' . "\n";
 		 	}

			return('<div id="' . $elementId . '_' . $idRadioCount . 'Error" class="error"></div>
			        <div id="' . $elementId . 'Element_' . $idRadioCount . '" class="element">
			            <input class="radio" type="radio" name="' . $elementId . '" id="' . $elementId . '_' . $idRadioCount . '" 
			                value="' . htmlentities($defaultValue) . '" ' . $checkedAttribute . $extraAttribute . ' /> 
			            ' . $label . '
			        </div>' . "\n");
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Create an input file upload box using a common style
		// Depd: -
		//----------------------------------------------------------------------------------------------
	
		function getInputFile($elementId, $labelText='', $extraAttribute='') {
 		 	if (strlen($labelText)) {
 		 		$label = '<label id="' . $elementId . 'Label" for="' . $elementId . '" class="label">' . $labelText . '</label>' . "\n";
 		 	}

			return($label . '<div id="' . $elementId . 'Error" class="error"></div>
			                 <div id="' . $elementId . 'Element" class="element">
			                     <input class="file" name="' . $elementId . '" id="' . $elementId . '" 
			                         type="file" ' . $extraAttribute . ' />
			                 </div>' . "\n");
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Create an input text box using a common style
		// Depd: -
		//----------------------------------------------------------------------------------------------
	
		function getInputTextarea($elementId, $labelText='', $defaultValue='', $extraAttribute='') {
 		 	if (strlen($labelText)) {
 		 		$label = '<label id="' . $elementId . 'Label" for="' . $elementId . '" class="label">' . $labelText . '</label>' . "\n";
 		 	}

			return($label . '<div id="' . $elementId . 'Error" class="error"></div>
			                 <div id="' . $elementId . 'Element" class="element">
			                 <textarea class="textarea" name="' . $elementId . '" 
			                     id="' . $elementId . '" ' . $extraAttribute . '>' . htmlentities($defaultValue) . '</textarea>
			                 </div>' . "\n");
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Create a select box
		// Depd: -
		//----------------------------------------------------------------------------------------------
	
	 	function getSelectField($elementId, $labelText='', $arrayValues, $defaultValue='', $sizeOfDropdown=1, $multipleSelect=0, $extraAttribute='') {
 		 	if (strlen($labelText)) {
 		 		$label = '<label id="' . $elementId . 'Label" for="' . $labelText . '" class="label">' . $labelText . '</label>' . "\n";
 		 	}

 		 	$selectHtml  = $label;

 		 	$selectHtml .= '<div id="' . $elementId . 'Error" class="error"></div>
 		 	                <div id="' . $elementId . 'Element" class="element">
 		 	                ';

 		 	// Multi-select box?
	 		if ($multipleSelect!=0) {
	 		 	$selectHtml .= '<select name="' . $elementId . '[]" multiple="multiple"';
			} else {
	 		 	$selectHtml .= '<select name="' . $elementId . '"';
	 		}
	
			$selectHtml .= ' class="select" id="' . $elementId . '" size="' . $sizeOfDropdown . '" ' . $extraAttribute . '>' . "\n";

			if (count($arrayValues)) {
		 		foreach ($arrayValues as $arrayKey=>$arrayData) {
		 			// Now check if we need to select this value
					if (is_array($defaultValue) && in_array($arrayKey, $defaultValue)) {
			 			$selectHtml .= '<option value="' . $arrayKey . '" selected="selected">' . $arrayData . '</option>' . "\n";
		 			} elseif ($arrayKey == $defaultValue) {
			 			$selectHtml .= '<option value="' . $arrayKey . '" selected="selected">' . $arrayData . '</option>' . "\n";
					} else {
			 			$selectHtml .= '<option value="' . $arrayKey . '">' . $arrayData . '</option>' . "\n";
		 			}
		 		}
	 		}
	
	 		$selectHtml .='</select></div>' . "\n";

 			return $selectHtml;
	 	}

		//----------------------------------------------------------------------------------------------
		// Desc: Create two select boxes that can have entries be added to or removed from
		// Depd: -
		//----------------------------------------------------------------------------------------------
	
	 	function getAddRemoveSelectField($elementId, $labelText='', $arrayValues, $defaultValue='', $sizeOfDropdown=1, $extraAttribute='') {
			$this->getAddRemoveSupportingJavascript();

 		 	if (strlen($labelText)) {
 		 		$label = '<label id="' . $elementId . 'Label" for="' . $labelText . '" class="label">' . $labelText . '</label>' . "\n";
 		 	}

 		 	$selectHtml  = $label;

 		 	$selectHtml .= '<div id="' . $elementId . 'Error" class="error"></div>
 		 	                <div id="' . $elementId . 'Element" class="element">
 		 	                	<div id="' . $elementId . 'ElementSelectPoolHolder" class="elementAddRemoveSelectPoolHolder">
 		 	                		<select class="selectAddRemove" id="' . $elementId . 'Pool" name="' . $elementId . 'Pool[]" size="' . $sizeOfDropdown . '" multiple="multiple">' . "\n";
	
			if (is_array($arrayValues)) {
		 		foreach ($arrayValues as $arrayKey=>$arrayData) {
		 			$selectHtml .= '	<option value="' . $arrayKey . '">' . $arrayData . '</option>' . "\n";
		 		}
	 		}
	
	 		$selectHtml .='			</select>
	 		               		</div>
 		 	               		<div class="elementAddRemoveSelectButtons">
 		 	               			<input type="button" onclick="javascript:addBrowseToDestList(this.form.' . $elementId . 'Pool, this.form.' . $elementId . ') " class="addButton" value="Add &raquo;&raquo;" />
 		 	               			<input type="button" onclick="javascript:deleteFromDestList(this.form.' . $elementId . ');" class="removeButton" value="&laquo;&laquo; Remove" />
 		 	               		</div>
 		 	               		<div id="' . $elementId . 'ElementSelectHolder" class="elementAddRemoveSelectHolder">
 		 	               			<select class="selectAddRemove" id="' . $elementId . '" name="' . $elementId . '[]" size="' . $sizeOfDropdown . '" multiple="multiple"' . "\n";
	
			$selectHtml .= ' ' . $extraAttribute . '>' . "\n";
		
 			// Now check if we need to select this value
			if (is_array($arrayValues)) {
		 		foreach ($arrayValues as $arrayKey=>$arrayData) {
		 			// Now check if we need to select this value
					if (is_array($defaultValue) && in_array($arrayKey, $defaultValue)) {
			 			$selectHtml .= '<option value="' . $arrayKey . '" selected="selected">' . $arrayData . '</option>' . "\n";
		 			} elseif ($arrayKey == $defaultValue) {
			 			$selectHtml .= '<option value="' . $arrayKey . '" selected="selected">' . $arrayData . '</option>' . "\n";
		 			}
		 		}
	 		}

	 		$selectHtml .='			</select>' . "\n";
	 		$selectHtml .='		</div>' . "\n";
	 		$selectHtml .='</div>' . "\n";

 			return $selectHtml;
	 	}

		//----------------------------------------------------------------------------------------------
		// Desc: Output supporting javascript functions for the add remove select
		// Depd: -
		//----------------------------------------------------------------------------------------------

		function getAddRemoveSupportingJavascript() {
			// WARNING: Make sure the javascript only gets outputted once on the page
			if (! strpos(ob_get_contents(), 'addSearchToDestList')) {
				ob_start();
?>
				<script type="text/javascript">
					// Adds to the destination list via the search input field
					function addSearchToDestList(argFormField, argDestList) {
						var len = argDestList.length;
					
						var found = false;
					
						for(var count = 0; count < len; count++) {
							if (argDestList.options[count] != null) {
							    if (argFormField.value == argDestList.options[count].value) {
								   found = true;
								   break;
							    }
							}
						}
					
						if (found != true) {
						    	argDestList.options[len] = new Option(argFormField.value, argFormField.value);
						}
					
						// Reset search box
						argFormField.value = '';
					    
						// Select all right column entries
						allSelect(argDestList);
					}
					
					// Adds to the destination list via browse select box
					function addBrowseToDestList(argSrcList, argDestList) {
						var len = argDestList.length;
					
						for(var i = 0; i < argSrcList.length; i++) {
							if ((argSrcList.options[i] != null) && (argSrcList.options[i].selected)) {
							    // Check if this value already exist in the destList or not
							    // if not then add it otherwise do not add it.
							    var found = false;
							
							    for(var count = 0; count < len; count++) {
							        if (argDestList.options[count] != null) {
							            if (argSrcList.options[i].value == argDestList.options[count].value) {
							        	   found = true;
							        	   break;
							            }
							        }
							    }
							
							    if (found != true) {
							        argDestList.options[len] = new Option(argSrcList.options[i].text, argSrcList.options[i].value);
							        len++;
							    }
							}
						}
					    
						// Select all right column entries
						allSelect(argDestList);
					}
					
					// Deletes from the destination list
					function deleteFromDestList(argDestList) {
					    var len = argDestList.options.length;
					
					    for(var i = (len-1); i >= 0; i--) {
					        if ((argDestList.options[i] != null) && (argDestList.options[i].selected == true)) {
					            argDestList.options[i] = null;
					        }
					    }
					
					     // Select all right column entries
					     allSelect(argDestList);
					}
					
					// Selects all entries in a multiple select box
					function allSelect(list) {
					    for (i=0;i<list.length;i++) {
					       list.options[i].selected = true;
					    }
					}
				</script>
<?php
			}
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Create a submit button using a common style
		// Depd: -
		//----------------------------------------------------------------------------------------------
	
		function getSubmitButton($elementId, $elementValue='Save', $extraAttribute='') {
			return('<div id="' . $elementId . 'Element" class="elementSubmit">
			        <input class="submit" type="submit" 
			            name="' . $elementId . '"
			            id="' . $elementId . '"
			            value="' . $elementValue . '" ' . $extraAttribute . '/>
			        </div>' . "\n");
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Checks for DOM compliant javascript and prints out a button, if successful
		// Depd: -
		//----------------------------------------------------------------------------------------------
	
		function getJavascriptSubmitButtonCheck($elementId, $elementValue='Save', $extraAttribute='') {
			return('<div id="' . $elementId . 'Element" class="elementButton">
			            <script type="text/javascript">
			                if (document.getElementById) {
			                    document.write(\' <input class="button" type="button" name="' . $elementId . '" id="' . $elementId . '" value="' . $elementValue . '" ' . $extraAttribute . ' />\');
			                } else {
			                    document.write(\' <p style="font-weight:bold; color:#FF0000;">\');
			                    document.write(\' 	Error: It appears that your browser does not support the current JavaScript web standard.\');
			                    document.write(\' 	Please upgrade your browser software.\');
			                    document.write(\' 	You may then reload this page to submit this form.\');
			                    document.write(\' <\/p>\');
			                }
			            </script>
			            <noscript>
			                <p style="font-weight:bold; color:#FF0000;">
			                    Error: It appears that your browser does not support JavaScript, or you have it disabled.
			                    Please adjust your browser settings to support JavaScript.
			                    You may then reload this page to submit this form.
			                </p>
			            </noscript>
			        </div>' . "\n");
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Get form element for a hidden field
		// Depd: -
		//----------------------------------------------------------------------------------------------
	
		function getInputHidden($elementId, $elementValue='', $extraAttribute='') {
			return('<div id="' . $elementId . 'Error" class="error"></div>
			        <div id="' . $elementId . 'Element">
			            <input type="hidden"
			                id="' . $elementId . '"
			                name="' . $elementId . '"
			                value="' . htmlentities($elementValue) . '" ' . $extraAttribute . '/>
			        </div>' . "\n");
		}
	}	
?>
