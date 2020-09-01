<?php
namespace Scit\Attendence;
use \Scit\Ssp;
/**
 * Pugin Name : Attendance
 Plugin URI 
 Vision: 1.0.0
 Auther : Amal
 */
class Attendence 
{
	public static function start()
	{
		// add_action('wp_enqueue_style',array(get_called_class(),'assets'));
		add_action("admin_menu",array(get_called_class(),"custom_admin_menu"));
		add_action('admin_enqueue_scripts',array(get_called_class(),'enque_bootstrap_library'));
		add_action('admin_enqueue_scripts',array(get_called_class(),'enque_calander_library'));
		add_action('admin_enqueue_scripts',array(get_called_class(),'enque_multiselect_library'));
		add_action('admin_enqueue_scripts',array(get_called_class(),'enque_custom_library'));
		add_action( 'init',array(get_called_class(),'ajax_script_enqueuer'));
		add_action( 'init',array(get_called_class(),'ajax_script_enqueuer'));
		add_action( 'init',array(get_called_class(),'ajax_script_enqueuer'));
		add_action("wp_ajax_create_checkin",array(get_called_class(), "create_checkin"));
		add_action("wp_ajax_get_checkin_list",array(get_called_class(), "get_checkin_list"));
		add_action("wp_ajax_delete_checkin",array(get_called_class(), "delete_checkin"));
		add_action("init",array(get_called_class(), "edit_submit"));
		// add_action("wp_ajax_nopriv_my_user_vote", "my_must_login");

	}

	public static function custom_admin_menu ()
	{
	   	add_menu_page('Attendence', 'Attendence', 'manage_options', 'attendence-slug',array( get_called_class(), 'content'));
	   	add_submenu_page('attendence-slug', 'Attendence', 'Attendence', 'manage_options', 'attendence-slug', array( get_called_class(), 'content'));
	}

	public static function ajax_script_enqueuer() {
	   wp_register_script( "ajax_attendance_script", get_template_directory_uri().'/classes/Scit/Attendence/script.js', array('jquery') );
	   wp_localize_script( 'ajax_attendance_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
	   wp_enqueue_script( 'jquery' );
	   wp_enqueue_script( 'ajax_attendance_script' );

	}

	public static function enque_bootstrap_library(  ) {

	  wp_enqueue_style('bootstrapstyle',get_template_directory_uri().'/assets/bootstrap/css/bootstrap.min.css');
	  wp_enqueue_script('bootstrapjs',get_template_directory_uri().'/assets/bootstrap/js/bootstrap.min.js');

	  wp_enqueue_script('datatable',get_template_directory_uri().'/classes/Scit/Attendence/DataTables/datatables.js');
	  wp_enqueue_style('datatable',get_template_directory_uri().'/classes/Scit/Attendence/DataTables/datatables.css');

	  wp_enqueue_script('datatable',get_template_directory_uri().'/classes/Scit/Attendence/DataTables/Buttons/js/buttons.bootstrap.js');
	  wp_enqueue_style('datatable',get_template_directory_uri().'/classes/Scit/Attendence/DataTables/Buttons/js/buttons.bootstrap.css');
	 	
	  wp_enqueue_style('jqueryui','https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

	}


	public static function enque_calander_library(  ) {
		
	  	wp_enqueue_style('calnderjs',get_template_directory_uri().'/classes/Scit/Attendence/Calander/lib/main.css');
	  	wp_enqueue_script('calandercss',get_template_directory_uri().'/classes/Scit/Attendence/Calander/lib/main.js');

	}

	public static function enque_multiselect_library()
	{
		wp_enqueue_script('timepickerjs',get_template_directory_uri().'/classes/Scit/Attendence/timepicker/src/jquery.ptTimeSelect.js');
	  	wp_enqueue_style('timepickercss',get_template_directory_uri().'/classes/Scit/Attendence/timepicker/src/jquery.ptTimeSelect.css');

	  	wp_enqueue_style('multiselectcss',get_template_directory_uri().'/classes/Scit/Attendence/MultiSelect/jquery.multiselect.css');
	  	wp_enqueue_script('multiselectjs',get_template_directory_uri().'/classes/Scit/Attendence/MultiSelect/jquery.multiselect.js');
	}

	public static function enque_custom_library()
	{
	  wp_enqueue_style('customcss',get_template_directory_uri().'/classes/Scit/Attendence/style.css');
	}
	
	public static function content()
	{
		require_once 'content.php';
	}

	public static function create_checkin()
	{
			global $wpdb;
			if(isset($_GET["checkin"]) && $_GET["checkout"]  && $_GET["user_id_list"])
			{

					$checkin=strtotime($_GET["checkin"]);
					$checkout=strtotime($_GET["checkout"]);
					$date_value=strtotime($_GET["date_value"]);

					$user_id_list=$_GET["user_id_list"];
					$create_date=strtotime(date("Y-m-d"));
					$failed_count=0;
					$success_count=0;
					$total_count=0;
					foreach ($user_id_list as $key => $value) {
						$total_count++;
		        		$res=$wpdb->get_results("select * from  wp_attendancem where (date='$date' AND check_in='$check_in' AND check_out='$check_out' AND user_id='$value')");
		        		// if(count($res)>0)
		        		// {
							$data=array("user_id"=>$value,"check_in"=>$checkin,"check_out"=>$checkout,"date"=>$date_value,"c_date"=>$create_date);
							$wpdb->insert("wp_attendancem",$data);
							$success_count++;
		        		// }
		        		// else
		        		// {
		        		// 	$failed_count++;
		        		// }
						
					}
					
					// echo "Total $k records ,insert failed records $i,insert succes records $j";
			}
	}
	public static function get_checkin_list()
	{
		global $wpdb;

		$order=$_GET["order"] ;
		$date=strtotime($_GET["date"]) ;
		$search=$_GET["search"] ;
		$start=$_GET["start"] ;
		$length=$_GET["length"] ;

		$filter_query = "SELECT id as 'SI',(SELECT user_login FROM wp_users WHERE ID=A.user_id) as 'Name', FROM_UNIXTIME(check_in ,'%h:%i %p') 'Check in',FROM_UNIXTIME(check_out,'%h:%i %p')'Check Out',FROM_UNIXTIME(date,'%d-%m-%Y') as 'Date','<a href=javascript:; class=delete >Delete</a>' AS 'Delete','<a href=javascript:; class=edit >Edit</a>' AS 'Edit' FROM `wp_attendancem` A WHERE `date`='$date' ORDER BY id  asc LIMIT ".$start." ,".$length." ";

		$filter_res=$wpdb->get_results($filter_query);
		$total_query = "SELECT * FROM `wp_attendancem` WHERE `date`='$date' ";
		$total_res=$wpdb->get_results($total_query);

		$json_data = array(
			"draw"            => $_GET['draw'] ,   
			"recordsTotal"    => COUNT($total_res),  
			"recordsFiltered" => COUNT($total_res),
			"data"            => $filter_res   // total data array
			);

		echo json_encode($json_data);
		exit;
	}
	public static function delete_checkin()
	{
			global $wpdb;
		if(isset($_GET["id"]))
		{
			$id=$_GET["id"];
			$sql="DELETE FROM wp_attendancem WHERE  id='$id'";
			$filter_res=$wpdb->query($sql);
			if($filter_res)
			{
				return "success";
			}
			else
			{
				return "failed";

			}
		}
	}

	public static function edit_checkin()
	{
			global $wpdb;
		if(isset($_GET["id"]))
		{
			$id=$_GET["id"];
			$sql="DELETE FROM wp_attendancem WHERE  id='$id'";
			$filter_res=$wpdb->query($sql);

			if($filter_res)
			{
				return "success";
			}
			else
			{
				return "failed";

			}
		}
	}
	public static function edit_submit()
	{
		global $wpdb;
		if(isset($_GET["edit_submit"]))
		{

			$id=$_GET["id"];
			$checkin=strtotime($_GET["checkin"]);
			$checkout=strtotime($_GET["checkout"]);
			$sql="UPDATE `wp_attendancem` SET check_in='$checkin',check_out='$checkout' WHERE  id='$id'";
			$filter_res=$wpdb->query($sql);
   			wp_redirect(  "http://localhost/wp_local/wp-admin/admin.php?page=attendence-slug" );

		}
	}
}
Attendence::start();
?>