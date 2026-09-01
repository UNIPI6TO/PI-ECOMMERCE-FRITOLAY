---
name: limpiar-ventas-fritolay
description: >-
  Limpia todas las tablas de la base de datos relacionadas con el proceso de ventas, pedidos, rutas, guías, facturas, notas de crédito, bodegas de los vehículos y transacciones de inventario.
---

# Limpiar Ventas Fritolay

## Overview
Este skill ejecuta un script en PHP para truncar de manera segura (desactivando las llaves foráneas temporalmente) todas las tablas transaccionales del flujo de ventas y distribución del sistema Fritolay.

## Tablas Afectadas
- `notas_credito`
- `facturas`
- `mercaderia_mal_estado`
- `asignacion_pedido_camion`
- `items_pedido`
- `pedidos`
- `carritos_abandonados`
- `guias_ruta`
- `guias_remision`
- `bodega_camion`
- `transacciones_inventario`

## Workflow

### 1. Ejecutar el script
- El script de limpieza se encuentra en `.agents/skills/limpiar-ventas-fritolay/clean.php`.
- Debes ejecutarlo utilizando el binario de PHP configurado para el proyecto: `C:\MAMP\bin\php\php8.2.14\php.exe`.
- El comando exacto a usar en la terminal es:
  `C:\MAMP\bin\php\php8.2.14\php.exe .agents\skills\limpiar-ventas-fritolay\clean.php`

### 2. Confirmar la ejecución
- Informa al usuario que las tablas han sido vaciadas exitosamente y que el sistema está listo para pruebas limpias.
