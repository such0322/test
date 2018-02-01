<?PHP
	////////////////////////////////////////////////////////////////////////////
	// �~�b�V�����B�����O�̎�荞��
//mylog(__LINE__);
//var_dump(__LINE__);
	
	// �Ώۃ��O�t�@�C���̈ꗗ���m��
	$log_files = glob("{$log_dir}/mission_clear_{$exec_date}.log");
	
	// �e���v���[�g�Ƃ��̍쐬
	$tpl = "INSERT INTO mission_clear(log_date, player_id, mission_id, level, regist_date) VALUES";
	$tpl_val = "('%s', %d, %d, %d, '%s')";
	$values = array();
	$values_max = 50;
	
	// ���O�t�@�C���̓ǂݍ���
//var_dump($log_files);
	foreach ($log_files AS $f) {
		$fp = fopen($f, 'r');
		if ($fp) {
			while (! feof($fp)) {
				$log = rtrim(fgets($fp), "\r\n");
				
				// ��s�͎���
				if (! $log) {
					continue;
				}
				
				// �܂����f����
				list(
					$log_date,    // ���O��������
					$pftype,      // �v���b�g�t�H�[���敪
					$uid,         // UID
					$user_id,     // ���[�UID
					$chara_id,    // �L�����N�^�[ID
					$mission_id,  // �~�b�V����ID
					$level,       // �B�����̃v���C���[�̃��x��
					$regist_date, // �v���C���[�̉���o�^����
				) = explode("\t", $log);
				
				// ���t�̖������̂͏��O
				if (! $log_date) {
					continue;
				}
				if (! preg_match('/^[0-9]*$/', $user_id) || ! preg_match('/^[0-9]*$/', $chara_id)) {
					continue;
				}
				if (! preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/', $log_date)) {
					continue;
				}
				
				// ��������Ă����̂܂܊i�[
				$values[] = sprintf($tpl_val
				                  , db_qs($con, $log_date)
				                  , $user_id
				                  , $mission_id
				                  , $level
				                  , db_qs($con, $log_date)
				);
				
				// ��萔���܂����瓊��
				if (sizeof($values) > $values_max) {
					$sql = $tpl . implode(',', $values);
					$res = db_exec($con, $sql);
//var_dump($sql, $res);
					$values = array();
				}
			}
			
			fclose($fp);
		}
	}
	
	// �܂��c���Ă�Γ���
	if (sizeof($values) > 0) {
		$sql = $tpl . implode(',', $values);
		$res = db_exec($con, $sql);
//var_dump($sql, $res);
		$values = array();
	}
	