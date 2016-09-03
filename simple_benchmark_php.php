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
	echo str_pad( substr($content,0,16), 16, ' ', STR_PAD_BOTH );
	if( $bold_text )
		echo "\033[0m";
}

// Get benchmark code template given from this file.
$bench_code_template = file_get_contents(__FILE__);
$bench_code_template = '<?php ' . substr( $bench_code_template, strpos($bench_code_template,'#BENCHMARK'.'_CODE') );


// ~~~~~~~~~


// Output welcome message and prepare configuration given from script arguments.

$full_repeats     = 5;
$loops            = 10000;
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

	if( $loops < 100 or $loops > 100000 )
		die('    Loops value must bu larger than 99 and lower than 100001.'.PHP_EOL);

	if( !empty($script_arguments[1]) and is_numeric($script_arguments[1]) )
	{
		$full_repeats = $script_arguments[1]; unset($script_arguments[1]);

		if( $full_repeats < 3 )
			die('    Full repeats value must bu larger than 2.'.PHP_EOL);
		elseif( $full_repeats > 10 )
			die('    I am stupid script. I can count only to ten. Sorry.'.PHP_EOL);
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
	$names = array( '1'=>'first', 2=>'second', 3=>'third', 4=>'fourth', 5=>'fifth', 6=>'sixth', 7=>'seventh', 8=>'eighth', 9=>'ninth', 10=>'tenth' );
	output_table_row( $names[$repeat_number].' repeat', true );

	// Run code of each file and output execution time.
	foreach( $script_arguments as $number => $file_name )
	{
		$code = trim( str_replace( array('<?php','#!/bin/php'), null, file_get_contents($file_name) ) );
		$bench_code = str_replace(
			array( '#BENCH_CODE_HERE', '$BENCH_LOOP_HERE' ),
			array( $code,              $loops ),
			$bench_code_template
		);
		$bench_file = '/tmp/php_bench_file'.$number.'.php';

		file_put_contents($bench_file, $bench_code);
		exec( 'php '.$bench_file, $unused_variable, $status_code);

		if( $status_code != 0 )	die(PHP_EOL."\033[31m".'    Your script exited with error!'."\033[0m".PHP_EOL);

		$result = file_get_contents('/tmp/php_bench_result.txt');
		output_table_row( $result );
		$execution_times[$file_name][] = $result;
	}
}


// Output table row with awerange values.

output_table_row( 'AVERANGE', true, true );
foreach( $script_arguments as $number => $file_name )
{
	$averange = round( array_sum($execution_times[$file_name])/count($execution_times[$file_name]), 3 );
	output_table_row( $averange, false, true );
}


// ~~~~~~~~~



// End script.
echo PHP_EOL, PHP_EOL;
exit;


// ~~~~~~~~~


/* This script opens itself and puts code below in $bench_code_template variable. */

#BENCHMARK_CODE
$start_microtime = microtime(true);
for( $i=0; $i<$BENCH_LOOP_HERE; $i++ ) {
	#BENCH_CODE_HERE
}
$execution_time = round( round(microtime(true),3) - round($start_microtime,3), 3 );
file_put_contents( '/tmp/php_bench_result.txt', $execution_time );