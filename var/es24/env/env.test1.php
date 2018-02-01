<?PHP
	function env() {
		return array(
			
			'env' => 'test1', 
			'host_prefix' => 'test1-app', 
			
			// 管理昨日用のデータベースの繋ぎ先
			'admin_db' => array(
				'db_host' => '192.168.8.179', 
				'db_user' => 'magiday', 
				'db_pass' => '123456', 
				'db_name' => 'admin_magiday', 
			), 
			
			// ほぼ生ログの入ったDBのつなぎ先情報
			'log_db' => array(
				'db_host' => '192.168.8.179', 
				'db_user' => 'magiday', 
				'db_pass' => '123456', 
				'db_name' => 'admin_magiday', 
			), 
			
			// 各種マスタ格納先のデータベース繋ぎ先
			'master_db' => array(
				'db_host' => '192.168.8.179', 
				'db_user' => 'magiday', 
				'db_pass' => '123456', 
				'db_name' => 'magiday', 
			), 
			'slave_db' => array(
				'db_host' => '192.168.8.179', 
				'db_user' => 'magiday', 
				'db_pass' => '123456', 
				'db_name' => 'magiday', 
			), 
			
			// crontask 用のコマンド予約メモ
			'crontask_file' => '/var/www/app.magicaldays.jp/magicaldays-admin/var/es24/var/crontask.txt', 
			
			// Mag\Autoloader のファイルパス
			'mag_autoloader' => '/var/www/app.magicaldays.jp/magicaldays-web/app/Mag/Autoloader.php', 
			
			// 基準ディレクトリ
			'basepath' => '/var/www/app.magicaldays.jp/magicaldays-web{version}/', 
			
			// 退避済みログディレクトリのパス
			'log_bak' => '/var/Backup/app.magicaldays.jp/magicaldays-web/*/log/*', 
			
		);
	}
