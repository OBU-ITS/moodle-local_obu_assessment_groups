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

function local_obu_assessment_groups_sync_group_members($group, $members) {
    global $CFG, $DB;

    require_once("$CFG->dirroot/grouplib.php");

    $current_members = groups_get_members($group->id);

    $current_member_usernames = array_map(function($user) {
        return $user->username;
    }, $current_members);

    $members_to_add = array_diff($members, $current_member_usernames);
    foreach($members_to_add as $username) {
        local_obu_assessment_groups_sync_group_member_add($group, $username);
    }

    $members_to_delete = array_diff($current_member_usernames, $members);
    foreach($members_to_delete as $username) {
        local_obu_assessment_groups_sync_group_member_delete($group, $username);
    }

    return array(
        'Result' => 1
    );
}

function local_obu_assessment_groups_sync_group_member_add($group, $username) {
    global $CFG, $DB;

    require_once("$CFG->dirroot/grouplib.php");
    require_once("$CFG->dirroot/local/obu_assessment_extensions/lib.php");

    $user = $DB->get_record('user', array('username'=>$username), '*', MUST_EXIST);

    groups_add_member($group, $user);

    local_obu_assess_ex_recalculate_due_dates_for_user($user, $group);
}

function local_obu_assessment_groups_sync_group_member_delete($group, $username) {
    global $CFG, $DB;

    require_once("$CFG->dirroot/grouplib.php");

    $user = $DB->get_record('user', array('username'=>$username), 'id', MUST_EXIST);

    groups_remove_member($group, $user);
}

function local_obu_assessment_groups_create_group($course, $groupidnumber, $groupname) {
    global $CFG;

    require_once("$CFG->dirroot/local/obu_group_manager/lib.php");

    return local_obu_group_manager_create_system_group($course, $groupname, $groupidnumber);
}

function local_obu_assessment_groups_delete_group($group) {
    global $CFG, $DB;

    require_once("$CFG->dirroot/grouplib.php");

    groups_delete_group($group);

    return true;
}