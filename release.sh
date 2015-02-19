#!/bin/bash

# ------------------------------------------------------------------------------
# Variables:
# ------------------------------------------------------------------------------
PLUGIN_NAME=woocommerce-bring-fraktguiden
SRC_DIR=src
TEMP_DIR=temp
RELEASE_DIR=release
CHANGE_LOG_FIRST_LINE=`head -n 1 ${SRC_DIR}/changelog.txt`
VERSION=`echo "$CHANGE_LOG_FIRST_LINE" | sed 's/[0-9]\{4\}.[0-9]\{2\}.[0-9]\{2\} - version //g'`
RELEASE_DATE=`echo "$CHANGE_LOG_FIRST_LINE" | sed 's/ - version [0-9]\{1,2\}.[0-9]\{1,2\}.[0-9]\{1,2\}//g'`

function print_out() {
  local d=$(date +"%m-%d-%Y %H:%I:%S")
  echo "${d} - $1"
}


#------------------------------------------------------------------------------
# Start:
#------------------------------------------------------------------------------
print_out "[MSG] Releasing version ${VERSION}"

# ------------------------------------------------------------------------------
# Setup directories for the release:
# ------------------------------------------------------------------------------
if [ ! -d ${RELEASE_DIR} ]; then
  mkdir ${RELEASE_DIR}
  print_out "[MSG] ${RELEASE_DIR} directory created."
fi

temp_src=${TEMP_DIR}/${PLUGIN_NAME}
mkdir -p ${temp_src}

# ------------------------------------------------------------------------------
# Copy source files to temp dir:
# ------------------------------------------------------------------------------
cp -r ${SRC_DIR}/* ${temp_src}

# ------------------------------------------------------------------------------
# Update version number in plugin file:
# ------------------------------------------------------------------------------

# Temporary rename plugin file.
mv ${temp_src}/${PLUGIN_NAME}.php ${temp_src}/${PLUGIN_NAME}.temp

# Replace the version placeholder with the version number and save the file.
sed -e 's/##VERSION##/'${VERSION}\/g ${temp_src}/${PLUGIN_NAME}.temp > ${temp_src}/${PLUGIN_NAME}.php

# Remove the temporary plugin file.
rm ${temp_src}/${PLUGIN_NAME}.temp

# ------------------------------------------------------------------------------
# Create the zip release:
# ------------------------------------------------------------------------------
epoch_time=$(date +%s)
file_name=${PLUGIN_NAME}-${VERSION}-${epoch_time}.zip
cd ${TEMP_DIR} && zip -r -q ${file_name} ${PLUGIN_NAME}/*
mv ${file_name} ../${RELEASE_DIR} && cd ..

# ------------------------------------------------------------------------------
# Clean up:
# ------------------------------------------------------------------------------
rm -rf ${TEMP_DIR}
print_out "[MSG] Cleaning up."
print_out "[SUCCESS] ${RELEASE_DIR}/${file_name} created."







