<?php

namespace Database\Seeders;

use App\Models\Leader;
use Illuminate\Database\Seeder;

class LeadershipSeeder extends Seeder
{
    public function run(): void
    {
        $leaders = [
            [
                'name' => 'Ramlal Jain',
                'company' => 'Kalptaru General Stores',
                'description' => 'A trusted leader known for operational discipline, integrity, and customer-first thinking.',
            ],
            [
                'name' => 'Pukhraj Jain',
                'company' => 'Kalptaru General Stores',
                'description' => 'Brings decades of retail expertise with a strong focus on consistency, reliability, and service excellence.',
            ],
            [
                'name' => 'Mahavir Jain',
                'company' => 'Vinayak Stores',
                'description' => 'Recognized for strategic retail leadership, operational excellence, and creating exceptional customer experiences.',
            ],
            [
                'name' => 'Rajiv Jain',
                'company' => 'Surtaru Wellness Pvt. Ltd.',
                'description' => 'Visionary entrepreneur driving growth through strategic planning, innovation, and long-term value creation.',
            ],
            [
                'name' => 'Mahendra Jain',
                'company' => 'Jay Tulsi Agency',
                'description' => 'Procurement expert with deep expertise in supply chain management, business relationships, and operational efficiency.',
            ],
            [
                'name' => 'Raj Jain',
                'company' => 'Kalptaru Super Market',
                'description' => 'Dynamic retail leader committed to innovation, operational excellence, and customer satisfaction.',
            ],
            [
                'name' => 'Arvind Jain',
                'company' => 'Hospitality & Catering Services',
                'description' => 'Known for delivering large-scale hospitality solutions with precision, quality, and dependable execution.',
            ],
            [
                'name' => 'Vimal Jain',
                'company' => 'Sathiya Enterprise',
                'description' => 'Manufacturing specialist focused on quality craftsmanship, process excellence, and scalable production.',
            ],
            [
                'name' => 'Kalpesh Jain',
                'company' => 'Shubh Avsar Events',
                'description' => 'Creative visionary with a passion for delivering memorable events through flawless planning and execution.',
            ],
            [
                'name' => 'Naveen Jain',
                'company' => 'Bhikshu Enterprise',
                'description' => 'Business strategist focused on innovation, digital transformation, and building long-term corporate partnerships.',
            ],
            [
                'name' => 'Rahul Jain',
                'company' => 'Green Paradise',
                'description' => 'Visionary developer dedicated to sustainable communities, thoughtful planning, and future-ready infrastructure.',
            ],
            [
                'name' => 'Ansh Jain',
                'company' => 'GP Studio Club & Resort',
                'description' => 'Driven hospitality leader committed to exceptional guest experiences, innovation, and service excellence.',
            ],
        ];

        foreach ($leaders as $i => $leader) {
            Leader::updateOrCreate(
                ['name' => $leader['name'], 'company' => $leader['company']],
                $leader + ['sort_order' => $i]
            );
        }
    }
}
