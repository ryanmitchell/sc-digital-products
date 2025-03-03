<?php

namespace DoubleThreeDigital\DigitalProducts\Http\Controllers;

use DoubleThreeDigital\DigitalProducts\Http\Requests\VerificationRequest;
use Illuminate\Routing\Controller;
use Statamic\Facades\Entry;

class VerificationController extends Controller
{
    public function index(VerificationRequest $request)
    {
        $orders = Entry::whereCollection(config('simple-commerce.collections.orders'))
            ->filter(function ($order) {
                return $order->get('is_paid') === true;
            })
            ->map(function ($order) use ($request) {
                foreach ($order->get('items') as $item) {
                    if (isset($item['license_key']) && $item['license_key'] === $request->license_key) {
                        return ['result' => true];
                    }
                }

                return ['result' => false];
            })
            ->where('result', true)
            ->flatten()
            ->toArray();

        if (isset($orders[0])) {
            return $this->validResponse($request);
        }

        return $this->invalidResponse($request);
    }

    protected function validResponse($request)
    {
        return [
            'license_key' => $request->license_key,
            'valid' => true,
        ];
    }

    protected function invalidResponse($request)
    {
        return [
            'license_key' => $request->license_key,
            'valid' => false,
        ];
    }
}
