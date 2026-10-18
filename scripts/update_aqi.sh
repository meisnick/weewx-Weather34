#!/bin/bash
python3 -c "
import json
with open('/var/www/html/weewx/weather34/jsondata/aq.txt') as f:
    data = json.load(f)
iaqi = data.get('data', {}).get('iaqi', {})
out = {
    'pm25': iaqi.get('pm25', {}).get('v', 0),
    'pm10': iaqi.get('pm10', {}).get('v', 0)
}
with open('/var/www/html/weewx/weather34/jsondata/aqiJson.txt', 'w') as f:
    json.dump(out, f)
"
