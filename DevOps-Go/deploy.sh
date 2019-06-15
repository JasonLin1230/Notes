#! /bin/sh

kill -9 $(pgrep webserver)

cd ~/devOps/

git pull https://github.com/JasonLin1230/Notes.git

cd ./DevOps-Go/webserver

./webserver &
