<?PHP
	$color = 'black';
	$width = 1;
	$height = 1;
	if (isset($_GET['c']) or isset($_GET['color'])) {
		$c = (isset($_GET['c']) ? $_GET['c'] : $_GET['color']);
		if (preg_match('/^[a-zA-Z]+$/', $c)) {
			$color = $c;
		}
		if (preg_match('/^[0-9a-fA-F]+$/', $c)) {
			$color = "#" . $c;
		}
	}
	if (isset($_GET['w']) or isset($_GET['width'])) {
		$w = (isset($_GET['w']) ? $_GET['w'] : $_GET['width']);
		if (preg_match('/^[0-9]+$/', $w)) {
			$width = $w;
		}
	}
	if (isset($_GET['h']) or isset($_GET['height'])) {
		$h = (isset($_GET['h']) ? $_GET['h'] : $_GET['height']);
		if (preg_match('/^[0-9]+$/', $h)) {
			$height = $h;
		}
	}
	header("Content-type: image/svg+xml");
?>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 <?PHP echo $width; ?> <?PHP echo $height; ?>" preserveAspectRatio="none">
  <rect width="<?PHP echo $width; ?>" height="<?PHP echo $height; ?>" fill="<?PHP echo $color; ?>" />
</svg>