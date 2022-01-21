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

namespace enrol_onepay\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy Subsystem implementation for enrol_onepay.
 */
class provider implements
        // Transactions store user data.
        \core_privacy\local\metadata\provider,

        // The onepay enrolment plugin contains user's transactions.
        \core_privacy\local\request\plugin\provider,

        // This plugin is capable of determining which users have data within it.
        \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_external_location_link(
            'onepay.lk',
            [
                'os0'        => 'privacy:metadata:enrol_onepay:onepay_lk:os0',
                'custom'     => 'privacy:metadata:enrol_onepay:onepay_lk:custom',
                'first_name' => 'privacy:metadata:enrol_onepay:onepay_lk:first_name',
                'last_name'  => 'privacy:metadata:enrol_onepay:onepay_lk:last_name',
                'address'    => 'privacy:metadata:enrol_onepay:onepay_lk:address',
                'city'       => 'privacy:metadata:enrol_onepay:onepay_lk:city',
                'email'      => 'privacy:metadata:enrol_onepay:onepay_lk:email',
                'country'    => 'privacy:metadata:enrol_onepay:onepay_lk:country',
            ],
            'privacy:metadata:enrol_onepay:onepay_lk'
        );

        // The enrol_onepay has a DB table that contains user data.
        $collection->add_database_table(
                'enrol_onepay',
                [
                    'merchant_id'           => 'privacy:metadata:enrol_onepay:enrol_onepay:merchant_id',
                    'app_id'           => 'privacy:metadata:enrol_onepay:enrol_onepay:app_id',
                    'app_token'           => 'privacy:metadata:enrol_onepay:enrol_onepay:app_token',
                    'hash_salt'           => 'privacy:metadata:enrol_onepay:enrol_onepay:hash_salt',
                    'order_id'              => 'privacy:metadata:enrol_onepay:enrol_onepay:order_id',
                    'payment_id'            => 'privacy:metadata:enrol_onepay:enrol_onepay:payment_id',
                    'onepay_currency'      => 'privacy:metadata:enrol_onepay:enrol_onepay:onepay_currency',
                    'onepay_amount'        => 'privacy:metadata:enrol_onepay:enrol_onepay:onepay_amount',
                    'status_code'           => 'privacy:metadata:enrol_onepay:enrol_onepay:status_code',
                    'method'                => 'privacy:metadata:enrol_onepay:enrol_onepay:method',
                    'status_message'        => 'privacy:metadata:enrol_onepay:enrol_onepay:status_message',
                    'card_holder_name'      => 'privacy:metadata:enrol_onepay:enrol_onepay:card_holder_name',
                    'card_holder_no'        => 'privacy:metadata:enrol_onepay:enrol_onepay:card_holder_no',
                    'card_holder_expiry'    => 'privacy:metadata:enrol_onepay:enrol_onepay:card_holder_expiry',
                    'custom_1'              => 'privacy:metadata:enrol_onepay:enrol_onepay:custom_1',
                    'local_md5sig'          => 'privacy:metadata:enrol_onepay:enrol_onepay:local_md5sig',
                    'md5sig'                => 'privacy:metadata:enrol_onepay:enrol_onepay:md5sig',
                    'courseid'              => 'privacy:metadata:enrol_onepay:enrol_onepay:courseid',
                    'userid'                => 'privacy:metadata:enrol_onepay:enrol_onepay:userid',
                    'plugin_processed'      => 'privacy:metadata:enrol_onepay:enrol_onepay:plugin_processed',
                    'instanceid'            => 'privacy:metadata:enrol_onepay:enrol_onepay:instanceid'
                ],
                'privacy:metadata:enrol_onepay:enrol_onepay'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new contextlist();

        // Values of ep.receiver_email and ep.business are already normalised to lowercase characters by onepay,
        // therefore there is no need to use LOWER() on them in the following query.
        $sql = "SELECT ctx.id
                  FROM {enrol_onepay} ep
                  JOIN {enrol} e ON ep.instanceid = e.id
                  JOIN {context} ctx ON e.courseid = ctx.instanceid AND ctx.contextlevel = :contextcourse
                  JOIN {user} u ON u.id = ep.userid
                 WHERE u.id = :userid";
        $params = [
            'contextcourse' => CONTEXT_COURSE,
            'userid'        => $userid,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_course) {
            return;
        }

        // Values of ep.receiver_email and ep.business are already normalised to lowercase characters by onepay,
        // therefore there is no need to use LOWER() on them in the following query.
        $sql = "SELECT u.id
                  FROM {enrol_onepay} ep
                  JOIN {enrol} e ON ep.instanceid = e.id
                  JOIN {user} u ON ep.userid = u.id
                 WHERE e.courseid = :courseid";
        $params = ['courseid' => $context->instanceid];

        $userlist->add_from_sql('id', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        // Values of ep.receiver_email and ep.business are already normalised to lowercase characters by onepay,
        // therefore there is no need to use LOWER() on them in the following query.
        $sql = "SELECT ep.*
                  FROM {enrol_onepay} ep
                  JOIN {enrol} e ON ep.instanceid = e.id
                  JOIN {context} ctx ON e.courseid = ctx.instanceid AND ctx.contextlevel = :contextcourse
                  JOIN {user} u ON u.id = ep.userid
                 WHERE ctx.id {$contextsql} AND u.id = :userid
              ORDER BY e.courseid";

        $params = [
            'contextcourse' => CONTEXT_COURSE,
            'userid'        => $user->id,
            'emailuserid'   => $user->id,
        ];
        $params += $contextparams;

        // Reference to the course seen in the last iteration of the loop. By comparing this with the current record, and
        // because we know the results are ordered, we know when we've moved to the onepay transactions for a new course
        // and therefore when we can export the complete data for the last course.
        $lastcourseid = null;

        $strtransactions = get_string('transactions', 'enrol_onepay');
        $transactions = [];
        $onepayrecords = $DB->get_recordset_sql($sql, $params);
        foreach ($onepayrecords as $onepayrecord) {
            if ($lastcourseid != $onepayrecord->courseid) {
                if (!empty($transactions)) {
                    $coursecontext = \context_course::instance($onepayrecord->courseid);
                    writer::with_context($coursecontext)->export_data(
                            [$strtransactions],
                            (object) ['transactions' => $transactions]
                    );
                }
                $transactions = [];
            }

            $transaction = (object) [
                'merchant_id'           => $onepayrecord->merchant_id,
                'order_id'              => $onepayrecord->order_id,
                'payment_id'            => $onepayrecord->payment_id,
                'onepay_currency'      => $onepayrecord->onepay_currency,
                'onepay_amount'        => $onepayrecord->onepay_amount,
                'status_code'           => $onepayrecord->status_code,
                'method'                => $onepayrecord->method,
                'status_message'        => $onepayrecord->status_message,
                'card_holder_name'      => $onepayrecord->card_holder_name,
                'card_holder_no'        => $onepayrecord->card_holder_no,
                'card_holder_expiry'    => $onepayrecord->card_holder_expiry,
                'custom_1'              => $onepayrecord->custom_1,
                'local_md5sig'          => $onepayrecord->local_md5sig,
                'md5sig'                => $onepayrecord->md5sig,
                'courseid'              => $onepayrecord->courseid,
                'userid'                => $onepayrecord->userid,
                'instanceid'            => $onepayrecord->instanceid,
                'plugin_processed'      => $onepayrecord->plugin_processed,
                'timeupdated'           => \core_privacy\local\request\transform::datetime($onepayrecord->timeupdated),
            ];

            $transactions[] = $onepayrecord;

            $lastcourseid = $onepayrecord->courseid;
        }
        $onepayrecords->close();

        // The data for the last activity won't have been written yet, so make sure to write it now!
        if (!empty($transactions)) {
            $coursecontext = \context_course::instance($onepayrecord->courseid);
            writer::with_context($coursecontext)->export_data(
                    [$strtransactions],
                    (object) ['transactions' => $transactions]
            );
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \context_course) {
            return;
        }

        $DB->delete_records('enrol_onepay', array('courseid' => $context->instanceid));
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        $contexts = $contextlist->get_contexts();
        $courseids = [];
        foreach ($contexts as $context) {
            if ($context instanceof \context_course) {
                $courseids[] = $context->instanceid;
            }
        }

        list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);

        $select = "userid = :userid AND courseid $insql";
        $params = $inparams + ['userid' => $user->id];
        $DB->delete_records_select('enrol_onepay', $select, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $userids = $userlist->get_userids();

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $params = ['courseid' => $context->instanceid] + $userparams;

        $select = "courseid = :courseid AND userid $usersql";
        $DB->delete_records_select('enrol_onepay', $select, $params);
    }
}
