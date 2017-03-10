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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Event list filter form.
 *
 * @package   report_lastaccess
 * @copyright 2014 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lastaccess_form extends moodleform {

	/**
	 * Form definition method.
	 */
	public function definition() {
		global $DB;
		$mform = $this->_form;
		
		$options = array();
		$options[0] = get_string('choose');
		$options[1] = get_string('all');
		$options += $this->_customdata['courses'];
		
		$mform->addElement('select', 'course', get_string('course'), $options);
		$mform->setType('course', PARAM_ALPHANUMEXT);
		
		$mform->addElement('date_selector', 'fromdate', get_string('from'));
		$mform->setType('fromdate', PARAM_INT);
		//$mform->addHelpButton('lastaccess', 'lastaccess');
		//$mform->setDefault('fromdate', time() + 3600 * 24);
		
		$dt = new DateTime();
		$mform->addElement('date_time_selector', 'todate', get_string('to'));
		$mform->setType('todate', PARAM_INT);
		//$mform->setDefault('todate', $dt->getTimestamp());
		
		//$mform->addHelpButton('todate', 'todate');
		
		$mform->addElement('submit', 'save', get_string('display','report_lastaccess'));
		
	}
	
	function validation($data, $files) {
		global $DB;
	
		$errors = parent::validation($data, $files);
	
		/* if ($errorcode = course_validate_dates($data)) {
			$errors['enddate'] = get_string($errorcode, 'error');
		} */
	
		if($data['course'] == '0'){
			$errors['course'] = get_string('error_invalidcourse','report_lastaccess');
		}
		
		if($data['fromdate'] > time(date("d-m-Y"))){
			$errors['fromdate'] = get_string('error_invaliddate','report_lastaccess');
		}
		
		if($data['fromdate'] > $data['todate']){
			$errors['fromdate'] = get_string('error_invalidfromdate','report_lastaccess');
		}
		
		if($data['todate'] > time(date("d-m-Y"))){
			$errors['todate'] = get_string('error_invaliddate','report_lastaccess');
		} 
		
		return $errors;
	}
}
