# Micomercio_FotoramaLazy

Hotfix para Fotorama en Magento 2 que optimiza el LCP:
- El **slide activo** del PDP **no** usa `loading="lazy"` y tiene `fetchpriority="high"`.
- Los slides inactivos y thumbnails usan `loading="lazy"` y `fetchpriority="low"`.

## Instalación
1. Copia la carpeta `Micomercio_FotoramaLazy` a:
   `app/code/Micomercio/FotoramaLazy`

2. Ejecuta:
```
bin/magento module:enable Micomercio_FotoramaLazy
bin/magento setup:upgrade
bin/magento cache:flush
```

3. (Opcional) Regenera estáticos si estás en modo producción:
```
bin/magento setup:static-content:deploy -f es_CL
```

## Notas
- Este módulo **no** modifica plantillas ni el core; sólo inyecta JS vía RequireJS.
- Combínalo con una **reserva de aspecto** para el contenedor de la galería (CSS) para reducir CLS.
- Si usas personalizaciones de Fotorama, verifica en DevTools que el slide visible no quede con `loading="lazy"` y sí con `fetchpriority="high"`.
