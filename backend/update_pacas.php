<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Illuminate\Support\Facades\DB::table('productos')->whereIn('id', [1,2,3])->update(['unidades_por_paca' => 12]);
\Illuminate\Support\Facades\DB::table('productos')->whereIn('id', [4,5])->update(['unidades_por_paca' => 24]);
echo "Done\n";
