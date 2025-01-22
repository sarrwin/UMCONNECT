<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Supervisor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // List of departments
        $departments = [
            'Artificial Intelligence',
            'Software Engineering',
            'Computer System and Network',
            'Multimedia',
            'Information System',
        ];

        // Names for students
        $studentNames = [
            'Alice Tan', 'Brian Lee', 'Cheryl Wong', 'David Lim', 'Elaine Ng',
            'Farid Hassan', 'Grace Chua', 'Hassan Ahmad', 'Isabelle Tan', 'Jason Koh',
            'Katherine Ng', 'Liam Ho', 'Melissa Wong', 'Nathan Chan', 'Olivia Yeo',
            'Peter Goh', 'Quincy Lim', 'Rachel Chua', 'Samuel Tan', 'Tiffany Teo',
        ];

        // Names for supervisors
        $supervisorNames = [
            'Dr. Alan Ng', 'Dr. Siti', 'Dr. Amirul', 'Dr. Deborah Lee', 'Dr. Edwin Wong',
            'Dr. Fiona Ho', 'Dr. George Chan', 'Dr. Hannah Ng', 'Dr. Ivan Koh', 'Dr. Julia Chua',
            'Dr. Kelvin Ong', 'Dr. Hajar', 'Dr. Michael Yeo', 'Dr. Azim', 'Dr. Oliver Tan',
            'Dr. Pauline Ng', 'Dr. Quentin Wong', 'Dr. Raymond Ho', 'Dr. Sarah Lee', 'Dr. Timothy Chan',
        ];

        // Insert students
        foreach ($studentNames as $index => $name) {
            $department = $departments[array_rand($departments)];
            $user = User::create([
                'name' => $name,
                'email' => "student{$index}@example.com",
                'password' => Hash::make('password123'),
                'role' => 'student',
            ]);

            Student::create([
                'student_id' => $user->id,
                'name' => $name,
                'email' => $user->email,
                'matric_number' => "STU12345" . ($index + 1),
                'contact_number' => '0123456789',
                'department' => $department,
            ]);
        }

        // Insert supervisors
        foreach ($supervisorNames as $index => $name) {
            $department = $departments[array_rand($departments)];
            $user = User::create([
                'name' => $name,
                'email' => "supervisor{$index}@example.com",
                'password' => Hash::make('password123'),
                'role' => 'supervisor',
            ]);

            Supervisor::create([
                'supervisor_id' => $user->id,
                'name' => $name,
                'email' => $user->email,
                'staff_id' => "SUP12345" . ($index + 1),
                'is_coordinator' => false,
                'contact_number' => '0987654321',
                'department' => $department,
                'office_address' => "Office " . ($index + 1),
            ]);
        }

        echo "20 students and 20 supervisors with names have been created.";
    }
}
