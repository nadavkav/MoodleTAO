<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Dynamic CSS styles for the rafl pages
	// Depd: -
	// Auth: Various
	//----------------------------------------------------------------------------------------------

	// Override file extension
	header('Content-Type: text/css');

	$user_bright_colour = '06599B';
	$user_dark_colour = '06599B';
?>

/* body {
  background-color: #FFFFFF;
  margin: 0px;
  padding: 0px;
  font-family: Arial;
  font-size: 12px;
  color: #000000;
} */

#raflWrapper {
    margin-left: auto;
    margin-right: auto;
    width: 595px;
}

/* #footer {
  position: fixed;
  left: 0px;
  bottom: 0px;
  width: 100%;
  height: 24px;
  z-index: 1;
  overflow: hidden;
  background: url(images/footer_bg.gif) no-repeat right bottom #<?php echo $user_dark_colour; ?>;
}

#footer_realsmart {
  position: absolute;
  bottom: 6px;
  left: 42px;
}

#footer_smartassess {
  position: absolute;
  bottom: 6px;
  right: 104px;
}

#footer a {
  font-size: 10px;
  font-weight: bold;
  color: #ffffff;
  text-decoration: none;
}

#footer a:hover {
  font-size: 10px;
  font-weight: bold;
  color: #ffffff;
  text-decoration: underline;
} */

/******************************************** General HTML tag styles ********************************************/

form {
  margin: 0px;
}

input[type="button"] {
  display: inline;
}

a img {
  border: 0;
}

a {
  color: #DE3727;
  text-decoration: none;
  outline: none;
}

a:hover {
  color: #DE3727;
  text-decoration: underline;
}

h1 {
  margin-bottom: 6px;
}

/******************************************** Section-specific styles ********************************************/

#content h1,
h1.breadcrumb {
  font-size: 14px;
  font-weight: bold;
  color: #<?php echo $user_dark_colour; ?>;
  margin-top: 0;
  padding-top: 5px;
  padding-left: 2px;
  padding-bottom: 3px;
}

#content h3 span {
  color: #ffffff;
  margin: 0 3px;
}

table.successTable {
  width: 571px;
  margin-bottom: 16px;
  background: #FFFFFF;
}

table.successTable th {
  text-align: left;
  color: #ffffff;
  font-size: 14px;
  padding: 4px;
  padding-left: 6px;
  font-weight: normal;
}

table.datatable th.overview_students {
  background: #<?php echo $user_bright_colour; ?>;
}

table.datatable th.overview_task,
table.datatable th.overview_overall {
  background: #<?php echo $user_dark_colour; ?>;
}

table.datatable td.overview2_desc,
table.datatable td.overview2_link,
table.datatable td.overview2_desc a,
table.datatable td.overview2_link a {
  color: #<?php echo $user_dark_colour; ?>;
}

table.successTable th.task_a {
  font-size: 11px;
  font-weight: bold;
  width: 251px;
  background: #<?php echo $user_bright_colour; ?>;
}

table.successTable td.task_a {
  width: 251px;
}

table.successTable th.task_b {
  font-size: 11px;
  font-weight: bold;
  width: 419px;
  background: #<?php echo $user_bright_colour; ?>;
}

table.successTable td.task_b {
  width: 419px;
}

table.successTable th.guidance, table.successTable th.evidence, table.successTable th.progress {
  font-size: 11px;
  font-weight: bold;
  width: 52px;
  background: #<?php echo $user_dark_colour; ?>;
}

table.successTable th.who_is {
  font-size: 11px;
  font-weight: bold;
  width: 170px;
  background: #<?php echo $user_dark_colour; ?>;
}

table.successTable select.who_is {
  width: 160px;
}

table.successTable td {
  font-size: 14px;
  padding: 4px;
  border: 1px solid #c9cacc;
}

table.cohortTable,
table.cohortSuccessBlogTable {
  width: 672px;
  margin-bottom: 17px;
}

table.cohortTable td {
  font-size: 14px;
  padding-left: 3px;
  padding-right: 3px;
  padding-top: 0px;
  padding-bottom: 0px;
  border: 1px solid #c9cacc;
}

table.cohortSuccessBlogTable td {
  font-size: 14px;
  padding-left: 3px;
  padding-right: 3px;
  padding-top: 4px;
  padding-bottom: 4px;
  border: 1px solid #c9cacc;
}

table.cohortTable th,
table.cohortSuccessBlogTable th {
  font-size: 11px;
  font-weight: bold;
  color: #FFFFFF;
  background: #<?php echo $user_bright_colour; ?>;
}

table.cohortTable td.cohortTick {
  text-align: center;
}

div#unitGuide,
div#taskGuide,
div.guidanceContainer,
div#evidence {
}

div#unitGuide a,
div#taskGuide a,
div.guidanceContainer a,
div#evidence a,
table.cohortSuccessBlogTable a,
body.tinyMceEditor a {
  color: #0000FF;
  text-decoration: underline;
}

.datatable {
  width: 672px;
  table-layout: fixed;
}

.datatable td {
  padding: 1px;
  border: 1px solid #c9cacc;
}

.datatable th {
  padding: 2px;
  color: #ffffff;
  text-align:left;
}

/******************************************** Dynamic evidence + guidance sections ********************************************/

#evidence {
  margin: 2px;
}

.popupHead {
  background: #<?php echo $user_bright_colour; ?>;
  padding: 5px;
  margin-bottom: 2px;
  width: 561px;
}

.popupHead p {
  margin: 0;
  padding-bottom: 2px;
  color: #ffffff;
  font-size: 11px;
  font-weight: bold;
}

.popupHead img {
  float: right;
  cursor: pointer;
}

#taskGuide,
#unitGuide,
#successGuide,
#evidence,
#commentDiv {
  width: 653px;
}

#taskText,
#unitGuideText,
#successText,
#evidenceText,
#commentText {
  width: 559px;
  font-size: 14px;
  padding-left: 5px;
  padding-right: 5px;
  padding-top: 0px;
  padding-bottom: 0px;
  margin-bottom: 17px;
  border: 1px solid #c9cacc;
}

/******************************************** Comments dynamic styles ********************************************/

div.comment_head h2 {
  margin: 0px;
  padding: 0px;
  font-size: 20px;
  font-weight: bold;
  color: #<?php echo $user_dark_colour; ?>;
}

div.comment_info {
  clear: left;
  padding: 0px;
  font-weight: bold;
  color: #<?php echo $user_bright_colour; ?>;
}

div.comment_content {
  float: left;
  margin-top: 15px;
  width: 465px;
}
