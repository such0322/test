<?PHP

require_once('env.php');
$env = env();

function get_local_session($key) {return $GLOBALS['LOCAL_SESSION'][$key];}
function set_local_session($key, $val) {$GLOBALS['LOCAL_SESSION'][$key] = $val;}




if ($_POST) {
	
	$functions = array(
		'ajax' => array(
			'data' => 'ajax_player_quest', 
		), 
		'post' => array(
			'backup' => 'post_backup', 
			'restore' => 'post_restore', 
		), 
	);
	// ajax 系のデータ取得はそちらへ
	if (isset($_GET['ajax'])) {
		
		$f = $_GET['ajax'];
		
		$json = array();
		
		if (isset($functions['ajax'][$f])) {
			$json = call_user_func($functions['ajax'][$f]);
		}
		
		header('Content-type:application/json');
		echo json_encode($json);
		exit;
	}
	
	if (isset($_POST['fn_submit'])) {
		
		$f = $_POST['fn_submit'];
		
		if (isset($functions['post'][$f])) {
			$json = call_user_func($functions['post'][$f]);
		}
		
		// post の場合は特に何も戻さない
		
		return;
	}
	
	return;
}





/**
 * クエスト情報取得
 * 
 * @return array
 */
function ajax_player_quest()
{
	$ret = array();
	return $ret;
}


/**
 * バックアップの生成
 * 
 * @return array
 */
function post_backup()
{
	$from_player_id = $_POST['from_player_id'];
	$to_player_id = $_POST['to_player_id'];
	
	//$fn = sprintf('player_%d_to_%d.dump', $from_player_id, $to_player_id);
	$fn = sprintf('player_%d.dump', $from_player_id);
	
	$outputs = array();
	
	try {
		// conf/table.php から対象テーブルを取得
		$tables = array();
		$t = \Mag\Conf::load('table');
		foreach ($t as $k => $v) {
			if (preg_match('/^player/', $k) and $v == 'save') {
				$tables[] = $k;
			}
		}
		
		// player_id から分散先を取得
		$db = \Mag\DB::connect($k, 0, $player_id);
		
		// 各テーブルの処理
		foreach ($tables as $table) {
			
			// まず DELETE を用意
			$outputs[] = "DELETE FROM {$table} WHERE player_id = {$to_player_id}";
			
			// 現在のデータを取得
			$sql = "SELECT * FROM {$table} WHERE player_id = {$from_player_id}";
			$arr = $db->select($sql);
			if ($arr) {
				
				$cols = '';         // `hoge`,`fuga`,`piyo`
				$values = array();  // ["('hoge','fuga','piyo')", ...]
				
				// 全レコードの中身をダンプ用に確保
				foreach ($arr as $rec) {
					
					// カラム一覧の生成は初回のみ
					if (! $cols) {
						$a = array();
						foreach ($rec as $col => $val) {
							if ($col != 'id') {
								$a[] = $col;
							}
						}
						$cols = "`" . implode("`,`", $a) . "`";
					}
					
					// 各項目の中身を INSERT 時の VALUES で使いやすい形にまとめる
					$a = array();
					foreach ($rec as $col => $val) {
						if ($col != 'id') {
							if ($col == 'player_id') {
								$val = $to_player_id;
							}
							$a[$col] = $db->qs($val);
						}
					}
					$values[] = sprintf("('%s')", implode("','", $a));
				}
				
				// 多くなり過ぎないように適当に分断して INSERT の生成
				foreach (array_chunk($values, 10) as $vals) {
					$v = implode(',', $vals);
					$outputs[] = "INSERT INTO {$table}({$cols}) VALUES{$v}";
				}
			}
		}
		
		header('Content-Type:application/octet-stream');
		header('Content-Disposition:attachment;filename='.$fn);
		foreach ($outputs as $s) {
			echo "{$s};\n";
		}
		exit;
		
	} catch (Exception $e) {
		
		var_dump($e);
		
	}
	
}

/**
 * バックアップからの復元
 * 
 * @return array
 */
function post_restore()
{
	
	// 来たファイルの中身を確保して確認
	$c = file_get_contents($_FILES['dumpfile']['tmp_name']);
	
	// 雑に player_id を確保
	$player_id = intval(substr($c, strpos($c, 'player_id = ') + strlen('player_id = '), 10));
	
	try {
		// player_id から分散先を取得
		$db = \Mag\DB::connect($k, 0, $player_id);
		
		//
		foreach (explode("\n", $c) as $line) {
			$db->exec($line);
		}
		\Mag\DB::commitStack();
		
	} catch (Exception $e) {
		
		var_dump($e);
		
	}
	
}
