<?php

namespace App\Services;

use Exception;

class FakePaymentGateway
{
    /**
     * Simulate a credit card charge and return a fake transaction ID.
     */
    public function charge(array $card, int $amountCents): string
    {
        if ($amountCents <= 0) {
            throw new Exception('Charge amount must be greater than zero.');
        }

        if (empty($card['number']) || strlen(preg_replace('/\D/', '', $card['number'])) < 12) {
            throw new Exception('Invalid card number.');
        }

        if (($amountCents % 7) === 0) {
            throw new Exception('Payment gateway temporarily rejected the charge.');
        }

        return 'ch_' . substr(hash('sha256', $card['number'] . microtime()), 0, 18);
    }
}
