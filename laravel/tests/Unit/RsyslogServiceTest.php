<?php

use App\Models\Log;
use App\Services\RsyslogService;

beforeEach(function () {
    $this->service = new RsyslogService;
});

it('formats syslog message with correct facility and priority mapping', function () {
    $carbon = Mockery::mock(\Illuminate\Support\Carbon::class);
    $carbon->shouldReceive('format')->with('Y-m-d\TH:i:s.vP')->andReturn('2026-06-11T10:00:00.000000+00:00');

    $log = Mockery::mock(Log::class)->shouldIgnoreMissing();
    $log->shouldReceive('getAttribute')->with('facility')->andReturn('kern');
    $log->shouldReceive('getAttribute')->with('priority')->andReturn('emerg');
    $log->shouldReceive('getAttribute')->with('message')->andReturn('Test kernel emergency message');
    $log->shouldReceive('getAttribute')->with('created_at')->andReturn($carbon);

    $result = $this->service->formatSyslogMessage($log);

    expect($result)->toBe('<0>1 2026-06-11T10:00:00.000000+00:00 php laravel - - - Test kernel emergency message');
});

it('formats syslog message with default facility when unknown', function () {
    $carbon = Mockery::mock(\Illuminate\Support\Carbon::class);
    $carbon->shouldReceive('format')->with('Y-m-d\TH:i:s.vP')->andReturn('2026-06-11T10:00:00.000000+00:00');

    $log = Mockery::mock(Log::class)->shouldIgnoreMissing();
    $log->shouldReceive('getAttribute')->with('facility')->andReturn('unknown_facility');
    $log->shouldReceive('getAttribute')->with('priority')->andReturn('info');
    $log->shouldReceive('getAttribute')->with('message')->andReturn('Test message');
    $log->shouldReceive('getAttribute')->with('created_at')->andReturn($carbon);

    $result = $this->service->formatSyslogMessage($log);

    expect($result)->toBe('<14>1 2026-06-11T10:00:00.000000+00:00 php laravel - - - Test message');
});

it('formats syslog message with default priority when unknown', function () {
    $carbon = Mockery::mock(\Illuminate\Support\Carbon::class);
    $carbon->shouldReceive('format')->with('Y-m-d\TH:i:s.vP')->andReturn('2026-06-11T10:00:00.000000+00:00');

    $log = Mockery::mock(Log::class)->shouldIgnoreMissing();
    $log->shouldReceive('getAttribute')->with('facility')->andReturn('user');
    $log->shouldReceive('getAttribute')->with('priority')->andReturn('unknown_priority');
    $log->shouldReceive('getAttribute')->with('message')->andReturn('Test message');
    $log->shouldReceive('getAttribute')->with('created_at')->andReturn($carbon);

    $result = $this->service->formatSyslogMessage($log);

    expect($result)->toBe('<14>1 2026-06-11T10:00:00.000000+00:00 php laravel - - - Test message');
});

it('computes correct PRI value for various facility/priority combinations', function (int $expectedPri, string $facility, string $priority) {
    $carbon = Mockery::mock(\Illuminate\Support\Carbon::class);
    $carbon->shouldReceive('format')->with('Y-m-d\TH:i:s.vP')->andReturn('2026-06-11T10:00:00.000000+00:00');

    $log = Mockery::mock(Log::class)->shouldIgnoreMissing();
    $log->shouldReceive('getAttribute')->with('facility')->andReturn($facility);
    $log->shouldReceive('getAttribute')->with('priority')->andReturn($priority);
    $log->shouldReceive('getAttribute')->with('message')->andReturn('PRI test');
    $log->shouldReceive('getAttribute')->with('created_at')->andReturn($carbon);

    $result = $this->service->formatSyslogMessage($log);

    expect($result)->toMatch("/^<{$expectedPri}>/");
})->with([
    [0, 'kern', 'emerg'],
    [5, 'kern', 'notice'],
    [23, 'mail', 'debug'],
    [162, 'local4', 'crit'],
    [191, 'local7', 'debug'],
]);

it('maps all known facilities correctly', function () {
    $reflection = new ReflectionClass($this->service);
    $property = $reflection->getProperty('facilityMap');
    $property->setAccessible(true);
    $map = $property->getValue($this->service);

    expect($map)->toHaveKeys([
        'kern', 'user', 'mail', 'daemon', 'auth', 'syslog',
        'lpr', 'news', 'uucp', 'cron', 'authpriv', 'ftp',
        'local0', 'local1', 'local2', 'local3', 'local4', 'local5', 'local6', 'local7',
    ]);
});

it('maps all known priorities correctly', function () {
    $reflection = new ReflectionClass($this->service);
    $property = $reflection->getProperty('priorityMap');
    $property->setAccessible(true);
    $map = $property->getValue($this->service);

    expect($map)->toHaveKeys(['emerg', 'alert', 'crit', 'error', 'warning', 'notice', 'info', 'debug']);
});

it('send method does not throw when socket connection fails', function () {
    $carbon = Mockery::mock(\Illuminate\Support\Carbon::class);
    $carbon->shouldReceive('format')->with('Y-m-d\TH:i:s.vP')->andReturn('2026-06-11T10:00:00.000000+00:00');
    $carbon->shouldReceive('toIso8601String')->andReturn('2026-06-11T10:00:00+00:00');

    $log = Mockery::mock(Log::class)->shouldIgnoreMissing();
    $log->shouldReceive('getAttribute')->with('facility')->andReturn('user');
    $log->shouldReceive('getAttribute')->with('priority')->andReturn('info');
    $log->shouldReceive('getAttribute')->with('message')->andReturn('This will fail to send');
    $log->shouldReceive('getAttribute')->with('created_at')->andReturn($carbon);
    $log->shouldReceive('getAttribute')->with('user_id')->andReturn(1);
    $log->shouldReceive('getAttribute')->with('type')->andReturn('test');
    $log->shouldReceive('getAttribute')->with('score')->andReturn(null);
    $log->shouldReceive('getAttribute')->with('total')->andReturn(null);
    $log->shouldReceive('getAttribute')->with('questions_data')->andReturn(null);

    $this->service->send($log);

    expect(true)->toBeTrue();
});
