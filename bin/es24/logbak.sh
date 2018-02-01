#!/bin/sh

AGO=$1
DOMAIN="app.magicaldays.jp"
SVR="magicaldays-web"

KOKO=`dirname $0`


WEB="localhost"
#. /usr/local/bin/magicaldays-hostlist.fnc

LOGDAY=`date -d "${AGO} day ago" +%Y%m%d`
WORKDIR="/tmp/.es24-logs-magicaldays"


# 処理日から見て全日のクエストログを一次ディレクトリに退避
for WH in $WEB;
do
  for SH in $SVR;
  do
    mkdir -p ${WORKDIR}/${WH}/${SH}
    # for development enviromnet, admin server is the SAME as web server. so this script use "cp" command NOT "scp"
    cp -r /var/www/${DOMAIN}/${SH}/log/*_${LOGDAY}.log ${WORKDIR}/${WH}/${SH}
    #scp -r ${WH}:/var/www/${DOMAIN}/${SH}/log/*_${LOGDAY}.log ${WORKDIR}/${WH}/${SH}
    #echo scp -r ${WH}:/var/www/${DOMAIN}/${SH}/log/*_${LOGDAY}.log ${WORKDIR}/${WH}/${SH}
  done
done

# 処理発動
php ${KOKO}/ngp_basic_log_import.php -d "${WORKDIR}/*/*/" -t ${AGO}
#echo php ${KOKO}/ngp_basic_log_import.php -d "'${WORKDIR}/*/*/'" -t ${AGO}

# 終わったログの削除
rm -rf $WORKDIR

