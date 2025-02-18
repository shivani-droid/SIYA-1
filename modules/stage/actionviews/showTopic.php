<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT  - DO NOT REMOVE THIS NOTICE                      //
//                                                                       //
// OPENTADKA FRAMEWORK											         //
//          http://www.opentadka.org                                     //
//                                                                       //
// Copyright (C) 2010 onwards  Manu Sharma  http://www.opentadka.org     //
//                                                                       //
// STUDENT INFORMATION YARN (SIYA)								         //
//          http://www.siya.org.in                                       //
//                                                                       //
// Copyright (C) 2012 onwards  Manu Sharma  http://www.siya.org.in       //
//                                                                       //
// OPENTADKA FRAMEWORK LICENSE :                                         //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
// STUDENT INFORMATION YARN (SIYA) LICENSE :                             //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
//   OPENTADKA FRAMEWORK & STUDENT INFORMATION YARN (SIYA)               //
//   FOR LICENCESPLEASE REFER LICENCE PAGE                               //
//   FOR MORE DETAILS                                                    //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
?>	


<?php

	$id = _ACTION_VIEW_PARAMETER_ID;
	
	
	$selected_batch_id = (isset($_SESSION['batchid']))?$_SESSION['batchid']:$_SESSION['defaultbatchid'];

	$columns = array('g.id = groupid','g.grouptypetag','g.name = groupname','c.id = chapterid','c.name = chaptername','s.id = subjectid','s.name = subjectname');
	$conditions = array();

	$tables = array();
	$tables['chapters'] = 'c';
	$tables['subjects'] = 's';
	$tables['groups'] = 'g';
	$tables['topics'] = 't';


	$conditions['=']['t.id'] = $id;
	$conditions['K AND =']['t.chapterid'] = 'c.id';
	$conditions['K AND =']['c.subjectid'] = 's.id';
	$conditions['K AND =']['s.groupid'] = 'g.id';



	$sqlObj = new MainSQL();

	$sql = $sqlObj->SQLCreatorJ('S', $tables, $columns, $conditions, '', '', '');

	if($result = $sqlObj->FireSQL($sql)){
	if($sqlObj->getNumRows($result) !=0){ 
	if($resultset = $sqlObj->FetchResult($result)){
	$groupid_placeholder = $sqlObj->getCleanData($resultset->groupid);
	$groupname_placeholder = $sqlObj->getCleanData($resultset->groupname);
	$grouptypetag_placeholder = $sqlObj->getCleanData($resultset->grouptypetag);
	$subjectid_placeholder = $sqlObj->getCleanData($resultset->subjectid);
	$subjectname_placeholder = $sqlObj->getCleanData($resultset->subjectname);	
	$chapterid_placeholder = $sqlObj->getCleanData($resultset->chapterid);
	$chaptername_placeholder = $sqlObj->getCleanData($resultset->chaptername);

	$urlforgroup='stage/showClass/'.$groupid_placeholder.'/';
	$urlforsubject='stage/showSubject/'.$subjectid_placeholder.','.$groupid_placeholder.'/';
	$urlforchapter='stage/showSubject/'.$chapterid_placeholder.','.$groupid_placeholder.'/';
	
	//////////////////////////////////////////////////////////////////////////////////////
	// 	Action Permissions can be controlled by the Controller, but here the 			//
	//  Group Permissions can be checked and the action can be taken accordingly 		//
	//////////////////////////////////////////////////////////////////////////////////////

	MainSystem::CheckGroupPermissions($groupid_placeholder,'group');

	?>
	
	<br /><a class="buttonsfortitles" href="<?php echo MainSystem::URLCreator($urlforgroup); ?>">Class : <?php echo $grouptypetag_placeholder.' ['.$groupname_placeholder.']'; ?></a><br /><br /><a class="buttonsfortitles" href="<?php echo MainSystem::URLCreator($urlforsubject); ?>"><?php echo $subjectname_placeholder; ?></a><br /><br /><a class="buttonsfortitles" href="<?php echo MainSystem::URLCreator($urlforchapter); ?>"><?php echo $chaptername_placeholder; ?></a><br />
										
	<?php
	}
	}else{
	trigger_error('Data Fetch Error');
	die;
	}
	}else{
	trigger_error('Data Fetch Error');
	die;
	}	
	?>


<table width="100%" height="100%">
<tr>
	<td width="50%" valign="top">
	<br />
	<p class="buttonsfortitles4">Topic Contents</p>
	<br />

	<?php
	$columns = array('id','contenttype','title','filename');
	$conditions = array();
	$conditions['=']['topicid'] = $id;
	$sqlObj = new MainSQL();
	$sql = $sqlObj->SQLCreator('S', 'topiccontentsuploads', $columns, $conditions, '', '', '');

	if($result = $sqlObj->FireSQL($sql)){
	if($sqlObj->getNumRows($result) !=0){ 
	while($resultset = $sqlObj->FetchResult($result)){
	$content_id = $sqlObj->getCleanData($resultset->id);
	$contenttype = $sqlObj->getCleanData($resultset->contenttype);
	$contenttitle = $sqlObj->getCleanData($resultset->title);
	$contentfilename = $sqlObj->getCleanData($resultset->filename);
	$applicationiconfile = topiccontents::returnApplicationIcons($contentfilename);
	$applicationtype = topiccontents::returnApplicationType($contentfilename);

	$download_files_extentions_array = array('doc','rtf','xls','ppt','docx','xlsx','pptx','rar','zip');
	
	if($contenttype=='FLASH' || $contenttype=='VIDEO'){
	$file_open_url = 'topiccontents/openTopicContentVideo/'.$content_id.','.$groupid_placeholder.'/';
	}else{
	$file_open_url = 'topiccontents/openTopicContentDoc/'.$content_id.','.$groupid_placeholder.'/';
	}

	if(in_array($applicationtype,$download_files_extentions_array)){
	$open_url_txt = '';
	}else{
	$open_url_txt = '<a class="buttonsfortopiccontents" href="'.MainSystem::URLCreator($file_open_url).'">Open</a>';
	}



	?>
	<a href="#"><img src="<?php echo PROJ_MODULES_WWW_DIR._WS.'topiccontents'._WS.'images'._WS.$applicationiconfile; ?>" /><?php echo $contenttitle; ?></a><br /><?php echo $open_url_txt; ?> <a class="buttonsfortopiccontents" href="<?php echo MainSystem::URLCreator('topiccontents/downloadTopicContentFile/'.$content_id.'/'); ?>">Download</a>
	<?php 
		if(isset($_SESSION['controllers']['SHOWCONTROLS']) && $_SESSION['controllers']['SHOWCONTROLS']==1){
		?>
		<a href="<?php echo MainSystem::URLCreator('topiccontents/editTopicContent/'.$content_id.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_editblockcontent.png'; ?>" alt="Edit" title="Edit" /></a>
		<a href="<?php echo MainSystem::URLCreator('topiccontents/deleteFile/'.$content_id.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_deleteblockcontent.png'; ?>" alt="Delete" title="Delete" /></a>
		<a href="<?php echo MainSystem::URLCreator('topiccontents/changeTopicContentsStatus/'.$content_id.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_inactive.png'; ?>" alt="Make this inactive" title="Make this inactive" /></a>
		<a href="<?php echo MainSystem::URLCreator('topiccontents/moveTopicContents/'.$content_id.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_moveupblock.png'; ?>" alt="Move this up" title="Move this up" /></a>
		<a href="<?php echo MainSystem::URLCreator('topiccontents/moveTopicContents/'.$content_id.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_movedownblock.png'; ?>" alt="Move this down" title="Move this down" /></a></p>
		<?php
		}
		?>
	<br /><br />
	<?php
	}
	}
	}
	?>

	<?php
	$columns = array('id','contenttype','title','data');
	$conditions = array();
	$conditions['=']['topicid'] = $id;
	$sqlObj = new MainSQL();
	$sql = $sqlObj->SQLCreator('S', 'topiccontentsdata', $columns, $conditions, '', '', '');

	if($result = $sqlObj->FireSQL($sql)){
	if($sqlObj->getNumRows($result) !=0){ 
	while($resultset = $sqlObj->FetchResult($result)){
	$content_id = $sqlObj->getCleanData($resultset->id);
	$contenttype = $sqlObj->getCleanData($resultset->contenttype);
	$contenttitle = $sqlObj->getCleanData($resultset->title);
	$contentfilename = '.html';
	$applicationiconfile = topiccontents::returnApplicationIcons($contentfilename);
	
	
	if($contenttype=='HTML' || $contenttype=='LINK'){
	$file_open_url = 'topiccontents/openTopicContentHTML/'.$content_id.','.$groupid_placeholder.'/';
	}else{
	$file_open_url = 'topiccontents/openTopicContentDoc/'.$content_id.','.$groupid_placeholder.'/';
	}

	?>
	<a href="#"><img src="<?php echo PROJ_MODULES_WWW_DIR._WS.'topiccontents'._WS.'images'._WS.$applicationiconfile; ?>" /><?php echo $contenttitle; ?></a><br /><a class="buttonsfortopiccontents" href="<?php echo MainSystem::URLCreator($file_open_url); ?>">Open this Content</a><br /><br />
	<?php
	}
	}
	}
	?>
	
	<?php
	if(PROJ_RUN_AJAX==1){
	$formaction = MainSystem::URLCreator('topiccontents/addTopicContent/'.$id.'/','ajax','post','',PROJ_AJAX_DEFAULT_HTML_ID_FOR_TEMPLATE,false);
	}else{
	$formaction = MainSystem::URLCreator('topiccontents/addTopicContent/'.$id.'/');
	}


	$returnarrayblockaddaccess = MainSystem::CheckModuleActionAccess('admin','topiccontents','addTopicContent');
	if($returnarrayblockaddaccess['noerror'] == 1){

	?>
		<form id="addformtopiccontents" name="addformtopiccontents" method="post" action="<?php echo $formaction; ?>">


		<fieldset>

		<legend>Add Content For This Topic</legend>

		<ol>
			
		<li style="height:150px;">
		<label class="label_radio" for="contenttype1"><input name="contenttype" id="contenttype1" value="DOC" type="radio" class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required" />Add Document</label>
		<label class="label_radio" for="contenttype2"><input name="contenttype" id="contenttype2" value="VIDEO" type="radio" />Add Video</label>
		<label class="label_radio" for="contenttype3"><input name="contenttype" id="contenttype3" value="FLASH" type="radio" />Add Flash (Animation or Video)</label>
		<label class="label_radio" for="contenttype4"><input name="contenttype" id="contenttype4" value="HTML" type="radio" />Create HTML Content</label>
		<label class="label_radio" for="contenttype5"><input name="contenttype" id="contenttype5" value="LINK" type="radio" />Add Link</label>
		</li> 
		</ol>
		
		</fieldset>
		
		<fieldset>
		
		<input type="hidden" name="groupid" value="<?php echo $groupid_placeholder; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>

		<input type="hidden" name="add" value="1" />


		<button type="submit">Add Content</button>
		<?php
		}
		?>

		</fieldset>

		</form>	
	</td>
	<td width="50%" valign="top"><br /><p class="buttonsfortitles5">Topic Assignments</p><br />


	<?php
	if(PROJ_RUN_AJAX==1){
	$formaction3 = MainSystem::URLCreator('assignments/addAssignment/','ajax','post','',PROJ_AJAX_DEFAULT_HTML_ID_FOR_TEMPLATE,false);
	}else{
	$formaction3 = MainSystem::URLCreator('assignments/addAssignment/');
	}
	
	
	$returnarrayblockaddaccess = MainSystem::CheckModuleActionAccess('admin','assignments','addAssignment');
	if($returnarrayblockaddaccess['noerror'] == 1){
	
	?>

	<form id="addform2" name="addform2" method="post" action="<?php echo $formaction3; ?>">

	<fieldset>

	<legend>Assignment List</legend>

	<?php
	}
	?>




		<?php
		$columns3 = array('id','name');
		$conditions3 = array();
		$conditions3['=']['subjectid'] = $subjectid_placeholder;
		$conditions3['AND =']['groupid'] = $groupid_placeholder;
		$conditions3['AND =']['chapterid'] = $chapterid_placeholder;
		$conditions3['AND =']['batchid'] = $selected_batch_id;
		$conditions3['AND =']['topicid'] = $id;
		$sqlObj = new MainSQL();
		$sql = $sqlObj->SQLCreator('S', 'assignments', $columns3, $conditions3, '', '', '');

		if($result3 = $sqlObj->FireSQL($sql)){
		if($sqlObj->getNumRows($result3) !=0){ 
		while($resultset3 = $sqlObj->FetchResult($result3)){
		$id_placeholder = $sqlObj->getCleanData($resultset3->id);
		$name_placeholder = $sqlObj->getCleanData($resultset3->name);	
		$url='assignments/showAssignment/'.$id_placeholder.','.$groupid_placeholder.'/';

		?>
		
		<p><a href="<?php echo MainSystem::URLCreator($url); ?>"><?php echo $name_placeholder; ?></a>
		<?php 
		if(isset($_SESSION['controllers']['SHOWCONTROLS']) && $_SESSION['controllers']['SHOWCONTROLS']==1){
		?>
		<a href="<?php echo MainSystem::URLCreator('assignments/editAssignment/'.$id_placeholder.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_editblockcontent.png'; ?>" alt="Edit" title="Edit" /></a>
		<a href="<?php echo MainSystem::URLCreator('assignments/deleteAssignment/'.$id_placeholder.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_deleteblockcontent.png'; ?>" alt="Delete" title="Delete" /></a>
		</p>
		<?php
		}
		?>
		</p>
											
		<?php
		}
		}
		}else{
		trigger_error('Data Fetch Error');
		}		
		?>


		<ol>
			
		<input type="hidden" name="subjectid" value="<?php echo $subjectid_placeholder; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		<input type="hidden" name="groupid" value="<?php echo $groupid_placeholder; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		<input type="hidden" name="chapterid" value="<?php echo $chapterid_placeholder; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		<input type="hidden" name="topicid" value="<?php echo $id; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		<input type="hidden" name="batchid" value="<?php echo $selected_batch_id; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		</ol>
		
		</fieldset>
		
		<fieldset>

		<?php
		$returnarrayblockaddaccess = MainSystem::CheckModuleActionAccess('admin','assignments','addAssignment');
		if($returnarrayblockaddaccess['noerror'] == 1){
		?>

		<button type="submit">Add Assignment</button>

		<?php
		}
		?>

		</fieldset>

	</form>	
	</td>
</tr>
<tr>
	<td width="50%"><br /><p class="buttonsfortitles6">Topic Assessments</p><br />


	<?php
	if(PROJ_RUN_AJAX==1){
	$formaction3 = MainSystem::URLCreator('assessments/addAssessment/','ajax','post','',PROJ_AJAX_DEFAULT_HTML_ID_FOR_TEMPLATE,false);
	}else{
	$formaction3 = MainSystem::URLCreator('assessments/addAssessment/');
	}
	
	$returnarrayblockaddaccess = MainSystem::CheckModuleActionAccess('admin','assessments','addAssessment');
	if($returnarrayblockaddaccess['noerror'] == 1){
	
	?>

	<form id="addform2" name="addform2" method="post" action="<?php echo $formaction3; ?>">
	
	<fieldset>

	<legend>Assessment List</legend>
	<?php
	}
	?>

		<?php
		$columns3 = array('id','name');
		$conditions3 = array();
		$conditions3['=']['topicid'] = $id;
		$conditions3['AND =']['chapterid'] = $chapterid_placeholder;
		$conditions3['AND =']['subjectid'] = $subjectid_placeholder;
		$conditions3['AND =']['groupid'] = $groupid_placeholder;
		$conditions3['AND =']['batchid'] = $selected_batch_id;
		$sqlObj = new MainSQL();
		$sql = $sqlObj->SQLCreator('S', 'assessments', $columns3, $conditions3, '', '', '');

		if($result3 = $sqlObj->FireSQL($sql)){
		if($sqlObj->getNumRows($result3) !=0){ 
		while($resultset3 = $sqlObj->FetchResult($result3)){
		$id_placeholder = $sqlObj->getCleanData($resultset3->id);
		$name_placeholder = $sqlObj->getCleanData($resultset3->name);	
		$url='assessments/showAssessment/'.$id_placeholder.','.$groupid_placeholder.'/';

		?>
		
		<p><a href="<?php echo MainSystem::URLCreator($url); ?>"><?php echo $name_placeholder; ?></a>

		<?php
		if(isset($_SESSION['controllers']['SHOWCONTROLS']) && $_SESSION['controllers']['SHOWCONTROLS']==1){
		?>
		<a href="<?php echo MainSystem::URLCreator('assessments/editAssessment/'.$id_placeholder.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_editblockcontent.png'; ?>" alt="Edit" title="Edit" /></a>
		<a href="<?php echo MainSystem::URLCreator('assessments/deleteAssessment/'.$id_placeholder.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_deleteblockcontent.png'; ?>" alt="Delete" title="Delete" /></a>
		<a href="<?php echo MainSystem::URLCreator('assessments/moveAssessment/'.$id_placeholder.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_moveupblock.png'; ?>" alt="Move this up" title="Move this up" /></a>
		<a href="<?php echo MainSystem::URLCreator('assessments/moveAssessment/'.$id_placeholder.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_movedownblock.png'; ?>" alt="Move this down" title="Move this down" /></a></p>								
		<?php
		}
		?>
		
		<?php
		}
		}
		}else{
		trigger_error('Data Fetch Error');
		}		
		?>


		<ol>
			
		<input type="hidden" name="subjectid" value="<?php echo $subjectid_placeholder; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		<input type="hidden" name="groupid" value="<?php echo $groupid_placeholder; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		<input type="hidden" name="chapterid" value="<?php echo $chapterid_placeholder; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		<input type="hidden" name="topicid" value="<?php echo $id; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		<input type="hidden" name="batchid" value="<?php echo $selected_batch_id; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		</ol>
		
		</fieldset>
		
		<fieldset>

		<?php
		$returnarrayblockaddaccess = MainSystem::CheckModuleActionAccess('admin','assessments','addAssessment');
		if($returnarrayblockaddaccess['noerror'] == 1){
		?>

		<button type="submit">Add Assessment</button>

		<?php
		}
		?>
		</fieldset>

	</form>	</td>
	<td width="50%"><br /><p class="buttonsfortitles7">Topic Tests</p><br />


	<?php
	if(PROJ_RUN_AJAX==1){
	$formaction3 = MainSystem::URLCreator('tests/addTest/','ajax','post','',PROJ_AJAX_DEFAULT_HTML_ID_FOR_TEMPLATE,false);
	}else{
	$formaction3 = MainSystem::URLCreator('tests/addTest/');
	}

	$returnarrayblockaddaccess = MainSystem::CheckModuleActionAccess('admin','tests','addTest');
	if($returnarrayblockaddaccess['noerror'] == 1){
	?>

	<form id="addform2" name="addform2" method="post" action="<?php echo $formaction3; ?>">
		
	<fieldset>

	<legend>Test List</legend>

	<?php
	}
	?>

		<?php
		$columns3 = array('id','name');
		$conditions3 = array();
		$conditions3['=']['subjectid'] = $subjectid_placeholder;
		$conditions3['AND =']['groupid'] = $groupid_placeholder;
		$conditions3['AND =']['chapterid'] = $chapterid_placeholder;
		$conditions3['AND =']['batchid'] = $selected_batch_id;
		$conditions3['AND =']['topicid'] = $id;
		$sqlObj = new MainSQL();
		$sql = $sqlObj->SQLCreator('S', 'tests', $columns3, $conditions3, '', '', '');

		if($result3 = $sqlObj->FireSQL($sql)){
		if($sqlObj->getNumRows($result3) !=0){ 
		while($resultset3 = $sqlObj->FetchResult($result3)){
		$id_placeholder = $sqlObj->getCleanData($resultset3->id);
		$name_placeholder = $sqlObj->getCleanData($resultset3->name);	
		$url='tests/showTest/'.$id_placeholder.','.$groupid_placeholder.'/';

		?>
		
		<p><a href="<?php echo MainSystem::URLCreator($url); ?>"><?php echo $name_placeholder; ?></a>


		<?php
		if(isset($_SESSION['controllers']['SHOWCONTROLS']) && $_SESSION['controllers']['SHOWCONTROLS']==1){
		?>
		<a href="<?php echo MainSystem::URLCreator('tests/editTest/'.$id_placeholder.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_editblockcontent.png'; ?>" alt="Edit" title="Edit" /></a>
		<a href="<?php echo MainSystem::URLCreator('tests/deleteTest/'.$id_placeholder.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_deleteblockcontent.png'; ?>" alt="Delete" title="Delete" /></a>
		<a href="<?php echo MainSystem::URLCreator('tests/moveTest/'.$id_placeholder.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_moveupblock.png'; ?>" alt="Move this up" title="Move this up" /></a>
		<a href="<?php echo MainSystem::URLCreator('tests/moveTest/'.$id_placeholder.'/'); ?>"><img src="<?php echo _TEMPLATE_IMG_DIR._WS.'siya_movedownblock.png'; ?>" alt="Move this down" title="Move this down" /></a>
		</p>
		<?php
		}
		?>
		

		<?php
		}
		}
		}else{
		trigger_error('Data Fetch Error');
		}		
		?>


		<ol>
			
		<input type="hidden" name="subjectid" value="<?php echo $subjectid_placeholder; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		<input type="hidden" name="groupid" value="<?php echo $groupid_placeholder; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		<input type="hidden" name="chapterid" value="<?php echo $chapterid_placeholder; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		<input type="hidden" name="topicid" value="<?php echo $id; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		<input type="hidden" name="batchid" value="<?php echo $selected_batch_id; ?>"  class="<?php echo PROJ_AJAX_HTML_POST_CLASS_NORMAL;?> required"/>
		</ol>
		
		</fieldset>
		
		<fieldset>

		<?php
		$returnarrayblockaddaccess = MainSystem::CheckModuleActionAccess('admin','tests','addTest');
		if($returnarrayblockaddaccess['noerror'] == 1){
		?>

		<button type="submit">Add Test</button>

		<?php
		}
		?>

		</fieldset>

	</form>	</td>
</tr>
</table>

	

	

	


	