#!/bin/bash
umask 002
DEST=/var/www/html/weewx/weather34/jsondata/aurora_power.txt
TMP=${DEST}.tmp
curl -s --connect-timeout 15 \
  "https://services.swpc.noaa.gov/text/aurora-nowcast-hemi-power.txt" \
  -o "$TMP" && mv "$TMP" "$DEST" && chmod 664 "$DEST" || rm -f "$TMP"
