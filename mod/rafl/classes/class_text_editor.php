<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Provides a rich-text text editor for web pages
	// Depd: The actual text editor must be present and configured right
	// Depd: If in need of more specialised configurations, please use the editorType to switch between configurations
	//----------------------------------------------------------------------------------------------

	class textEditor { 
		var $formField = '';
		var $editorType = 'full';
		var $content = '';
		var $width = 640;
		var $height = 320;
		var $cssPath = '';

		//----------------------------------------------------------------------------------------------
		// Desc: Display the editor
		// Depd: Page output buffering, i.e. ob_get_contents()
		//----------------------------------------------------------------------------------------------

		function getEditor() {
                        global $CFG;
			if (! strlen($this->formField)) {
				return "Error: Please use the setFormField('fieldName') method to set what the name of the form field is you want to create.";
			} else {
				$tinyMceFullConfig = 'mode : "textareas",
				                      theme : "advanced",
				                      plugins : "safari,spellchecker,style,table,advimage,advlink,emotions,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,fullscreen,imagemanager,filemanager",
				                      theme_advanced_buttons1 : "fullscreen,preview,print,newdocument,|,cut,copy,paste,pastetext,pasteword,|,spellchecker,search,separator,bullist,numlist,|,outdent,indent,|,undo,redo",
				                      theme_advanced_buttons2 : "insertfile,insertimage,link,unlink,anchor,|,table,emotions,charmap,hr,sub,sup,|,insertdate,inserttime,|,styleprops,removeformat,cleanup,code",
				                      theme_advanced_buttons3 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
				                      theme_advanced_blockformats : "p,address,pre,h1,h2,h3,h4,h5,h6",
				                      theme_advanced_fonts : "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
				                      spellchecker_languages : "+English=en",
				                      theme_advanced_font_sizes : "1,2,3,4,5,6,7",
				                      theme_advanced_more_colors : 1,
				                      theme_advanced_row_height : 23,
				                      theme_advanced_toolbar_location : "top",
				                      theme_advanced_toolbar_align : "left",
				                      theme_advanced_path : true,
				                      theme_advanced_statusbar_location : "bottom",
				                      theme_advanced_resize_horizontal : true,
				                      theme_advanced_resizing : true,
				                      theme_advanced_resizing_use_cookie : true,
				                      relative_urls : false,
				                      remove_script_host : true,
				                      invalid_elements : "script,style,iframe",
				                      plugin_preview_width : "600",
				                      plugin_preview_height : "400",
				                      plugin_insertdate_dateFormat : "%d-%m-%Y",
				                      plugin_insertdate_timeFormat : "%H:%M:%S",
				                      apply_source_formatting : true,
				                      body_class : "tinyMceEditor",
				                      content_css : "' . $this->cssPath . '",
				                      width : "' . $this->width . '",
				                      height : "' . $this->height . '",
				                      setup : function(ed) {
				                      	ed.onInit.add(function(ed) {
				                      		editor_' . $this->formField . '_ready_to_use = true;
				                      	});
				                      }';
	
				// Coming soon
				// spellchecker_languages : "+English=en,French=fr,German=de,Italian=it,Spanish=es",
	
				if ($this->editorType == 'full') {
					$returnString = '';
					
					/*// WARNING: Make sure the javascript library only gets outputted once on the page, or IE gets confused
					if (! strpos(ob_get_contents(), 'tiny_mce.js')) {
						ob_start();
						$returnString = '<script language="javascript" type="text/javascript" src="' . $CFG->wwwroot . '/mod/rafl/tiny_mce/tiny_mce.js"></script>';
					}*/
	
					$returnString .= '<script language="javascript" type="text/javascript">
								var editor_' . $this->formField . '_ready_to_use = false;
	
					                  	tinyMCE.init({
					                  		editor_selector : "' . $this->formField . '",
					                  		imagemanager_insert_template : \'<img src="{$url}" width="{$custom.width}" height="{$custom.height}" />\',' .
					                  		$tinyMceFullConfig .
					                  	'});
					                 </script>
					                 <textarea name="' . $this->formField . '" id="' . $this->formField . '" class="' . $this->formField . '">' . $this->content . '</textarea>';
				} elseif ($this->editorType == 'fullinplace') {
					//$returnString = 'var editor_' . $this->formField . '_ready_to_use = false;
					//                 tinymce_advanced_options ={'. $tinyMceFullConfig . '}; tinyMCE.init(tinymce_advanced_options);';

					$returnString = '';
				} else {
					$returnString = "Error: The editor type that you specified is not supported.";
				}

				return $returnString;
			}
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Display the proprietary javascript command for testing that the editor
		//       has finished all it's initialization i.e. when it's ready to use.
		// Depd: -
		//----------------------------------------------------------------------------------------------

		function getEditorReadyToUseJavascriptVariable() {
			if (! strlen($this->formField)) {
				alert("Error: Please use the setFormField('fieldName') method to set what the name of the form field is you want to create.");
			} elseif ($this->editorType == 'full') {
				return 'editor_' . $this->formField . '_ready_to_use';
			}
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Display the proprietary javascript command for the editor to get content from the editor's textarea
		// Depd: -
		//----------------------------------------------------------------------------------------------

		function getContentViaJavascript() {
			if (! strlen($this->formField)) {
				alert("Error: Please use the setFormField('fieldName') method to set what the name of the form field is you want to create.");
			} elseif ($this->editorType == 'full') {
				return "tinyMCE.get('" . $this->formField . "').getContent()";
			}
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Display the proprietary javascript command for the editor to set content for the editor's textarea
		// Depd: -
		//----------------------------------------------------------------------------------------------

		function setContentViaJavascript($argEditorContent) {
			if (! strlen($this->formField)) {
				alert("Error: Please use the setFormField('fieldName') method to set what the name of the form field is you want to create.");
			} elseif ($this->editorType == 'full') {
				// Pass at least an empty string to the javascript
				if (! strlen($argEditorContent)) {
					$argEditorContent = "''";
				}

				return "tinyMCE.get('" . $this->formField . "').setContent(" . $argEditorContent . ")";
			}
		}

		//----------------------------------------------------------------------------------------------
		// Desc: Properties
		// Depd: -
		//----------------------------------------------------------------------------------------------

		function setFormField($argInput) {
			$this->formField = $argInput;
		}

		function setEditorType($argInput) {
			$this->editorType = $argInput;
		}

		function setContent($argInput) {
			$this->content = $argInput;
		}

		function setWidth($argInput) {
			$this->width = $argInput;
		}

		function setHeight($argInput) {
			$this->height = $argInput;
		}

		function setCssPath($argInput) {
			$this->cssPath = $argInput;
		}
	}
?>
