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
 * @author    Joe Souch
 * @copyright 2017, Oxford Brookes University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Define the web service functions to install.
$functions = array(
	'local_obu_assessment_groups_sync_group_members' => array(
		'classname'   => 'local_obu_assessment_groups_external',
		'methodname'  => 'sync_group_members',
		'classpath'   => 'local/obu_assessment_groups/externallib.php',
		'description' => 'Synchronises the members of a group. Returns result code and array of user id who have had issues sync-ing',
		'type'        => 'write',
		'capabilities'=> ''
	),
	'local_obu_assessment_groups_create_group' => array(
		'classname'   => 'local_obu_assessment_groups_external',
		'methodname'  => 'create_group',
		'classpath'   => 'local/obu_assessment_groups/externallib.php',
		'description' => 'Creates an assessment group. Returns result code and group name',
		'type'        => 'write',
		'capabilities'=> ''
	),
	'local_obu_assessment_groups_delete_group' => array(
		'classname'   => 'local_obu_assessment_groups_external',
		'methodname'  => 'delete_group',
		'classpath'   => 'local/obu_assessment_groups/externallib.php',
		'description' => 'Deletes an assessment group. Returns result code',
		'type'        => 'read',
		'capabilities'=> ''
	)
);

// Define the services to install as pre-build services.
$services = array(
	'Attendance web service' => array(
		'shortname' => 'obu_assessment_group',
		'functions' => array(
			'local_obu_assessment_groups_sync_group_members',
			'local_obu_assessment_groups_create_group',
			'local_obu_assessment_groups_delete_group'
		),
		'restrictedusers' => 1,
		'enabled' => 1
	)
);
