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
