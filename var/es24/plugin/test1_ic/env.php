<?PHP
require_once( 'env/env.test1.php' );
require_once(env()['mag_autoloader']);
\Mag\Autoloader::register();


function res2kv($res, $key, $val) {
	$ret = array();
	foreach ($res as $rec) {
		$ret[$rec[$key]] = $rec[$val];
	}
	return $ret;
}
