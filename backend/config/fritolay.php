<?php

return [
    'iva_porcentaje' => (float) env('IVA_PORCENTAJE', 15),
    'stock_alert_threshold_percent' => (int) env('STOCK_ALERT_THRESHOLD_PERCENT', 10),
    'pin_digits' => (int) env('PIN_DIGITS', 6),
    'gps_update_interval_seconds' => (int) env('GPS_UPDATE_INTERVAL_SECONDS', 5),
    'gcs_image_cache_hours' => (int) env('GCS_IMAGE_CACHE_HOURS', 4),
    'gcs_bucket_imagenes' => env('GCS_BUCKET_IMAGENES', ''),
    'gcs_bucket_comprobantes' => env('GCS_BUCKET_COMPROBANTES', ''),
];
