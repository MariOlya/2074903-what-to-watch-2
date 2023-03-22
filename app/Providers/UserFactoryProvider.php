<?php

declare(strict_types=1);

namespace App\Providers;

use App\Factories\Interfaces\UserFactoryInterface;
use App\Factories\UserFactory;
use Illuminate\Support\ServiceProvider;

class UserFactoryProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserFactoryInterface::class, UserFactory::class);
    }

}
