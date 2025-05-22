<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\AuthServiceInterface;
use App\Interfaces\DashboardServiceInterface;
use App\Interfaces\StoreServiceInterface;
use App\Interfaces\TransactionServiceInterface;
use App\Interfaces\TransactionSubtypeServiceInterface;
use App\Interfaces\TransactionTypeServiceInterface;
use App\Interfaces\UploadedFileServiceInterface;
use App\Services\AuthService;
use App\Services\DashboardService;
use App\Services\StoreService;
use App\Services\TransactionService;
use App\Services\TransactionSubtypeService;
use App\Services\TransactionTypeService;
use App\Services\UploadedFileService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(DashboardServiceInterface::class, DashboardService::class);
        $this->app->bind(StoreServiceInterface::class, StoreService::class);
        $this->app->bind(TransactionServiceInterface::class, TransactionService::class);
        $this->app->bind(TransactionSubtypeServiceInterface::class, TransactionSubtypeService::class);
        $this->app->bind(TransactionTypeServiceInterface::class, TransactionTypeService::class);
        $this->app->bind(UploadedFileServiceInterface::class, UploadedFileService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
