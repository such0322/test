<?PHP
	
	////////////////////////////////////////////////////////////////////////////
	// プロジェクトによらずどこでも使える処理
	
	if (! function_exists('charaid_to_userid')) {
		/** chara_id を user_id に変換 */
		function charaid_to_userid($chara_id) {
			return intval(0xFFFFFF & $chara_id);
		}
	}
	
	/**
	 * uidデータのよみこみ
	 * 
	 * 
	 * 
	 * @param string $bin d_master_uid.data の中身
	 * @param integer アプリ内ユーザID
	 */
	function ngp_read_uid($bin) {
		$ret = 0;
		
		do {
			////////////////////////////////
			// 一番外枠の読み込み
			$parsed_file = ngp_parse_binary($bin);
			if (! is_array($parsed_file) || ! $parsed_file['chunks']) {
				break;
			}
			
			foreach ($parsed_file['chunks'] AS $chunk) {
				$head = $chunk['head'];
				$size = $chunk['size'];
				$body = $chunk['body'];
				
				if ($head == '@UID') {
					list(,$ret) = @my_unpack('l', $body);
				}
			}
		} while (0);
		
		return $ret;
	}
	
	
	/**
	 * 所持金データのよみこみ
	 * 
	 * ゲーム内通貨 (gamemoney)、課金通貨 (realmoney) を読み込み
	 * 
	 * @param string $bin d_master_user.money_data の中身
	 * @return array gamemoney と realmoney 要素を持つ配列、読込失敗時は空配列
	 */
	function ngp_read_money($bin) {
		$money_data = array(
		/*
			'realmoney' => 0, 
			'gamemoney' => 0, 
		*/
		);
		
		do {
			////////////////////////////////
			// 一番外枠の読み込み
			$parsed_file = ngp_parse_binary($bin);
			if (! is_array($parsed_file) || ! $parsed_file['chunks']) {
				break;
			}
			
			foreach ($parsed_file['chunks'] AS $chunk) {
				$head = $chunk['head'];
				$size = $chunk['size'];
				$body = $chunk['body'];
				
				if ($head == '@RMY') {
					list(,$money_data['realmoney']) = @my_unpack('l', $body);
				}
				elseif ($head == '@GMY') {
					list(,$money_data['gamemoney']) = @my_unpack('l', $body);
				}
			}
		} while (0);
		
		////////////////////////////////
		// 終わり
		
		return $money_data;
	}
	
	
	// ここまでプロジェクトによらずどこでも使える処理
	////////////////////////////////////////////////////////////////////////////
	// 内部的に使う処理とか
	
	
	/**
	 * チャンク型ファイルの中身を分断する
	 */
	function ngp_parse_binary($b, $header_size = 0) {
		$ret = array(
			'header' => '',   // 読み飛ばしたヘッダ部分
			'chunks' => array(
				/* チャンクのテンプレート
					'haad' => ヘッダ (4 byte str), 
					'size' => サイズ (4 byte signed int)
					'body' => 本文 (サイズ byte binary), 
				*/
			), 
		);
		$seek = 0;
		$parsed_chunks = array();
		
		// まずは先頭ｎバイトの謎部分を読み込み
		if ($header_size > 0) {
			$ret['header'] = substr($b, $seek, $header_size);
			$seek += $header_size;
		}
		
		// 順にチャンク部分を読み込み
		while ($seek < strlen($b)) {
			$head = substr($b, $seek, 4);
			$seek += 4;
			
			// NULLチャンクヘッダが来た場合はファイルの終端を示す
			if (strcmp($head, "\x00\x00\x00\x00") == 0) {
				break;
			}
			
			list(,$size) = my_unpack('L', substr($b, $seek, 4));
			$seek += 4;
			
			$parsed_chunks[] = array(
				'head' => $head, 
				'size' => $size, 
				'body' => substr($b, $seek, $size), 
			);
			
			$seek += $size;
		}
		
		$ret['chunks'] = $parsed_chunks;
		return $ret;
	}
	
	/**
	 * 分断したチャンクを結合する
	 */
	function ngp_join_chunks($header, $chunks) {
		
		$ret = $header;
		
		foreach ($chunks As $v) {
			$ret .= $v['head'];
			$ret .= pack('N', strlen($v['body']));
			$ret .= $v['body'];
		}
		
		// 末尾の null ヘッダ (ラグナ固有)
		//$ret .= "\x00\x00\x00\x00";
		
		return $ret;
	}
	
	// 
	////////////////////////////////////////////////////////////////////////////
	// pack / unpack のラッパ
	
	
	/**
	 * 自前 unpack
	 * 
	 * マシンのエンディアンと逆にしたい場合に "たぶん" 対応、
	 * ちなみに c, s, l, x は確実に動作、それ以外は怪しい。
	 * １バイト戻るの X は確実に動作しない。
	 * 
	 * @param string $format unpack の第一引数
	 * @param string $data unpack の第二引数
	 * @return array unpack の戻り値
	 */
	function my_unpack($format, $data) {
		$env = env();
		$is_rev = $env['is_endian_little'];
		$ret = null;
		
		if ($is_rev) {
			$ret = unpack($format, $data);
		}
		else {
			// 基本的な実装方針としては unpack("l2", $body) とかするとして
			// $body の中身を先にいじってエンディアンを変換しとく、
			// 解釈する際にはマシンのバイトオーダで行けば良い、という方針。
			
			$rev_data = rev_endian($format, $data);
			if (strlen($rev_data) > 0) {
				$ret = unpack($format, $rev_data);
			}
		}
		
		return $ret;
	}
	
	/**
	 * 自前 pack
	 * 
	 * 対応とか非対応は my_unpack を参照。
	 * 引数は pack と同じで可変長
	 * 
	 * @return array unpack の戻り値
	 */
	function my_pack() {
		$env = env();
		$is_rev = $env['is_endian_little'];
		$ret = null;
		
		$args = func_get_args();
		$ret = call_user_func_array('pack', $args);
		
		// 逆にする必要があれば逆にする
		if (! $is_rev) {
//			$format = implode('/', str_split($args[0]));
			$format = '';
			$a = str_split($args[0]);
			foreach ($a AS $c) {
				if (strlen($format) > 0 && ! is_numeric($c)) {
					$format .= '/';
				}
				$format .= $c;
			}
			$ret = rev_endian($format, $ret);
		}
		
		return $ret;
	}
	
	/**
	 * エンディアンを変える
	 * 
	 * pack の結果や unpack する前のデータのエンディアンを反転させる
	 * １バイト戻すの X は確実にトラブルを起こす
	 * 
	 * @param string $format pack/unpack の第一引数
	 * @param string $data 反転させるバイナリ列
	 * @return string 反転後のバイナリ列
	 */
	function rev_endian($format, $data) {
		
		// がんばって自前解析
		$rev_data = '';
		$arr = explode('/', $format);
		
		$pos = 0;
		foreach ($arr AS $rec) {
			// 繰り返し回数、大体は１回出来れば良い
			$lim = 1;
			
			// 繰り返しの場合も一応対応しとく
			if (preg_match('/^[cslCSL]([1-9][0-9]*)/', $rec, $a)) {
				$lim = $a[1];
			}
			
			while ($lim-- > 0) {
				switch (substr($rec, 0, 1)) {
					// c, s, l が最も使用頻度が高い
					case 'c' :
					case 'C' :
						$rev_data .= substr($data, $pos, 1);
						$pos += 1;
						break;
					case 's' :
					case 'S' :
						$rev_data .= strrev(substr($data, $pos, 2));
						$pos += 2;
						break;
					case 'l' :
					case 'L' :
						$rev_data .= strrev(substr($data, $pos, 4));
						$pos += 4;
						break;
					
					case 'x' :
						$rev_data .= "\x00"; // 読み込まれないし何でもいいか
						$pos += 1;
						break;
					case 'X' :
						// 例えば 010203 を s1/X1/s1 として解釈しようとすると色々面倒なので対応しない 
						//$pos -= 1;  // ←これは違う
						break;
					
					// エンディアンを明示してるならそのまま通す
					case 'n' :
					case 'v' :
						$rev_data .= substr($data, $pos, 2);
						$pos += 2;
						break;
					case 'N' :
					case 'V' :
						$rev_data .= substr($data, $pos, 4);
						$pos += 4;
						break;
					
					// とりあえず int は 4 byte と勝手に決め付けて動かす
					case 'i' :
					case 'I' :
						$rev_data .= strrev(substr($data, $pos, 4));
						$pos += 4;
						break;
					
					// 浮動小数点は余りあっちとこっちで使いたくないため余り勧めたくはない
					case 'f' :
						$rev_data .= strrev(substr($data, $pos, 4));
						$pos += 4;
						break;
					case 'd' :
						$rev_data .= strrev(substr($data, $pos, 8));
						$pos += 8;
						break;
					
					// それ以外が来たら全部わたして終わりってことにする
					default :
						$s = substr($data, $pos);
						$rev_data .= $s;
						$pos += strlen($s);
						break;
				}
			}
		}
		
		return $rev_data;
	}
	
	// ここまで pack / unpack のラッパ
	////////////////////////////////////////////////////////////////////////////
?>
