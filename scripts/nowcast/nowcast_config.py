# nowcast_config.py
# Configuration for the Analogue Nowcast Engine

# 1. Geographic Location (Used for Solar/Sunrise/Sunset Calculations)
# Adjust to your station's latitude and longitude.
LAT = 43.4686
LON = -87.9507

# 2. Local Microclimate / Onshore Winds
# Many coastal or lakeside stations have a distinct 'breeze' effect.
# Define the wind directions that trigger this, and the name to use in the forecast.
# If you are inland, you can leave these as is and the LLM just won't trigger the prefix, 
# or you can clear the ONSHORE_WINDS list entirely.
ONSHORE_WINDS = {"NE", "ENE", "E", "ESE", "SE"}
ONSHORE_NAME = "Lake breeze"
# ONSHORE_NAME = "Sea breeze"
# ONSHORE_NAME = "Ocean breeze"

# 3. Timezone Logic (in solar_util.py)
# Note: Timezone logic is currently hardcoded for US Central (CDT/CST) in solar_util.py.
# If you are outside the US Central time zone, you will need to update the 
# get_central_offset() function in solar_util.py to match your local timezone offsets.

# 4. Ollama LLM Configuration
OLLAMA_URL = "http://localhost:11434/api/chat"
OLLAMA_MODEL = "gemma3:1b"
