<?php

namespace App\Http\Controllers;

use App\Services\RiderIdCardService;

class VerificationController extends Controller
{
    public function __construct(private RiderIdCardService $idCardService) {}

    public function verifyRider(string $cardNumber)
    {
        $data = $this->idCardService->getVerificationData($cardNumber);

        if (!$data) {
            return view('verification.not-found', ['cardNumber' => $cardNumber]);
        }

        return view('verification.rider', $data);
    }
}
