<?php

namespace App\Services;

use App\Models\Rider;
use App\Models\RiderIdCard;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RiderIdCardService
{
    public function generateIdCard(Rider $rider): RiderIdCard
    {
        // Revoke existing active cards
        $rider->idCards()->where('status', 'active')->update(['status' => 'revoked']);

        $cardNumber = 'CTR-RID-' . str_pad($rider->id, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(4));
        $verificationUrl = url("/verify/rider/{$cardNumber}");

        // Generate QR code
        $qrDir = storage_path('app/public/qrcodes');
        if (!file_exists($qrDir)) {
            mkdir($qrDir, 0755, true);
        }
        $qrPath = "qrcodes/{$cardNumber}.svg";
        QrCode::format('svg')->size(200)->generate($verificationUrl, storage_path("app/public/{$qrPath}"));

        return RiderIdCard::create([
            'rider_id' => $rider->id,
            'partner_id' => $rider->partner_id,
            'card_number' => $cardNumber,
            'qr_code_path' => $qrPath,
            'verification_url' => $verificationUrl,
            'issue_date' => now()->toDateString(),
            'expiry_date' => now()->addYear()->toDateString(),
            'status' => 'active',
        ]);
    }

    public function getVerificationData(string $cardNumber): ?array
    {
        $card = RiderIdCard::where('card_number', $cardNumber)
            ->with(['rider.partner', 'rider.currentMotorcycle'])
            ->first();

        if (!$card) return null;

        return [
            'card' => $card,
            'rider' => $card->rider,
            'partner' => $card->rider->partner,
            'motorcycle' => $card->rider->currentMotorcycle,
            'is_valid' => $card->isValid(),
            'verified_at' => now()->toIso8601String(),
        ];
    }
}
