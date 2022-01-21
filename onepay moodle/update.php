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

require("../../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/enrollib.php');
require_once($CFG->libdir . '/filelib.php');

global $DB, $CFG;
$data = $_GET['data'];
$user_id = $USER->id;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    
    $json = file_get_contents('php://input');
    $json_data = json_decode($json);
    $transaction_id = $json_data->transaction_id;
    $status = $json_data->status;
    $status_message = $json_data->status_message;
   
if (! $enrol_onepay_tbl = $DB->get_record("enrol_onepay", array("ipg_transaction_id" => $transaction_id))) {
   
   die;
}

  $enroleonepay = new stdClass();
  $enroleonepay->id =$enrol_onepay_tbl->id;
  $enroleonepay->transaction_id= $transaction_id;
  $enroleonepay->payment_status_code= 1;
  $enroleonepay->payment_status= $status_message;
  $ret2=$DB->update_record("enrol_onepay", $enroleonepay, false);

 }else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $id = required_param('id', PARAM_INT); 

    if (! $enrol_onepay_tbl = $DB->get_record("enrol_onepay", array("id" => $id))) {
     print_error("Not a valid  Request"); die;
    }
 
    if( $enrol_onepay_tbl->payment_status_code==1 && $enrol_onepay_tbl->plugin_processed !='no'){
    print_error("Process Faild"); die;
    }
    $transaction_id = $enrol_onepay_tbl->transaction_id;
    $status = $enrol_onepay_tbl->payment_status_code;
    if($transaction_id =="" || $status ==""  ){
    
        $PAGE->set_url($destination);
        echo $OUTPUT->header();
        $a = new stdClass();
        $a->teacher = get_string('defaultcourseteacher');
        $a->fullname = $fullname;
        notice(get_string('paymentsorry', '', $a), $destination);
    }



  if($status=='1'){

    $PAGE->set_context(context_system::instance());

    if (! $enrol_onepay_tbl = $DB->get_record("enrol_onepay", array("ipg_transaction_id" => $transaction_id))) {
    print_error("Not a valid user id"); die;
    }
    $recorded_user = $enrol_onepay_tbl->userid;

    $cust_1 =  explode('-', $enrol_onepay_tbl->custom_1);

    if (empty($cust_1) || count($cust_1) < 3) {
        print_error("Received an invalid payment notification!! (Fake payment????)"); die;
    }
    if($USER->id !=$cust_1[0]){
        print_error("Not a valid user"); die;
    }
    if (! $user = $DB->get_record("user", array("id" => $cust_1[0]))) {
        print_error("Not a valid user id"); die;
    }
    if (! $course = $DB->get_record("course", array("id" => $cust_1[1]))) {
        print_error("Not a valid course id"); die;
    }
    if (! $context = context_course::instance($cust_1[1], IGNORE_MISSING)) {
        print_error("Not a valid context id"); die;
    }

    if (! $plugininstance = $DB->get_record("enrol", array("id" => $cust_1[2], "status" => 0))) {
        print_error("Not a valid instance id"); die;
    }
    $enroleonepay = $userenrolments = $roleassignments = new stdClass();
    $enroleonepay->id = $id;
    $enroleonepay->courseid = $cust_1[1];
    $enroleonepay->userid = $cust_1[0];
    $enroleonepay->instanceid = $cust_1[2];
    $enroleonepay->payment_status_code = $status;

    if($status =='1'){
        $enroleonepay->payment_status = $status_message; //approved
        $PAGE->set_context($context);
        $coursecontext = context_course::instance($course->id, IGNORE_MISSING);
        if ($users = get_users_by_capability($context, 'moodle/course:update', 'u.*', 'u.id ASC',
                                            '', '', '', '', false, true)) {
            $users = sort_by_roleassignment_authority($users, $context);
            $teacher = array_shift($users);
        } else {
            $teacher = false;
        }
        $plugin = enrol_get_plugin('onepay');
        $mailstudents = $plugin->get_config('mailstudents');
        $mailteachers = $plugin->get_config('mailteachers');
        $mailadmins   = $plugin->get_config('mailadmins');
        $shortname = format_string($course->shortname, true, array('context' => $context));

        if (!empty($mailstudents)) {
            $a = new stdClass();
            $a->coursename = format_string($course->fullname, true, array('context' => $coursecontext));
            $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id";

            if ($CFG->version >= 2015051100) {
                $eventdata = new \core\message\message();
            } else {
                $eventdata = new stdClass();
            }
            $eventdata->component         = 'enrol_onepay';
            $eventdata->name              = 'onepay_enrolment';
            //$eventdata->courseid          = $course->id;
            $eventdata->userfrom          = empty($teacher) ? core_user::get_noreply_user() : $teacher;
            $eventdata->userto            = $user;
            $eventdata->subject           = get_string("enrolmentnew", 'enrol', $shortname);
            $eventdata->fullmessage       = get_string('welcometocoursetext', '', $a);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';
            message_send($eventdata);

    }

        if (!empty($mailteachers) && !empty($teacher)) {
            $a->course = format_string($course->fullname, true, array('context' => $coursecontext));
            $a->user = fullname($user);

            if ($CFG->version >= 2015051100) {
                $eventdata = new \core\message\message();
            } else {
                $eventdata = new stdClass();
            }
            $eventdata->component         = 'enrol_onepay';
            $eventdata->name              = 'onepay_enrolment';
            //$eventdata->courseid          = $course->id;
            $eventdata->userfrom          = $user;
            $eventdata->userto            = $teacher;
            $eventdata->subject           = get_string("enrolmentnew", 'enrol', $shortname);
            $eventdata->fullmessage       = get_string('enrolmentnewuser', 'enrol', $a);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';
            message_send($eventdata);
        }

        if (!empty($mailadmins)) {
            $a->course = format_string($course->fullname, true, array('context' => $coursecontext));
            $a->user = fullname($user);
            $admins = get_admins();
            foreach ($admins as $admin) {
            
                if ($CFG->version >= 2015051100) {
                    $eventdata = new \core\message\message();
                } else {
                    $eventdata = new stdClass();
                }
                $eventdata->component         = 'enrol_onepay';
                $eventdata->name              = 'onepay_enrolment';
                //$eventdata->courseid          = $course->id;
                $eventdata->userfrom          = $user;
                $eventdata->userto            = $admin;
                $eventdata->subject           = get_string("enrolmentnew", 'enrol', $shortname);
                $eventdata->fullmessage       = get_string('enrolmentnewuser', 'enrol', $a);
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml   = '';
                $eventdata->smallmessage      = '';
                message_send($eventdata);
            }
        }	
}else if($status =='0'){
	    $enroleonepay->payment_status = $status_message; //Failed
}else {
	    $enroleonepay->payment_status = $status_message; //Other
}
    $ret1 = $DB->update_record("enrol_onepay", $enroleonepay, false);


    if ($plugininstance->enrolperiod) {
    $timestart = time();
    $timeend   = $timestart + $plugininstance->enrolperiod;
    } else {
        $timestart = 0;
        $timeend   = 0;
    }

/* Enrol User */
    $ue_id= $plugin->enrol_user($plugininstance, $user->id, $plugininstance->roleid, $timestart, $timeend);
    $enroleonepay = new stdClass();
    $enroleonepay->id = $id;
    $enroleonepay->plugin_processed = "yes";
    $ret1 = $DB->update_record("enrol_onepay", $enroleonepay, false);
    echo '<script type="text/javascript">
        window.location.href="'.$CFG->wwwroot.'/enrol/onepay/return.php?id='.$cust_1[1].'";
        </script>';
    die;
  }else {
    $cust_1 =  explode('-', $enrol_onepay_tbl->custom_1);
    echo '<script type="text/javascript">
    window.location.href="'.$CFG->wwwroot.'/enrol/onepay/return.php?id='.$cust_1[1].'";
  </script>';
die; 
  }
}
 ?>