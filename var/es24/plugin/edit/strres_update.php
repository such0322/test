<?PHP
//print_r($_SESSION);
//var_dump($_SESSION["__mylog"]);
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	$strres_filename = $GLOBALS['root'] . '/var/string_table2_utf8.csv';
	$prj_strres_filename = $GLOBALS['root'] . '/var/string_table2_utf8_prj.csv';
	
	$error_code = $_SESSION['error_code'];
	if (isset($_SESSION['error_code'])) {
		unset($_SESSION['error_code']);
	}
	
	// 受け取ったCSVを文字コード直して書き換える
	if (isset($_POST['f1_submit'])) {
		
		$a = string_resource_load($strres_filename, 1);
		
		$s = file_get_contents($_FILES['strres']['tmp_name']);
		$s = mb_convert_encoding($s, 'UTF-8', 'SJIS');
		$fp = tmpfile();
		if ($fp) {
			fwrite($fp, $s);
			rewind($fp);
			$sr = string_resource_parse($fp, 1);
			fclose($fp);
			
			if ($sr) {
				$new_strres = array_merge($a, $sr);
				$fp = fopen($strres_filename, 'w');
				if ($fp) {
					foreach ($new_strres As $k => $v) {
						foreach ($v AS $rec) {
							fputcsv($fp, $rec);
						}
					}
					fclose($fp);
				}
				else {
					$_SESSION['error_code'] = __LINE__;
				}
			}
			else {
				$_SESSION['error_code'] = __LINE__;
			}
		}
		else {
			$_SESSION['error_code'] = __LINE__;
		}
		return ;
	}
	
	// 取得の場合
	if (isset($_POST['f2_submit'])) {
		
		// 出力
		header("Content-Type: application/octet-stream");
//		header("Content-Type: application/x-csv");
		header("Content-Disposition: attachment; filename=strres.csv");
		header("Cache-Control: public");
		header("Pragma: public");
		
		$s = file_get_contents($strres_filename);
		//echo mb_convert_encoding($s, 'SJIS', 'UTF-8');
		echo $s;
		exit;
	}
	
	// 受け取ったCSVを文字コード直して書き換える
	if (isset($_POST['f3_submit'])) {
		
		$a = string_resource_load($prj_strres_filename, 1);
		
		$s = file_get_contents($_FILES['strres']['tmp_name']);
		$s = mb_convert_encoding($s, 'UTF-8', 'SJIS');
		$fp = tmpfile();
		if ($fp) {
			fwrite($fp, $s);
			rewind($fp);
			$sr = string_resource_parse($fp, 1);
			fclose($fp);
			
			if ($sr) {
				$csv = array_merge($a, $sr);
				$fp = fopen($prj_strres_filename, 'w');
				if ($fp) {
					foreach ($new_strres As $k => $v) {
						foreach ($v AS $rec) {
							fputcsv($fp, $rec);
						}
					}
					fclose($fp);
				}
				else {
					$_SESSION['error_code'] = __LINE__;
				}
			}
			else {
				$_SESSION['error_code'] = __LINE__;
			}
		}
		else {
			$_SESSION['error_code'] = __LINE__;
		}
		return ;
	}
	
	// 取得の場合
	if (isset($_POST['f4_submit'])) {
		
		// 出力
		header("Content-Type: application/octet-stream");
//		header("Content-Type: application/x-csv");
		header("Content-Disposition: attachment; filename=prjstrres.csv");
		header("Cache-Control: public");
		header("Pragma: public");
		
		$s = file_get_contents($strres_filename);
		//echo mb_convert_encoding($s, 'SJIS', 'UTF-8');
		echo $s;
		exit;
	}
	
	
	if ($error_code) {
		$mainstage .= sprintf('<span class="error">%s</span>', esc($error_code));
	}
	
	$mainstage .= <<<_HTML_
	
	<form method="POST" id="f1" enctype="multipart/form-data">
		<fieldset>
			<legend>文字列テーブルの更新</legend>
			<input type="hidden" name="f1_submit" value="1">
			<input type="file" name="strres"  value="">
			<button type="submit" onclick="return confirm('更新します、よろしいですか。');">送信</button>
		</fieldset>
	</form>
	<form method="POST" id="f2">
		<fieldset>
			<legend>文字列テーブルの取得</legend>
			<input type="hidden" name="f2_submit" value="1">
			<button type="submit">取得</button>
		</fieldset>
	</form>
	
	<form method="POST" id="f1" enctype="multipart/form-data">
		<fieldset>
			<legend>プロジェクト固有の文字列テーブルの更新</legend>
			<input type="hidden" name="f3_submit" value="1">
			<input type="file" name="strres"  value="">
			<button type="submit" onclick="return confirm('更新します、よろしいですか。');">送信</button>
		</fieldset>
	</form>
	<form method="POST" id="f2">
		<fieldset>
			<legend>プロジェクト固有の文字列テーブルの取得</legend>
			<input type="hidden" name="f4_submit" value="1">
			<button type="submit">取得</button>
		</fieldset>
	</form>
	
	
_HTML_;

?>
