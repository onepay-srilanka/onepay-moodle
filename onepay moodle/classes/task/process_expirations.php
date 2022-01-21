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

namespace enrol_onepay\task;

defined('MOODLE_INTERNAL') || die();

class process_expirations extends \core\task\scheduled_task {

    /**
     * Name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('processexpirationstask', 'enrol_onepay');
    }

    /**
     * Run task for processing expirations.
     */
    public function execute() {
        $enrol = enrol_get_plugin('onepay');
        $trace = new \text_progress_trace();
        $enrol->process_expirations($trace);
    }

}
