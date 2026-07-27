<?php

use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for audit finding #17 (issue #57): a malformed event date
 * must never fatal rendering. The old code did
 * `date_format(date_create($raw), ...)`, which throws a TypeError on an
 * unparseable value; the helpers below must degrade to null / a safe fallback.
 *
 * @covers ::academyafrica_event_timestamp
 * @covers ::academyafrica_format_event_date
 */
final class EventDateTest extends TestCase
{
    public function test_helpers_exist(): void
    {
        $this->assertTrue(function_exists('academyafrica_event_timestamp'));
        $this->assertTrue(function_exists('academyafrica_format_event_date'));
    }

    /**
     * @dataProvider invalidDates
     * @param mixed $value
     */
    public function test_invalid_dates_return_null($value): void
    {
        $this->assertNull(academyafrica_event_timestamp($value));
    }

    public function invalidDates(): array
    {
        return array(
            'empty string'    => array(''),
            'null'            => array(null),
            'garbage'         => array('not-a-date'),
            'zero date'       => array('0000-00-00'),
            'zero datetime'   => array('0000-00-00 00:00:00'),
            'non-string int'  => array(12345),
            'array'           => array(array('2026-01-01')),
        );
    }

    public function test_valid_date_returns_timestamp(): void
    {
        $ts = academyafrica_event_timestamp('2026-03-15');
        $this->assertIsInt($ts);
        $this->assertSame('2026-03-15', date('Y-m-d', $ts));
    }

    public function test_valid_date_with_time(): void
    {
        $ts = academyafrica_event_timestamp('2026-03-15', '14:30');
        $this->assertIsInt($ts);
        $this->assertSame('2026-03-15 14:30', date('Y-m-d H:i', $ts));
    }

    public function test_format_returns_fallback_for_invalid(): void
    {
        $this->assertSame('', academyafrica_format_event_date(''));
        $this->assertSame('TBA', academyafrica_format_event_date('not-a-date', 'd/m/Y', 'TBA'));
        $this->assertSame('TBA', academyafrica_format_event_date('0000-00-00', 'Y-m-d', 'TBA'));
    }

    public function test_format_formats_valid_date(): void
    {
        $this->assertSame('15/03/2026', academyafrica_format_event_date('2026-03-15', 'd/m/Y', 'TBA'));
    }
}
