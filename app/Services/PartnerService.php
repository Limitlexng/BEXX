<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PartnerService
{
    public function createPartner(User $user, array $data): Partner
    {
        $partner = Partner::create([
            'user_id' => $user->id,
            'partner_code' => $this->generatePartnerCode($data['partner_type']),
            'partner_type' => $data['partner_type'],
            'company_name' => $data['company_name'] ?? null,
            'contact_person' => $data['contact_person'] ?? $user->name,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'status' => 'pending',
        ]);

        $role = match($data['partner_type']) {
            'logistics_company' => 'fleet_owner',
            'corporate_fleet_partner' => 'fleet_owner',
            default => 'fleet_owner',
        };

        $user->assignRole($role);

        return $partner;
    }

    public function approvePartner(Partner $partner, User $admin): void
    {
        $partner->update([
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);
    }

    public function generatePartnerCode(string $type): string
    {
        $prefix = match($type) {
            'logistics_company' => 'CTX-LC',
            'corporate_fleet_partner' => 'CTX-CF',
            default => 'CTX-INV',
        };

        return $prefix . '-' . strtoupper(Str::random(6));
    }

    public function creditWallet(Partner $partner, float $amount, string $description, ?int $earningId = null): void
    {
        $balanceBefore = $partner->wallet_balance;
        $balanceAfter = $balanceBefore + $amount;

        $partner->walletTransactions()->create([
            'reference' => 'TXN-' . strtoupper(Str::random(12)),
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'earning_id' => $earningId,
        ]);

        $partner->increment('wallet_balance', $amount);
        $partner->increment('lifetime_earnings', $amount);
    }

    public function debitWallet(Partner $partner, float $amount, string $description): bool
    {
        if ($partner->wallet_balance < $amount) {
            return false;
        }

        $balanceBefore = $partner->wallet_balance;
        $balanceAfter = $balanceBefore - $amount;

        $partner->walletTransactions()->create([
            'reference' => 'TXN-' . strtoupper(Str::random(12)),
            'type' => 'debit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
        ]);

        $partner->decrement('wallet_balance', $amount);

        return true;
    }
}
