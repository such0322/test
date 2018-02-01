<?php


// TODO: ˆ—ŽžŠÔ‚ª‹C‚É‚È‚é


$sql = <<<_SQL_
INSERT INTO otetsudai_set(log_date, type, card1_id, card2_id, total)
 SELECT '{$exec_datetime}', 1, card1_id, card2_id, count(*) FROM (
 SELECT card_1_1 AS card1_id, card_1_2 AS card2_id FROM lt_otetsudai_start WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND card_1_1 <= card_1_2 AND card_1_1 > 0 AND card_1_2 > 0
 UNION ALL
 SELECT card_1_2 AS card1_id, card_1_1 AS card2_id FROM lt_otetsudai_start WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND card_1_1 >  card_1_2 AND card_1_1 > 0 AND card_1_2 > 0
 ) t GROUP BY card1_id, card2_id;
_SQL_;
$res = db_exec($con, $sql);



$sql = <<<_SQL_
INSERT INTO otetsudai_set(log_date, type, card1_id, card2_id, total)
 SELECT '{$exec_datetime}', 2, card1_id, card2_id, count(*) FROM (
 SELECT card_2_1 AS card1_id, card_2_2 AS card2_id FROM lt_otetsudai_start WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND card_2_1 <= card_2_2 AND card_2_1 > 0 AND card_2_2 > 0
 UNION ALL
 SELECT card_2_2 AS card1_id, card_2_1 AS card2_id FROM lt_otetsudai_start WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND card_2_1 >  card_2_2 AND card_2_1 > 0 AND card_2_2 > 0
 ) t GROUP BY card1_id, card2_id;
_SQL_;
$res = db_exec($con, $sql);



$sql = <<<_SQL_
INSERT INTO otetsudai_set(log_date, type, card1_id, card2_id, total)
 SELECT '{$exec_datetime}', 3, card1_id, card2_id, count(*) FROM (
 SELECT card_3_1 AS card1_id, card_3_2 AS card2_id FROM lt_otetsudai_start WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND card_3_1 <= card_3_2 AND card_3_1 > 0 AND card_3_2 > 0
 UNION ALL
 SELECT card_3_2 AS card1_id, card_3_1 AS card2_id FROM lt_otetsudai_start WHERE '{$exec_datetime}' <= log_date AND log_date < '{$exec_term_datetime}' AND card_3_1 >  card_3_2 AND card_3_1 > 0 AND card_3_2 > 0
 ) t GROUP BY card1_id, card2_id;
_SQL_;
$res = db_exec($con, $sql);
