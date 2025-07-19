<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        // Tạo 10 bản ghi với factory
        \App\Models\Employee::factory(10)->create();
    }
}
