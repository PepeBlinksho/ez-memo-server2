<?php

namespace App\Providers;

use App\Protocols\MemoRepositoryProtocol;
use App\Repositories\MemoRepository;
use App\Repositories\MemoRepositoryV2;
use Illuminate\Support\ServiceProvider;

class MemoRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MemoRepositoryProtocol::class, MemoRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
