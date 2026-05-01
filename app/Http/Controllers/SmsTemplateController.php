<?php

namespace App\Http\Controllers;

use App\Models\SmsTemplate;
use Illuminate\Http\Request;


class SmsTemplateController extends Controller
{
    // GET /api/sms/templates?showroom_id=1
    public function index(Request $request)
    {
        $data = $request->validate([
            'showroom_id' => ['required', 'integer', 'min:1'],
            'operator_id' => ['nullable', 'integer', 'min:1'],
        ]);

        return SmsTemplate::query()
            ->where('showroom_id', $data['showroom_id'])
            ->where('is_active', 1)
            ->when(
                !empty($data['operator_id']),
                fn ($q) => $q->where('operator_id', $data['operator_id'])
            )
            ->orderBy('name')
            ->get(['id', 'name', 'body']);
    }

    // POST /api/sms/templates
    public function store(Request $request)
    {
        $data = $request->validate([
            'showroom_id' => ['required', 'integer', 'min:1'],
            'operator_id' => ['required', 'integer', 'min:1'],
            'name'        => ['required', 'string', 'max:100'],
            'body'        => ['required', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $template = SmsTemplate::create([
            'showroom_id' => $data['showroom_id'],
            'operator_id' => $data['operator_id'],
            'name'        => $data['name'],
            'body'        => $data['body'],
            'is_active'   => $data['is_active'] ?? true,
        ]);

        return response()->json($template, 201);
    }

    // PUT /api/sms/templates/{id}
    public function update(Request $request, int $id)
    {
        $template = SmsTemplate::findOrFail($id);

        $data = $request->validate([
            'name'      => ['sometimes', 'string', 'max:100'],
            'body'      => ['sometimes', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $template->update($data);

        return response()->json($template);
    }

    // DELETE /api/sms/templates/{id}
    public function destroy(int $id)
    {
        $template = SmsTemplate::findOrFail($id);
        $template->delete();

        return response()->json(['status' => 'ok']);
    }
}
