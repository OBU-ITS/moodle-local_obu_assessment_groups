<?php
////**
////  URL REMINDER: http://poodledev/moodle/local/obu_assessment_extensions/test/coursemod_created_test.php
////  */
//
//
//require_once('../../../config.php');  // Include Moodle's config.php file
//global $DB, $CFG;
//require_once("$CFG->libdir/grouplib.php");
//require_once("$CFG->dirroot/local/obu_assessment_extensions/lib.php");
//require_once("$CFG->dirroot/local/obu_group_manager/lib.php");
//require_once($CFG->dirroot.'/local/obu_assessment_groups/locallib.php');
//
//
//defined('MOODLE_INTERNAL') || die();
//
//$group = $DB->get_record('groups', array(
//    'idnumber' => 'Test.Test.01',
//    'courseid' => 72190,
//));
//
//$members = array();
//$user = new stdClass();
//$user->userid = 3157;
//array_push($members, $user);
//
//local_obu_assessment_groups_sync_group_members($group,$members);
//die();