<?php
/**
* Example URL: /local/obu_assessment_groups/test/test.php
**/


require_once('../../../config.php');  // Include Moodle's config.php file
global $DB, $CFG;
require_once("$CFG->libdir/grouplib.php");
require_once("$CFG->dirroot/local/obu_assessment_extensions/lib.php");
require_once("$CFG->dirroot/local/obu_group_manager/lib.php");
require_once($CFG->dirroot.'/local/obu_assessment_groups/locallib.php');
require_once($CFG->dirroot.'/local/obu_assessment_groups/externallib.php');


defined('MOODLE_INTERNAL') || die();

$group = $DB->get_record('groups', array(
    'idnumber' => 'Test.Test.01',
    'courseid' => 72190,
));

$members = array();
$user = array();
$user['userid'] = 2;
$members[0] = $user;

//local_obu_assessment_groups_sync_group_members($group,$members);

$res = local_obu_assessment_groups_external::sync_group_members('2023.ANML7004_S2_0','Test.Test.01', $members);

var_dump($res);

die();