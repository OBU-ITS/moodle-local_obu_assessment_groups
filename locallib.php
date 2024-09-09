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

require_once($CFG->dirroot."/grouplib.php");

function local_obu_assessment_groups_sync_group_members() {

}

function local_obu_assessment_groups_create_group($course, $groupidnumber, $groupname) {

    return local_obu_group_manager_create_system_group($course, $groupname, $groupidnumber);
}

function local_obu_assessment_groups_delete_group($group) {
    global $DB;

    $DB->delete_records('groups', array('id' => $group->id));
    $DB->delete_records('groups_members', array('groupid' => $group->id));

    return true;
}