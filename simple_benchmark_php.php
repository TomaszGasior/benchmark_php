#!/bin/php
<?php

/*
	Simple PHP benchmark.
*/



// ~~~~~~~~~


// Exit, if script is running outside command line.
if ( php_sapi_name() != 'cli' ) die('You must run this script in command line!');

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
$loops            = 1000;
$script_arguments = array_slice( $_SERVER['argv'], 1 ); // First element from $_SERVER['argv'] contain name of this file.
$execution_times  = array();

echo 'Simple PHP benchmark   —   (c) Tomasz Gąsior  2016-09-03', PHP_EOL;

// Display usage message if no arguments.
if( empty($script_arguments) )
{
	die('    Usage: ' . basename(__FILE__) .  ' <loops> <full repeats> script1.php script2.php …' . PHP_EOL .
	    '           (loops and full repeats are integers and are optional).' . PHP_EOL);
}
// Get values of 'loops' and 'full repeats' from parameters or show errors messages.
if( is_numeric($script_arguments[0]) )
{
	$loops = $script_arguments[0]; unset($script_arguments[0]);

	if( $loops < 1 or $loops > 9999 )
		die('    Loops value must bu larger than 0 and lower than 10000.'.PHP_EOL);

	if( !empty($script_arguments[1]) and is_numeric($script_arguments[1]) )
	{
		$full_repeats = $script_arguments[1]; unset($script_arguments[1]);

		if( $full_repeats < 3 )
			die('    Full repeats value must bu larger than 2.'.PHP_EOL);
	}
}
// Check files existence and prepare array of execution time.
foreach( $script_arguments as $file_name )
{
	if( file_exists($file_name) )
		$execution_times[$file_name] = array();
	else
		die('    File does not exists: ' . $file_name . '.' . PHP_EOL);
}



// ~~~~~~~~~


// Output information message and top headers row in table.

echo '    Running ' . $loops . ' loops repeated ' . $full_repeats . ' times.', PHP_EOL;

output_table_row(' ', true);

foreach( $script_arguments as $number => $file_name )
	output_table_row( $file_name, false, true );


// Run full repeats.

for( $repeat_number=1; $repeat_number<=$full_repeats; $repeat_number++ )
{
	// Output left header cell in table.
	output_table_row( $repeat_number.' repeat:', true );

	// Run code of each file and output execution time.
	foreach( $script_arguments as $number => $file_name )
	{
		$start_microtime = microtime(true);

		for( $i=0; $i<$loops; $i++ ) {
			exec('php '.$file_name);
		}

		$result = round( round(microtime(true),3) - round($start_microtime,3), 3 );
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


// End script.
echo PHP_EOL, PHP_EOL;
