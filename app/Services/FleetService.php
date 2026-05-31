<?php

namespace App\Services;

use App\Models\Motorcycle;
use App\Models\Partner;
use App\Models\Rider;
use App\Models\RiderAssignment;
use Illuminate\Support\Str;

class FleetService
{
    public function generateFleetId(): string
    {
        do {
            $id = 'CTX-' . strtoupper(Str::random(8));
        } while (Motorcycle::where('fleet_id', $id)->exists());

        return $id;
    }

    public function registerMotorcycle(Partner $partner, array $data): Motorcycle
    {
        return $partner->motorcycles()->create([
            ...$data,
            'fleet_id' => $this->generateFleetId(),
            'status' => 'active',
            'health_rating' => 'good',
            'health_score' => 100,
        ]);
    }

    public function assignRider(Motorcycle $motorcycle, Rider $rider): RiderAssignment
    {
        // End any current assignment
        if ($rider->current_motorcycle_id) {
            $this->unassignRider($rider);
        }

        if ($motorcycle->currentRider) {
            $this->unassignRiderFromMotorcycle($motorcycle);
        }

        $assignment = RiderAssignment::create([
            'motorcycle_id' => $motorcycle->id,
            'rider_id' => $rider->id,
            'partner_id' => $motorcycle->partner_id,
            'assigned_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $rider->update([
            'current_motorcycle_id' => $motorcycle->id,
            'assignment_date' => now()->toDateString(),
        ]);

        return $assignment;
    }

    public function unassignRider(Rider $rider): void
    {
        if (!$rider->current_motorcycle_id) return;

        RiderAssignment::where('rider_id', $rider->id)
            ->where('status', 'active')
            ->update(['status' => 'completed', 'unassigned_date' => now()->toDateString()]);

        $rider->update(['current_motorcycle_id' => null, 'assignment_date' => null]);
    }

    public function unassignRiderFromMotorcycle(Motorcycle $motorcycle): void
    {
        $rider = $motorcycle->currentRider;
        if ($rider) {
            $this->unassignRider($rider);
        }
    }

    public function calculateHealthScore(Motorcycle $motorcycle): array
    {
        $score = 100;
        $factors = [];

        // Downtime penalty
        $totalDowntime = $motorcycle->maintenanceLogs()->sum('downtime_hours');
        if ($totalDowntime > 100) {
            $score -= 20;
            $factors[] = 'High downtime';
        } elseif ($totalDowntime > 50) {
            $score -= 10;
            $factors[] = 'Moderate downtime';
        }

        // Maintenance frequency
        $maintenanceCount = $motorcycle->maintenanceLogs()->count();
        if ($maintenanceCount > 10) {
            $score -= 10;
            $factors[] = 'Frequent maintenance';
        }

        // Insurance status
        if ($motorcycle->insurance_status === 'expired') {
            $score -= 15;
            $factors[] = 'Insurance expired';
        }

        // Age penalty
        $age = now()->year - $motorcycle->year;
        if ($age > 5) {
            $score -= 10;
            $factors[] = 'Vehicle age';
        }

        $score = max(0, $score);

        $rating = match(true) {
            $score >= 90 => 'excellent',
            $score >= 75 => 'good',
            $score >= 60 => 'average',
            $score >= 40 => 'poor',
            default => 'critical',
        };

        return ['score' => $score, 'rating' => $rating, 'factors' => $factors];
    }
}
