<?php

namespace App\Providers;

use App\Repositories\AdminInterface;
use App\Repositories\AdminRepository;
use App\Repositories\ArticleInterface;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\DepartmentInterface;
use App\Repositories\DepartmentRepository;
use App\Repositories\ExampleInterface;
use App\Repositories\ExampleRepository;
use App\Repositories\HealthInsuranceHospitalInterface;
use App\Repositories\HealthInsuranceHospitalRepository;
use App\Repositories\HealthInsurancesInterface;
use App\Repositories\HealthInsurancesRepository;
use App\Repositories\HospitalDepartmentInterface;
use App\Repositories\HospitalDepartmentRepository;
use App\Repositories\HospitalServiceInterface;
use App\Repositories\HospitalServiceRepository;
use App\Repositories\InforDoctorInterface;
use App\Repositories\InforDoctorRepository;
use App\Repositories\InforHospitalInterface;
use App\Repositories\InforHospitalRepository;
use App\Repositories\InforUserInterface;
use App\Repositories\InforUserRepository;
use App\Repositories\UserInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ExampleInterface::class, ExampleRepository::class);
        $this->app->bind(AdminInterface::class, AdminRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(InforUserInterface::class, InforUserRepository::class);
        $this->app->bind(InforHospitalInterface::class, InforHospitalRepository::class);
        $this->app->bind(InforDoctorInterface::class, InforDoctorRepository::class);
        $this->app->bind(PasswordResetInterface::class, PasswordResetRepository::class);
        $this->app->bind(CategoryInterface::class, CategoryRepository::class);
        $this->app->bind(ArticleInterface::class, ArticleRepository::class);
        $this->app->bind(DepartmentInterface::class, DepartmentRepository::class);
        $this->app->bind(HospitalServiceInterface::class, HospitalServiceRepository::class);
        $this->app->bind(HospitalDepartmentInterface::class, HospitalDepartmentRepository::class);
        $this->app->bind(HealthInsurancesInterface::class, HealthInsurancesRepository::class);
        $this->app->bind(HealthInsuranceHospitalInterface::class, HealthInsuranceHospitalRepository::class);
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
