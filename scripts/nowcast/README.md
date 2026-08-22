# Hyperlocal Short-Term Forecast (Nowcast Engine)

Generates a plain-English 6-hour forecast from your local weewx weather station using historical analogue matching and a local LLM. Runs entirely locally on your Pi/server with no external forecast services involved (completely offline capability).

## How to Deploy for Your Microclimate

This engine has been refactored to be deployed by ANY station, anywhere in the world. 

1. **Configure Your Station (scripts/w34config.py)**
   - Copy `scripts/w34config.example.py` to `scripts/w34config.py` and open it.
   - Set your `LAT` and `LON` (required for sunset/sunrise tracking).
   - Set your `NOWCAST_ONSHORE_WINDS`. If your station is near a coast (ocean or large lake), define which wind vectors bring the marine layer (e.g. `["NE", "ENE", "E"]`).
   - Set `NOWCAST_ONSHORE_NAME` (e.g. "Lake breeze", "Sea breeze"). If you are completely inland, you can leave the list empty `NOWCAST_ONSHORE_WINDS = []`.

2. **Initialize the SQLite Database**
   - Run `python3 feature_builder.py`.
   - This script reads your existing `weewx.sdb` (in read-only mode) and builds a streamlined historical database (`features.db`) that the k-NN engine can search instantly.
   - You need at least 1-2 years of weewx data for analogue matching to be effective.

3. **Install Ollama**
   - Install Ollama locally and download a small model (e.g. `gemma3:1b` or `llama3.2:1b`).
   - `ollama run gemma3:1b`

4. **Set Up the Cron Jobs**
   Run `crontab -e` and add:
   ```cron
   # 1. Update the historical feature database with the latest hour of weewx data (run at :05)
   5  *    * * *  /usr/bin/python3 /path/to/feature_builder.py
   
   # 2. Run the k-NN matcher, format the prompt, and ask Ollama for the text (run at :10)
   10 *    * * *  /usr/bin/python3 /path/to/analogue_forecast.py --json | /usr/bin/python3 /path/to/forecast_narrator.py --write
   ```

## Output JSON format

Output writes to `/var/www/html/weewx/weather34/jsondata/local_forecast.json` (or locally if run manually) and will look like:
```json
{
  "generated_utc": "2026-05-30 09:00 UTC",
  "generated_ts":  1748599500,
  "forecast":      "Temperatures cooling from 74F to near 67F this evening after sunset with a chance of showers.",
  "rain_pct_6h":    13
}
```
