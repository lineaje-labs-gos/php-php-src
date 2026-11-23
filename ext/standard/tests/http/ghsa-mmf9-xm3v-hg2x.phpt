--TEST--
GHSA-mmf9-xm3v-hg2x: a response header folded into many lines is parsed in linear time
--SKIPIF--
<?php
if (!function_exists('proc_open')) die('skip proc_open() not available');
?>
--FILE--
<?php
/* Each continuation line used to be concatenated with the whole accumulated header
 * value, so parsing a header folded into N lines took quadratic time. With 600000
 * lines that is many seconds, while linear parsing takes well under one second. */
$serverCode = <<<'CODE'
    $server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
    phpt_notify_server_start($server);

    $conn = stream_socket_accept($server);
    $request = '';
    while (!str_contains($request, "\r\n\r\n")) {
        $request .= fread($conn, 8192);
    }

    $body = 'ok';
    fwrite($conn, "HTTP/1.1 200 OK\r\nX-Folded: a\r\n"
        . str_repeat(" b\r\n", 600000)
        . "Content-Length: " . strlen($body) . "\r\nConnection: close\r\n\r\n" . $body);

    fclose($conn);
    fclose($server);
CODE;

$clientCode = <<<'CODE'
    $start = hrtime(true);
    $body = file_get_contents('http://{{ ADDR }}/');
    $elapsed = (hrtime(true) - $start) / 1e9;

    var_dump($body);
    foreach ($http_response_header as $header) {
        if (str_starts_with($header, 'X-Folded:')) {
            var_dump(strlen($header));
            var_dump(substr($header, 0, 20));
        }
    }
    var_dump($elapsed < 5);
CODE;

include sprintf('%s/../../../openssl/tests/ServerClientTestCase.inc', __DIR__);
ServerClientTestCase::getInstance()->run($clientCode, $serverCode);
?>
--EXPECT--
string(2) "ok"
int(1200011)
string(20) "X-Folded: a b b b b "
bool(true)
