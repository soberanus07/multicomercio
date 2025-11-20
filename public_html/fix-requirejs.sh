#!/bin/bash

echo "Borrando pub/static..."
rm -rf pub/static/*

echo "Borrando preprocesados..."
rm -rf var/view_preprocessed/*

echo "Borrando requirejs-config.js generados..."
find pub/static -name requirejs-config.js -exec rm -f {} \;

echo "Borrando JS generados en var..."
find var -type f -name '*.js' -delete

echo "Recompilando DI..."
php bin/magento setup:di:compile

echo "Haciendo deploy de static content (todos los idiomas)..."
php bin/magento setup:static-content:deploy

echo "Limpiando caché..."
php bin/magento cache:clean
php bin/magento cache:flush

echo "Todo listo. Recarga el frontend con Ctrl + Shift + R y revisa consola."
