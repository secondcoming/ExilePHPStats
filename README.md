![Exile 1.0.1 Sweet Potato](https://img.shields.io/badge/Exile-1.0.1%20Sweet%20Potato-C72651.svg) 

# ExilePHPStats

For more info:
http://www.exilemod.com/topic/5104-exilephpstats

###License Overview:
This work is protected by [Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)](http://creativecommons.org/licenses/by-nc-sa/4.0/). By using, downloading, or copying any of the work contained, you agree to the license included.

<a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-sa/4.0/88x31.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" href="http://purl.org/dc/dcmitype/Text" property="dct:title" rel="dct:type">ExilePHPStats</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="https://github.com/secondcoming/ExilePHPStats" property="cc:attributionName" rel="cc:attributionURL">second_coming</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>.

### Donations:
Anyone wishing to donate can do so here http://exileyorkshire.co.uk/
All donations go towards coffee to keep me awake :)

### Extras:
You need to use the following overrides to be able to log trader and recycle logs:
ExileServer_system_trading_network_sellItemRequest.sqf
ExileServer_system_trading_network_wasteDumpRequest.sqf

Create a database called exile_logs and import the database extras/exile_logs.sql
add the connection details to includes/config.php

Either add a cron job to run the importLogs.php script or run it manually to read the trader and recycle logs in to the trader database
