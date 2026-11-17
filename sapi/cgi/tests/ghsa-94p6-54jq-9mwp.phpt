--TEST--
GHSA-94p6-54jq-9mwp: cgi.force_redirect cannot be bypassed with HTTP_REDIRECT_STATUS
--SKIPIF--
<?php
include "skipif.inc";
?>
--FILE--
<?php

include "include.inc";

$php = get_cgi_path();
reset_env_vars();

$script = __DIR__ . '/ghsa-94p6-54jq-9mwp.test.php';
file_put_contents($script, '<?php echo "script ran";');

/* Run php-cgi with cgi.force_redirect=1 and exactly the given environment variables. */
function run_cgi($php, $script, array $env, $ini = '') {
    reset_env_vars();
    putenv('HTTP_REDIRECT_STATUS');
    putenv('MY_REDIRECT_STATUS');
    putenv('REQUEST_METHOD=GET');
    putenv("SCRIPT_FILENAME=$script");
    foreach ($env as $name => $value) {
        putenv("$name=$value");
    }
    $out = (string) shell_exec("$php -n -d cgi.force_redirect=1 $ini -q");
    putenv('HTTP_REDIRECT_STATUS');
    putenv('MY_REDIRECT_STATUS');
    reset_env_vars();
    if (strpos($out, 'Security Alert') !== false) {
        return 'denied';
    }
    if (strpos($out, 'script ran') !== false) {
        return 'script ran';
    }
    return "unexpected output: $out";
}

/* Normal case: the web server (Apache) sets REDIRECT_STATUS. */
echo "REDIRECT_STATUS set: ",
    run_cgi($php, $script, ['REDIRECT_STATUS' => '200']), "\n";

/* Direct access: nothing set, the request must be refused. */
echo "no status variable: ",
    run_cgi($php, $script, []), "\n";

/* The bypass: a client can send a "Redirect-Status" request header, which web servers turn
 * into the HTTP_REDIRECT_STATUS variable. It must not satisfy force_redirect. */
echo "HTTP_REDIRECT_STATUS only: ",
    run_cgi($php, $script, ['HTTP_REDIRECT_STATUS' => '1']), "\n";

/* A variable configured by the administrator is honoured, and only that one. */
$ini = '-d cgi.redirect_status_env=MY_REDIRECT_STATUS';
echo "configured variable set: ",
    run_cgi($php, $script, ['MY_REDIRECT_STATUS' => '1'], $ini), "\n";
echo "configured variable, HTTP_REDIRECT_STATUS only: ",
    run_cgi($php, $script, ['HTTP_REDIRECT_STATUS' => '1'], $ini), "\n";

?>
--CLEAN--
<?php
@unlink(__DIR__ . '/ghsa-94p6-54jq-9mwp.test.php');
?>
--EXPECT--
REDIRECT_STATUS set: script ran
no status variable: denied
HTTP_REDIRECT_STATUS only: denied
configured variable set: script ran
configured variable, HTTP_REDIRECT_STATUS only: denied
