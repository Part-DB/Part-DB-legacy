#!/bin/bash

for directory in `find * -type d -name "update_from_*"`
do
    echo $directory
    
    rm -f "$directory/files.zip"
    zip -qrj "$directory/files.zip" "$directory/files"
    
    rm -f "$directory.zip"
    zip -qr "$directory.zip" "$directory" -x "$directory/files/*"
done
