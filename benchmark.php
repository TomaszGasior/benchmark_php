#!/usr/bin/php
<?php

/*
	Simple PHP benchmark.
	Dirty and simple benchmark script written by Tomasz Gąsior.

	Usage: <loops> <full repeats> script1.php script2.php …
	Loops and full repeats are integers and are optional.

	Each full repeat runs special test script in separate PHP interpreter.
	In test script is placed a loop with specified number of iterations.
	This loop contain content of PHP script file without <?php and ?> tags.
*/



// ~~~~~~~~~


// Exit, if script is running outside command line.
if ( php_sapi_name() != 'cli' ) exit('You must run this script in command line!');

// Function for outputing table row in terminal.
function output_table_row( $content, $start_new_line = false, $bold_text = false ) {
	echo ( $start_new_line ) ? PHP_EOL : ' | ';
	if( $bold_text )
		echo "\033[32m\033[1m";
	if( is_numeric($content) )
		$content = number_format( $content, 3 ) . ' secs.';
	echo str_pad( substr($content,0,15), 15, ' ', STR_PAD_BOTH );
	if( $bold_text )
		echo "\033[0m";
}



// ~~~~~~~~~


// Output welcome message and prepare configuration given from script arguments.

$full_repeats     = 3;
$loops            = 10000;
$script_arguments = array_slice( $_SERVER['argv'], 1 ); // First element from $_SERVER['argv'] contain name of this file.
$execution_times  = array();

echo 'Simple PHP benchmark   —   (c) Tomasz Gąsior  2016-09-21', PHP_EOL;

// Display usage message if no arguments.
if( empty($script_arguments) )
{
	exit('» Usage: ' . basename(__FILE__) .  ' <loops> <full repeats> script1.php script2.php …' . PHP_EOL .
	    '         (loops and full repeats are integers and are optional).' . PHP_EOL);
}
// Get values of 'loops' and 'full repeats' from parameters or show errors messages.
if( is_numeric($script_arguments[0]) )
{
	$loops = (integer)$script_arguments[0]; unset($script_arguments[0]);

	if( $loops < 1 or $loops > 99999999999 )
		exit('» Loops value must bu larger than 0 and lower than 100000000000.' . PHP_EOL);

	if( !empty($script_arguments[1]) and is_numeric($script_arguments[1]) )
	{
		$full_repeats = (integer)$script_arguments[1]; unset($script_arguments[1]);

		if( $full_repeats < 3 )
			exit('» Full repeats value must bu larger than 2.' . PHP_EOL);
	}
}
// Check files existence and prepare array of execution time.
foreach( $script_arguments as $file_name )
{
	if( file_exists($file_name) ) {
		if( $file_name != basename($file_name) )
			exit('» Set current working directory to place, that contain tested scripts!' . PHP_EOL);
		$execution_times[$file_name] = array();
	}
	else
		exit('» File does not exists: ' . $file_name . '.' . PHP_EOL);
}


// ~~~~~~~~~


// Output information message and top headers row in table.

echo '» Running ' . $loops . ' loops repeated ' . $full_repeats . ' times.', PHP_EOL;

output_table_row(' ', true);

foreach( $script_arguments as $number => $file_name )
	output_table_row( basename($file_name), false, true );


// Run full repeats.

$test_code_template = '<?php set_include_path(getcwd()); $t=microtime(1); for( $i=0; $i<%d; $i++ ){ %s } file_put_contents(\'/tmp/benchphp_time.txt\', microtime(1)-$t);';

for( $repeat_number=1; $repeat_number<=$full_repeats; $repeat_number++ )
{
	// Output left header cell in table.
	output_table_row( $repeat_number.' repeat:', true );

	// Run code of each file and output execution time.
	foreach( $script_arguments as $file_name )
	{
		$test_code = php_strip_whitespace($file_name);
		$test_code = str_replace('<?php', null, $test_code, $replace_count_begin);
		$test_code = str_replace( '?>',   null, $test_code, $replace_count_end);
		$test_code = sprintf($test_code_template, $loops, $test_code);

		if( $replace_count_begin > 1 or $replace_count_end > 1 ) {
			@unlink('/tmp/benchphp_err.txt'); @unlink('/tmp/benchphp_time.txt'); @unlink('/tmp/benchphp_code.php');
			exit(PHP_EOL . '» Tested script must not contain more than one open or close PHP tag.' . PHP_EOL);
		}

		file_put_contents('/tmp/benchphp_code.php', $test_code);
		`php /tmp/benchphp_code.php 2> /tmp/benchphp_err.txt`;

		if( $error = trim(file_get_contents('/tmp/benchphp_err.txt')) ) {
			@unlink('/tmp/benchphp_err.txt'); @unlink('/tmp/benchphp_time.txt'); @unlink('/tmp/benchphp_code.php');
			exit(PHP_EOL.PHP_EOL . '» Tested script ' . $file_name .  ' interrupted with error!' . PHP_EOL.PHP_EOL . $error . PHP_EOL);
		}

		$result = round( file_get_contents('/tmp/benchphp_time.txt'), 3 );
		output_table_row($result);
		$execution_times[$file_name][] = $result;
	}
}


// Output table row with average values.

output_table_row( 'AVERAGE:', true, true );
foreach( $script_arguments as $number => $file_name )
{
	$average = round( array_sum($execution_times[$file_name])/count($execution_times[$file_name]), 3 );
	output_table_row($average, false, true );
}


// ~~~~~~~~~


// Clean up temporary files at the end of script.
@unlink('/tmp/benchphp_err.txt'); @unlink('/tmp/benchphp_time.txt'); @unlink('/tmp/benchphp_code.php');
echo PHP_EOL, PHP_EOL;