<?php
namespace Tests\Unit;
use Tests\TestCase;
use App\Services\ScheduleService;

class ScheduleTest extends TestCase
{
    public function test_calendar_boundary(): void
    {
        $s = new ScheduleService();
        $r = $s->status('calendar', '2026-11-01', null, 30, 0, '2026-11-30', null);
        $this->assertSame('Not due', $r['status']);
        $r = $s->status('calendar', '2026-11-01', null, 30, 0, '2026-12-01', null);
        $this->assertSame('Due', $r['status']);
        $r = $s->status('calendar', '2026-11-01', null, 30, 0, '2026-12-02', null);
        $this->assertSame('Overdue', $r['status']);
    }
    public function test_hours_boundary(): void
    {
        $s = new ScheduleService();
        foreach ([1449=>'Not due',1450=>'Due',1451=>'Overdue'] as $h=>$exp) {
            $r = $s->status('hours', null, 1200, 0, 250, null, $h);
            $this->assertSame($exp, $r['status'], "hours=$h");
        }
    }
    public function test_dual_or_date(): void
    {
        $s = new ScheduleService();
        $r = $s->status('dual', '2026-11-01', 1200, 30, 250, '2026-12-01', 1400);
        $this->assertSame('Due', $r['status']);
        $this->assertSame('calendar', $r['trigger']);
    }
    public function test_dual_or_hours(): void
    {
        $s = new ScheduleService();
        $r = $s->status('dual', '2026-11-01', 1200, 30, 250, '2026-11-20', 1450);
        $this->assertSame('Due', $r['status']);
        $this->assertSame('hours', $r['trigger']);
    }
}
