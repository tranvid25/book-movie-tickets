<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'Employee_Number' => $this->faker->unique()->numberBetween(1000, 9999),
            'Last_Name' => $this->faker->lastName,
            'First_Name' => $this->faker->firstName,
            'SSN' => $this->faker->ssn,
            'Pay_Rate' => $this->faker->word,
            'PayRates_id' => $this->faker->numberBetween(1, 5),
            'Vacation_Days' => $this->faker->numberBetween(10, 30),
            'Paid_To_Date' => $this->faker->randomFloat(2, 1000, 5000),
            'Paid_Last_Year' => $this->faker->randomFloat(2, 5000, 20000),
        ];
    }
}
