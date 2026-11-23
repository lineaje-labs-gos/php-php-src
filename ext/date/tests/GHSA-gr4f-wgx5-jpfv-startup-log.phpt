--TEST--
GHSA-gr4f-wgx5-jpfv: log timestamps written during request startup do not use the timezone cache
--CGI--
--ENV--
QUERY_STRING=a=1&b=2
--INI--
date.timezone=Asia/Kolkata
log_errors=1
error_log="{TMP}/GHSA-gr4f-wgx5-jpfv-startup.log"
max_input_vars=1
display_errors=0
--FILE--
<?php
/* The "Input variables exceeded" warning is logged while the request is still
 * starting up, before the date extension has been activated. Formatting that
 * timestamp with the configured timezone populates DATEG(tzcache) too early, so
 * it must be written in UTC instead. */
$log = file_get_contents(ini_get('error_log'));
preg_match('/^\[[^ ]+ [^ ]+ ([^\]]+)\] PHP Warning:  [^:]+: Input variables exceeded/m', $log, $m);
var_dump($m[1] ?? null);
?>
--CLEAN--
<?php
@unlink(sys_get_temp_dir() . '/GHSA-gr4f-wgx5-jpfv-startup.log');
?>
--EXPECT--
string(3) "UTC"
