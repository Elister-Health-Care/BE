<?php

namespace App\Repositories;

/**
 * Interface ExampleRepository.
 */
interface HealthInsurancesInterface extends RepositoryInterface
{
    public function getExamples($filter);
}
