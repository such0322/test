#!/bin/sh

# sh sync_var.sh (dev1|dev2|dev3|test1|live) (var/version|conf) [version]
# ex: sh sync_var.sh live manage

. /usr/local/bin/magicaldays-hostlist.fnc

ENV=${1:-dummy}
DIR=${2:-dummy}
VER=$3

if test $ENV == "live"; then
	ENV=""
fi
CPDIR=/var/www/${ENV}${ENV:+.}magicaldays.jp/magicaldays-web${VER:+.}${VER}/${DIR}

#echo "/usr/bin/scp ${CPDIR}/*"

if test -d `dirname $CPDIR`; then
	for HNAME in $WEB;
	do
		#echo "/usr/bin/scp ${CPDIR}/* $HNAME:${CPDIR}/"
		/usr/bin/scp ${CPDIR}/* $HNAME:${CPDIR}/
	done
fi
