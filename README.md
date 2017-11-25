benchmark_php
===

Dirty and simple PHP benchmark script. Tested on Linux only.

Usage
---

Add execute permission to `benchmark_php` file and move it to your `PATH` location for convenience.

Usage: `benchmark_php [loops] [full repeats] script1.php script2.php script3.php …`.

Prepare PHP scripts, that do the same thing in different ways and specify these files as arguments. Benchmark will run these scripts many times and output results immediately in table form. Optionally, you can specify "loops" and "full repeats" values as first and second argument.

In each full repeat special test script is ran in separate PHP interpreter. In test script is placed a loop with specified number of iterations. This loop contain your PHP code without `<?php` and `?>` tags.

It's possible to specify PHP interpreter path by `PHP_PATH` environment variable.

Important notes
---

**Keep in mind, that your tested code will be ran from `for` loop.** Don't use namespaces and don't define constants in tested scripts. Also, use `include_once`/`require_once` instead of `include`/`require`.

Always use absolute paths rather than relative paths. Temporary test script is saved under `/tmp`, so don't use magic constants like `__FILE__` or `__DIR__`.

Output is redirected to `/dev/null`, so don't print text from tested code. If your code has syntax errors or trigger PHP errors, benchmark will fail.

Example usage
---

Create three PHP scripts, that parse example settings in various formats.

`ini.php`:

	<?php
	$data = 's3571 = "f8f5161cf94df05793592f5fab95138b"'."\n".'s3078 = "1091660f3dff84fd648efe31391c5524"'."\n".'s7335 = "81b44841fd564c347f7f21ae19b97659"'."\n".'s1868 = "c164bbc9d6c72a52c599bbb43d8db8e1"'."\n".'s4894 = "723dadb8c699bf14f74503dbcb6e09c1"'."\n".'s6854 = "2ba2520186ee376e835ce7bf1554ef7b"';
	var_dump(parse_ini_string($data));

`json.php`:

	<?php
	$data = '{"s3571":"f8f5161cf94df05793592f5fab95138b","s3078":"1091660f3dff84fd648efe31391c5524","s7335":"81b44841fd564c347f7f21ae19b97659","s1868":"c164bbc9d6c72a52c599bbb43d8db8e1","s4894":"723dadb8c699bf14f74503dbcb6e09c1","s6854":"2ba2520186ee376e835ce7bf1554ef7b"}';
	var_dump(json_decode($data));

`xml.php`:

	<?php
	$data = '<r><s3571>f8f5161cf94df05793592f5fab95138b</s3571>'."\n".'<s3078>1091660f3dff84fd648efe31391c5524</s3078>'."\n".'<s7335>81b44841fd564c347f7f21ae19b97659</s7335>'."\n".'<s1868>c164bbc9d6c72a52c599bbb43d8db8e1</s1868>'."\n".'<s4894>723dadb8c699bf14f74503dbcb6e09c1</s4894>'."\n".'<s6854>2ba2520186ee376e835ce7bf1554ef7b</s6854></r>';
	var_dump(simplexml_load_string($data));

Run benchmark by command: `benchmark_php 60000 4 ini.php json.php xml.php`.

	Simple PHP benchmark  —  (c) Tomasz Gąsior
	» Running loop with 60 000 iterations repeated 4 times.

	                |     ini.php     |    json.php     |     xml.php
	   1 repeat:    |     1.83729     |     2.05977     |     3.90672
	   2 repeat:    |     1.93612     |     2.13234     |     3.95973
	   3 repeat:    |     1.89203     |     2.15431     |     3.90543
	   4 repeat:    |     1.90288     |     2.07594     |     3.81798
	    AVERAGE:    |     1.89208     |     2.10559     |     3.89747