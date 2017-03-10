<?php
// This file is part of Moodle - http://moodle.org/
//
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
 * Last Access report
*
* @package    report
* @subpackage lastaccess
* @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/report/lastaccess/index_form.php');

//#1 - Get the system context
$systemcontext = context_system::instance();

//#2 - Check basic permisiion
require_capability('report/lastaccess:view', $systemcontext);

admin_externalpage_setup('reportlastaccess', '', null, '', array('pagelayout'=>'report'));

//#3 - Get the lang strings from the lang file
$strtitle      = get_string('title','report_lastaccess');
$strcourse     = get_string('course');
$strname       = get_string('name','report_lastaccess');
$strlastaccess = get_string('lastaccess','report_lastaccess');
$strgrade      = get_string('grade','report_lastaccess');

/* //#4 - Set up $PAGE object
$PAGE->set_url('/report/lastaccess/index.php');
$PAGE->set_context($systemcontext);
$PAGE->set_title($strtitle);
$PAGE->set_pagelayout('report');
$PAGE->set_heading($strtitle); */

//#5 - Get the courses
$sql = "SELECT id, fullname
		  FROM {course}
		 WHERE visible= :visible
		   AND id != :siteid
	  ORDER BY fullname";
$courses = $DB->get_records_sql_menu($sql, array('visible' => 1, 'siteid' => SITEID));

//#6 - Load up the form
$mform = new lastaccess_form('', array('courses' => $courses));

//Get data submitted in the form
$data = $mform->get_data();

$sql1 = "SELECT ua.id, 
				ua.userid, u.firstname, u.lastname, ua.courseid, c.fullname, ua.timeaccess
		   FROM mdl_user_lastaccess ua 
		   JOIN mdl_course c
		     ON (ua.courseid = c.id)
		   JOIN mdl_user u
		     ON (ua.userid = u.id)";
		/* For concatenating firstname and lastname as full name of the user
		 * Use: 
		 * 
		 * SELECT ua.id, 
		 * 		  ua.userid, CONCAT(u.firstname, u.lastname) AS userfullname, ua.courseid, c.fullname, ua.timeaccess 
		 *   FROM mdl_user_lastaccess ua 
		 *   JOIN mdl_course c 
		 *     ON (ua.courseid = c.id) 
		 *   JOIN mdl_user u 
		 *     ON (ua.userid = u.id) 
		 * 
		 * and 
		 * $table->data[] = array($u->fullname, $u->userfullname, userdate($u->timeaccess));
		
		 * Or Use:
		 * implode($glue, $pieces) as done below
		 * */

//Get data from database
$users = $DB->get_records_sql($sql1);

echo $OUTPUT->header();
echo $OUTPUT->heading($strtitle);

//Display the form elements
$mform->display();

//Output the result table
if(isset($data->course)){
	//echo userdate($data->fromdate)."  fromdate: ".$data->todate."<br>";
	//echo userdate($data->todate)."  todate: ".$data->todate;
	
	$table = new html_table();
	$table->head=array($strcourse, $strname, $strlastaccess);
	switch($data->course){
		case 1:
			foreach($users as $u){
				if(($u->timeaccess > $data->fromdate) && ($u->timeaccess < $data->todate)){
					$name = array($u->firstname, $u->lastname);
					$table->data[] = array($u->fullname, implode(" ", $name), userdate($u->timeaccess));
				}
			}
			if($table->data){
				echo html_writer::table($table);
			}
			break;
		default:
			foreach($users as $u){
				if(($data->course == $u->courseid) & ($u->timeaccess > $data->fromdate) && ($u->timeaccess < $data->todate)){
					$name = array($u->firstname, $u->lastname);
					$table->data[] = array($u->fullname, implode(" ", $name), userdate($u->timeaccess));
				}
			} 
			if($table->data){
				echo html_writer::table($table);
			}
		break;
	}
	if($table->data == null){
		echo "<br>No data found!";
	}
	//echo $data->course." ";
	//echo userdate($data->todate)." ";
	//echo userdate($data->fromdate);
	//echo $OUTPUT->single_button("index.php?export=1&cid=$data->course&cd=$data->todate&ld=$data->fromdate", get_string('exportcsv','report_lastaccess'));
}
echo "Hey..I'm editing this file online through Github";	
echo $OUTPUT->footer();

