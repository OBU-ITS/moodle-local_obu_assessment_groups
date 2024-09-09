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
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * OBU Assessment groups - external library
 *
 * @package    obu_assessment_groups
 * @author     Emir Kamel
 * @copyright  2024, Oxford Brookes University {@link http://www.brookes.ac.uk/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG;

require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot.'/group/externallib.php');

class local_obu_assessment_groups_external extends external_api {

    public static function sync_group_members_parameters() {
        return new external_function_parameters(
            array(
                'courseidnumber' => new external_value(PARAM_TEXT, 'course record id number'),
                'groupidnumber' => new external_value(PARAM_TEXT, 'group record id number'),
                'members' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'userid' => new external_value(PARAM_INT, 'user id'),
                        )
                    )
                )
            )
        );
    }

    public static function sync_group_members_returns() {
        return new external_function_parameters(
            array(
                'result' => new external_value(PARAM_INT, 'Result of the sync group members. 2 = success with issues, 1 = success, 0 = failure, -1 = course not found, -2 = group not found'),
                'issuemembers' => new external_multiple_structure(
                    'userid' => new external_value(PARAM_INT, 'user id'),
                )
            )
        );
    }

    public static function sync_group_members($courseidnumber, $groupidnumber, $members)
    {
        global $CFG, $DB;

        require_once("$CFG->dirroot/grouplib.php");

        $params = self::validate_parameters(self::sync_group_members_parameters(), array(
            'courseidnumber' => $courseidnumber,
            'groupidnumber' => $groupidnumber,
            'members' => $members
        ));

        if (!($course = $DB->get_record('course', array('idnumber' => $params['courseidnumber'])))) {
            return array('result' => -1);
        }

        if (!($group = groups_get_group_by_idnumber($course->id, $params['groupidnumber']))) {
            return array('result' => -2);
        }

        return local_obu_assessment_groups_sync_group_members($group, $members);
    }


    public static function create_group_parameters() {
        return new external_function_parameters(
            array(
                'courseidnumber' => new external_value(PARAM_TEXT, 'Course ID number', true),
                'groupidnumber' => new external_value(PARAM_TEXT, 'Group ID number', true),
                'groupname' => new external_value(PARAM_TEXT, 'Group Name', true),
            )
        );
    }

    public static function create_group_returns() {
        return new external_single_structure(
            array(
                'result' => new external_value(PARAM_INT, 'Result of the group creation. 1 = success, 0 = failure, -1 = course not found'),
                'groupname' => new external_value(PARAM_TEXT, 'The name of the created group, empty if creation failed', VALUE_OPTIONAL),
            )
        );
    }

    public static function create_group($courseidnumber, $groupidnumber, $groupname) {
        global $DB;

        // Context validation
        self::validate_context(context_system::instance());

        // Parameter validation
        $params = self::validate_parameters(
            self::add_session_parameters(), array(
                'courseidnumber' => $courseidnumber,
                'groupidnumber' => $groupidnumber,
                'groupname' => $groupname,
            )
        );

        // Check if the course with the provided courseidnumber exists
        if (!($course = $DB->get_record('course', array('idnumber' => $params['courseidnumber'])))) {
            return array('result' => -1, 'groupname' => ''); // Course not found, return empty groupname
        }

        if ($group = local_obu_assessment_groups_create_group($course, $groupidnumber, $groupname)) {
            return array('result' => 1, 'groupname' => $group->name); // Success, return groupname
        }

        return array('result' => 0, 'groupname' => ''); // Failure, return empty groupname
    }

    public static function delete_group_parameters() {
        return new external_function_parameters(
            array(
                'courseidnumber' => new external_value(PARAM_TEXT, 'Course ID number', true),
                'groupidnumber' => new external_value(PARAM_TEXT, 'Group ID number', true),
            )
        );
    }

    public static function delete_group_returns() {
        return new external_single_structure(
            array(
                'result' => new external_value(PARAM_INT, 'Result of the group creation. 1 = success, 0 = failure, -1 = course not found, -2 = group not found'),
            )
        );
    }

    public static function delete_group($courseidnumber, $groupidnumber) {
        global $DB;

        // Context validation
        self::validate_context(context_system::instance());

        // Parameter validation
        $params = self::validate_parameters(
            self::add_session_parameters(), array(
                'courseidnumber' => $courseidnumber,
                'groupidnumber' => $groupidnumber,
            )
        );

        // Check if the course with the provided courseidnumber exists
        if (!($course = $DB->get_record('course', array('idnumber' => $params['courseidnumber'])))) {
            return array('result' => -1);
        }

        // Check if the group with the provided groupidnumber exists within course
        if (!($group = $DB->get_record('groups', array(
            'idnumber' => $params['groupidnumber'],
            'courseid' => $course->id,
            )))) {
            return array('result' => -2);
        }

        if (local_obu_assessment_groups_delete_group($group)) {
            return array('result' => 1);
        }

        return array('result' => 0);
    }
}