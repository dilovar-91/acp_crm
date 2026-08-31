<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Sharoff\Mango\Api\MangoHelper;

class MangoCommandController extends Controller
{
    public function makeCall(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'showroom_id' => ['required', 'integer'],
            'ext_number' => ['required'],
            'phone' => ['required', 'string'],
        ]);

        MangoHelper::setApiKey(config('mango.api_key_' . $validated['showroom_id']))
            ->setApiSalt(config('mango.api_salt_' . $validated['showroom_id']));

        $response = MangoHelper::sendCall(
            $validated['ext_number'],
            $validated['phone']
        );

        return response()->json($response);
    }
}
