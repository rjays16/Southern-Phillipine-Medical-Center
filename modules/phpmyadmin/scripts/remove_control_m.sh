#!/bin/sh
#
# $Id: remove_control_m.sh,v 1.2 2005/10/29 20:08:13 kaloyan_raev Exp $
#
# Script to remove ^M from files for DOS <-> UNIX conversions
#

if [ $# != 1 ]
then
  echo "Usage: remove_control_m.sh <extension of files>"
  echo ""
  echo "Example: remove_control_m.sh php3"
  exit
fi

for i in `find . -name "*.$1"`
	 do 
	 echo $i
	 tr -d '\015' < $i > ${i}.new
	 rm $i
	 mv ${i}.new $i
	done;

