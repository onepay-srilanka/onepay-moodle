<?php
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

/**
 * onepay enrolment plugin version specification.
 *
 * @package    enrol_onepay
 * @copyright  2022 SPEMAI (PVT) LTD.
 * @author     SPEMAI (PVT) LTD.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_DEBUG_DISPLAY', true);
//@ini_set('display_errors', '1'); // NOT FOR PRODUCTION SERVERS!
//$CFG->debug = 32767;         // NOT FOR PRODUCTION     SERVERS! // for Moodle 2.0 - 2.2, use:  $CFG->debug = 38911;  
//$CFG->debugdisplay = true;   // NOT FOR PRODUCTION SERVERS!
require("../../config.php");
require_once("lib.php");
//require_once($CFG->libdir.'/eventslib.php');
require_once($CFG->libdir.'/enrollib.php');
require_once($CFG->libdir . '/filelib.php');

require_login();

global $DB, $CFG,$USER, $PAGE;

if (empty($_POST) or !empty($_GET)) {
    print_error("Sorry,cannot process."); die;
}

 /*$use_sandbox = $this->get_config('onepaysandbox');
    $onepayurl = '';
    if ($use_sandbox){
        $onepayurl = 'https://sandbox.onepay.lk/pay/checkout';
    }
    else {
        $onepayurl = 'https://www.onepay.lk/pay/checkout';
    }*/
   $plugin = enrol_get_plugin('onepay'); 
  
   
   $app_id = get_config('enrol_onepay','appid');
   $app_token = get_config('enrol_onepay','apptoken');
   $hash_salt = get_config('enrol_onepay','hashsalt');
  
   if(empty($app_id) || $app_id=="" || empty($app_token) || $app_token==""|| empty($hash_salt) || $hash_salt=="" ){
    print_error("Sorry,Please Contact System Administrator."); die;
   }

$PAGE->set_pagelayout('admin');
$PAGE->set_url($CFG->wwwroot.'/enrol/onepay/process.php');
echo $OUTPUT->header();
echo $OUTPUT->heading("Your Payment is in Process....");
echo $OUTPUT->heading("Don't Reload or Leave This Page. This Page Will Automatically Redirect You To The Course Page. ");
echo $OUTPUT->footer();
    $course_id = $_POST['course_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $email = $USER->email/*$_POST['email']*/;
    $custom_1 = $_POST['custom_1'];
    $merchant_id = $_POST['merchant_id'];
    $cust_1 =  explode('-',$custom_1);
    if (! $plugininstance = $DB->get_record("enrol", array("id" => $cust_1[2]))) {
      
    }

    $onepay_currency = $plugininstance ->currency;

    $order_timestamp = time();
    $order_id = "onepay_{$USER->id}_{$course->id}_{$order_timestamp}";
    $onepay_amount = (float) $_POST['cost'];
    
    
    $reference = "1234567898";
	$destination = "$CFG->wwwroot/course/view.php?id=$course_id";
  if($app_id =="" || $app_token =="" || $hash_salt =="" ){
	  
	  
	  $PAGE->set_url($destination);
    echo $OUTPUT->header();
    $a = new stdClass();
    $a->teacher = get_string('defaultcourseteacher');
    $a->fullname = $fullname;
    notice(get_string('paymentsorry', '', $a), $destination);
  }


$enroleonepay = new stdClass();

$enroleonepay->method= "POST";
$enroleonepay->courseid= $_POST['course_id'];
$enroleonepay->custom_1= $custom_1;
$enroleonepay->customer_first_name= $first_name;
$enroleonepay->customer_last_name= $last_name;
$enroleonepay->customer_email= $email;
$enroleonepay->mobile= $phone;
$enroleonepay->timeupdated= time();
$ret1 = $DB->insert_record("enrol_onepay", $enroleonepay,true);


$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$reference= substr(str_shuffle($permitted_chars), 0,10);

$onepay_args= array (
  'amount' => $onepay_amount,
  'app_id' => $app_id,
  'currency'=>$onepay_currency,
  'reference' => $reference,
  'customer_first_name' => $first_name,
  'customer_last_name' => $last_name,
  'customer_phone_number' => $phone,
  'customer_email' => $email,
  'transaction_redirect_url' =>$CFG->wwwroot.'/enrol/onepay/update.php?id='.$ret1.'&c_id='.rand(0, 10000)  
  
);
$data=json_encode($onepay_args,JSON_UNESCAPED_SLASHES);
$data_1 = $data.$hash_salt;
$hash_result = hash('sha256',$data_1);
$onepayurl ='https://merchant-api-live-v2.onepay.lk/api/ipg/gateway/request-transaction/';

$curl = curl_init();

$url = 'https://merchant-api-live-v2.onepay.lk/api/ipg/gateway/request-transaction/?hash=';
$url .= $hash_result;

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_POSTFIELDS => $data,
  CURLOPT_HTTPHEADER => array(
    'Authorization:'."".$app_token,
    'Content-Type:application/json'
  ),
));

$response = curl_exec($curl);


curl_close($curl);
$result = json_decode($response, true);

$status = $result['status'];
$message = $result['message'];
$ret_data = $result['data'];
$ipg_transaction_id = $ret_data['ipg_transaction_id'];

$amount_data = $ret_data['amount'];
$gross_amount = $amount_data['gross_amount'];
$discount = $amount_data['discount'];
$handling_fee = $amount_data['handling_fee'];
$net_amount = $amount_data['net_amount'];
$currency = $amount_data['currency'];

$gateway = $ret_data['gateway'];
$redirect_url = $gateway['redirect_url'];

if($status =='1000'){
	
  $enroleonepay = new stdClass();
  $enroleonepay->id = $ret1;
  $enroleonepay->ipg_transaction_id= $ipg_transaction_id;
  $enroleonepay->order_id= 1000;
  $enroleonepay->onepay_currency= $currency;
  $enroleonepay->onepay_amount= $onepay_amount;
  $enroleonepay->status_code= $status;
  $enroleonepay->payment_status_code= 2;
  $enroleonepay->status_message= $message;
  $ret2=$DB->update_record("enrol_onepay", $enroleonepay, false);

 echo '<script type="text/javascript">
             window.location.href="'.$redirect_url.'";
             </script>';
}else{
  $ret3 = $DB->delete_records("enrol_onepay", array('id'=>$ret1));
  notice("Unable to process your payment.Please Contact your system administrator : status Code: $status", $destination);
  exit;

}



 ?>