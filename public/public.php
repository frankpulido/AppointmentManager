<?php
// Simple script to test if PHP is working in the server
echo "PHP Server is working! Time: " . date('Y-m-d H:i:s');
phpinfo(); // Shows PHP configuration

/* Temporarilly use this variable in Railway :
NIXPACKS_START_CMD="while true; do php -S 0.0.0.0:$PORT -t public test.php; done"

Open :
https://appointmentmanager-production-564f.up.railway.app/test.php
Possible Outcomes:
    Works (shows PHP info) → Laravel bootstrap is broken
    502 Error → PHP/server environment is broken
    Timeout → Network/firewall issues

After testing, remove this file and use the appropiate NIXPACKS_START_CMD variable.

NIXPACKS_BUILD_CMD="composer install --optimize-autoloader && npm ci --omit=dev"
NIXPACKS_START_CMD="php artisan migrate:fresh --force && php artisan db:seed --force && php artisan storage:link && while true; do php -S 0.0.0.0:$PORT -t public; done"

*/
?>