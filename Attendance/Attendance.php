<?php
   /*
   Plugin Name: Attendance
   Plugin URI: http://admin-restrict-by-ip.com
   description:Attendance checkin  
   Version: 1.0
   Author:  Amal
   */


   class Attendance
   {

      public static  function start()
      {
            // add_action( 'init',array(get_called_class(),'activate'));
            // add_action( 'init',array(get_called_class(),'deactivate'));
            // add_action( 'init',array(get_called_class(),'uninstall'));
            add_action("admin_menu",array(get_called_class(),"custom_admin_menu"));
            add_action('admin_enqueue_scripts',array(get_called_class(),'enque_bootstrap_library'));
            add_action('admin_enqueue_scripts',array(get_called_class(),'enque_calander_library'));
            add_action('admin_enqueue_scripts',array(get_called_class(),'enque_multiselect_library'));
            add_action('admin_enqueue_scripts',array(get_called_class(),'enque_custom_library'));
            add_action("wp_ajax_create_checkin",array(get_called_class(), "create_checkin"));
            add_action("wp_ajax_get_checkin_list",array(get_called_class(), "get_checkin_list"));
            add_action("wp_ajax_delete_checkin",array(get_called_class(), "delete_checkin"));
            add_action("init",array(get_called_class(), "edit_submit"));

      }
   public static function activate()
   {
      flush_rewrite_rules();
   }
   public static function deactivate()
   {
      flush_rewrite_rules();
   }
   public static function uninstall()
   {

   }

   public static function custom_admin_menu ()
   {
         add_menu_page('Attendence', 'Attendence', 'manage_options', 'attendence-slug',array( get_called_class(), 'content'));
         // add_submenu_page('attendence-slug', 'Attendence', 'Attendence', 'manage_options', 'attendence-slug', array( get_called_class(), 'content'));
   }

   public static function ajax_script_enqueuer() {
      wp_register_script( "ajax_attendance_script",plugin_dir_url(__FILE__).'/assets/css/script.js', array('jquery') );
      wp_localize_script( 'ajax_attendance_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
      wp_enqueue_script( 'jquery' );
      wp_enqueue_script( 'ajax_attendance_script' );
   }

   public static function enque_bootstrap_library(  ) {

     wp_enqueue_style('bootstrap',plugin_dir_url(__FILE__).'/assets/css/bootstrap.min.css');
     wp_enqueue_script('bootstrap',plugin_dir_url(__FILE__).'/assets/js/bootstrap.min.js');

     wp_enqueue_script('datatable',plugin_dir_url(__FILE__).'/assets/js/datatables.min.js');
     wp_enqueue_style('datatable',plugin_dir_url(__FILE__).'/assets/css/datatables.min.css');

     wp_enqueue_script('datatable_button',plugin_dir_url(__FILE__).'/assets/js/buttons.bootstrap.min.js');
     wp_enqueue_style('datatable_button',plugin_dir_url(__FILE__).'/assets/css/buttons.bootstrap.min.css');
      
     wp_enqueue_style('jqueryui','https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

   }


   public static function enque_calander_library(  ) {
      
      wp_enqueue_style('calnderjs',plugin_dir_url(__FILE__).'/assets/css/calander.min.css');
      wp_enqueue_script('calandercss',plugin_dir_url(__FILE__).'/assets/js/calander.min.js');

   }

   public static function enque_multiselect_library()
   {
      wp_enqueue_script('timepickerjs',plugin_dir_url(__FILE__).'assets/js/jquery.ptTimeSelect.js');
      wp_enqueue_style('timepickercss',plugin_dir_url(__FILE__).'assets/css/jquery.ptTimeSelect.css');

      wp_enqueue_style('multiselectcss',plugin_dir_url(__FILE__).'assets/css/jquery.multiselect.css');
      wp_enqueue_script('multiselectjs',plugin_dir_url(__FILE__).'assets/js/jquery.multiselect.js');
   }

   public static function enque_custom_library()
   {
     wp_enqueue_style('customcss',plugin_dir_url(__FILE__).'/assets/css/style.css');
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
                  //    $failed_count++;
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

   $obj=new Attendance();

   // Activation
   register_activation_hook(__FILE__,array($obj,'activate'));
   // Deactivation
   register_activation_hook(__FILE__,array($obj,'deactivate'));
   // Uninstall
   register_activation_hook(__FILE__,array($obj,'uninstall'));


   Attendance::start();

?>