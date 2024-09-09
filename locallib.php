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
 * @author     Joe Souch
 * @copyright  2024, Oxford Brookes University {@link http://www.brookes.ac.uk/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG, $DB;
require_once("$CFG->libdir/grouplib.php");
require_once("$CFG->dirroot/local/obu_assessment_extensions/lib.php");
require_once("$CFG->dirroot/local/obu_group_manager/lib.php");

function local_obu_assessment_groups_sync_group_members($group, $members) {

    $current_members = groups_get_members($group->id);
    $current_member_userids = array_map(function($user) {
        return $user->id;
    }, $current_members);

    $member_userids = array_map(function($user) {
        return $user->userid;
    }, $members);

    $issue_members = array();
    $members_to_add = array_diff($member_userids, $current_member_userids);
    foreach($members_to_add as $userid) {
        if(!local_obu_assessment_groups_sync_group_member_add($group, $userid)) {
            array_push($issue_members, $userid);
        }
    }

    $members_to_delete = array_diff($current_member_userids, $member_userids);
    foreach($members_to_delete as $userid) {
        local_obu_assessment_groups_sync_group_member_delete($group, $userid);
    }

    return array(
        'result' => 1,
        'issuemembers' => $issue_members
    );
}

function local_obu_assessment_groups_sync_group_member_add($group, $userid) {

    global $DB;

    try {
        $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);

        groups_add_member($group, $user);
    }
    catch (Exception $e) {
        return false;
    }

    local_obu_assess_ex_recalculate_due_dates_for_user($user, $group);

    return true;
}

function local_obu_assessment_groups_sync_group_member_delete($group, $userid) {

    groups_remove_member($group, $userid);
}

function local_obu_assessment_groups_create_group($course, $groupidnumber, $groupname) {

    return local_obu_group_manager_create_system_group($course, $groupname, $groupidnumber);
}

function local_obu_assessment_groups_delete_group($group) {

    groups_delete_group($group);

    return true;
}