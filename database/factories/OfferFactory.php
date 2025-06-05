<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $service = $this->generateMedicalService();

        return [
            'user_id' => User::factory(),
            'title' => $service['title'],
            'description' => $service['description'],
            'price' => $service['price'],
        ];
    }

    /**
     * Generate realistic medical service data.
     */
    private function generateMedicalService(): array
    {
        $services = [
            [
                'title' => 'General Health Checkup',
                'description' => 'Comprehensive health examination including vital signs, blood pressure, and general wellness assessment.',
                'price_range' => [120, 180],
            ],
            [
                'title' => 'Blood Work Analysis',
                'description' => 'Complete blood count, metabolic panel, and lipid profile with detailed results.',
                'price_range' => [75, 120],
            ],
            [
                'title' => 'Cardiology Consultation',
                'description' => 'Expert cardiac evaluation including ECG and cardiovascular risk assessment.',
                'price_range' => [250, 350],
            ],
            [
                'title' => 'Dermatology Screening',
                'description' => 'Comprehensive skin examination and mole screening for early detection.',
                'price_range' => [150, 220],
            ],
            [
                'title' => 'Mental Health Counseling',
                'description' => 'Professional therapy session with licensed mental health counselor.',
                'price_range' => [100, 150],
            ],
            [
                'title' => 'Vaccination Services',
                'description' => 'Immunizations including flu shots, travel vaccines, and routine boosters.',
                'price_range' => [50, 100],
            ],
            [
                'title' => 'X-Ray Imaging',
                'description' => 'Digital X-ray examination for bone and joint evaluation.',
                'price_range' => [90, 140],
            ],
            [
                'title' => 'Ultrasound Examination',
                'description' => 'Non-invasive ultrasound imaging for diagnostic purposes.',
                'price_range' => [150, 220],
            ],
            [
                'title' => 'Physical Therapy Session',
                'description' => 'Therapeutic exercise and rehabilitation session with licensed therapist.',
                'price_range' => [80, 120],
            ],
            [
                'title' => 'Diabetes Management',
                'description' => 'Comprehensive diabetes care including glucose monitoring and lifestyle counseling.',
                'price_range' => [150, 200],
            ],
            [
                'title' => 'Pediatric Checkup',
                'description' => 'Complete health examination for children including growth and development assessment.',
                'price_range' => [100, 150],
            ],
            [
                'title' => 'Women\'s Health Exam',
                'description' => 'Comprehensive women\'s health screening including preventive care.',
                'price_range' => [140, 200],
            ],
        ];

        $service = fake()->randomElement($services);

        return [
            'title' => $service['title'],
            'description' => $service['description'],
            'price' => rand($service['price_range'][0], $service['price_range'][1]),
        ];
    }
}
