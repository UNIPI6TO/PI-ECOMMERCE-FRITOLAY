<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Contracts\ProductoRepositoryInterface;
use App\Repositories\ProductoRepository;
use App\Contracts\PedidoRepositoryInterface;
use App\Repositories\PedidoRepository;
use App\Contracts\GuiaRepositoryInterface;
use App\Repositories\GuiaRepository;
use App\Contracts\BodegaRepositoryInterface;
use App\Repositories\BodegaRepository;
use App\Contracts\CamionRepositoryInterface;
use App\Repositories\CamionRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ProductoRepositoryInterface::class, ProductoRepository::class);
        $this->app->bind(PedidoRepositoryInterface::class, PedidoRepository::class);
        $this->app->bind(GuiaRepositoryInterface::class, GuiaRepository::class);
        $this->app->bind(BodegaRepositoryInterface::class, BodegaRepository::class);
        $this->app->bind(CamionRepositoryInterface::class, CamionRepository::class);
    }

    public function boot(): void {}
}
