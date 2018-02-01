<?PHP
	require_once('env.php');
	require_once('lib/func.php');
	
	/////////////////////////////////////////////
	// 広域変数定義
	
	$env = env();
	$site_con = master_con();
	
	$message = '';
	
	$banner_pos_large = 1;
	$banner_pos_small = 2;
	
	$banner_limit_large = 20;
	$banner_limit_small = 20;
	
	// オフセット リミット オーダ
	$order_list = array('id', 'start_date', 'end_date');  // order の対象として許容する値
	$limit_list = array(10, 30, 50, 100, 300, 500);
	$default_limit  = 10;
	$default_offset = 0;
	$default_order  = 'id';
	$default_desc   = 'DESC';
	
	$limit  = (in_array($LOCAL_SESSION['limit'], $limit_list) ? $LOCAL_SESSION['limit'] : $default_limit);
	$offset = (is_numeric($LOCAL_SESSION['offset']) ? $LOCAL_SESSION['offset'] : $default_offset);
	$order  = (in_array($LOCAL_SESSION['order'], $order_list) ? $LOCAL_SESSION['order'] : $default_order);
	$desc   = (in_array($LOCAL_SESSION['desc'], array('DESC', '')) && $LOCAL_SESSION['limit'] > 0 ? $LOCAL_SESSION['desc']: $default_desc);
	
	$priorities = array(
		'0' => '一覧に表示しない', 
		'1' => '優先度の低い告知', 
		'2' => '普段の告知', 
		'3' => '優先度の高い告知', 
		'4' => 'とても優先度の高い告知', 
		'5' => '非常事態宣言', 
	);
	
	$info_types = array(
		'1' => '通常お知らせ', 
		'2' => 'イベント情報', 
		'3' => 'メンテナンス', 
		'4' => '重要', 
	);
	
	$link_types = array(
		'gacha' => 'ガチャ', 
		'event' => 'イベント', 
	);
	
	$image_dir_labels = array(
		'infoimg' => 'お知らせ', 
		'lb' => '大バナー', 
		'sb' => '小バナー', 
	);
	
	$imgdir_path = str_replace('{version}', '', $env['basepath']) . '/htdocs/app/images';
	$infoimg_dir = 'infoimg';
	$infoimg_fulldir = str_replace('{version}', '', $env['basepath']) . '/htdocs/app/' . $infoimg_dir;
	
	$image_dirs = array(
		'infoimg' => "{$imgdir_path}/infoimg", 
		'lb'      => "{$imgdir_path}/lb", 
		'sb'      => "{$imgdir_path}/sb", 
	);
	
	/////////////////////////////////////////////
	// AJAX系処理
	
	if (isset($_GET['json']) and $_GET['json'] and preg_match('/^[0-9]+$/', $_GET['id'])) {
		
		$ret = array();
		
		// 指定されたレコードを取得
		$sql = sprintf("SELECT * FROM siteinfo WHERE id = %d", $_GET['id']);
		$arr = db_select($site_con, $sql);
		if ($arr) {
			$ret = $arr[0];
		}
		
		header('Content-type: application/json');
		echo json_encode($ret);
		
		exit;
	}
	
	/////////////////////////////////////////////
	// POSTを受け取ったときの処理
	
	if (isset($_POST['limit']) || isset($_POST['offset']) || isset($_POST['order'])) {
		$LOCAL_SESSION['limit']  = (in_array($_POST['limit'], $limit_list)       ? $_POST['limit']  : $default_limit );
		$LOCAL_SESSION['offset'] = (preg_match("/^\d+$/", $_POST['offset'])      ? $_POST['offset'] : $default_offset);
		$LOCAL_SESSION['order']  = (in_array($_POST['order'], $order_list)       ? $_POST['order']  : $default_order );
		$LOCAL_SESSION['desc']   = (in_array($_POST['desc'], array('DESC', ''))  ? $_POST['desc']   : $default_desc  );
	}
	
	if (isset($_POST['f1_submit'])) {
		
		do {
			$_SESSION['message'] = '';
			
			// とりあえず値の確保
			$id = $_POST['id'];
			$info_type      = $_POST['info_type'];
			$subject        = $_POST['subject'];
			$link_type      = $_POST['link_type'];
			$link           = $_POST['link'];
			$launch_param   = $_POST['launch_param'];
			$body           = $_POST['body'];
			$start_date     = $_POST['start_date'];
			$end_date       = $_POST['end_date'];
			$expire_date    = $_POST['expire_date'];
			$priority       = $_POST['priority'];
			
			// 簡単に入力内容を確認
			if (! isset($info_types[$info_type])) {
				$_SESSION['message'] .= 'お知らせタイプに誤りがあります';
				break;
			}
			if ($link_type and ! isset($link_types[$link_type])) {
				$_SESSION['message'] .= 'リンク種別に誤りがあります';
				break;
			}
			if (strlen($link) > 0) {
				if (! preg_match('/^[\.0-9a-zA-Z_-]+(\/[\.0-9a-zA-Z_-]+)*$/', $link)) {
					$_SESSION['message'] .= 'リンクに誤りがあります';
					break;
				}
			}
			
			// 日時型のチェック
			if (! preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $start_date)) {
				$_SESSION['message'] .= '開始日時に誤りがあります';
				break;
			}
			if (! preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $end_date)) {
				$_SESSION['message'] .= '新着表示終了日時に誤りがあります';
				break;
			}
			if (! preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $expire_date)) {
				$_SESSION['message'] .= '公開終了日時に誤りがあります';
				break;
			}
			
			
			// 入れる前に値の直し
			if ($_POST['nl2br'] == 1) {
				$subject = nl2br($subject);
				$body = nl2br($body);
			}
			if ($_POST['id'] > 0) {
				$sql = sprintf("UPDATE siteinfo SET info_type=%d, subject='%s', link='%s', link_type='%s', body='%s', start_date='%s', end_date='%s', expire_date='%s', priority=%d WHERE id=%d"
				               , $info_type, db_qs($site_con, $subject), db_qs($site_con, $link), db_qs($site_con, $link_type), db_qs($site_con, $body)
				               , $start_date, $end_date, $expire_date, $priority, $id);
				$res = db_exec($site_con, $sql);
				if ($res) {
					$_SESSION['message'] = sprintf('%d 番のメッセージを更新しました。<br>', $_POST['id']);
				} else {
					$_SESSION['message'] .= '更新に失敗しました ('.db_error($site_con).')';
					break;
				}
			}
			else {
				$sql = sprintf("INSERT INTO siteinfo(info_type, subject, link, link_type, body, start_date, end_date, expire_date, priority, created) VALUES(%d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, now())"
				               , $info_type, db_qs($site_con, $subject), db_qs($site_con, $link), db_qs($site_con, $link_type), db_qs($site_con, $body)
				               , $start_date, $end_date, $expire_date, $priority);
				$res = db_exec($site_con, $sql);
				if ($res) {
					
					// 最後に auto_increment の値を取得
					$sql = "SELECT last_insert_id() AS id";
					$arr = db_select($site_con, $sql);
					$rec = $arr[0];
					$id = $rec["id"];
					$_SESSION['message'] = sprintf('%d 番のメッセージを作成しました。<br>', $id);
				} else {
					$_SESSION['message'] .= '新規登録に失敗しました ('.db_error($site_con).')';
					break;
				}
			}
			
		} while (0);
		
		$_SESSION['id'] = $id;
	}
	elseif (isset($_POST['f3_submit'])) {
		// 削除処理
		// $_POST['delete_id'] が配列になってるのでそれを削除
		$ids = array();
		foreach ($_POST['delete_id'] AS $del_id) {
			if (preg_match('/^[0-9]+$/', $del_id)) {
				$ids[] = $del_id;
			}
		}
		if (sizeof($ids) > 0) {
			$sql = sprintf("DELETE FROM siteinfo WHERE id IN (%s)", implode(',', $ids));
			$ret = db_exec($site_con, $sql);
		}
	}
	elseif (isset($_POST['f4_submit'])) {
		// バナー(大)の更新
//mylog($_POST);
		
		$banners = array();
		if (is_array($_POST['order'])) {
			foreach ($_POST['order'] as $order) {
				$banners[] = $_POST['banner'][$order];
			}
		}
		if ($banners) {
			$res = db_exec($site_con, 'BEGIN');
			try {
				foreach ($banners as $priority => $banner) {
					$update_id = 0;
					if ($banner['id']) {
						// 更新かどうかの確認
						$sql = sprintf('SELECT id FROM siteinfo_banner WHERE id = %d', $banner['id']);
						$arr = db_select($site_con, $sql);
						if ($arr) {
							$update_id = $arr[0]['id'];
						}
					}
					
					if ($update_id) {
						// 更新
						$sql = sprintf(
							"UPDATE siteinfo_banner SET priority=%d, img='%s',link=%d,start_date='%s',end_date='%s' WHERE id = %d"
							, $priority
							, db_qs($site_con, $banner['img'])
							, db_qs($site_con, $banner['link'])
							, db_qs($site_con, $banner['start_date'])
							, db_qs($site_con, $banner['end_date'])
							, $update_id
						);
						$res = db_exec($site_con, $sql);
						if (! $res) {
							throw new Exception(mysql_error($site_con));
						}
					} else {
						// 挿入
						$sql = sprintf(
							"INSERT INTO siteinfo_banner(pos, priority, img, link, start_date, end_date, created) VALUES(%d, %d, '%s', %d, '%s', '%s', now())"
							, $banner_pos_large
							, $priority
							, db_qs($site_con, $banner['img'])
							, db_qs($site_con, $banner['link'])
							, db_qs($site_con, $banner['start_date'])
							, db_qs($site_con, $banner['end_date'])
						);
						$res = db_exec($site_con, $sql);
						if (! $res) {
							throw new Exception(db_error($site_con));
						}
					}
				}
				db_exec($site_con, 'COMMIT');
				
				$_SESSION['message'] = '正常に更新されました';
			} catch (Exception $e) {
				db_exec($site_con, 'ROLLBACK');
				
				$_SESSION['message'] = sprintf('更新に失敗しました (%s)', $e->getMessage());
			}
		}
	}
	elseif (isset($_POST['f4s_submit'])) {
		// バナー(小)の更新
//mylog($_POST);
		
		$banners = array();
		if (is_array($_POST['order'])) {
			foreach ($_POST['order'] as $order) {
				$banners[] = $_POST['banner'][$order];
			}
		}
		if ($banners) {
			$res = db_exec($site_con, 'BEGIN');
			try {
				foreach ($banners as $priority => $banner) {
					$update_id = 0;
					if ($banner['id']) {
						// 更新かどうかの確認
						$sql = sprintf('SELECT id FROM siteinfo_banner WHERE id = %d', $banner['id']);
						$arr = db_select($site_con, $sql);
						if ($arr) {
							$update_id = $arr[0]['id'];
						}
					}
					
					if ($update_id) {
						// 更新
						$sql = sprintf(
							"UPDATE siteinfo_banner SET priority=%d, img='%s',link=%d,start_date='%s',end_date='%s' WHERE id = %d"
							, $priority
							, db_qs($site_con, $banner['img'])
							, db_qs($site_con, $banner['link'])
							, db_qs($site_con, $banner['start_date'])
							, db_qs($site_con, $banner['end_date'])
							, $update_id
						);
						$res = db_exec($site_con, $sql);
						if (! $res) {
							throw new Exception(mysql_error($site_con));
						}
					} else {
						// 挿入
						$sql = sprintf(
							"INSERT INTO siteinfo_banner(pos, priority, img, link, start_date, end_date, created) VALUES(%d, %d, '%s', %d, '%s', '%s', now())"
							, $banner_pos_small
							, $priority
							, db_qs($site_con, $banner['img'])
							, db_qs($site_con, $banner['link'])
							, db_qs($site_con, $banner['start_date'])
							, db_qs($site_con, $banner['end_date'])
						);
						$res = db_exec($site_con, $sql);
						if (! $res) {
							throw new Exception(db_error($site_con));
						}
					}
				}
				db_exec($site_con, 'COMMIT');
				
				$_SESSION['message'] = '正常に更新されました';
			} catch (Exception $e) {
				db_exec($site_con, 'ROLLBACK');
				
				$_SESSION['message'] = sprintf('更新に失敗しました (%s)', $e->getMessage());
			}
		}
	}
	elseif ($_POST['f2_post'] == 1) {
		// lim off ord の変更
	}
	elseif (isset($_POST['f5_submit'])) {
		// バナーのアップロード
		$result = 0;
		do {
			// パラメータの確認
			if (! isset($image_dirs[$_POST['image_dir']])) {
				$result = __LINE__;
				$_SESSION['message'] .= '用途の指定がありません。';
				break;
			}
			if (! preg_match('/\.(gif|jpg|jpeg|png)$/', $_POST['tofilename'])) {
				$result = __LINE__;
				$_SESSION['message'] .= 'アップロード後のファイル名を正しく指定してください。';
				break;
			}
			if (! isset($_FILES['banner_img'])) {
				$result = __LINE__;
				$_SESSION['message'] .= 'ファイルアップロードに失敗しました。';
				break;
			}
			if (! preg_match('/\.(gif|jpg|jpeg|png)$/', $_FILES['banner_img']['name'])) {
				$result = __LINE__;
				$_SESSION['message'] .= 'アップロード可能な形式は gif, jpeg, png のみになります';
				break;
			}
			if (! is_uploaded_file($_FILES['banner_img']['tmp_name'])) {
				$result = __LINE__;
				$_SESSION['message'] .= '処理に失敗しました。';
				break;
			}
			
			// ファイルの移動
			$tofilepath = $image_dirs[$_POST['image_dir']] . '/' . $_POST['tofilename'];
			move_uploaded_file($_FILES['banner_img']['tmp_name'], $tofilepath);
			chmod($tofilepath, 0664);
			
			$_SESSION['message'] .= 'ファイルアップをアップロードしました、各サーバへの転送は行われていません。';
		} while (0);
//die($result);
	}
	elseif (isset($_POST['f5b_submit'])) {
		// ファイル転送
		do {
			// パラメータの確認
			if (! isset($image_dirs[$_POST['image_dir']])) {
				$_SESSION['message'] .= '用途の指定がありません。';
				break;
			}
			
			// crontask.txt にタスクの出力
			settask($_POST['image_dir'], array(''));  // 要素が空文字 (=バージョン指定なし) の値一つ入った配列をバージョンとして指定
			
			$_SESSION['message'] .= 'ファイルの転送を予約しました。';
		} while (0);
	}
	else {
		unset($_SESSION['__mylog']);
		if (isset($_SESSION['message'])) {
			$message = $_SESSION['message'];
			unset($_SESSION['message']);
		}
	}
	
	
	
	
	/////////////////////////////////////////////
	// 画像出力とか
	if ($_GET['image_dir'] and $_GET['img']) {
		
		// http://admin2.dev1.ikemen.esgm.jp/es24/inline.php?menukey=dev1_ic&include_file=plugin/dev1_ic/site_info.php&image_dir=infoimg&img=01.jpg
		
		$content_types = array(
			'gif'  => 'image/gif', 
			'jpg'  => 'image/jpg', 
			'jpeg' => 'image/jpeg', 
			'png'  => 'image/png', 
		);
		
		$filepath = '';
		if ($image_dirs[$_GET['image_dir']] and ! strpos($_GET['img'], '/')) {
			$filepath = $image_dirs[$_GET['image_dir']] . '/' . $_GET['img'];
		}
		$a = explode('.', $_GET['img']);
		$ext = array_pop($a);
		if (file_exists($filepath) and isset($content_types[$ext])) {
		    header("Content-Type: {$content_types[$ext]}");
			readfile($filepath);
		} else {
		    header("Content-Type: image/gif");
			echo implode('', array(
				chr(0x47), chr(0x49), chr(0x46), chr(0x38), chr(0x39), chr(0x61),
				chr(0x01), chr(0x00), chr(0x01), chr(0x00), chr(0x80), chr(0xff),
				chr(0x00), chr(0xff), chr(0xff), chr(0xff), chr(0x00), chr(0x00),
				chr(0x00), chr(0x2c), chr(0x00), chr(0x00), chr(0x00), chr(0x00),
				chr(0x01), chr(0x00), chr(0x01), chr(0x00), chr(0x00), chr(0x02),
				chr(0x02), chr(0x44), chr(0x01), chr(0x00), chr(0x3b)
			));
		}
		
		return;
	}
	elseif ($_GET['json']) {
		$output = array(
			'result' => 0, 
		);
		
		if ($_GET['infoimg_exists']) {
			
			$path = "{$infoimg_fulldir}/{$_GET['infoimg_exists']}";
			$output['path'] = $path;
			$output['result'] = 1;
			$output['exists'] = (file_exists($path) ? 1 : 0);
			
		} else {
			
		}
		
		header('Content-type: application/json');
		echo json_encode($output);
		return;
	}
	
	
	/////////////////////////////////////////////
	// 各種パラメータの設定
	$psmarty->assign('message', $message);
	$psmarty->assign('priorities', $priorities);
	$psmarty->assign('info_types', $info_types);
	$psmarty->assign('link_types', $link_types);
	$psmarty->assign('image_dir_labels', $image_dir_labels);
	
	
	/////////////////////////////////////////////
	// 大バナー設定の表示
	$banners = array();
	$sql = "SELECT * FROM siteinfo_banner WHERE pos = 1 ORDER BY priority";
	$arr = db_select($site_con, $sql);
	if (sizeof($arr) > 0) {
		foreach ($arr AS  $rec) {
			$banners[] = $rec;
		}
	}
	while (sizeof($banners) < $banner_limit_large) {
		$banners[] = array(
			'id' => '', 
			'img' => '', 
			'link' => '', 
		);
	}
	$psmarty->assign('banners', $banners);
	
	/////////////////////////////////////////////
	// 小バナー設定の表示
	$banners_small = array();
	$sql = "SELECT * FROM siteinfo_banner WHERE pos = 2 ORDER BY priority";
	$arr = db_select($site_con, $sql);
	if (sizeof($arr) > 0) {
		foreach ($arr AS  $rec) {
			$banners_small[] = $rec;
		}
	}
	while (sizeof($banners_small) < $banner_limit_small) {
		$banners_small[] = array(
			'id' => '', 
			'img' => '', 
			'link' => '', 
		);
	}
	$psmarty->assign('banners_small', $banners_small);
	
	/////////////////////////////////////////////
	// バナー一覧の表示
	$images = array();
	foreach ($image_dirs as $k => $d) {
		$images[$k] = array();
		
		foreach (glob("{$d}/*") as $f) {
			if (preg_match('/\.(gif|jpg|jpeg|png)$/', $f)) {
				$images[$k][] = basename($f);
			}
		}
	}
	$psmarty->assign('images', $images);
	
	/////////////////////////////////////////////
	// 記事用画像設定
	$psmarty->assign('infoimg_dir', $infoimg_dir);
	
	/////////////////////////////////////////////
	// 各お知らせ情報の表示
	$sql = "SELECT SQL_CALC_FOUND_ROWS id, subject, body, link, link_type, start_date, end_date, priority FROM siteinfo ";
	$sql .= sprintf(" ORDER BY %s %s LIMIT %d,%d", $order, $desc, $offset, $limit);
	$arr = db_select($site_con, $sql);
	if (sizeof($arr) > 0) {
		foreach ($arr AS  $rec) {
			
			$rec['id'] = $rec['id'];
			$records[] = $rec;
		}
	}
	$psmarty->assign('records', $records);
	
	/////////////////////////////////////////////
	// 最大件数取得と現在のページ取得
	$paging = array(
		'offset' => $offset, 
		'limit'  => $limit, 
		'order'  => $order, 
		'desc'   => $desc, 
		
		'limit_list' => $limit_list, 
	);
	
	$query = 'SELECT FOUND_ROWS() As cnt;';
	$cur = intval($offset / $limit) + 1;
	$ret = db_select($site_con, $query);
	$max = $ret[0]['cnt'];
	$maxpage = (($max - 1) / $limit) + 1;
	
	$paging['max']   = $max;
	$paging['start'] = ($max==0?0:($offset + 1));
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
	$psmarty->assign('paging', $paging);
	
	
	
	
	
	/** crontask に登録する */
	function settask($cmd, $vers) {
		$env = env();
		
		$task = '';
		$h = ($env['host_prefix']?:$env['env']);
		foreach ($vers as $ver) {
			$task .= "{$cmd} {$h} {$ver}\n";
		}
		
		file_put_contents($env['crontask_file'], $task, FILE_APPEND);
	}
	
	/** 転送処理が残っているか調べる */
	function is_lefttask() {
		$env = env();
		
		// 素直にファイルが空かそうでないかで確認
		return (filesize($env['crontask_file']) > 0 ? 1 : 0);
		
		/* 指定したコマンドがあるかどうかの確認
		$crontask_cmd  = 'conf';
		$crontask_cmd  = 'ver';
		$crontask_file = $env['crontask_file'];
		$c = file_get_contents($crontask_file);
		if (strpos($c, $crontask_cmd) !== false) {
			return true;
		}
		return false;
		*/
	}
