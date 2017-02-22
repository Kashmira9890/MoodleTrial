<?php
require_once(dirname(__FILE__) . '/../../config.php');        // Bootstrapping Moodle

require_login();                                              // Checking the User Is Logged In
$context = context_system::instance();                        // Getting the Context
require_capability('local/greet:begreeted', $context);        // Checking the User Has Permission to Use This Script

$name = optional_param('name', '', PARAM_TEXT);               // Get Data From the Request
if (!$name) {
	$name = fullname($USER);                                  // Global Variables
}

add_to_log(SITEID, 'local_greet', 'begreeted',
		'local/greet/index.php?name=' . urlencode($name));    // Logging

$PAGE->set_context($context);                                 // The $PAGE Global
$PAGE->set_url(new moodle_url('/local/greet/index.php'),
		array('name' => $name));                              // Moodle URL
		$PAGE->set_title(get_string('welcome', 'local_greet'));       // Internationalisation

		echo $OUTPUT->header();                                       // Starting Output
		echo $OUTPUT->box(get_string('greet', 'local_greet',
				format_string($name)));                               // Outputting the Body of the Page
				echo $OUTPUT->footer();								  // Finishing Output

?>