#!/bin/bash

i=0
while ! nc "$1" "$2" >/dev/null 2>&1 < /dev/null; do
  i=`expr $i + 1`
  if [ $i -ge 50 ]; then
    echo "$(date) - $1:$2 still not reachable, giving up"
    exit 1
  fi
  echo "$(date) - waiting for $1:$2..."
  sleep 1
done
echo "$1 connection established"
