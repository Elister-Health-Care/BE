<?php

namespace App\Repositories;

/**
 * Interface ExampleRepository.
 */
interface HealthInsuranceHospitalInterface extends RepositoryInterface
{
    public function getExamples($filter);
}
