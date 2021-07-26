#!/bin/sh

current_build=`cat build`
new_build=`echo $current_build | awk -F. -v OFS=. 'NF==1{print ++$NF}; NF>1{if(length($NF+1)>length($NF))$(NF-1)++; $NF=sprintf("%0*d", length($NF), ($NF+1)%(10^length($NF))); print}'`
printf $new_build > build
echo "Build Increment from $current_build to $new_build"
