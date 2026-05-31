<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = [
            'super_admin', 'finance_admin', 'compliance_admin', 'operations_admin',
            'fleet_owner', 'fleet_manager', 'fleet_accountant', 'fleet_supervisor',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gocartlex.com'],
            [
                'name' => 'Cartlex Super Admin',
                'password' => bcrypt('Cartlex@2025!'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('super_admin');

        // Finance Admin
        $financeAdmin = User::firstOrCreate(
            ['email' => 'finance@gocartlex.com'],
            [
                'name' => 'Finance Admin',
                'password' => bcrypt('Cartlex@2025!'),
                'email_verified_at' => now(),
            ]
        );
        $financeAdmin->assignRole('finance_admin');

        // Demo fleet partner
        $demoUser = User::firstOrCreate(
            ['email' => 'demo@partner.com'],
            [
                'name' => 'Demo Fleet Partner',
                'password' => bcrypt('Demo@2025!'),
                'email_verified_at' => now(),
            ]
        );
        $demoUser->assignRole('fleet_owner');

        $partner = Partner::firstOrCreate(
            ['user_id' => $demoUser->id],
            [
                'partner_code' => 'CTX-INV-DEMO01',
                'partner_type' => 'independent_investor',
                'company_name' => 'Demo Fleet Investments',
                'contact_person' => 'Demo Partner',
                'phone' => '08012345678',
                'status' => 'active',
                'approved_at' => now(),
                'wallet_balance' => 245000,
                'lifetime_earnings' => 1250000,
            ]
        );

        // Seed demo motorcycles
        $motorcycleData = [
            ['brand' => 'Honda', 'model' => 'CB300R', 'year' => 2023, 'color' => 'Red', 'status' => 'active', 'total_earnings' => 320000],
            ['brand' => 'Yamaha', 'model' => 'MT-15', 'year' => 2022, 'color' => 'Blue', 'status' => 'active', 'total_earnings' => 280000],
            ['brand' => 'Bajaj', 'model' => 'Pulsar 150', 'year' => 2023, 'color' => 'Black', 'status' => 'maintenance', 'total_earnings' => 150000],
            ['brand' => 'TVS', 'model' => 'Apache RTR', 'year' => 2022, 'color' => 'White', 'status' => 'active', 'total_earnings' => 210000],
            ['brand' => 'Honda', 'model' => 'CG 125', 'year' => 2021, 'color' => 'Silver', 'status' => 'active', 'total_earnings' => 290000],
        ];

        foreach ($motorcycleData as $i => $data) {
            $num = str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $motorcycle = $partner->motorcycles()->firstOrCreate(
                ['fleet_id' => "CTX-{$num}"],
                [
                    ...$data,
                    'plate_number' => "LG-{$num}-ABC",
                    'purchase_cost' => 450000,
                    'purchase_date' => now()->subMonths(rand(6, 18))->toDateString(),
                    'insurance_status' => 'active',
                    'insurance_expiry' => now()->addMonths(rand(1, 12))->toDateString(),
                    'health_rating' => 'good',
                    'health_score' => rand(75, 98),
                ]
            );

            // Add riders
            if ($data['status'] === 'active') {
                $rider = $partner->riders()->firstOrCreate(
                    ['rider_id' => "CTR-RID-{$num}"],
                    [
                        'first_name' => "Rider{$num}",
                        'last_name' => 'Cartlex',
                        'phone' => '0801234' . $num,
                        'status' => 'active',
                        'license_number' => "LAG-{$num}-2023",
                        'license_expiry' => now()->addMonths(rand(3, 18))->toDateString(),
                        'current_motorcycle_id' => $motorcycle->id,
                        'assignment_date' => now()->subMonths(3)->toDateString(),
                        'total_earnings' => $data['total_earnings'],
                        'total_deliveries' => rand(100, 500),
                        'compliance_score' => rand(85, 99),
                        'performance_score' => rand(80, 99),
                    ]
                );
            }

            // Add earnings records
            for ($j = 0; $j < 30; $j++) {
                $date = now()->subDays($j)->toDateString();
                $amount = rand(5000, 15000);
                $fee = round($amount * 0.15);
                $partner->earnings()->create([
                    'motorcycle_id' => $motorcycle->id,
                    'amount' => $amount,
                    'platform_fee' => $fee,
                    'net_amount' => $amount - $fee,
                    'earning_date' => $date,
                    'period_type' => 'daily',
                    'source' => 'delivery',
                    'status' => 'confirmed',
                ]);
            }
        }
    }
}
