<?php
// Run: php tests/security/csrf-server.test.php
// Exercise the project's actual CI3 verifier without connecting to its database.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', __DIR__ . '/../../system/');
define('FCPATH', __DIR__ . '/../../');
require __DIR__ . '/../../application/config/config.php';
function config_item($key) { global $config; return isset($config[$key]) ? $config[$key] : null; }
function log_message($level, $message) {}
require BASEPATH . 'core/Security.php';
class TestSecurity extends CI_Security
{
    public function csrf_set_cookie() { return $this; }
    public function csrf_show_error() { throw new RuntimeException('CSRF rejected'); }
}
function check($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
check(config_item('csrf_protection') === true, 'Protection must be enabled');
check(config_item('csrf_regenerate') === false, 'Concurrent requests need a stable token');
check(config_item('csrf_exclude_uris') === [], 'No endpoint should bypass verification');
$name = config_item('csrf_token_name');
$cookieName = config_item('cookie_prefix') . config_item('csrf_cookie_name');
$token = str_repeat('a', 32);
$_SERVER['REQUEST_METHOD'] = 'POST';
foreach (['valid', 'missing', 'mismatch', 'array', 'missing-cookie', 'repeat'] as $case) {
    $_COOKIE = [$cookieName => $token];
    $_POST = [$name => $token, 'CBReq_No' => ['CBR-1']];
    $security = new TestSecurity();
    if ($case === 'missing') unset($_POST[$name]);
    if ($case === 'mismatch') $_POST[$name] = str_repeat('b', 32);
    if ($case === 'array') $_POST[$name] = [$token];
    if ($case === 'missing-cookie') unset($_COOKIE[$cookieName]);
    $rejected = false;
    try { $security->csrf_verify(); } catch (RuntimeException $error) { $rejected = true; }
    check($rejected === !in_array($case, ['valid', 'repeat'], true), 'Unexpected result: ' . $case);
    check(!isset($_POST[$name]), 'Token must be removed before controller processing');
    check($_POST['CBReq_No'] === ['CBR-1'], 'Business input must remain intact');
}
echo "PASS: valid/repeated POST accepted; missing, mismatched, array and cookieless tokens rejected\n";
