<?php

namespace Tests\Unit\Mango;

use App\Services\Mango\MangoCallData;
use PHPUnit\Framework\TestCase;

class MangoCallDataTest extends TestCase
{
    private MangoCallData $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = new MangoCallData();
    }

    public function test_it_detects_incoming_call_and_client_phone(): void
    {
        $payload = $this->payload([
            'from' => ['number' => '79037776964'],
            'to' => [
                'number' => 'sip:user@vpbx.mangosip.ru',
                'line_number' => 'sip:line71554@vpbx400371758.mangosip.ru',
            ],
        ]);

        $direction = $this->data->directionFromRealtime($payload);

        $this->assertSame(MangoCallData::INCOMING, $direction);
        $this->assertSame('79037776964', $this->data->clientPhone($payload, $direction));
    }

    public function test_connected_without_line_number_is_still_incoming(): void
    {
        $payload = $this->payload([
            'call_state' => 'Connected',
            'location' => 'abonent',
            'from' => ['number' => '79037776964'],
            'to' => [
                'extension' => '106',
                'number' => 'sip:user106@vpbx.mangosip.ru',
            ],
        ]);

        $direction = $this->data->directionFromRealtime($payload);

        $this->assertSame(MangoCallData::INCOMING, $direction);
        $this->assertSame('106', $this->data->operatorExtension($payload, $direction));
    }

    public function test_route_to_abonent_with_command_id_stays_incoming(): void
    {
        // Маршрутизация из IVR: command_id + taken_from_call_id — входящий.
        $payload = $this->payload([
            'call_state' => 'Appeared',
            'location' => 'abonent',
            'command_id' => 'c111',
            'from' => [
                'number' => '79037776964',
                'taken_from_call_id' => '100:500:256',
            ],
            'to' => [
                'extension' => '123',
                'number' => 'sip:aaa@mangosip.ru',
                'line_number' => '7800123456789',
            ],
        ]);

        $this->assertFalse($this->data->isOutboundApiCall($payload));
        $this->assertSame(
            MangoCallData::INCOMING,
            $this->data->directionFromRealtime($payload)
        );
    }

    public function test_it_detects_outgoing_call_and_uses_to_number(): void
    {
        $payload = $this->payload([
            'from' => [
                'extension' => '106',
                'number' => '74951234567',
            ],
            'to' => ['number' => '79037776964'],
        ]);

        $direction = $this->data->directionFromRealtime($payload);

        $this->assertSame(MangoCallData::OUTGOING, $direction);
        $this->assertSame('79037776964', $this->data->clientPhone($payload, $direction));
    }

    public function test_softphone_appeared_with_only_from_extension_is_outgoing(): void
    {
        // Дока: исходящий Appeared — from.extension, to.number
        $payload = $this->payload([
            'call_state' => 'Appeared',
            'location' => 'abonent',
            'from' => ['extension' => '1234'],
            'to' => ['number' => '79037776964'],
        ]);

        $this->assertSame(
            MangoCallData::OUTGOING,
            $this->data->directionFromRealtime($payload)
        );
    }

    public function test_api_callback_looking_like_inbound_is_outgoing(): void
    {
        $payload = $this->payload([
            'call_state' => 'Appeared',
            'location' => 'abonent',
            'callback_initiator' => 'API',
            'from' => ['number' => '79037776964'],
            'to' => [
                'extension' => '106',
                'number' => 'sip:user106@vpbx.mangosip.ru',
                'line_number' => '74951234567',
            ],
        ]);

        $this->assertTrue($this->data->isOutboundApiCall($payload));
        $this->assertSame(
            MangoCallData::OUTGOING,
            $this->data->directionFromRealtime($payload)
        );
    }

    public function test_callback_command_id_without_taken_from_is_outgoing(): void
    {
        // Дока «Инициирование исходящего»: command_id, оба extension, без taken_from_call_id
        $payload = $this->payload([
            'call_state' => 'Appeared',
            'location' => 'abonent',
            'command_id' => 'cmd.2.vpbx.12345.external.system.com.net',
            'from' => [
                'extension' => '5555',
                'number' => '74955404444',
            ],
            'to' => [
                'extension' => '1234',
                'number' => '12345678',
            ],
        ]);

        $this->assertTrue($this->data->isOutboundApiCall($payload));
        $this->assertSame(
            MangoCallData::OUTGOING,
            $this->data->directionFromRealtime($payload)
        );
    }

    public function test_obdial_task_is_outgoing(): void
    {
        $payload = $this->payload([
            'call_state' => 'Appeared',
            'task_id' => 12345,
            'from' => ['number' => '79037776964'],
            'to' => [
                'extension' => '106',
                'line_number' => '74951234567',
            ],
        ]);

        $this->assertSame(
            MangoCallData::OUTGOING,
            $this->data->directionFromRealtime($payload)
        );
    }

    public function test_sip_line_is_never_treated_as_client_phone(): void
    {
        $this->assertNull(
            $this->data->externalPhone('sip:line71554@vpbx400371758.mangosip.ru')
        );
        $this->assertNull(
            $this->data->externalPhone('sip:line76@vpbx400371758.mangosip.ru')
        );
    }

    public function test_invalid_plus_seven_prefix_is_rejected(): void
    {
        $this->assertNull($this->data->externalPhone('75400371758'));
        $this->assertNull($this->data->externalPhone('76400371758'));
    }

    public function test_internal_call_has_no_client_phone(): void
    {
        $payload = $this->payload([
            'from' => ['extension' => '101', 'number' => 'sip:user1@mangosip.ru'],
            'to' => ['extension' => '102', 'number' => 'sip:user2@mangosip.ru'],
        ]);

        $direction = $this->data->directionFromRealtime($payload);

        $this->assertSame(MangoCallData::INTERNAL, $direction);
        $this->assertNull($this->data->clientPhone($payload, $direction));
    }

    private function payload(array $data): object
    {
        return json_decode(json_encode($data));
    }
}
