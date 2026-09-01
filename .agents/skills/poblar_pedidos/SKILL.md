---
name: poblar-pedidos
description: >-
  Puebla la base de datos con pedidos de prueba (mock) garantizando la integridad referencial. Permite especificar la cantidad de pedidos deseada.
---

# Poblar Pedidos

## Overview
Este skill ejecuta un script en PHP (PDO puro) que genera automáticamente pedidos de prueba en el sistema. Los pedidos generados utilizan clientes reales, direcciones reales y productos reales de la base de datos para mantener la integridad referencial. 

Los estados de los pedidos generados se establecerán en pendiente de procesamiento (`en_espera_aprobacion`). Además, insertará un comprobante de pago simulado automáticamente para los métodos que lo requieran (transferencias, de_una, etc).

## Workflow

### 1. Obtener la cantidad de pedidos
- Determina con el usuario cuántos pedidos quiere generar.
- Si el usuario no lo especifica, asume `20` por defecto.

### 2. Ejecutar el script
- El script generador se encuentra en `.agents/skills/poblar_pedidos/seed.php`.
- Debes ejecutarlo utilizando el binario de PHP configurado para el proyecto: `C:\MAMP\bin\php\php8.2.14\php.exe`.
- Pasa la cantidad de pedidos como el primer argumento. 
- Comando exacto a usar en la terminal:
  `C:\MAMP\bin\php\php8.2.14\php.exe .agents\skills\poblar_pedidos\seed.php [CANTIDAD]`
  
  *Ejemplo para 30 pedidos:*
  `C:\MAMP\bin\php\php8.2.14\php.exe .agents\skills\poblar_pedidos\seed.php 30`

### 3. Confirmar la ejecución
- Informa al usuario que los pedidos se han generado con éxito y que están listos ("pendientes") para ser procesados o asignados en el Dashboard/Mis Rutas.
