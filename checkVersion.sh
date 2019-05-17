#!/bin/bash

# Author: Asher Simcha
# Date: 05-01-2019
# Version: 1.0.1
# Date Modified 05-16-2019

# // filename: checkVersion.sh
# // Copyright (C) 2019 aka Asher Simcha 
# // This library is free software; you can redistribute it and/or modify it under the
# // terms of the The 3-Clause BSD License as published by the
# // Open Source Initiative; version 3
# 
# // This library is distributed in the hope that it will be useful,
# // but WITHOUT ANY WARRANTY; without even the implied warranty of
# // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# // The 3-Clause BSD License for more details.
# 
# // You should have received a copy of The 3-Clause BSD License
# // see the files The 3-Clause BSD License.txt respectively.  If not, see
# // <https://opensource.org/licenses/BSD-3-Clause/>.

# exiting codes
# 0 both versions match... You have the most uptodate version
# 1 the versions do NOT match... You need to upgrade your system
# 15 url does NOT exits
# 16 the SERVER's version control file is missing, most likely write permission issues
# 17 the CLIENT's version control file is missing

filenameCheckVersion="Version.inf" # CLIENT's version control file
SERVERCHECKVERSION="https://asher-simcha.github.io/Current-Version/index.html" # SERVER's file
MAINSITE="https://github.com/Asher-Simcha/Current-Version" # if out of date this is where to go to find the updated program.
# $MAINSITE you could also pull this from the SERVER's file if you wanted to.

exiting () {
	# this is a great place to erase tmp files, files, and do your closing of the program.
	exit $1
}


checkVersion () {
	local filenameCheckVersionSERVER="CheckVersionSERVER"
	# check if file on the internet exist otherwise exit
	if ! curl --output /dev/null --silent --head --fail "$2"; then
# 	echo "The way to check if your Version is up to date is NOT present on the internet"
# 	echo "The URL does NOT exist: $2"
	return 15
	fi

	# download the file from the internet
	curl -L $2 -o "$filenameCheckVersionSERVER" &> /dev/null

	# check if server file exist if NOT exit
	if [ -s $filenameCheckVersionSERVER ]; then
		COUNTSERVER=0
		#echo "the SERVER file exist and is not empty"
		
		# open and read the file line by line
		while NOTEOF4='' read -r line || [[ -n "$line" ]]; do
			let "COUNTSERVER += 1"
			if [ $COUNTSERVER -eq 1  ]; then
				SERVERCURRENTVERSION=$line
			fi
		done < "$filenameCheckVersionSERVER"
	else
# 		echo "the file does NOT exist:"
# 		echo "$filenameCheckVersionSERVER"
		#echo "exiting"
		return 16
	fi

	if [ -s $1 ]; then
		COUNTVERSION=0
		#echo "the CLIENT file exist and is not empty"
		while NOTEOF4='' read -r line || [[ -n "$line" ]]; do
			let "COUNTVERSION += 1"
			# NOTE if you wanted you could create a filter here if the server file is more than just one line!
			# is you look at the source code of the html file, there is only one line, there is NO html headings there.
			if [ $COUNTVERSION -eq 1  ]; then
				CLIENTSCURRENTVERSION=$line
			fi
		done < "$1" 	
	else
# 		echo "the file does NOT exist:"
# 		echo "$1"
# 		echo "exiting"
		return 17
	fi
	#echo "SERVERCURRENTVERSION $SERVERCURRENTVERSION"
	#echo "CLIENTSCURRENTVERSION $CLIENTSCURRENTVERSION"
	if [ "$SERVERCURRENTVERSION" = "$CLIENTSCURRENTVERSION"  ]; then
# 		echo "The Versions Match"
		return 0
	else
# 		echo "The current version of this program is out of date"
# 		echo "There is a new updated version of this program"
# 		echo "Please go to: $MAINSITE"
# 		echo "and Update this program"
		return 1
	fi

	rm $filenameCheckVersionSERVER
}

# this is the main() part of the program

checkVersion $filenameCheckVersion $SERVERCHECKVERSION
retval=$?
# the previous line converted the output $? to a variable named $retval

#echo "the return is: $retval"

# if retrieved value equals zero 
if [ $retval -eq 0 ]; then
	echo "Your Version is up to date!:)";
	exiting 0
# else if the retrieved value equals 1	
elif [ $retval -eq 1 ]; then
	echo "Your Version is out of date.:(";
	echo "Please go to: $MAINSITE"
	echo "and Update this program"
	exiting $retval
# else if the retrieved value is greater than 1	
elif [ $retval -gt 1 ]; then
	echo "error code something went wrong"
	echo "exiting with error code $retval"
	echo "check notes at top of page or the README.md file."
	exiting $retval;
fi;
#EOF
