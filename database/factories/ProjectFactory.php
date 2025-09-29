<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 30.09.25
 * Time: 00:34
 */

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'is_active' => $this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),

            'user_id' => User::factory(),
        ];
    }
}
