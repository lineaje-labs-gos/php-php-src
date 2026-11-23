--TEST--
GHSA-fm2m-fjf5-v3cp: files handler always truncates the session file before writing
--EXTENSIONS--
session
--SKIPIF--
<?php include('skipif.inc'); ?>
--INI--
session.save_handler=files
session.use_cookies=0
session.use_strict_mode=0
--FILE--
<?php
$path = __DIR__ . '/session_files_write_truncate_dir';
@mkdir($path);
session_save_path($path);
$sid = 'truncatetest';
$file = "$path/sess_$sid";
@unlink($file);

/* The session file is empty when the session is opened, so the handler records a
 * size of 0. Another writer then leaves a longer partial session behind before
 * we write ours. */
session_id($sid);
session_start();
file_put_contents($file, str_repeat('X', 100));

$_SESSION['a'] = 1;
session_write_close();

/* Previously the file was only truncated when the new data was shorter than the
 * size recorded at open, so the stale tail of the other write survived. */
var_dump(file_get_contents($file));
?>
--CLEAN--
<?php
$path = __DIR__ . '/session_files_write_truncate_dir';
@unlink("$path/sess_truncatetest");
@rmdir($path);
?>
--EXPECT--
string(6) "a|i:1;"
