#!/bin/sh

# sh sync_file.sh (dev1|dev2|dev3|test1|live) (path/to/file) [version]
# ex: sh sync_file.sh live res/global_bonus_rate.php 130

. /usr/local/bin/magicaldays-hostlist.fnc

ENV=${1:-dummy}
FILE=${2:-dummy}
VER=$3

if test $ENV == "live"; then
	ENV=""
fi
CPFILE=/var/www/${ENV}${ENV:+.}magicaldays.jp/magicaldays-web${VER:+.}${VER}/${DIR}/${FILE}

echo "/usr/bin/scp ${CPFILE} "

if test -f $CPFILE; then
	for HNAME in $WEB;
	do
		echo "/usr/bin/scp ${CPFILE} $HNAME:${CPFILE}"
		/usr/bin/scp ${CPFILE} $HNAME:${CPFILE}
	done
fi
