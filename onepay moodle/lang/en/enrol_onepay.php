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
 * @copyright  2021 onepay (Pvt.) Ltd.
 * @author     onepay (Pvt.) Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['onepaysalt'] = 'Hash Salt';
$string['hash_salt'] = 'Hash Salt';
$string['onepaysalt_desc'] = 'The Hash Salt of your onepay Account';
$string['app_id'] = 'App ID';
$string['assignrole'] = 'Assign role';
$string['merchant_id'] = 'onepay Merchant ID';
$string['merchant_id_desc'] = 'The Merchant ID of your onepay Merchant Account';
$string['app_token'] = 'App Token';
$string['merchant_secret'] = 'App Token';
$string['merchant_secret_desc'] = 'The Merchant Secret of your onepay Merchant Account';
$string['onepaysandbox'] = 'Use onepay Sandbox';
$string['cost'] = 'Enrol cost';
$string['costerror'] = 'The enrolment cost is not numeric';
$string['costorkey'] = 'Please choose one of the following methods of enrolment.';
$string['allowreenrol'] = 'Allow Re-enrol';
$string['allowreenrol_desc'] = 'Allow users to re-enrol, even after their enrolment has expired. Only applies to users who\'s records are not removed after enrolment has expired';
$string['allowreenrol_help'] = 'Allow users to re-enrol themselves if enrolment status is expired.';
$string['currency'] = 'Currency';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during onepay enrolments';
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can be enrolled until this date only.';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrolment is valid. If set to zero, the enrolment duration will be unlimited by default.';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user is enrolled. If disabled, the enrolment duration will be unlimited.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can be enrolled from this date onward only.';
$string['errdisabled'] = 'The onepay enrolment plugin is disabled and does not handle payment notifications.';
$string['erripninvalid'] = 'Instant payment notification has not been verified by onepay.';
$string['erronepayconnect'] = 'Could not connect to {$a->url} to verify the instant payment notification: {$a->result}';
$string['expiredaction'] = 'Enrolment expiry action';
$string['expiredaction_help'] = 'Select action to carry out when user enrolment expires. Please note that some user data and settings are purged from course during course unenrolment.';
$string['mailadmins'] = 'Notify admin';
$string['mailstudents'] = 'Notify students';
$string['mailteachers'] = 'Notify teachers';
$string['messageprovider:onepay_enrolment'] = 'onepay enrolment messages';
$string['nocost'] = 'There is no cost associated with enrolling in this course!';
$string['onepay:config'] = 'Configure onepay enrol instances';
$string['onepay:manage'] = 'Manage enrolled users';
$string['onepay:unenrol'] = 'Unenrol users from course';
$string['onepay:unenrolself'] = 'Unenrol self from the course';
$string['onepayaccepted'] = 'onepay payments accepted';
$string['pluginname'] = 'onepay';
$string['pluginname_desc'] = 'The onepay module allows you to set up paid courses.  If the cost for any course is zero, then students are not asked to pay for entry.  There is a site-wide cost that you set here as a default for the whole site and then a course setting that you can set for each course individually. The course cost overrides the site cost.';
$string['privacy:metadata:enrol_onepay:enrol_onepay'] = 'Information about the onepay transactions for onepay enrolments.';
$string['privacy:metadata:enrol_onepay:enrol_onepay:business'] = 'Email address or onepay account ID of the payment recipient (that is, the merchant).';
$string['privacy:metadata:enrol_onepay:enrol_onepay:courseid'] = 'The ID of the course that is sold.';
$string['privacy:metadata:enrol_onepay:enrol_onepay:instanceid'] = 'The ID of the enrolment instance in the course.';
$string['privacy:metadata:enrol_onepay:enrol_onepay:item_name'] = 'The full name of the course that its enrolment has been sold.';
$string['privacy:metadata:enrol_onepay:enrol_onepay:memo'] = 'A note that was entered by the buyer in onepay website payments note field.';
$string['privacy:metadata:enrol_onepay:enrol_onepay:option_selection1_x'] = 'Full name of the buyer.';
$string['privacy:metadata:enrol_onepay:enrol_onepay:parent_txn_id'] = 'In the case of a refund, reversal, or canceled reversal, this would be the transaction ID of the original transaction.';
$string['privacy:metadata:enrol_onepay:enrol_onepay:payment_status'] = 'The status of the payment.';
$string['privacy:metadata:enrol_onepay:enrol_onepay:payment_type'] = 'Holds whether the payment was funded with an eCheck (echeck), or was funded with onepay balance, credit card, or instant transfer (instant).';
$string['privacy:metadata:enrol_onepay:enrol_onepay:pending_reason'] = 'The reason why payment status is pending (if that is).';
$string['privacy:metadata:enrol_onepay:enrol_onepay:reason_code'] = 'The reason why payment status is Reversed, Refunded, Canceled_Reversal, or Denied (if the status is one of them).';
$string['privacy:metadata:enrol_onepay:enrol_onepay:receiver_email'] = 'Primary email address of the payment recipient (that is, the merchant).';
$string['privacy:metadata:enrol_onepay:enrol_onepay:receiver_id'] = 'Unique onepay account ID of the payment recipient (i.e., the merchant).';
$string['privacy:metadata:enrol_onepay:enrol_onepay:tax'] = 'Amount of tax charged on payment.';
$string['privacy:metadata:enrol_onepay:enrol_onepay:timeupdated'] = 'The time of Moodle being notified by onepay about the payment.';
$string['privacy:metadata:enrol_onepay:enrol_onepay:txn_id'] = 'The merchant\'s original transaction identification number for the payment from the buyer, against which the case was registered';
$string['privacy:metadata:enrol_onepay:enrol_onepay:userid'] = 'The ID of the user who bought the course enrolment.';
$string['privacy:metadata:enrol_onepay:onepay_com'] = 'The onepay enrolment plugin transmits user data from Moodle to the onepay website.';
$string['privacy:metadata:enrol_onepay:onepay_com:address'] = 'Address of the user who is buying the course.';
$string['privacy:metadata:enrol_onepay:onepay_com:city'] = 'City of the user who is buying the course.';
$string['privacy:metadata:enrol_onepay:onepay_com:country'] = 'Country of the user who is buying the course.';
$string['privacy:metadata:enrol_onepay:onepay_com:custom'] = 'A hyphen-separated string that contains ID of the user (the buyer), ID of the course, ID of the enrolment instance.';
$string['privacy:metadata:enrol_onepay:onepay_com:email'] = 'Email address of the user who is buying the course.';
$string['privacy:metadata:enrol_onepay:onepay_com:first_name'] = 'First name of the user who is buying the course.';
$string['privacy:metadata:enrol_onepay:onepay_com:last_name'] = 'Last name of the user who is buying the course.';
$string['privacy:metadata:enrol_onepay:onepay_com:os0'] = 'Full name of the buyer.';
$string['processexpirationstask'] = 'onepay enrolment send expiry notifications task';
$string['sendpaymentbutton'] = 'Send payment via onepay';
$string['status'] = 'Allow onepay enrolments';
$string['status_desc'] = 'Allow users to use onepay to enrol into a course by default.';
$string['transactions'] = 'onepay transactions';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
