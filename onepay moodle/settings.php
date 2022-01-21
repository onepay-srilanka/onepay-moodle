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

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    //--- settings ------------------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_onepay_settings', '', get_string('pluginname_desc', 'enrol_onepay')));

    $settings->add(new admin_setting_configtext('enrol_onepay/appid', get_string('app_id', 'enrol_onepay'), get_string('merchant_id_desc', 'enrol_onepay'), '', PARAM_ALPHANUM));
    $settings->add(new admin_setting_configtext('enrol_onepay/apptoken', get_string('merchant_secret', 'enrol_onepay'), get_string('merchant_secret_desc', 'enrol_onepay'), ''));
    $settings->add(new admin_setting_configtext('enrol_onepay/hashsalt', get_string('hash_salt', 'enrol_onepay'), get_string('onepaysalt_desc', 'enrol_onepay'), '', PARAM_ALPHANUM));
    $settings->add(new admin_setting_configcheckbox('enrol_onepay/mailstudents', get_string('mailstudents', 'enrol_onepay'), '', 0));
    $settings->add(new admin_setting_configcheckbox('enrol_onepay/mailteachers', get_string('mailteachers', 'enrol_onepay'), '', 0));
    $settings->add(new admin_setting_configcheckbox('enrol_onepay/mailadmins', get_string('mailadmins', 'enrol_onepay'), '', 0));

   $options = array(
        "0" => get_string('yes'),
        "1" => get_string('no'),
    );
    $safe_description = get_string('allowreenrol_desc', 'enrol_onepay');
    if ($safe_description == '[[ allowreenrol_desc ]]'){
        $safe_description = 'Allow users to re-enrol, even after their enrolment has expired. Only applies to users who\'s records are not removed after enrolment has expired';
    }
    $settings->add(new admin_setting_configselect(
            'enrol_onepay/allowreenrol', 
            get_string('allowreenrol', 'enrol_onepay'), 
            $safe_description,
            "1",
            $options
        )
    );

    // Note: let's reuse the ext sync constants and strings here, internally it is very similar,
    //       it describes what should happen when users are not supposed to be enrolled any more.
    $options = array(
        ENROL_EXT_REMOVED_KEEP           => get_string('extremovedkeep', 'enrol'),
        ENROL_EXT_REMOVED_SUSPENDNOROLES => get_string('extremovedsuspendnoroles', 'enrol'),
        ENROL_EXT_REMOVED_UNENROL        => get_string('extremovedunenrol', 'enrol'),
    );
    $settings->add(new admin_setting_configselect('enrol_onepay/expiredaction', get_string('expiredaction', 'enrol_onepay'), get_string('expiredaction_help', 'enrol_onepay'), ENROL_EXT_REMOVED_SUSPENDNOROLES, $options));

    //--- enrol instance defaults ----------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_onepay_defaults',
        get_string('enrolinstancedefaults', 'admin'), get_string('enrolinstancedefaults_desc', 'admin')));

    $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                     ENROL_INSTANCE_DISABLED => get_string('no'));
    $settings->add(new admin_setting_configselect('enrol_onepay/status',
        get_string('status', 'enrol_onepay'), get_string('status_desc', 'enrol_onepay'), ENROL_INSTANCE_DISABLED, $options));

    $settings->add(new admin_setting_configtext('enrol_onepay/cost', get_string('cost', 'enrol_onepay'), '', 0, PARAM_FLOAT, 4));

    $onepaycurrencies = enrol_get_plugin('onepay')->get_currencies();
    $settings->add(new admin_setting_configselect('enrol_onepay/currency', get_string('currency', 'enrol_onepay'), '', 'LKR', $onepaycurrencies));

    if (!during_initial_install()) {
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(new admin_setting_configselect('enrol_onepay/roleid',
            get_string('defaultrole', 'enrol_onepay'), get_string('defaultrole_desc', 'enrol_onepay'), $student->id, $options));
    }

    $settings->add(new admin_setting_configduration('enrol_onepay/enrolperiod',
        get_string('enrolperiod', 'enrol_onepay'), get_string('enrolperiod_desc', 'enrol_onepay'), 0));
}
