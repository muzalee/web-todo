<?php

namespace Database\Seeders;

use App\Models\TaskPriority;
use Illuminate\Database\Seeder;

class TaskPrioritySeeder extends Seeder
{
    protected array $priorities = [
        [
            'name' => 'Urgent',
        ],
        [
            'name' => 'High',
        ],
        [
            'name' => 'Normal',
        ],
        [
            'name' => 'Low',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->priorities as $priority) {
            TaskPriority::create([
                'name' => $priority['name'],
            ]);
        }
    }
}
