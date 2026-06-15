<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Operation;
use App\Models\UserFocusOperation;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFocusOperationFactory extends Factory
{
    protected $model = UserFocusOperation::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'operation_id' => Operation::factory(),
        ];
    }
}
