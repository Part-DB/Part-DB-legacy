#!/bin/sh
FILES=`svn diff | grep "Index: " | cut -d" " -f2`

for FILE in $FILES; do
	echo -e "\$up2date[513]['copy'][]=array('from'=>'up513/$FILE','to'=>'$FILE');"
done

