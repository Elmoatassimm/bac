<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeederCommand extends Seeder
{
    /**
     * Seed the database with test data and provide statistics.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting test data seeding...');
        
        // Clear existing data
        $this->command->info('🧹 Clearing existing data...');
        $this->clearExistingData();
        
        // Run the test data seeder
        $this->command->info('📊 Creating test data...');
        $this->call(TestDataSeeder::class);
        
        // Display statistics
        $this->displayStatistics();
        
        $this->command->info('✅ Test data seeding completed successfully!');
    }

    /**
     * Clear existing data from all tables.
     */
    private function clearExistingData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = ['payments', 'bookings', 'offers', 'clients', 'users'];
        
        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $this->command->line("   Cleared {$table} table");
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Display statistics about the seeded data.
     */
    private function displayStatistics(): void
    {
        $this->command->info('📈 Seeding Statistics:');
        
        $stats = [
            'Users (Medical Centers)' => DB::table('users')->count(),
            'Offers (Medical Services)' => DB::table('offers')->count(),
            'Clients' => DB::table('clients')->count(),
            'Bookings' => DB::table('bookings')->count(),
            'Payments' => DB::table('payments')->count(),
        ];
        
        foreach ($stats as $label => $count) {
            $this->command->line("   {$label}: {$count}");
        }
        
        // Booking status breakdown
        $this->command->info('📋 Booking Status Breakdown:');
        $bookingStats = DB::table('bookings')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
            
        foreach ($bookingStats as $stat) {
            $this->command->line("   {$stat->status}: {$stat->count}");
        }
        
        // Payment status breakdown
        $this->command->info('💳 Payment Status Breakdown:');
        $paymentStats = DB::table('payments')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
            
        foreach ($paymentStats as $stat) {
            $this->command->line("   {$stat->status}: {$stat->count}");
        }
    }
}
