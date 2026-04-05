# Air Quality Modules

The new air quality modules require a database source of particle concentration data. The new WeeWX 4.x.x extended database schema includes fields for PM1.0, PM2.5 and PM10.0. Due to the variety of air quality measuring devices that are now available, it is beyond the remit of these instructions to describe in detail the process for each type of sensor. It is assumed for the purposes of these instructions that you are using the extended database and the particle concentration fields are being populated.

## Module 1 - World (US EPA) AQI PM2.5
Requires the database field `pm2_5` to be populated. This module gives a readout for the Global AQI and NOW which is based on the United States Environmental Protection Agency method of calculation.

## Module 2 - UK DAQI PM2.5 and PM10
Requires both the `pm2_5` and `pm10_0` database fields to be populated. The United Kingdom Daily Air Quality Index for both 2.5 and 10.0 particle concentrations are displayed.

Both these modules have information pop-up windows when the centre of a module is clicked. Selection of these modules is facilitated from the Weather34 settings page.

## Module 3 - Regular Sized AQI Display
This is a regular sized module, again selected from the Weather34 settings page. It displays the World (US EPA) AQI PM2.5 figure. Links to charts for PM2.5 and PM10.0 particle concentrations can be found at the base of this module.
