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
defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_onepay_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018053000) {

        // Define field instanceid to be added to enrol_onepay.
        // For some reason, some Moodle instances that are upgraded from old versions do not have this field.
        $table = new xmldb_table('enrol_onepay');
        $field = new xmldb_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'userid');

        // Conditionally launch add field instanceid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // onepay savepoint reached.
        upgrade_plugin_savepoint(true, 2018053000, 'enrol', 'onepay');
    }

    if ($oldversion < 2018062500) {

        // Define key courseid (foreign) to be added to enrol_onepay.
        $table = new xmldb_table('enrol_onepay');
        $key = new xmldb_key('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));

        // Launch add key courseid.
        $dbman->add_key($table, $key);

        // onepay savepoint reached.
        upgrade_plugin_savepoint(true, 2018062500, 'enrol', 'onepay');
    }

    if ($oldversion < 2018062501) {

        // Define key userid (foreign) to be added to enrol_onepay.
        $table = new xmldb_table('enrol_onepay');
        $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch add key userid.
        $dbman->add_key($table, $key);

        // onepay savepoint reached.
        upgrade_plugin_savepoint(true, 2018062501, 'enrol', 'onepay');
    }

    if ($oldversion < 2018062502) {

        // Define key instanceid (foreign) to be added to enrol_onepay.
        $table = new xmldb_table('enrol_onepay');
        $key = new xmldb_key('instanceid', XMLDB_KEY_FOREIGN, array('instanceid'), 'enrol', array('id'));

        // Launch add key instanceid.
        $dbman->add_key($table, $key);

        // onepay savepoint reached.
        upgrade_plugin_savepoint(true, 2018062502, 'enrol', 'onepay');
    }

    if ($oldversion < 2018062503) {

        $table = new xmldb_table('enrol_onepay');

        // Define index merchant_id (not unique) to be added to enrol_onepay.
        $index = new xmldb_index('merchant_id', XMLDB_INDEX_NOTUNIQUE, array('merchant_id'));

        // Conditionally launch add index merchant_id.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        /*
        // Define index receiver_email (not unique) to be added to enrol_onepay.
        $index = new xmldb_index('receiver_email', XMLDB_INDEX_NOTUNIQUE, array('receiver_email'));

        // Conditionally launch add index receiver_email.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }*/

        // onepay savepoint reached.
        upgrade_plugin_savepoint(true, 2018062503, 'enrol', 'onepay');
    }

    return true;
}
