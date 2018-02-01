<?PHP
//print_r($_SESSION);
//var_dump($_SESSION['__mylog']);
	
	require_once( 'env.php' );
	require_once( 'lib/common.php' );
	
	////////////////////////////////////////////////////////////////////////////
	// パーツのインクルード
	
	////////////////////////////////////////////////////////////////////////////
	// 広域変数定義
	$select_size_max = 10;
	$stat_unit = (isset($LOCAL_SESSION['stat_unit']) ? $LOCAL_SESSION['stat_unit'] : 'h');
	$max_width = 300;
	
	$limit_list = array(10, 50, 100, 200, 500, 1000);
	$default_limit  = 100;
	$default_offset = 0;
	$default_order  = 'log_date';
	$default_display_cols = array('l_item_move_id', 'log_date', 'chara_id', 'item_id', 'item_num');
	
	$table_name = 'l_item_move';
	$select_columns = $tables[$table_name];
	// マスタデータ、キーの名称は $select_columns の type=enum の場合はそれに合わせる、それ以外はすきに
	$master = $code_master['l_item_move'];
	
	$limit  = (intval($LOCAL_SESSION['limit']) > 0 ? intval($LOCAL_SESSION['limit']) : $default_limit);
	$offset = (intval($LOCAL_SESSION['offset']) >= 0 ? intval($LOCAL_SESSION['offset']) : $default_offset);
	$order  = (array_key_exists($LOCAL_SESSION['order'], $select_columns) ? $LOCAL_SESSION['order'] : $default_order);
	$desc   = ($LOCAL_SESSION['desc'] == 'DESC' ? 'DESC' : '');
	$LOCAL_SESSION['desc'] = $desc;  // セッション変数に null が入って warnning が出ることがあるので暫定処置
	$display_cols = (is_array($LOCAL_SESSION['display_cols'])?$LOCAL_SESSION['display_cols']:$default_display_cols);
	
	$post_data = (isset($LOCAL_SESSION['post_data']) ? $LOCAL_SESSION['post_data'] : array('display_cols' => $display_cols));
	
	////////////////////////////////////////////////////////////////////////////
	// POSTを受け取った際の動作
	
	// 検索条件の変更
	if (isset($_POST['f1_post'])) {
		$LOCAL_SESSION['post_data'] = $_POST;
		
/* 構造変更でちょち面倒になったので後回し
		// 日付時刻系カラムの時刻省略時
		foreach ($_POST AS $wcol => $val) {
			if (preg_match('/_(hour|minute|second)$/', $wcol)) {
				$basename = preg_replace('/_(hour|minute|second)$/', '', $wcol);
				if (
					! preg_match('/^\d{1,2}$/', $_POST[$wcol]) &&
					preg_match('/^\d{4}$/',   $_POST[$basename.'_year']  ) &&
					preg_match('/^\d{1,2}$/', $_POST[$basename.'_month'] ) &&
					preg_match('/^\d{1,2}$/', $_POST[$basename.'_day']   )
				) {
					$_SESSION['LOCAL_SESSION'][$wcol] = '00';
				}
			}
		}
*/
		
		if (isset($stat_units[$_POST['stat_unit']])) {
			$LOCAL_SESSION['stat_unit'] = $_POST['stat_unit'];
		}
		
		$LOCAL_SESSION['offset'] = 0;
		$LOCAL_SESSION['f1_search'] = 1;
		
		// 表示対象カラムの変更
		$LOCAL_SESSION['display_cols'] = $_POST['display_cols'];
	}
	
	// オーダリミットオフセットの変更
	if ($_POST['f2_post']) {
		if (in_array($_POST['limit'], $limit_list)) {
			$LOCAL_SESSION['limit'] = intval($_POST['limit']);
		}
		if (intval($_POST['offset']) >= 0) {
			$LOCAL_SESSION['offset'] = intval($_POST['offset']);
		}
		if (array_key_exists($_POST['order'], $select_columns)) {
			$LOCAL_SESSION['order'] = $_POST['order'];
		}
		if (in_array($_POST['desc'], array('DESC', ''))) {
			$LOCAL_SESSION['desc'] = $_POST['desc'];
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、検索・抽出条件フォーム
	
	$psmarty->assign('menukey', $menukey);
	
	$psmarty->assign('master', $master);
	$psmarty->assign('select_columns', $select_columns);
	$psmarty->assign('display_cols', $display_cols);
	
	$psmarty->assign('yesterday', strtotime('-1 day'));
	$psmarty->assign('tomorrow', strtotime('+1 day'));
	
	// 検索条件が指定されていない場合はここで終わらせる
	if (! isset($LOCAL_SESSION['f1_search'])) {
		return ;
	}
	$psmarty->assign('post_data', $post_data);
	$psmarty->assign('is_f1_search', 1);
	
	////////////////////////////////////////////////////////////////////////////
	// 検索条件の生成
	$admin_con = admin_con();
	
	$where_array = array();
	foreach ($select_columns AS $col => $info) {
		$wcol = (isset($info['colname'])?$info['colname']:$col);
		switch ($info['type']) {
			case 'primary' :
			case 'key' :
				$v = preg_replace('/(,|\.|\s)/', '', $post_data[$col]);
				if (preg_match('/^[0-9]+$/', $v)) {
					$where_array[] = sprintf('%s = %s', $wcol, $v);
				}
				break;
			case 'strkey' :
				if (strlen($post_data[$col]) > 0) {
					$where_array[] = sprintf("%s = '%s'", $wcol, db_qs($admin_con, $post_data[$col]));
				}
				break;
			case 'num' :
				$min = preg_replace('/(,|\.|\s)/', '', $post_data[$col]['min']);
				if (is_numeric($min)) {
					$where_array[] = sprintf('%d <= %s', $min, $wcol);
				}
				$max = preg_replace('/(,|\.|\s)/', '', $post_data[$col]['max']);
				if (is_numeric($max)) {
					$where_array[] = sprintf('%s <= %d', $wcol, $max);
				}
				break;
			case 'enum' :
				if (array_key_exists($col, $master) && is_array($post_data[$col])) {
					$vals = array();
					$w = array();
					foreach ($post_data[$col] AS $val) {
						if (strlen($val) == 0) {
							$w[] = sprintf('(%s IS NULL)', $wcol);
						}
						elseif (array_key_exists($val, $master[$col])) {
							$vals[] = $val;
						}
					}
					if (sizeof($vals) > 0) {
						$w[] = sprintf("%s IN ('%s')", $wcol, implode("','", $vals));
					}
					if (sizeof($w) > 0) {
						$where_array[] = sprintf('(%s)', implode(' OR ', $w));
					}
				}
				break;
			case 'string' :
				if (strlen($post_data[$col]) > 0) {
					$where_array[] = sprintf("%s LIKE '%%%s%%'", $wcol, db_qs($admin_con, $post_data[$col]));
				}
				break;
			case 'datetime' :
			case 'unixtime' :
				if (preg_match('/^20[0-9]{2}-(0?[0-9]|1[0-2])-([012]?[0-9]|3[01]) ([01]?[0-9]|2[0-3]):([0-5]?[0-9]):([0-5]?[0-9])$/', $post_data[$col]['begin'])) {
					if ($info['type'] == 'unixtime') {
						$where_array[] = sprintf('%d < %s', strtotime($post_data[$col]['begin']), $wcol);
					}
					else {
						$where_array[] = sprintf("'%s' < %s", $post_data[$col]['begin'], $wcol);
					}
				}
				if (preg_match('/^20[0-9]{2}-(0?[0-9]|1[0-2])-([012]?[0-9]|3[01]) ([01]?[0-9]|2[0-3]):([0-5]?[0-9]):([0-5]?[0-9])$/', $post_data[$col]['end'])) {
					if ($info['type'] == 'unixtime') {
						$where_array[] = sprintf('%s < %d', $wcol, strtotime($post_data[$col]['end']));
					}
					else {
						$where_array[] = sprintf("%s < '%s'", $wcol, $post_data[$col]['end']);
					}
				}
				
				break;
			case 'checkbox' :
				
				break;
			default:
				break;
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// 表示内容作成、結果リスト
	
	// CSV 出力
	if (isset($_POST['export']) && $_POST['export'] == 1) {
		
		$results = array();
		
		// ヘッダ部分の作成
		$heads = array();
		foreach ($select_columns AS $col => $colinfo) {
			if (in_array($col, $display_cols)) {
				$heads[] = $colinfo['name'];
			}
		}
		$results[] = $heads;
		
		$sql = "SELECT * FROM {$table_name} ";
		if (sizeof($where_array) > 0) {
			$sql .= ' WHERE ' . implode(' AND ', $where_array);
		}
		$sql .= sprintf(' ORDER BY %s %s', $order, $desc);
		
		// 出力
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename=item_move.csv');
		
		$res = db_exec($admin_con, $sql);
		if ($res) {
			while ($rec = db_fetch($res)) {
				$line = array();
				foreach ($select_columns AS $col => $colinfo) {
					if (in_array($col, $display_cols)) {
						if (isset($master[$col])) {
							$line[] = sprintf('%s : %s', $rec[$col], $master[$col][$rec[$col]]);
						}
						elseif ($colinfo['type'] == 'num') {
							$line[] = intval($rec[$col]);
						}
						else {
							$line[] = $rec[$col];
						}
					}
				}
				
				print csv_encode('"' . implode('","', $line) . '"');
				print "\r\n";
			}
		}
		
		exit;
	}
	
	if ($_POST) {
		return;
	}
	else {
		unset($_SESSION['__mylog']);
	}
	
	////////////////////////////////////////////////////////
	// ここから履歴
//var_dump($where_array);
	$sql = "SELECT * FROM {$table_name} ";  // SQL_CALC_FOUND_ROWS はページング方法を変えた都合で入れない
	if (sizeof($where_array) > 0) {
		$sql .= ' WHERE ' . implode(' AND ', $where_array);
	}
	$sql .= sprintf(' ORDER BY %s %s LIMIT %d,%d', $order, $desc, $offset, ($limit + 1));
	$arr = db_select($admin_con, $sql);
//var_dump($sql);var_dump($arr);
	
	$next = 0;
	$f2_tr = array();
	if (sizeof($arr) > 0) {
		
		// 次のページがあるかどうか
		if (sizeof($arr) > $limit) {
			array_pop($arr);
			$next = $offset + $limit;
		}
		
		foreach ($arr AS $rec) {
			$row = array();
			foreach ($select_columns AS $col => $colinfo) {
				if (in_array($col, $display_cols)) {
					if (isset($master[$col])) {
						$row[] = sprintf('%s : %s', $rec[$col], $master[$col][$rec[$col]]);
					}
					elseif ($colinfo['type'] == 'num') {
						$row[] = (is_null($rec[$col]) ? '' : $rec[$col]);
					}
					else {
						$row[] = (is_null($rec[$col]) ? '' : $rec[$col]);
					}
				}
			}
			$f2_tr[] = $row;
		}
	}
	$psmarty->assign('f2_tr', $f2_tr);
	
	// ヘッダ行の作成
	$f2_trh = array();
	foreach ($select_columns AS $col => $colinfo) {
		if (in_array($col, $display_cols)) {
			$f2_trh[$col] = $colinfo;
		}
	}
	$psmarty->assign('f2_trh', $f2_trh);
	
	////////////////////////////////////
	// 最大件数取得と現在のページ取得
	$prev = ($offset >= $limit ? ($offset - $limit) : -1);
	$paging = array(
		'offset' => $offset, 
		'limit'  => $limit, 
		'order'  => $order, 
		'desc'   => $desc, 
		
		'next'   => $next, 
		'prev'   => $prev, 
		
		'limit_list' => $limit_list, 
	);
	
/*
	$query = 'SELECT FOUND_ROWS() As cnt;';
	$cur = intval($offset / $limit) + 1;
	$ret = db_select($admin_con, $query);
	$max = $ret[0]['cnt'];
	$maxpage = (($max - 1) / $limit) + 1;
	
	$paging['max']   = $max;
	$paging['begin'] = ($max==0?0:($offset + 1));
	$paging['end']   = (($offset+$limit)>$max?$max:($offset+$limit));
	
	// 各ページへのリンク作成
	$pages = array();
	if ($max > 0) {
		for ($i = ($cur>10?$cur-10:1);$i <= ($maxpage>($cur+10)?($cur+10):$maxpage);$i++) {
			if ($i == ($cur-10) && $i != 1) {
				$pages[(($i-1)*$limit)] = '...';
			}
			elseif ($i == ($cur+10) && $i != $maxpage) {
				$pages[(($i-1)*$limit)] = '...';
			}
			else {
				$pages[(($i-1)*$limit)] = $i;
			}
		}
	}
	$paging['pages'] = $pages;
*/
	
	$psmarty->assign('paging', $paging);
?>
