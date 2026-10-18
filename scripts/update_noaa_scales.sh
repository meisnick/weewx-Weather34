#!/bin/bash
umask 002
WD=/var/www/html/weewx/weather34/jsondata

# NOAA current R/S/G scales
DEST=$WD/noaa_scales.json; TMP=$DEST.tmp
curl -s --connect-timeout 15 "https://services.swpc.noaa.gov/products/noaa-scales.json" \
  -o "$TMP" && mv "$TMP" "$DEST" && chmod 664 "$DEST" || rm -f "$TMP"

# 3-day geomagnetic forecast text
DEST=$WD/aurora_forecast.txt; TMP=$DEST.tmp
curl -s --connect-timeout 15 "https://services.swpc.noaa.gov/text/3-day-geomag-forecast.txt" \
  -o "$TMP" && mv "$TMP" "$DEST" && chmod 664 "$DEST" || rm -f "$TMP"
