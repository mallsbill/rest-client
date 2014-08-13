#!/bin/bash

BASE=$(readlink -f $(dirname $0))

ATOUM_BIN=$BASE/../vendor/bin/atoum

if [ ! -f "$ATOUM_BIN" ] ; then
	echo "Atoum not installed, please run 'composer.phar update --dev'"
	exit
fi

echo "Run Atoum"

if [ ! -z "$1" ] ; then
	FILE=$BASE/$1
	$ATOUM_BIN -f $FILE
else

	for FILE in $(find $BASE/Flex/ -name '*.php'); do
	    if [ -f "$FILE" ] ; then
	        $ATOUM_BIN -f $FILE
	    fi
	done

fi

