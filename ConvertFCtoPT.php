<?php
use Drupal\Core\Entity\Display\EntityDisplayInterface;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\views\Entity\View;
use Drupal\views\Plugin\views\filter\NumericFilter;
use Drupal\views\Plugin\views\filter\StringFilter;
use Drupal\views\Views;
use Drupal\field_group;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\paragraphs; 
use Drupal\paragraphs\Entity; 
use Drupal\paragraphs\Entity\Paragraph; 


function ConvertFCtoPT() {
	try {
		_convertFCtoPT();
		clear_cache_container();
	} catch (Exception $e) {
		clear_cache_container();
	}
	
}


function _convertFCtoPT() {
	$doInstallNewStoredProcedures = true;
	$uininstallInitially = false;
	$installmodules = false;
	$pocessfctopt = false;
	$doConfigUpdate = false;
	
	
	if ($doInstallNewStoredProcedures) {
		//drush_print('SQL 1' );
		$module_path = drupal_get_path('module', 'ffxdrushcommands');
		$stored_procedures = array('sp_dbosubstr.sql');

		foreach ($stored_procedures as $file) {

			$query = \Drupal::database()->query("SELECT * FROM sys.objects where name like 'SUBSTR_TESTING' and type='FN'");
			$records = $query->fetchAll();
			if (count($records)==0) {

				drush_print("Create Stored Procedure");

//				\Drupal::database()->query('CREATE FUNCTION [dbo].[SUBSTR_TESTING] 	( @op1 nvarchar(max), @op2 sql_variant) RETURNS nvarchar(max) BEGIN RETURN CAST(SUBSTRING(CAST(@op1 AS nvarchar(max)), 0, CAST(@op2 AS int)) AS nvarchar(max))', [], ['allow_delimiter_in_query' => TRUE,]);
				$statement = "CREATE FUNCTION [dbo].[SUBSTR_TESTING] (@op1 nvarchar(max), @op2 sql_variant) RETURNS nvarchar(max) BEGIN RETURN CAST(SUBSTRING(CAST(@op1 AS nvarchar(max)), 0, CAST(@op2 AS int)) AS nvarchar(max))";

				drush_print("Create Stored Procedure Statement Complete");
				$query1 = \Drupal::database()->query($statement);
				
				drush_print("Execute Create Stored Procedure");
				$records1 = $query1->execute();
				//NOT GETTING HERE!!!
				drush_print(print_r($records1,1));	
				drush_print("Create Stored Procedure Complete");

				
			}

	
		}
	}

	// $doAssignPermissions = false;
	// $doAddToContentTypes = true;
	
	
	//Migration failed with source plugin exception: SQLSTATE[42000]: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]Incorrect syntax near the keyword &#039;FROM&#039;. C:\Projects\Fairfax\temp\wizardwidgetdrush\WWW\drivers\lib\Drupal\Driver\Database\sqlsrv\Connection.php line 536
	
	//TODO: Update TFS to remove 
	
	// Remove directories from \core\modules\menu_link_content
	// • migrations
	// • migration_templates
	// • Tests
	
	//TODO: Add code which creates dbo stored procedures 
	

	if ($uininstallInitially) { 
		drush_print('Uninstall field_collection_to_paragraphs Module' );
		drush_invoke_process(null,'pmu', array('migrate_tools'), array('quiet' => 0, 'yes' => TRUE));
		drush_invoke_process(null,'pmu', array('migrate_plus'), array('quiet' => 0, 'yes' => TRUE));
		drush_invoke_process(null,'pmu', array('migrate_drupal'), array('quiet' => 0, 'yes' => TRUE));
		drush_invoke_process(null,'pmu', array('migrate'), array('quiet' => 0, 'yes' => TRUE));
		drush_invoke_process(null,'pmu', array('field_collection_to_paragraphs'), array('quiet' => 0, 'yes' => TRUE));
		
	}
	
	if ($installmodules) { 
		drush_print('Installing Modules' );
		drush_invoke_process(null,'en', array('migrate'), array('quiet' => 0, 'yes' => TRUE));
		drush_invoke_process(null,'en', array('migrate_drupal'), array('quiet' => 0, 'yes' => TRUE));
		drush_invoke_process(null,'en', array('migrate_plus'), array('quiet' => 0, 'yes' => TRUE));
		drush_invoke_process(null,'en', array('migrate_tools'), array('quiet' => 0, 'yes' => TRUE));
		drush_invoke_process(null,'en', array('field_collection_to_paragraphs'), array('quiet' => 0, 'yes' => TRUE));
	}

	if ($pocessfctopt) {
		//drush migrate-import field_collection_to_paragraph_parent_node,field_collection_to_paragraph_parent_taxonomy_term --execute-dependencies
		//STOP:
		//drush -l http://office-for-children migrate-stop field_collection_to_paragraph_parent_node
		//ROLLBACK
		//drush -l http://office-for-children migrate-rollback field_collection_to_paragraph_parent_node --all
		
	// drush_print('STOPPING Migrations' );
		// drush_invoke_process(null,'migrate-stop',array('field_collection_to_paragraph_item'));
		// drush_invoke_process(null,'migrate-stop',array('field_collection_to_paragraph_type'));
		// drush_invoke_process(null,'migrate-stop',array('field_collection_to_paragraph_parent_node'));
		// drush_invoke_process(null,'migrate-stop',array('field_collection_to_paragraph_parent_paragraph'));
		
	// drush_print('ROLLINGBACK Migrations' );
		// drush_invoke_process(null,'migrate-rollback',array('field_collection_to_paragraph_item','--all'));
		// drush_invoke_process(null,'migrate-rollback',array('field_collection_to_paragraph_type','--all'));
		// drush_invoke_process(null,'migrate-rollback',array('field_collection_to_paragraph_field_instance','--all'));
		// drush_invoke_process(null,'migrate-rollback',array('field_collection_to_paragraph_field_storage','--all'));
		// drush_invoke_process(null,'migrate-rollback',array('field_collection_to_paragraph_parent_field_storage','--all'));
		
	drush_print('IMPORT Rollback' );
		drush_invoke_process(null,'migrate-rollback',array('field_collection_to_paragraph_parent_node','--all'));
		drush_invoke_process(null,'migrate-rollback',array('field_collection_to_paragraph_parent_paragraph','--all'));
		
	drush_print('IMPORT Migrations' );
		drush_invoke_process(null,'migrate-import',array('field_collection_to_paragraph_parent_node','--execute-dependencies'));
		drush_invoke_process(null,'migrate-import',array('field_collection_to_paragraph_parent_paragraph','--execute-dependencies'));
				
	drush_print('Migrations Complete' );
	}

	if ($doConfigUpdate) {
		drush_print('Updating Structures' );
		drush_invoke_process(null, 'cim', array("--partial"), array('source' => 'modules/ffxdrushcommands/config/fctopt/'));
	}
	if ($doAssignPermissions) {
		//assign permissions
		//PT_assignPermissions('wizard_widget_content');
		//PT_assignPermissions('wizard_widget_menu');
	}
	

	drush_print('Complete' );
}


function clear_cache_container() {
	//entry in here does not get cleared with drush cr and causes the site to not come backup
	 db_query("delete from cache_container");
}

//assign permissions
function PT_assignPermissions($paragraphType){

	drush_invoke_process(null,'role-add-perm', array('content_contributor','create paragraph content ' . $paragraphType,'--cache-clear=0'));
	drush_invoke_process(null,'role-add-perm', array('content_contributor','delete paragraph content ' . $paragraphType,'--cache-clear=0'));
	drush_invoke_process(null,'role-add-perm', array('content_contributor','update paragraph content ' . $paragraphType,'--cache-clear=0'));
	
	drush_invoke_process(null,'role-add-perm', array('content_manager','create paragraph content ' . $paragraphType,'--cache-clear=0'));
	drush_invoke_process(null,'role-add-perm', array('content_manager','delete paragraph content ' . $paragraphType,'--cache-clear=0'));
	drush_invoke_process(null,'role-add-perm', array('content_manager','update paragraph content ' . $paragraphType,'--cache-clear=0'));
	
	drush_invoke_process(null,'role-add-perm', array('multisite_admin','create paragraph content ' . $paragraphType,'--cache-clear=0'));
	drush_invoke_process(null,'role-add-perm', array('multisite_admin','delete paragraph content ' . $paragraphType,'--cache-clear=0'));
	drush_invoke_process(null,'role-add-perm', array('multisite_admin','update paragraph content ' . $paragraphType,'--cache-clear=0'));
	
	drush_invoke_process(null,'role-add-perm', array('anonymous','view paragraph content ' . $paragraphType,'--cache-clear=0'));
	drush_invoke_process(null,'role-add-perm', array('authenticated','view paragraph content ' . $paragraphType,'--cache-clear=0'));
	
}

//add to content types
function PT_addToContentTypes(){
	PT_updateWidgetsForContentType('node','event','event',0) ;

}

function PT_updateWidgetsForContentType($entity_type, $bundle_type, $field_name, $enableTopicPageAnchor) {
	
	//update widget field properties
    $field = FieldConfig::loadByName($entity_type, $bundle_type, $field_name);
	
	if ($field==null) {	
		error_log('ERROR: ' . $field_name . ' Not Found in ' . $bundle_type);
		return;
	}
	
	$field->setSettings(array(
			'handler' => 'default:paragraph',
			'handler_settings' => array
			(
					'target_bundles' => array
					(
						'field_ffx_address' => 'field_ffx_address',
						'field_ffx_address_paragraph' => 'field_ffx_address_paragraph',
						'field_contact_information' => 'field_contact_information',
						'field_contact_name' => 'field_contact_name',
						'field_cost' => 'field_cost',
						'field_end_date' => 'field_end_date',
						'field_description' => 'field_description',
						'field_ffx_eventtype' => 'field_ffx_eventtype',
						'field_fairfax_resource_library' => 'field_fairfax_resource_library',
						'field_fairfax_resource_library_p' => 'field_fairfax_resource_library_p',
						'field_ffx_featureditem' => 'field_ffx_featureditem',
						'field_ffx_imageanchor' => 'field_ffx_imageanchor',
						'field_link_to_event' => 'field_link_to_event',
						'field_link_to_registration' => 'field_link_to_registration',
						'field_locationref' => 'field_locationref',
						'field_ffx_locationviewmode' => 'field_ffx_locationviewmode',
						'field_ffx_fcmetadata' => 'field_ffx_fcmetadata',
						'field_ffx_fcmetadata_paragraph' => 'field_ffx_fcmetadata_paragraph',
						'field_deploy_info' => 'field_deploy_info',
						'field_deploy_info_paragraph' => 'field_deploy_info_paragraph',
						'field_ffx_showenddatetime' => 'field_ffx_showenddatetime',
						'field_show_globally' => 'field_show_globally',
						'field_ffx_showstartdatetime' => 'field_ffx_showstartdatetime',
						'field_start_date' => 'field_start_date',
						'field_image' => 'field_image',
						'field_urlcomputed' => 'field_urlcomputed',
						'field_ffx_pagewidgets' => 'field_ffx_pagewidgets',

						/**
						'accordion' => 'accordion',
						'ffx_calloutsectionwidget' => 'ffx_calloutsectionwidget',
						'contact' => 'contact',
						'ffx_singlecolorblock' => 'ffx_singlecolorblock',
						'ffx_content_section_with_link' => 'ffx_content_section_with_link',
						'ffx_pa_departmentsearch' => 'ffx_pa_departmentsearch',
						'ffx_eventswidget' => 'ffx_eventswidget',
						'ffx_pa_horizontalmenu' => 'ffx_pa_horizontalmenu',
						'ffx_pa_generic' => 'ffx_pa_generic',
						'ffx_pa_iconlistmenu' => 'ffx_pa_iconlistmenu',
						'ffx_locationwidget' => 'ffx_locationwidget',
						'ffx_mini_content_box' => 'ffx_mini_content_box',
						'ffx_pa_newsevents' => 'ffx_pa_newsevents',
						'ffx_newswidget' => 'ffx_newswidget',
						'ffx_pa_slideshow' => 'ffx_pa_slideshow',
						'ffx_socialmedia' => 'ffx_socialmedia',
						'ffx_tabbedcontent' => 'ffx_tabbedcontent',
						'ffx_pa_tabbedmenu' => 'ffx_pa_tabbedmenu',
						//'ffx_pa_topicpageanchor' => 'ffx_pa_topicpageanchor',
						'wizard_widget_menu' => 'wizard_widget_menu',
						 */
					),

					'target_bundles_drag_drop' => array
					(
						'field_ffx_address' => array
							(
								'enabled' => 1,
								'weight' => -47,
							)
						
					)
			)
		)
	)->save();
	
	
	drush_print('Events Registration ParagraphType has been added to ' .  $bundle_type);
	
}