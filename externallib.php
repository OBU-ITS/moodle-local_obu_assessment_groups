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

//    public static function get_course_groups_parameters() {
//        return new external_function_parameters(
//            array(
//                'groupids' => new external_multiple_structure(new external_value(PARAM_INT, 'Group ID')
//                    ,'List of group ids. A group id is an integer.'),
//            )
//        );
//    }
//
//    public static function get_course_groups_returns() {
//        return new external_multiple_structure(
//            new external_single_structure(
//                array(
//                    'id' => new external_value(PARAM_INT, 'group record id'),
//                    'courseid' => new external_value(PARAM_INT, 'id of course'),
//                    'name' => new external_value(PARAM_TEXT, 'group name'),
//                    'description' => new external_value(PARAM_RAW, 'group description text'),
//                    'descriptionformat' => new external_format_value('description'),
//                    'enrolmentkey' => new external_value(PARAM_RAW, 'group enrol secret phrase'),
//                    'idnumber' => new external_value(PARAM_RAW, 'id number')
//                )
//            )
//        );
//    }
//
//    public static function get_course_groups($groupIds) {
//        // Context validation
//        self::validate_context(context_system::instance());
//
//        // Parameter validation
//        self::validate_parameters(
//            self::add_session_parameters(), array(
//                'groupids' => $groupIds,
//            )
//        );
//
//        if (count($groupIds) == 0) {
//            return array('result' => -1);
//        }
//
//        if ($courseGroups = core_group_external::get_groups($groupIds)) {
//            return $courseGroups;
//        }
//
//        return array('result' => -9);
//    }


//    public static function get_group_members_parameters() {
//        return new external_function_parameters(
//            array(
//                'groupids' => new external_multiple_structure(new external_value(PARAM_INT, 'Group ID')
//                    ,'List of group ids. A group id is an integer.'),
//            )
//        );
//    }
//
//    public static function get_group_members_returns() {
//        return new external_multiple_structure(
//            new external_single_structure(
//                array(
//                    'groupid' => new external_value(PARAM_INT, 'group record id'),
//                    'userids' => new external_multiple_structure(new external_value(PARAM_INT, 'user id')),
//                )
//            )
//        );
//    }
//
//    public static function get_group_members($groupIds) {
//        // Context validation
//        self::validate_context(context_system::instance());
//
//        // Parameter validation
//        self::validate_parameters(
//            self::add_session_parameters(), array(
//                'groupids' => $groupIds,
//            )
//        );
//
//        if (count($groupIds) == 0) {
//            return array('result' => -1);
//        }
//
//        if ($groupMembers = core_group_external::get_group_members($groupIds)) {
//            return $groupMembers;
//        }
//
//        return array('result' => -9);
//    }


//    public static function delete_group_members_parameters() {
//        return new external_function_parameters(
//            array(
//                'members'=> new external_multiple_structure(
//                    new external_single_structure(
//                        array(
//                            'groupid' => new external_value(PARAM_INT, 'group record id'),
//                            'userid' => new external_value(PARAM_INT, 'user id'),
//                        )
//                    )
//                )
//            )
//        );
//    }
//
//    public static function delete_group_members_returns() {
//        return new external_single_structure(
//            array(
//                'result' => new external_value(PARAM_INT, 'Result')
//            )
//        );
//    }
//
//    public static function delete_group_members($members) {
//        global $DB;
//
//        // Context validation
//        self::validate_context(context_system::instance());
//
//        //TODO:: Param validation
//
//        $params = self::validate_parameters(self::delete_group_members_parameters(), array('members'=>$members));
//
//        foreach ($params['members'] as $member) {
//            $groupid = $member['groupid'];
//            $userid = $member['userid'];
//
//            groups_remove_member($groupid, $userid);
//        }
//
//        return array('result' => 1);
//    }


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
                'result' => new external_value(PARAM_INT, 'Result of the group creation. 1 = success, 0 = failure, -1 = course not found, -2 = group not found, -3 = group not in course'),
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

        // Check if the group with the provided groupidnumber exists
        if (!($group = $DB->get_record('groups', array('idnumber' => $params['groupidnumber'])))) {
            return array('result' => -2);
        }

        // Check if the group exists in the given course
        if (!($DB->record_exists('groups', array('id' => $group->id, 'courseid' => $course->id)))) {
            return array('result' => -3);
        };

        if (local_obu_assessment_groups_delete_group($group)) {
            return array('result' => 1);
        }

        return array('result' => 0);
    }
}