<?php
	
	// サーバ上に上がっているリソースを表示
	
	error_reporting(E_ALL ^ E_NOTICE);
	
	// 対象になりえるバージョン一覧を取得
	$ver = $_GET['ver'];
	$dirs = array();
	foreach (glob(dirname(dirname(dirname(__FILE__))) . '/*web*') as $dir) {
		$v = '';
		if (preg_match('/\.([0-9]*)$/', $dir, $m)) {
			$v = $m[1];
		}
		$dirs[$v] = $dir;
	}
	$res_dir = ($dirs[$ver]?:$dirs['']).'/res/';
	
    $files = array_map(
        function($f)use($res_dir){
            return str_replace($res_dir, '', $f);
        }
        , glob("{$res_dir}{,*/}*.php",GLOB_BRACE)
    );
    natsort($files);
	$file = '';
	$res = array();
	$res_headers = array();
	
	if (isset($_GET['file']) and in_array($_GET['file'], $files)) {
		if (file_exists("{$res_dir}{$_GET['file']}")) {
			$res = include("{$res_dir}{$_GET['file']}");
			
			foreach ($res as $id => $rec) {
				if (is_array($rec)) {
					foreach ($rec as $k => $v) {
						$res_headers[$k] = $k;
					}
				}
			}
            
			$file = $_GET['file'];
		}
	}
	
	function q($str) {return (is_null($str) ? '' : nl2br(htmlspecialchars($str)));}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>resview</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<script src="table.js"></script>
	</head>
	<body>
		<form>
			<fieldset>
				<legend>表示対象選択</legend>
				<select name="ver">
					<?PHP foreach ($dirs as $v => $d):?>
						<option value="<?=$v?>" <?PHP if ($v == $ver):?>selected<?PHP endif ?>><?=$v?></option>
					<?PHP endforeach ?>
				</select>
				<select name="file">
					<?PHP foreach ($files as $f):?>
						<option value="<?PHP echo $f ?>" <?PHP if ($f == $file) {echo "selected";}?>><?PHP echo $f ?></option>
					<?PHP endforeach ?>
				</select>
				<input type="submit" name="submit" value="submit">
			</fieldset>
		</form>
		<?PHP if ($res): ?>
			<form>
				<fieldset>
					<legend>なかみ</legend>
					<table border="1" class="table-autofilter">
						<thead>
							<tr>
								<?PHP if (is_array($rec)) : ?>
									<?PHP foreach ($res_headers as $k => $v):?>
										<th class="table-filterable"><?PHP echo q($k) ?></th>
									<?PHP endforeach ?>
								<?PHP else : ?>
									<th class="table-filterable">key</th>
									<th>val</th>
								<?PHP endif ?>
							</tr>
						</thead>
						<tbody>
							<?PHP foreach ($res as $id => $rec): ?>
								<tr>
									<?PHP if (is_array($rec)) : ?>
										<?PHP foreach ($res_headers as $k => $v):?>
											<td align="right"><?PHP echo q($rec[$k]) ?></td>
										<?PHP endforeach ?>
									<?PHP else: ?>
										<td><?PHP echo q($id) ?></td>
										<td><?PHP echo q($rec) ?></td>
									<?PHP endif ?>
								</tr>
							<?PHP endforeach ?>
						</tbody>
					</table>
				</fieldset>
			</form>
		<?PHP endif ?>
	</body>
</html>
