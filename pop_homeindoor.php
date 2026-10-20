<?php include_once('w34_i18n.php'); ?>
<?php 
//original weather34 script original css/svg/php by weather34 2015-2019 clearly marked as original by weather34//
include_once('w34CombinedData.php');
if (isset($_GET['theme'])) {
    $theme = ($_GET['theme'] === 'light') ? 'light' : 'dark';
}
$heatindexwu='<svg "weather34 heatindex icon" width="10px" height="10px" viewBox="0 0 250 250" version="3.4.2019" >
<path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 94.24 33.23 C 106.10 23.71 121.88 18.18 137.10 20.91 C 150.77 23.09 162.89 30.83 172.86 40.14 C 185.30 51.88 198.09 63.70 213.46 71.52 C 222.73 76.23 233.65 79.13 243.92 76.10 C 245.97 75.67 248.52 74.29 250.00 76.59 L 250.00 77.41 C 243.65 83.99 235.05 88.09 226.37 90.62 C 211.92 94.57 196.20 91.32 183.71 83.32 C 173.86 77.45 166.16 68.84 157.60 61.36 C 148.96 53.84 139.88 46.65 129.66 41.38 C 120.64 36.87 110.14 33.45 100.05 36.12 C 97.82 36.48 95.53 37.70 93.28 36.81 C 92.94 35.46 92.75 33.94 94.24 33.23 Z" /><path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 40.96 70.24 C 55.35 59.16 73.48 51.97 91.86 53.82 C 112.91 55.25 131.31 67.49 146.46 81.37 C 160.43 94.60 174.85 107.67 191.64 117.27 C 205.48 125.28 221.84 130.62 237.92 127.34 C 240.94 127.12 244.23 125.18 247.05 127.08 C 247.03 127.74 246.99 129.04 246.97 129.70 C 232.18 142.37 212.89 151.02 193.10 149.31 C 176.62 148.37 161.34 140.68 148.46 130.76 C 141.24 125.29 134.74 118.98 128.15 112.80 C 113.51 99.62 97.87 86.88 79.42 79.47 C 68.23 74.90 55.71 73.38 43.88 76.15 C 41.67 76.37 38.79 77.88 37.14 75.68 C 36.36 73.01 39.31 71.67 40.96 70.24 Z" /><path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 3.50 118.53 C 15.56 108.44 30.33 100.94 46.17 99.59 C 64.81 97.72 83.26 104.85 98.11 115.75 C 106.84 121.93 114.50 129.44 122.30 136.71 C 134.76 147.91 147.93 158.61 162.99 166.12 C 173.19 171.17 184.49 174.68 195.98 174.06 C 201.25 173.92 206.34 172.21 211.61 171.97 C 212.00 172.54 212.79 173.68 213.18 174.25 C 211.39 177.16 208.29 178.78 205.59 180.71 C 195.79 187.36 184.84 192.67 173.06 194.55 C 161.43 196.13 149.37 194.93 138.42 190.57 C 122.59 184.47 109.28 173.47 97.23 161.77 C 84.46 149.94 71.03 138.53 55.65 130.21 C 44.46 124.18 31.89 119.80 19.01 120.66 C 14.33 120.76 9.80 122.01 5.19 122.66 C 3.05 123.04 1.43 119.77 3.50 118.53 Z" /><path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 26.44 157.60 C 40.14 154.39 54.65 158.24 66.35 165.64 C 77.14 172.14 85.34 181.89 94.99 189.83 C 106.04 199.15 118.02 208.17 132.13 212.13 C 139.32 214.27 147.04 213.87 154.14 211.59 C 156.28 210.49 158.15 213.79 156.18 215.09 C 147.11 222.60 135.83 227.63 124.02 228.51 C 106.54 229.76 90.07 220.91 77.61 209.29 C 63.90 196.42 49.84 183.15 32.41 175.48 C 23.21 171.33 12.55 170.13 2.91 173.51 C 1.29 174.56 -1.18 171.20 0.84 170.21 C 8.51 164.54 17.09 159.79 26.44 157.60 Z" /></svg>';
$tempicon='<svg "weather34 temp icon" width="10px" height="10px" viewBox="0 0 250 250" version="3.4.2019" >
<path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 94.24 33.23 C 106.10 23.71 121.88 18.18 137.10 20.91 C 150.77 23.09 162.89 30.83 172.86 40.14 C 185.30 51.88 198.09 63.70 213.46 71.52 C 222.73 76.23 233.65 79.13 243.92 76.10 C 245.97 75.67 248.52 74.29 250.00 76.59 L 250.00 77.41 C 243.65 83.99 235.05 88.09 226.37 90.62 C 211.92 94.57 196.20 91.32 183.71 83.32 C 173.86 77.45 166.16 68.84 157.60 61.36 C 148.96 53.84 139.88 46.65 129.66 41.38 C 120.64 36.87 110.14 33.45 100.05 36.12 C 97.82 36.48 95.53 37.70 93.28 36.81 C 92.94 35.46 92.75 33.94 94.24 33.23 Z" /><path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 40.96 70.24 C 55.35 59.16 73.48 51.97 91.86 53.82 C 112.91 55.25 131.31 67.49 146.46 81.37 C 160.43 94.60 174.85 107.67 191.64 117.27 C 205.48 125.28 221.84 130.62 237.92 127.34 C 240.94 127.12 244.23 125.18 247.05 127.08 C 247.03 127.74 246.99 129.04 246.97 129.70 C 232.18 142.37 212.89 151.02 193.10 149.31 C 176.62 148.37 161.34 140.68 148.46 130.76 C 141.24 125.29 134.74 118.98 128.15 112.80 C 113.51 99.62 97.87 86.88 79.42 79.47 C 68.23 74.90 55.71 73.38 43.88 76.15 C 41.67 76.37 38.79 77.88 37.14 75.68 C 36.36 73.01 39.31 71.67 40.96 70.24 Z" /><path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 3.50 118.53 C 15.56 108.44 30.33 100.94 46.17 99.59 C 64.81 97.72 83.26 104.85 98.11 115.75 C 106.84 121.93 114.50 129.44 122.30 136.71 C 134.76 147.91 147.93 158.61 162.99 166.12 C 173.19 171.17 184.49 174.68 195.98 174.06 C 201.25 173.92 206.34 172.21 211.61 171.97 C 212.00 172.54 212.79 173.68 213.18 174.25 C 211.39 177.16 208.29 178.78 205.59 180.71 C 195.79 187.36 184.84 192.67 173.06 194.55 C 161.43 196.13 149.37 194.93 138.42 190.57 C 122.59 184.47 109.28 173.47 97.23 161.77 C 84.46 149.94 71.03 138.53 55.65 130.21 C 44.46 124.18 31.89 119.80 19.01 120.66 C 14.33 120.76 9.80 122.01 5.19 122.66 C 3.05 123.04 1.43 119.77 3.50 118.53 Z" /><path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 26.44 157.60 C 40.14 154.39 54.65 158.24 66.35 165.64 C 77.14 172.14 85.34 181.89 94.99 189.83 C 106.04 199.15 118.02 208.17 132.13 212.13 C 139.32 214.27 147.04 213.87 154.14 211.59 C 156.28 210.49 158.15 213.79 156.18 215.09 C 147.11 222.60 135.83 227.63 124.02 228.51 C 106.54 229.76 90.07 220.91 77.61 209.29 C 63.90 196.42 49.84 183.15 32.41 175.48 C 23.21 171.33 12.55 170.13 2.91 173.51 C 1.29 174.56 -1.18 171.20 0.84 170.21 C 8.51 164.54 17.09 159.79 26.44 157.60 Z" /></svg>';
$airqualitybreeze='<svg "weather34 aq icon" width="10px" height="10px" viewBox="0 0 250 250" version="3.4.2019" >
<path fill="currentcolor" stroke="currentcolor" stroke-width="0.09375" opacity="1.00" d=" M 94.24 33.23 C 106.10 23.71 121.88 18.18 137.10 20.91 C 150.77 23.09 162.89 30.83 172.86 40.14 C 185.30 51.88 198.09 63.70 213.46 71.52 C 222.73 76.23 233.65 79.13 243.92 76.10 C 245.97 75.67 248.52 74.29 250.00 76.59 L 250.00 77.41 C 243.65 83.99 235.05 88.09 226.37 90.62 C 211.92 94.57 196.20 91.32 183.71 83.32 C 173.86 77.45 166.16 68.84 157.60 61.36 C 148.96 53.84 139.88 46.65 129.66 41.38 C 120.64 36.87 110.14 33.45 100.05 36.12 C 97.82 36.48 95.53 37.70 93.28 36.81 C 92.94 35.46 92.75 33.94 94.24 33.23 Z" /><path fill="currentcolor" stroke="currentcolor" stroke-width="0.09375" opacity="1.00" d=" M 40.96 70.24 C 55.35 59.16 73.48 51.97 91.86 53.82 C 112.91 55.25 131.31 67.49 146.46 81.37 C 160.43 94.60 174.85 107.67 191.64 117.27 C 205.48 125.28 221.84 130.62 237.92 127.34 C 240.94 127.12 244.23 125.18 247.05 127.08 C 247.03 127.74 246.99 129.04 246.97 129.70 C 232.18 142.37 212.89 151.02 193.10 149.31 C 176.62 148.37 161.34 140.68 148.46 130.76 C 141.24 125.29 134.74 118.98 128.15 112.80 C 113.51 99.62 97.87 86.88 79.42 79.47 C 68.23 74.90 55.71 73.38 43.88 76.15 C 41.67 76.37 38.79 77.88 37.14 75.68 C 36.36 73.01 39.31 71.67 40.96 70.24 Z" /><path fill="currentcolor" stroke="currentcolor" stroke-width="0.09375" opacity="1.00" d=" M 3.50 118.53 C 15.56 108.44 30.33 100.94 46.17 99.59 C 64.81 97.72 83.26 104.85 98.11 115.75 C 106.84 121.93 114.50 129.44 122.30 136.71 C 134.76 147.91 147.93 158.61 162.99 166.12 C 173.19 171.17 184.49 174.68 195.98 174.06 C 201.25 173.92 206.34 172.21 211.61 171.97 C 212.00 172.54 212.79 173.68 213.18 174.25 C 211.39 177.16 208.29 178.78 205.59 180.71 C 195.79 187.36 184.84 192.67 173.06 194.55 C 161.43 196.13 149.37 194.93 138.42 190.57 C 122.59 184.47 109.28 173.47 97.23 161.77 C 84.46 149.94 71.03 138.53 55.65 130.21 C 44.46 124.18 31.89 119.80 19.01 120.66 C 14.33 120.76 9.80 122.01 5.19 122.66 C 3.05 123.04 1.43 119.77 3.50 118.53 Z" /><path fill="currentcolor" stroke="currentcolor" stroke-width="0.09375" opacity="1.00" d=" M 26.44 157.60 C 40.14 154.39 54.65 158.24 66.35 165.64 C 77.14 172.14 85.34 181.89 94.99 189.83 C 106.04 199.15 118.02 208.17 132.13 212.13 C 139.32 214.27 147.04 213.87 154.14 211.59 C 156.28 210.49 158.15 213.79 156.18 215.09 C 147.11 222.60 135.83 227.63 124.02 228.51 C 106.54 229.76 90.07 220.91 77.61 209.29 C 63.90 196.42 49.84 183.15 32.41 175.48 C 23.21 171.33 12.55 170.13 2.91 173.51 C 1.29 174.56 -1.18 171.20 0.84 170.21 C 8.51 164.54 17.09 159.79 26.44 157.60 Z" /></svg>';
$humiditybreeze='<svg "weather34 humidity icon" width="10px" height="10px" viewBox="0 0 250 250" version="3.4.2019" >
<path fill="#e6a141" stroke="#e6a141" stroke-width="0.09375" opacity="1.00" d=" M 94.24 33.23 C 106.10 23.71 121.88 18.18 137.10 20.91 C 150.77 23.09 162.89 30.83 172.86 40.14 C 185.30 51.88 198.09 63.70 213.46 71.52 C 222.73 76.23 233.65 79.13 243.92 76.10 C 245.97 75.67 248.52 74.29 250.00 76.59 L 250.00 77.41 C 243.65 83.99 235.05 88.09 226.37 90.62 C 211.92 94.57 196.20 91.32 183.71 83.32 C 173.86 77.45 166.16 68.84 157.60 61.36 C 148.96 53.84 139.88 46.65 129.66 41.38 C 120.64 36.87 110.14 33.45 100.05 36.12 C 97.82 36.48 95.53 37.70 93.28 36.81 C 92.94 35.46 92.75 33.94 94.24 33.23 Z" /><path fill="#e6a141" stroke="#e6a141" stroke-width="0.09375" opacity="1.00" d=" M 40.96 70.24 C 55.35 59.16 73.48 51.97 91.86 53.82 C 112.91 55.25 131.31 67.49 146.46 81.37 C 160.43 94.60 174.85 107.67 191.64 117.27 C 205.48 125.28 221.84 130.62 237.92 127.34 C 240.94 127.12 244.23 125.18 247.05 127.08 C 247.03 127.74 246.99 129.04 246.97 129.70 C 232.18 142.37 212.89 151.02 193.10 149.31 C 176.62 148.37 161.34 140.68 148.46 130.76 C 141.24 125.29 134.74 118.98 128.15 112.80 C 113.51 99.62 97.87 86.88 79.42 79.47 C 68.23 74.90 55.71 73.38 43.88 76.15 C 41.67 76.37 38.79 77.88 37.14 75.68 C 36.36 73.01 39.31 71.67 40.96 70.24 Z" /><path fill="#e6a141" stroke="#e6a141" stroke-width="0.09375" opacity="1.00" d=" M 3.50 118.53 C 15.56 108.44 30.33 100.94 46.17 99.59 C 64.81 97.72 83.26 104.85 98.11 115.75 C 106.84 121.93 114.50 129.44 122.30 136.71 C 134.76 147.91 147.93 158.61 162.99 166.12 C 173.19 171.17 184.49 174.68 195.98 174.06 C 201.25 173.92 206.34 172.21 211.61 171.97 C 212.00 172.54 212.79 173.68 213.18 174.25 C 211.39 177.16 208.29 178.78 205.59 180.71 C 195.79 187.36 184.84 192.67 173.06 194.55 C 161.43 196.13 149.37 194.93 138.42 190.57 C 122.59 184.47 109.28 173.47 97.23 161.77 C 84.46 149.94 71.03 138.53 55.65 130.21 C 44.46 124.18 31.89 119.80 19.01 120.66 C 14.33 120.76 9.80 122.01 5.19 122.66 C 3.05 123.04 1.43 119.77 3.50 118.53 Z" /><path fill="#e6a141" stroke="#e6a141" stroke-width="0.09375" opacity="1.00" d=" M 26.44 157.60 C 40.14 154.39 54.65 158.24 66.35 165.64 C 77.14 172.14 85.34 181.89 94.99 189.83 C 106.04 199.15 118.02 208.17 132.13 212.13 C 139.32 214.27 147.04 213.87 154.14 211.59 C 156.28 210.49 158.15 213.79 156.18 215.09 C 147.11 222.60 135.83 227.63 124.02 228.51 C 106.54 229.76 90.07 220.91 77.61 209.29 C 63.90 196.42 49.84 183.15 32.41 175.48 C 23.21 171.33 12.55 170.13 2.91 173.51 C 1.29 174.56 -1.18 171.20 0.84 170.21 C 8.51 164.54 17.09 159.79 26.44 157.60 Z" /></svg>';
$feelslikewu='<svg "weather34 feels  icon" width="10px" height="10px" viewBox="0 0 250 250" version="3.4.2019" >
<path fill="#d05f2d" stroke="#d05f2d" stroke-width="0.09375" opacity="1.00" d=" M 94.24 33.23 C 106.10 23.71 121.88 18.18 137.10 20.91 C 150.77 23.09 162.89 30.83 172.86 40.14 C 185.30 51.88 198.09 63.70 213.46 71.52 C 222.73 76.23 233.65 79.13 243.92 76.10 C 245.97 75.67 248.52 74.29 250.00 76.59 L 250.00 77.41 C 243.65 83.99 235.05 88.09 226.37 90.62 C 211.92 94.57 196.20 91.32 183.71 83.32 C 173.86 77.45 166.16 68.84 157.60 61.36 C 148.96 53.84 139.88 46.65 129.66 41.38 C 120.64 36.87 110.14 33.45 100.05 36.12 C 97.82 36.48 95.53 37.70 93.28 36.81 C 92.94 35.46 92.75 33.94 94.24 33.23 Z" /><path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 40.96 70.24 C 55.35 59.16 73.48 51.97 91.86 53.82 C 112.91 55.25 131.31 67.49 146.46 81.37 C 160.43 94.60 174.85 107.67 191.64 117.27 C 205.48 125.28 221.84 130.62 237.92 127.34 C 240.94 127.12 244.23 125.18 247.05 127.08 C 247.03 127.74 246.99 129.04 246.97 129.70 C 232.18 142.37 212.89 151.02 193.10 149.31 C 176.62 148.37 161.34 140.68 148.46 130.76 C 141.24 125.29 134.74 118.98 128.15 112.80 C 113.51 99.62 97.87 86.88 79.42 79.47 C 68.23 74.90 55.71 73.38 43.88 76.15 C 41.67 76.37 38.79 77.88 37.14 75.68 C 36.36 73.01 39.31 71.67 40.96 70.24 Z" /><path fill="#e6a141" stroke="#e6a141" stroke-width="0.09375" opacity="1.00" d=" M 3.50 118.53 C 15.56 108.44 30.33 100.94 46.17 99.59 C 64.81 97.72 83.26 104.85 98.11 115.75 C 106.84 121.93 114.50 129.44 122.30 136.71 C 134.76 147.91 147.93 158.61 162.99 166.12 C 173.19 171.17 184.49 174.68 195.98 174.06 C 201.25 173.92 206.34 172.21 211.61 171.97 C 212.00 172.54 212.79 173.68 213.18 174.25 C 211.39 177.16 208.29 178.78 205.59 180.71 C 195.79 187.36 184.84 192.67 173.06 194.55 C 161.43 196.13 149.37 194.93 138.42 190.57 C 122.59 184.47 109.28 173.47 97.23 161.77 C 84.46 149.94 71.03 138.53 55.65 130.21 C 44.46 124.18 31.89 119.80 19.01 120.66 C 14.33 120.76 9.80 122.01 5.19 122.66 C 3.05 123.04 1.43 119.77 3.50 118.53 Z" /><path fill="#90b12a" stroke="#90b12a" stroke-width="0.09375" opacity="1.00" d=" M 26.44 157.60 C 40.14 154.39 54.65 158.24 66.35 165.64 C 77.14 172.14 85.34 181.89 94.99 189.83 C 106.04 199.15 118.02 208.17 132.13 212.13 C 139.32 214.27 147.04 213.87 154.14 211.59 C 156.28 210.49 158.15 213.79 156.18 215.09 C 147.11 222.60 135.83 227.63 124.02 228.51 C 106.54 229.76 90.07 220.91 77.61 209.29 C 63.90 196.42 49.84 183.15 32.41 175.48 C 23.21 171.33 12.55 170.13 2.91 173.51 C 1.29 174.56 -1.18 171.20 0.84 170.21 C 8.51 164.54 17.09 159.79 26.44 157.60 Z" /></svg>';
$windchillwu='<svg "weather34 windchill icon" width="10px" height="10px" viewBox="0 0 250 250" version="3.4.2019" >
<path fill="#009bab" stroke="#009bab" stroke-width="0.09375" opacity="1.00" d=" M 94.24 33.23 C 106.10 23.71 121.88 18.18 137.10 20.91 C 150.77 23.09 162.89 30.83 172.86 40.14 C 185.30 51.88 198.09 63.70 213.46 71.52 C 222.73 76.23 233.65 79.13 243.92 76.10 C 245.97 75.67 248.52 74.29 250.00 76.59 L 250.00 77.41 C 243.65 83.99 235.05 88.09 226.37 90.62 C 211.92 94.57 196.20 91.32 183.71 83.32 C 173.86 77.45 166.16 68.84 157.60 61.36 C 148.96 53.84 139.88 46.65 129.66 41.38 C 120.64 36.87 110.14 33.45 100.05 36.12 C 97.82 36.48 95.53 37.70 93.28 36.81 C 92.94 35.46 92.75 33.94 94.24 33.23 Z" /><path fill="#009bab" stroke="#009bab" stroke-width="0.09375" opacity="1.00" d=" M 40.96 70.24 C 55.35 59.16 73.48 51.97 91.86 53.82 C 112.91 55.25 131.31 67.49 146.46 81.37 C 160.43 94.60 174.85 107.67 191.64 117.27 C 205.48 125.28 221.84 130.62 237.92 127.34 C 240.94 127.12 244.23 125.18 247.05 127.08 C 247.03 127.74 246.99 129.04 246.97 129.70 C 232.18 142.37 212.89 151.02 193.10 149.31 C 176.62 148.37 161.34 140.68 148.46 130.76 C 141.24 125.29 134.74 118.98 128.15 112.80 C 113.51 99.62 97.87 86.88 79.42 79.47 C 68.23 74.90 55.71 73.38 43.88 76.15 C 41.67 76.37 38.79 77.88 37.14 75.68 C 36.36 73.01 39.31 71.67 40.96 70.24 Z" /><path fill="#009bab" stroke="#009bab" stroke-width="0.09375" opacity="1.00" d=" M 3.50 118.53 C 15.56 108.44 30.33 100.94 46.17 99.59 C 64.81 97.72 83.26 104.85 98.11 115.75 C 106.84 121.93 114.50 129.44 122.30 136.71 C 134.76 147.91 147.93 158.61 162.99 166.12 C 173.19 171.17 184.49 174.68 195.98 174.06 C 201.25 173.92 206.34 172.21 211.61 171.97 C 212.00 172.54 212.79 173.68 213.18 174.25 C 211.39 177.16 208.29 178.78 205.59 180.71 C 195.79 187.36 184.84 192.67 173.06 194.55 C 161.43 196.13 149.37 194.93 138.42 190.57 C 122.59 184.47 109.28 173.47 97.23 161.77 C 84.46 149.94 71.03 138.53 55.65 130.21 C 44.46 124.18 31.89 119.80 19.01 120.66 C 14.33 120.76 9.80 122.01 5.19 122.66 C 3.05 123.04 1.43 119.77 3.50 118.53 Z" /><path fill="#009bab" stroke="#009bab" stroke-width="0.09375" opacity="1.00" d=" M 26.44 157.60 C 40.14 154.39 54.65 158.24 66.35 165.64 C 77.14 172.14 85.34 181.89 94.99 189.83 C 106.04 199.15 118.02 208.17 132.13 212.13 C 139.32 214.27 147.04 213.87 154.14 211.59 C 156.28 210.49 158.15 213.79 156.18 215.09 C 147.11 222.60 135.83 227.63 124.02 228.51 C 106.54 229.76 90.07 220.91 77.61 209.29 C 63.90 196.42 49.84 183.15 32.41 175.48 C 23.21 171.33 12.55 170.13 2.91 173.51 C 1.29 174.56 -1.18 171.20 0.84 170.21 C 8.51 164.54 17.09 159.79 26.44 157.60 Z" /></svg>';
?>

<!DOCTYPE html>
<html lang="<?php include_once __DIR__ . '/w34_i18n.php'; echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo w34t('LinkIndoorGuide', 'Indoor Guide'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/tokens.css">
  <style>
@font-face{font-family:weathertext2;src:url(css/fonts/verbatim-regular.woff) format("woff"),url(fonts/verbatim-regular.woff2) format("woff2"),url(fonts/verbatim-regular.ttf) format("truetype");}
html,body{font-size:13px;font-family:"weathertext2",Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;background-color:var(--pop-bg);color:var(--pop-ink);margin:0;padding:0;}
[data-theme="light"] body,[data-theme="light"] html{color:black;}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,2fr));grid-gap:5px;align-items:stretch;color:var(--pop-ink);-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;padding:4px 44px 4px 4px;box-sizing:border-box;}
[data-theme="light"] .grid{color:black;}
.grid > article{border:1px solid rgba(245,247,252,0.02);box-shadow:2px 2px 6px 0px rgba(0,0,0,0.3);padding:5px;font-size:0.8em;-webkit-border-radius:4px;border-radius:4px;height:160px;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;background:hsla(233,12%,13%,.5);}
[data-theme="light"] .grid > article{border:1px solid rgba(0,0,0,0.08);box-shadow:2px 2px 6px 0px rgba(0,0,0,0.1);background:#fff;}
.weather34darkbrowser{position:relative;background:0;width:97%;height:30px;margin:auto;margin-top:-5px;margin-left:0px;border-top-left-radius:5px;border-top-right-radius:5px;padding-top:10px;margin-right:44px;}
.weather34darkbrowser[url]:after{content:attr(url);color:var(--pop-link);font-size:14px;text-align:center;position:absolute;left:0;right:0;top:0;padding:4px 15px;margin:11px 44px 0 auto;font-family:arial;height:20px;}
[data-theme="light"] .weather34darkbrowser[url]:after{color:black;}
blue{color:#01a4b4;}orange{color:#009bb4;}orange1{position:relative;color:#009bb4;margin:0 auto;text-align:center;margin-left:5%;font-size:1.1rem;}green{color:#aaa;}red{color:#f37867;}red6{color:#d65b4a;}value{color:#fff;}yellow{color:#cc0;}purple{color:#916392;}
humgreen{color:rgba(132,169,39,1);}humred{color:#e27667;}humyellow{color:rgba(231,150,59,1);}humblue{color:rgba(0,155,180,1);}humorange{color:#ff832f;}
[data-theme="light"] blue{color:#007a8a;}
[data-theme="light"] red{color:#c00000;}
[data-theme="light"] yellow{color:#8a5a00;}
[data-theme="light"] purple{color:#800080;}
[data-theme="light"] green{color:#008000;}
[data-theme="light"] humred{color:#c00000;}
[data-theme="light"] humblue{color:#007a8a;}
[data-theme="light"] humgreen{color:#2d7a2d;}
[data-theme="light"] humyellow{color:#8a5a00;}
[data-theme="light"] humorange{color:#b05000;}
.actualt{position:relative;left:0px;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;background:teal;padding:5px;font-family:Arial,Helvetica,sans-serif;width:130px;height:0.85em;font-size:0.8rem;padding-top:1px;color:white;align-items:center;justify-content:center;margin-bottom:10px;top:0;}
smalluvunit{font-size:0.5rem;font-family:weathertext2,Arial,Helvetica,system;vertical-align:super;}
smalltempunit{font-size:0.85rem;font-family:weathertext2,Arial,Helvetica,system;vertical-align:super;color:#aaa;}
smallhumunit{font-size:0.85rem;font-family:weathertext2,Arial,Helvetica,system;vertical-align:super;}
.indoorsvgnest{position:relative;margin:0 auto;display:flex;align-items:center;justify-content:center;margin-top:-15px;}
.indoorsvgnestvalue{margin:0 auto;display:flex;align-items:center;justify-content:center;font-size:1.55em;font-family:weathertext2;position:absolute;}
.weather34indoortrendup{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:0.8rem;padding-top:0;color:#fff;background:#d35d4e;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;left:130px;top:128px;}
.weather34indoortrenddown{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:0.8rem;padding-top:0;color:#fff;background:#00a4b4;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;left:130px;top:128px;}
.weather34indoortrendsteady{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:0.8rem;padding-top:0;color:#fff;background:#90b12a;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:2px;-moz-border-radius:2px;-o-border-radius:2px;border-radius:2px;position:absolute;left:130px;top:128px;}
.weather34indoorcomfort{font-family:weathertext2,Arial,Helvetica,system;width:4rem;height:1rem;font-size:0.8rem;padding-top:0;color:#fff;background:#90b12a;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;left:130px;top:128px;}
.weather34indoorhumid{font-family:weathertext2,Arial,Helvetica,system;width:4rem;height:1rem;font-size:0.89rem;padding-top:0;color:#fff;background:#00a4b4;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;left:130px;top:128px;}
.weather34indoordry{font-family:weathertext2,Arial,Helvetica,system;width:4rem;height:1rem;font-size:0.8rem;padding-top:0;color:#fff;background:#d35d4e;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:2px;-moz-border-radius:2px;-o-border-radius:2px;border-radius:2px;position:absolute;left:130px;top:128px;}
.weather34indoorwarm{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:0.8rem;padding-top:0;color:#fff;background:#ff832f;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;left:130px;top:128px;}
.weather34indoorcold{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:0.8rem;padding-top:0;color:#fff;background:#00a4b4;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;left:130px;top:128px;}
.weather34indoormild{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:0.8rem;padding-top:0;color:#fff;background:rgba(230,161,65,1);border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:2px;-moz-border-radius:2px;-o-border-radius:2px;border-radius:2px;position:absolute;left:130px;top:128px;}
.weather34indoorhot{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:0.8rem;padding-top:0;color:#fff;background:rgba(211,93,78,1);border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:2px;-moz-border-radius:2px;-o-border-radius:2px;border-radius:2px;position:absolute;left:130px;top:128px;}
.weather34indoorpm{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:0.65rem;padding-top:0;color:#fff;background:#00a4b4;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;left:130px;top:128px;}
div.output{width:90px;height:90px;position:absolute;left:45px;top:86px;color:#fff;text-transform:uppercase;text-align:center;margin:0 auto;display:flex;align-items:center;justify-content:center;}
div.output small{display:block;margin:0;padding:0;font-size:1em;color:#c0c0c0;}
[data-theme="light"] div.output small{color:black;}
.trendicon{margin:0 auto;display:flex;margin-top:37px;align-items:center;justify-content:center;font-size:1.85em;font-family:weathertext2;position:absolute;}
.airqicon{margin:0 auto;display:flex;margin-top:37px;align-items:center;justify-content:center;font-size:1.85em;font-family:weathertext2;position:absolute;}
.tempicon{margin:0 auto;display:flex;margin-top:37px;align-items:center;justify-content:center;font-size:1.85em;font-family:weathertext2;position:absolute;}
.lotemp{color:var(--pop-footer-ink);font-size:11px;}
  .weather34indoortrenddown,.weather34indoortrendsteady,.weather34indoortrendup,.weather34indoorcold,.weather34indoorhot,.weather34indoormild,.weather34indoorwarm,.weather34indoorcomfort,.weather34indoordry,.weather34indoorhumid{text-shadow:var(--badge-text-shadow)}
  </style>
</head>
<body>

<div class="weather34darkbrowser" url="<?php echo htmlspecialchars(w34t('IndoorEnvTitle', 'Home Environment Temperature | Humidity')); ?>"></div>
  
<main class="grid">
  <article>  
   <div class=actualt><?php echo w34t('IndoorTemperature', 'Indoor Temperature'); ?></div>        
   
 <div class=indoorsvgnest> 
 <div class="output">
           <small><?php 
if($weather["temp_indoor_trend"] >0)echo w34t('Warming', 'Warming');
else if($weather["temp_indoor_trend"]<0)echo w34t('Cooling', 'Cooling');
else if($weather["temp_indoor_trend"] ==0)echo w34t('Stable', 'Stable'); 
?> </small>           
        </div>
<svg id="indoor temperature weather34" width="120px" height="120px" viewBox="0 0 600 600" version="1.1">
<path fill="#4b545c" opacity="1.00" d=" M 277.33 44.31 C 278.81 43.77 280.41 44.04 281.96 43.97 C 292.95 44.05 303.94 43.96 314.93 44.00 C 316.43 43.98 317.71 44.94 319.14 45.28 C 370.84 48.81 421.06 69.16 461.23 101.80 C 484.64 121.11 504.91 144.32 520.29 170.51 C 529.82 187.64 538.71 205.33 544.10 224.25 C 549.63 241.34 553.03 259.10 554.58 276.97 C 554.34 279.46 556.30 281.50 556.02 284.00 C 555.96 294.97 556.03 305.95 556.00 316.92 C 556.01 318.75 554.62 320.26 554.65 322.09 C 553.77 334.85 551.54 347.48 548.62 359.93 C 544.55 375.59 539.76 391.16 532.66 405.74 C 529.53 413.86 524.89 421.23 520.80 428.87 C 497.75 467.88 464.45 500.92 424.85 523.03 C 393.09 540.82 357.49 551.88 321.18 554.73 C 319.46 555.22 317.85 556.22 316.00 556.02 C 305.03 555.96 294.05 556.03 283.08 556.00 C 281.24 556.01 279.73 554.61 277.90 554.63 C 220.65 549.93 165.37 524.80 123.95 485.05 C 99.37 461.65 79.41 433.39 65.94 402.23 C 56.79 382.45 51.18 361.22 47.48 339.80 C 46.82 333.22 45.44 326.71 45.37 320.08 C 45.43 318.24 43.96 316.76 44.00 314.93 C 43.99 302.98 44.00 291.03 44.00 279.08 C 46.47 268.72 46.73 257.91 49.38 247.55 C 61.60 188.21 95.92 133.80 144.07 97.05 C 178.88 70.12 220.79 52.43 264.41 46.46 C 268.71 45.70 273.16 45.68 277.33 44.31 M 276.43 80.50 C 242.35 84.05 209.20 95.83 180.44 114.44 C 132.01 145.51 96.52 196.15 84.41 252.45 C 78.65 278.28 77.90 305.16 81.43 331.36 C 86.10 364.11 98.21 395.82 116.76 423.22 C 129.29 441.93 144.79 458.60 162.32 472.70 C 198.21 501.45 243.29 518.62 289.27 520.58 C 332.32 522.81 375.94 512.08 412.93 489.90 C 444.45 471.19 471.17 444.45 489.90 412.94 C 510.51 378.44 521.45 338.21 520.78 298.01 C 520.70 252.89 505.95 207.98 479.55 171.43 C 448.08 127.28 399.81 95.37 346.72 84.21 C 323.71 79.09 299.85 78.21 276.43 80.50 Z" />
<path fill="#1f1f1f" opacity="1.00" d=" M 276.43 80.50 C 299.85 78.21 323.71 79.09 346.72 84.21 C 399.81 95.37 448.08 127.28 479.55 171.43 C 505.95 207.98 520.70 252.89 520.78 298.01 C 521.45 338.21 510.51 378.44 489.90 412.94 C 471.17 444.45 444.45 471.19 412.93 489.90 C 375.94 512.08 332.32 522.81 289.27 520.58 C 243.29 518.62 198.21 501.45 162.32 472.70 C 144.79 458.60 129.29 441.93 116.76 423.22 C 98.21 395.82 86.10 364.11 81.43 331.36 C 77.90 305.16 78.65 278.28 84.41 252.45 C 96.52 196.15 132.01 145.51 180.44 114.44 C 209.20 95.83 242.35 84.05 276.43 80.50 M 286.41 150.46 C 242.51 154.25 200.72 178.50 176.17 215.17 C 162.81 234.07 154.66 256.34 151.24 279.17 C 147.21 309.14 152.07 340.35 165.68 367.40 C 176.33 388.48 191.81 407.16 210.97 421.04 C 249.80 450.25 303.35 458.22 349.12 442.07 C 379.21 431.96 405.62 411.69 423.60 385.58 C 448.45 350.20 456.72 303.60 445.26 261.87 C 442.59 251.03 438.74 240.45 433.33 230.67 C 425.90 216.47 416.41 203.29 404.75 192.24 C 373.92 161.88 329.44 146.38 286.41 150.46 Z" />
<path fill="<?php 
if($weather["temp_units"]=='F' &&  $weather["temp_indoor"]>78.8)echo "#d35d4e";
else if($weather["temp_units"]=='F' &&  $weather["temp_indoor"]>64.4)echo "#ff832f";
else if($weather["temp_units"]=='F' &&  $weather["temp_indoor"]>59)echo "#e6a141";
else if($weather["temp_units"]=='F' &&  $weather["temp_indoor"]>0)echo "#44a6b5";
else if ($weather["temp_indoor"]>=26) echo "#d35d4e";
else if ($weather["temp_indoor"]>18) echo "#ff832f";
elseif ($weather["temp_indoor"]>15) echo "#e6a141";
else if ($weather["temp_indoor"]>=0) echo "#44a6b5";
?>" opacity="1.00" d=" M 286.41 150.46 C 329.44 146.38 373.92 161.88 404.75 192.24 C 416.41 203.29 425.90 216.47 433.33 230.67 C 438.74 240.45 442.59 251.03 445.26 261.87 C 456.72 303.60 448.45 350.20 423.60 385.58 C 405.62 411.69 379.21 431.96 349.12 442.07 C 303.35 458.22 249.80 450.25 210.97 421.04 C 191.81 407.16 176.33 388.48 165.68 367.40 C 152.07 340.35 147.21 309.14 151.24 279.17 C 154.66 256.34 162.81 234.07 176.17 215.17 C 200.72 178.50 242.51 154.25 286.41 150.46 Z" />
</svg>
<div class=indoorsvgnestvalue>  
<?php echo number_format($weather["temp_indoor"],1);?><?php echo "<smalluvunit>&deg;",$weather["temp_units"]?></div>
<div class="trendicon"><?php 
if($weather["temp_indoor_trend"] >0)echo $heatindexwu;
else if($weather["temp_indoor_trend"]<0)echo $windchillwu;
else if($weather["temp_indoor_trend"] ==0)echo $airqualitybreeze; 
?> </div>
<?php 
if($weather["temp_indoor_trend"] >0)echo "<div class='weather34indoortrendup'>",number_format($weather["temp_indoor_trend"],1).'&deg; '.$risingsymbol;
else if($weather["temp_indoor_trend"]<0)echo "<div class='weather34indoortrenddown'>", number_format($weather["temp_indoor_trend"],1).'&deg;'.$fallingsymbol;
else if($weather["temp_indoor_trend"] ==0)echo "<div class='weather34indoortrendsteady'>" . w34t('Steady', 'Steady'); 
?></div></div>
</div>
   
   
</article>  

 <article>  
      <div class=actualt><?php echo w34t('IndoorHumidity', 'Indoor Humidity'); ?> </div>        
           
   <div class=indoorsvgnest> 
 <svg id="indoor humidity weather34" width="120px" height="120px" viewBox="0 0 600 600" version="1.1" >
<path fill="#4b545c" opacity="1.00" d=" M 277.33 44.31 C 278.81 43.77 280.41 44.04 281.96 43.97 C 292.95 44.05 303.94 43.96 314.93 44.00 C 316.43 43.98 317.71 44.94 319.14 45.28 C 370.84 48.81 421.06 69.16 461.23 101.80 C 484.64 121.11 504.91 144.32 520.29 170.51 C 529.82 187.64 538.71 205.33 544.10 224.25 C 549.63 241.34 553.03 259.10 554.58 276.97 C 554.34 279.46 556.30 281.50 556.02 284.00 C 555.96 294.97 556.03 305.95 556.00 316.92 C 556.01 318.75 554.62 320.26 554.65 322.09 C 553.77 334.85 551.54 347.48 548.62 359.93 C 544.55 375.59 539.76 391.16 532.66 405.74 C 529.53 413.86 524.89 421.23 520.80 428.87 C 497.75 467.88 464.45 500.92 424.85 523.03 C 393.09 540.82 357.49 551.88 321.18 554.73 C 319.46 555.22 317.85 556.22 316.00 556.02 C 305.03 555.96 294.05 556.03 283.08 556.00 C 281.24 556.01 279.73 554.61 277.90 554.63 C 220.65 549.93 165.37 524.80 123.95 485.05 C 99.37 461.65 79.41 433.39 65.94 402.23 C 56.79 382.45 51.18 361.22 47.48 339.80 C 46.82 333.22 45.44 326.71 45.37 320.08 C 45.43 318.24 43.96 316.76 44.00 314.93 C 43.99 302.98 44.00 291.03 44.00 279.08 C 46.47 268.72 46.73 257.91 49.38 247.55 C 61.60 188.21 95.92 133.80 144.07 97.05 C 178.88 70.12 220.79 52.43 264.41 46.46 C 268.71 45.70 273.16 45.68 277.33 44.31 M 276.43 80.50 C 242.35 84.05 209.20 95.83 180.44 114.44 C 132.01 145.51 96.52 196.15 84.41 252.45 C 78.65 278.28 77.90 305.16 81.43 331.36 C 86.10 364.11 98.21 395.82 116.76 423.22 C 129.29 441.93 144.79 458.60 162.32 472.70 C 198.21 501.45 243.29 518.62 289.27 520.58 C 332.32 522.81 375.94 512.08 412.93 489.90 C 444.45 471.19 471.17 444.45 489.90 412.94 C 510.51 378.44 521.45 338.21 520.78 298.01 C 520.70 252.89 505.95 207.98 479.55 171.43 C 448.08 127.28 399.81 95.37 346.72 84.21 C 323.71 79.09 299.85 78.21 276.43 80.50 Z" />
<path fill="#1f1f1f" opacity="1.00" d=" M 276.43 80.50 C 299.85 78.21 323.71 79.09 346.72 84.21 C 399.81 95.37 448.08 127.28 479.55 171.43 C 505.95 207.98 520.70 252.89 520.78 298.01 C 521.45 338.21 510.51 378.44 489.90 412.94 C 471.17 444.45 444.45 471.19 412.93 489.90 C 375.94 512.08 332.32 522.81 289.27 520.58 C 243.29 518.62 198.21 501.45 162.32 472.70 C 144.79 458.60 129.29 441.93 116.76 423.22 C 98.21 395.82 86.10 364.11 81.43 331.36 C 77.90 305.16 78.65 278.28 84.41 252.45 C 96.52 196.15 132.01 145.51 180.44 114.44 C 209.20 95.83 242.35 84.05 276.43 80.50 M 286.41 150.46 C 242.51 154.25 200.72 178.50 176.17 215.17 C 162.81 234.07 154.66 256.34 151.24 279.17 C 147.21 309.14 152.07 340.35 165.68 367.40 C 176.33 388.48 191.81 407.16 210.97 421.04 C 249.80 450.25 303.35 458.22 349.12 442.07 C 379.21 431.96 405.62 411.69 423.60 385.58 C 448.45 350.20 456.72 303.60 445.26 261.87 C 442.59 251.03 438.74 240.45 433.33 230.67 C 425.90 216.47 416.41 203.29 404.75 192.24 C 373.92 161.88 329.44 146.38 286.41 150.46 Z" />
<path fill="<?php if($weather["humidity_indoor"]<35)echo "#d35d4e";
			  else if($weather["humidity_indoor"]<60)echo "#e6a141";
			  else if($weather["humidity_indoor"]<80)echo "#ec5519";
			  else if($weather["humidity_indoor"]<=100)echo "#44a6b5";?>" opacity="1.00" d=" M 286.41 150.46 C 329.44 146.38 373.92 161.88 404.75 192.24 C 416.41 203.29 425.90 216.47 433.33 230.67 C 438.74 240.45 442.59 251.03 445.26 261.87 C 456.72 303.60 448.45 350.20 423.60 385.58 C 405.62 411.69 379.21 431.96 349.12 442.07 C 303.35 458.22 249.80 450.25 210.97 421.04 C 191.81 407.16 176.33 388.48 165.68 367.40 C 152.07 340.35 147.21 309.14 151.24 279.17 C 154.66 256.34 162.81 234.07 176.17 215.17 C 200.72 178.50 242.51 154.25 286.41 150.46 Z" />
</svg>

        <div class="output">
           <small><?php 
			  if($weather["humidity_indoor"]<35)echo w34t('Dry', 'Dry');			  
			  else if($weather["humidity_indoor"]<80)echo w34t('AqGood', 'Good');
			  else if($weather["humidity_indoor"]<=100)echo w34t('Damp', 'Damp');		  
			  ?></small>           
        </div>

<div class=indoorsvgnestvalue>  
<?php echo number_format($weather["humidity_indoor"],0);?><?php echo "<smallhumunit>%</smallhumunit>"?></div>
<div class="trendicon"><?php 
if($weather["humidity_indoor"]>80)echo $windchillwu;
else if($weather["humidity_indoor"]>50)echo $airqualitybreeze;
else if($weather["humidity_indoor"]>35)echo $humiditybreeze;
else if($weather["humidity_indoor"]<35)echo $heatindexwu;
?></div>
<?php 
if($weather["humidity_indoortrend"] >0)echo "<div class='weather34indoortrendup'>",number_format($weather["humidity_indoortrend"],0)."<sup>%</sup>".$risingsymbol;
else if($weather["humidity_indoortrend"]<0)echo "<div class='weather34indoortrenddown'>", number_format($weather["humidity_indoortrend"],0)."<sup>%</sup>".$fallingsymbol;
else if($weather["humidity_indoortrend"]==0)echo "<div class='weather34indoortrendsteady'>" . w34t('Steady', 'Steady'); 
?></div></div>
</div></div></div>

</div></div>
</article>     
</article>     
  <article>  
   <div class=actualt><?php echo w34t('Feelslike', 'Feels'); ?></div>        
   
 <div class=indoorsvgnest> 
 <div class="output">
           <small><?php echo w34t('RealFeel', 'Real Feel'); ?></small>           
        </div>      
<svg id="indoor feels weather34" width="120px" height="120px" viewBox="0 0 600 600" version="1.1" >
<path fill="#4b545c" opacity="1.00" d=" M 277.33 44.31 C 278.81 43.77 280.41 44.04 281.96 43.97 C 292.95 44.05 303.94 43.96 314.93 44.00 C 316.43 43.98 317.71 44.94 319.14 45.28 C 370.84 48.81 421.06 69.16 461.23 101.80 C 484.64 121.11 504.91 144.32 520.29 170.51 C 529.82 187.64 538.71 205.33 544.10 224.25 C 549.63 241.34 553.03 259.10 554.58 276.97 C 554.34 279.46 556.30 281.50 556.02 284.00 C 555.96 294.97 556.03 305.95 556.00 316.92 C 556.01 318.75 554.62 320.26 554.65 322.09 C 553.77 334.85 551.54 347.48 548.62 359.93 C 544.55 375.59 539.76 391.16 532.66 405.74 C 529.53 413.86 524.89 421.23 520.80 428.87 C 497.75 467.88 464.45 500.92 424.85 523.03 C 393.09 540.82 357.49 551.88 321.18 554.73 C 319.46 555.22 317.85 556.22 316.00 556.02 C 305.03 555.96 294.05 556.03 283.08 556.00 C 281.24 556.01 279.73 554.61 277.90 554.63 C 220.65 549.93 165.37 524.80 123.95 485.05 C 99.37 461.65 79.41 433.39 65.94 402.23 C 56.79 382.45 51.18 361.22 47.48 339.80 C 46.82 333.22 45.44 326.71 45.37 320.08 C 45.43 318.24 43.96 316.76 44.00 314.93 C 43.99 302.98 44.00 291.03 44.00 279.08 C 46.47 268.72 46.73 257.91 49.38 247.55 C 61.60 188.21 95.92 133.80 144.07 97.05 C 178.88 70.12 220.79 52.43 264.41 46.46 C 268.71 45.70 273.16 45.68 277.33 44.31 M 276.43 80.50 C 242.35 84.05 209.20 95.83 180.44 114.44 C 132.01 145.51 96.52 196.15 84.41 252.45 C 78.65 278.28 77.90 305.16 81.43 331.36 C 86.10 364.11 98.21 395.82 116.76 423.22 C 129.29 441.93 144.79 458.60 162.32 472.70 C 198.21 501.45 243.29 518.62 289.27 520.58 C 332.32 522.81 375.94 512.08 412.93 489.90 C 444.45 471.19 471.17 444.45 489.90 412.94 C 510.51 378.44 521.45 338.21 520.78 298.01 C 520.70 252.89 505.95 207.98 479.55 171.43 C 448.08 127.28 399.81 95.37 346.72 84.21 C 323.71 79.09 299.85 78.21 276.43 80.50 Z" />
<path fill="#1f1f1f" opacity="1.00" d=" M 276.43 80.50 C 299.85 78.21 323.71 79.09 346.72 84.21 C 399.81 95.37 448.08 127.28 479.55 171.43 C 505.95 207.98 520.70 252.89 520.78 298.01 C 521.45 338.21 510.51 378.44 489.90 412.94 C 471.17 444.45 444.45 471.19 412.93 489.90 C 375.94 512.08 332.32 522.81 289.27 520.58 C 243.29 518.62 198.21 501.45 162.32 472.70 C 144.79 458.60 129.29 441.93 116.76 423.22 C 98.21 395.82 86.10 364.11 81.43 331.36 C 77.90 305.16 78.65 278.28 84.41 252.45 C 96.52 196.15 132.01 145.51 180.44 114.44 C 209.20 95.83 242.35 84.05 276.43 80.50 M 286.41 150.46 C 242.51 154.25 200.72 178.50 176.17 215.17 C 162.81 234.07 154.66 256.34 151.24 279.17 C 147.21 309.14 152.07 340.35 165.68 367.40 C 176.33 388.48 191.81 407.16 210.97 421.04 C 249.80 450.25 303.35 458.22 349.12 442.07 C 379.21 431.96 405.62 411.69 423.60 385.58 C 448.45 350.20 456.72 303.60 445.26 261.87 C 442.59 251.03 438.74 240.45 433.33 230.67 C 425.90 216.47 416.41 203.29 404.75 192.24 C 373.92 161.88 329.44 146.38 286.41 150.46 Z" />
<path fill="<?php 
if($weather["temp_units"]=='F' &&  $weather["temp_indoor_feel"]>78.8)echo "#d35d4e";
else if($weather["temp_units"]=='F' &&  $weather["temp_indoor_feel"]>64.4)echo "#ff832f";
else if($weather["temp_units"]=='F' &&  $weather["temp_indoor_feel"]>59)echo "#e6a141";
else if($weather["temp_units"]=='F' &&  $weather["temp_indoor_feel"]>0)echo "#44a6b5";
else if ($weather["temp_indoor_feel"]>=26) echo "#d35d4e";
else if ($weather["temp_indoor_feel"]>18) echo "#ff832f";
elseif ($weather["temp_indoor_feel"]>15) echo "#e6a141";
else if ($weather["temp_indoor_feel"]>=0) echo "#44a6b5";
?>" opacity="1.00" d=" M 286.41 150.46 C 329.44 146.38 373.92 161.88 404.75 192.24 C 416.41 203.29 425.90 216.47 433.33 230.67 C 438.74 240.45 442.59 251.03 445.26 261.87 C 456.72 303.60 448.45 350.20 423.60 385.58 C 405.62 411.69 379.21 431.96 349.12 442.07 C 303.35 458.22 249.80 450.25 210.97 421.04 C 191.81 407.16 176.33 388.48 165.68 367.40 C 152.07 340.35 147.21 309.14 151.24 279.17 C 154.66 256.34 162.81 234.07 176.17 215.17 C 200.72 178.50 242.51 154.25 286.41 150.46 Z" />
</svg>
<div class=indoorsvgnestvalue>  
<?php echo number_format($weather["temp_indoor_feel"],1);?><?php echo "<smalluvunit>&deg;",$weather["temp_units"]?></div>
<div class="trendicon"><?php 
if($weather["temp_units"]=='F' &&  $weather["temp_indoor_feel"]>78.8)echo $heatindexwu;
else if($weather["temp_units"]=='F' &&  $weather["temp_indoor_feel"]>64.4)echo $heatindexwu;
else if($weather["temp_units"]=='F' &&  $weather["temp_indoor_feel"]>59)echo $airqualitybreeze;
else if($weather["temp_units"]=='F' &&  $weather["temp_indoor_feel"]>0)echo $windchillwu;
else if ($weather["temp_indoor_feel"]>=26) echo $heatindexwu;
else if ($weather["temp_indoor_feel"]>18) echo $heatindexwu;
elseif ($weather["temp_indoor_feel"]>15) echo $airqualitybreeze;
else if ($weather["temp_indoor_feel"]>=0) echo $windchillwu;
?></div>
<?php
if($weather["temp_units"]=='F' && $weather["temp_indoor_feel"] >78.8)echo "<div class='weather34indoorhot'>" . w34t('FeelingHot', 'Hot');   
else if($weather["temp_units"]=='F' &&  $weather["temp_indoor_feel"] >64.4)echo "<div class='weather34indoorwarm'>" . w34t('FeelingWarm', 'Warm');
else if($weather["temp_units"]=='F' &&  $weather["temp_indoor_feel"]<53.6)echo "<div class='weather34indoorcold'>" . w34t('FeelingCold', 'Cold');
else if($weather["temp_units"]=='F' && $weather["temp_indoor_feel"]<=64.4)echo "<div class='weather34indoormild'>" . w34t('Mild', 'Mild');
else if($weather["temp_indoor_feel"] >26)echo "<div class='weather34indoorhot'>" . w34t('FeelingHot', 'Hot');   
else if($weather["temp_indoor_feel"] >18)echo "<div class='weather34indoorwarm'>" . w34t('FeelingWarm', 'Warm');
else if($weather["temp_indoor_feel"]<12)echo "<div class='weather34indoorcold'>" . w34t('FeelingCold', 'Cold');
else if($weather["temp_indoor_feel"]<=18)echo "<div class='weather34indoormild'>" . w34t('Mild', 'Mild');
?></div></div>
</div></div>
</article>  

<?php if($purpleairhardware=='yes'){?>
<article> 
   <div class=actualt><?php echo w34t('AirQuality', 'Air Quality'); ?></div>        
   
 <div class=indoorsvgnest> 
 <div class="output">
           <small><?php 
if($aqiweather["aqi"] >300)echo w34t('AqHazardous', 'Hazardous');
else if($aqiweather["aqi"] >200)echo w34t('AqVeryUnhealthy', 'V. Unhealthy');
else if($aqiweather["aqi"] >150)echo w34t('AqUnhealthy', 'Unhealthy');
else if($aqiweather["aqi"] >100)echo w34t('AqUnhealthyFS', 'Unhealthy FS');
else if($aqiweather["aqi"] >50)echo w34t('AqModerate', 'Moderate');
else if($aqiweather["aqi"] >=0)echo w34t('AqGood', 'Good');
?> </small>           
        </div>      
<svg id="indoor air quality weather34" width="120px" height="120px" viewBox="0 0 600 600" version="1.1" >
<path fill="#4b545c" opacity="1.00" d=" M 277.33 44.31 C 278.81 43.77 280.41 44.04 281.96 43.97 C 292.95 44.05 303.94 43.96 314.93 44.00 C 316.43 43.98 317.71 44.94 319.14 45.28 C 370.84 48.81 421.06 69.16 461.23 101.80 C 484.64 121.11 504.91 144.32 520.29 170.51 C 529.82 187.64 538.71 205.33 544.10 224.25 C 549.63 241.34 553.03 259.10 554.58 276.97 C 554.34 279.46 556.30 281.50 556.02 284.00 C 555.96 294.97 556.03 305.95 556.00 316.92 C 556.01 318.75 554.62 320.26 554.65 322.09 C 553.77 334.85 551.54 347.48 548.62 359.93 C 544.55 375.59 539.76 391.16 532.66 405.74 C 529.53 413.86 524.89 421.23 520.80 428.87 C 497.75 467.88 464.45 500.92 424.85 523.03 C 393.09 540.82 357.49 551.88 321.18 554.73 C 319.46 555.22 317.85 556.22 316.00 556.02 C 305.03 555.96 294.05 556.03 283.08 556.00 C 281.24 556.01 279.73 554.61 277.90 554.63 C 220.65 549.93 165.37 524.80 123.95 485.05 C 99.37 461.65 79.41 433.39 65.94 402.23 C 56.79 382.45 51.18 361.22 47.48 339.80 C 46.82 333.22 45.44 326.71 45.37 320.08 C 45.43 318.24 43.96 316.76 44.00 314.93 C 43.99 302.98 44.00 291.03 44.00 279.08 C 46.47 268.72 46.73 257.91 49.38 247.55 C 61.60 188.21 95.92 133.80 144.07 97.05 C 178.88 70.12 220.79 52.43 264.41 46.46 C 268.71 45.70 273.16 45.68 277.33 44.31 M 276.43 80.50 C 242.35 84.05 209.20 95.83 180.44 114.44 C 132.01 145.51 96.52 196.15 84.41 252.45 C 78.65 278.28 77.90 305.16 81.43 331.36 C 86.10 364.11 98.21 395.82 116.76 423.22 C 129.29 441.93 144.79 458.60 162.32 472.70 C 198.21 501.45 243.29 518.62 289.27 520.58 C 332.32 522.81 375.94 512.08 412.93 489.90 C 444.45 471.19 471.17 444.45 489.90 412.94 C 510.51 378.44 521.45 338.21 520.78 298.01 C 520.70 252.89 505.95 207.98 479.55 171.43 C 448.08 127.28 399.81 95.37 346.72 84.21 C 323.71 79.09 299.85 78.21 276.43 80.50 Z" />
<path fill="#1f1f1f" opacity="1.00" d=" M 276.43 80.50 C 299.85 78.21 323.71 79.09 346.72 84.21 C 399.81 95.37 448.08 127.28 479.55 171.43 C 505.95 207.98 520.70 252.89 520.78 298.01 C 521.45 338.21 510.51 378.44 489.90 412.94 C 471.17 444.45 444.45 471.19 412.93 489.90 C 375.94 512.08 332.32 522.81 289.27 520.58 C 243.29 518.62 198.21 501.45 162.32 472.70 C 144.79 458.60 129.29 441.93 116.76 423.22 C 98.21 395.82 86.10 364.11 81.43 331.36 C 77.90 305.16 78.65 278.28 84.41 252.45 C 96.52 196.15 132.01 145.51 180.44 114.44 C 209.20 95.83 242.35 84.05 276.43 80.50 M 286.41 150.46 C 242.51 154.25 200.72 178.50 176.17 215.17 C 162.81 234.07 154.66 256.34 151.24 279.17 C 147.21 309.14 152.07 340.35 165.68 367.40 C 176.33 388.48 191.81 407.16 210.97 421.04 C 249.80 450.25 303.35 458.22 349.12 442.07 C 379.21 431.96 405.62 411.69 423.60 385.58 C 448.45 350.20 456.72 303.60 445.26 261.87 C 442.59 251.03 438.74 240.45 433.33 230.67 C 425.90 216.47 416.41 203.29 404.75 192.24 C 373.92 161.88 329.44 146.38 286.41 150.46 Z" />
<path fill="<?php 
if($aqiweather["aqi"]>300)echo "#99020d";
else if($aqiweather["aqi"]>200)echo "#a968b3";
else if($aqiweather["aqi"]>150)echo "#d35d4e";
else if($aqiweather["aqi"]>100)echo "#d05f2d";
else if ($aqiweather["aqi"]>=50) echo "#e6a141";
else if ($aqiweather["aqi"]>=0) echo "#90b12a";
?>" opacity="1.00" d=" M 286.41 150.46 C 329.44 146.38 373.92 161.88 404.75 192.24 C 416.41 203.29 425.90 216.47 433.33 230.67 C 438.74 240.45 442.59 251.03 445.26 261.87 C 456.72 303.60 448.45 350.20 423.60 385.58 C 405.62 411.69 379.21 431.96 349.12 442.07 C 303.35 458.22 249.80 450.25 210.97 421.04 C 191.81 407.16 176.33 388.48 165.68 367.40 C 152.07 340.35 147.21 309.14 151.24 279.17 C 154.66 256.34 162.81 234.07 176.17 215.17 C 200.72 178.50 242.51 154.25 286.41 150.46 Z" />
</svg>    
<div class=indoorsvgnestvalue> <?php echo $aqiweather["aqi"];?></div>
<div class="airqicon"><?php echo $airqualitybreeze;?></div>
<div class="weather34indoorpm">PM 2.5</div></div>
</div></div>
</article> 
<?php ;}?>

<article>
   <div class=actualt><?php echo w34t('TemperatureGuide', 'Temperature Guide'); ?></div>        
 <span style="font-size:.9em;">    
<?php echo $info ;?><?php echo w34f('IndoorTempBelow', 'Long term temperatures below %s can cause', '<yellow>15&deg;C/59&deg;F</yellow>'); ?> <br>
<?php echo $info ;?><?php echo w34t('IndoorDampness', 'Dampness'); ?><br>
<?php echo $info ;?><?php echo w34t('IndoorRiskColds', 'Risk of colds and respiratory illness'); ?><br><br>

<?php echo $info ;?><?php echo w34f('IndoorTempAbove', 'Long term temperatures above %s can cause', '<red>25&deg;C/77&deg;F</red>'); ?> <br>
<?php echo $info ;?><?php echo w34t('IndoorRiskOverheat', 'Risk of electrical items overheating'); ?><br>
<?php echo $info ;?><?php echo w34t('IndoorSleep', 'Sleep deprivation'); ?>
</span></div>
    </article> 

<article>
   <div class=actualt><?php echo w34t('HumidityGuide', 'Humidity Guide'); ?></div>        
 <span style="font-size:.9em;">    
<?php echo w34f('IndoorHumBelow', 'Long periods below %s can cause', '<humred>35%</humred>'); ?><br>
<?php echo $info ;?><?php echo w34t('IndoorDrySkin', 'Dry, itchy skin and hair'); ?><br>
<?php echo $info ;?><?php echo w34t('IndoorRiskColds', 'Risk of colds and respiratory illness'); ?><br>
<?php echo $info ;?><?php echo w34t('IndoorRiskDust', 'Risk of allergies from dust'); ?><br><br>
<?php echo w34f('IndoorHumAbove', 'Long periods above %s can cause', '<humblue>80%</humblue>'); ?> <br>
<?php echo $info ;?><?php echo w34t('IndoorIrritable', 'Irritable feelings'); ?><br>
<?php echo $info ;?><?php echo w34t('IndoorSleep', 'Sleep deprivation'); ?>
</span></div>
    </article> 
   
    <article>
  <div class=actualt>&copy; <?php echo w34t('Information', 'Information'); ?></div>  
  <div class="lotemp">
  <?php echo $info?> <?php echo w34t('CreditAdapted', 'Adapted by Steepleian for the WeeWX Weather34 skin from the original CSS/SVG/PHP scripts by weather34.com'); ?> &copy; 2015-<?php echo date('Y');?>
  <br><br>
     </div>
  </article> 
</main>
</body>
</html> 
