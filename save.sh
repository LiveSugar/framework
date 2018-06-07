#!/bin/sh -e

# Create commit
/usr/bin/git add -A
DATE=`date +%d-%m-%Y' '%H:%M:%S`
/usr/bin/git commit -m "$DATE development"
/usr/bin/git pull origin master
/usr/bin/git push origin master
