git checkout .
git fetch
git pull
chmod +x .update.sh
chmod +x .composer.sh

php dump.php
vendor/bin/doctrine orm:schema-tool:update --force
vendor/bin/doctrine orm:generate-proxies
