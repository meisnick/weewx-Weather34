# w34config.example.py
# Copy this file to w34config.py and fill in your site-specific values.
# w34config.py is gitignored and never committed.

# Station location
LAT = 0.0       # Decimal degrees, N positive  (e.g. 43.46)
LON = 0.0       # Decimal degrees, E positive  (e.g. -87.95)
TZ  = "America/Chicago"   # IANA timezone string

# Nearest airport for METAR data
ICAO = "KABC"   # 4-letter ICAO identifier (e.g. KETB)

# NWS alert zones — find yours at: https://api.weather.gov/points/LAT,LON
# Look for forecastZone (e.g. WIZ060) and county (e.g. WIC089)
ALERT_ZONES = "WIZ000,WIC000"

# Display name written into forecast JSON (cosmetic only)
PLACE_NAME    = "yourtown"
PLACE_STATE   = "xx"
PLACE_COUNTRY = "us"

# Paths to output files (change only if your web root differs)
WEB_ROOT   = "/var/www/html/weewx/weather34"
FORECAST_DAILY_PATH   = WEB_ROOT + "/jsondata/forecast_daily.txt"
FORECAST_HOURLY_PATH   = WEB_ROOT + "/jsondata/forecast_hourly.txt"
ME_PATH    = WEB_ROOT + "/jsondata/me.txt"
ALERTS_PATH = WEB_ROOT + "/jsondata/nws_alerts.txt"
