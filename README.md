Simple PHP benchmark
===

Dirty and simple benchmark script. Tested on Linux only.

Usage: `benchmark.php (loops) (full repeats) script1.php script2.php …`.
Loops and full repeats are integers and are optional.

Each full repeat runs special test script in separate PHP interpreter. In test script is placed a loop with specified number of iterations. This loop contain content of PHP script file without <?php and ?> tags.

Example usage
---

Create three PHP scripts, that do the same thing in different ways.

`ini.php`:

	<?php
	$data = 's3571 = "f8f5161cf94df05793592f5fab95138b"'."\n".'s3078 = "1091660f3dff84fd648efe31391c5524"'."\n".'s7335 = "81b44841fd564c347f7f21ae19b97659"'."\n".'s1868 = "c164bbc9d6c72a52c599bbb43d8db8e1"'."\n".'s4894 = "723dadb8c699bf14f74503dbcb6e09c1"'."\n".'s6854 = "2ba2520186ee376e835ce7bf1554ef7b"';
	var_dump( parse_ini_string($data) );

`json.php`:

	<?php
	$data = '{"s3571":"f8f5161cf94df05793592f5fab95138b","s3078":"1091660f3dff84fd648efe31391c5524","s7335":"81b44841fd564c347f7f21ae19b97659","s1868":"c164bbc9d6c72a52c599bbb43d8db8e1","s4894":"723dadb8c699bf14f74503dbcb6e09c1","s6854":"2ba2520186ee376e835ce7bf1554ef7b"}';
	var_dump( json_decode($data) );

`xml.php`:

	<?php
	$data = '<r><s3571>f8f5161cf94df05793592f5fab95138b</s3571>'."\n".'<s3078>1091660f3dff84fd648efe31391c5524</s3078>'."\n".'<s7335>81b44841fd564c347f7f21ae19b97659</s7335>'."\n".'<s1868>c164bbc9d6c72a52c599bbb43d8db8e1</s1868>'."\n".'<s4894>723dadb8c699bf14f74503dbcb6e09c1</s4894>'."\n".'<s6854>2ba2520186ee376e835ce7bf1554ef7b</s6854></r>';
	var_dump( simplexml_load_string($data) );

Run benchmark by command: `php benchmark.php 30000 4 ini.php json.php xml.php`. It outputs results immediately.

	Simple PHP benchmark   —   (c) Tomasz Gąsior
	» Running 30000 loops repeated 4 times.

	                |     ini.php     |    json.php     |     xml.php
	   1 repeat:    |   1.680 secs.   |   1.834 secs.   |   2.930 secs.
	   2 repeat:    |   1.675 secs.   |   1.845 secs.   |   2.927 secs.
	   3 repeat:    |   1.679 secs.   |   1.879 secs.   |   2.959 secs.
	   4 repeat:    |   1.693 secs.   |   1.849 secs.   |   2.897 secs.
	   AVERAGE:     |   1.682 secs.   |   1.852 secs.   |   2.928 secs.