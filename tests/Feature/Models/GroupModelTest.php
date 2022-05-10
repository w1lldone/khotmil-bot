<?php

namespace Tests\Feature\Models;

use App\Models\Group;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GroupModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_using_factory()
    {
        $group = Group::factory()->create();

        $this->assertDatabaseHas('groups', [
            'id' => $group->id
        ]);
    }

    /** @test */
    public function it_has_many_members()
    {
        /** @var Group */
        $group = Group::factory()->has(\App\Models\Member::factory())->create();

        $this->assertInstanceOf(\App\Models\Member::class, $group->members->first());
    }

    /** @test */
    public function it_can_get_the_last_member_order()
    {
        /** @var Group */
        $group = Group::factory()->create();
        \App\Models\Member::factory()->create([
            'group_id' => $group->id,
            'order' => 1
        ]);
        \App\Models\Member::factory()->create([
            'group_id' => $group->id,
            'order' => 5
        ]);

        $this->assertEquals(5, $group->getLastMemberOrder());
    }

    /** @test */
    public function it_can_generate_schedules()
    {
        /** @var Group */
        $group = Group::factory()->create();

        $this->assertEquals(30, $group->schedules()->count());
    }

    /** @test */
    public function it_can_assign_schedules()
    {
        Queue::fake();

        /** @var Group */
        $group = Group::factory()->create();
        \App\Models\Member::factory()->create([
            'group_id' => $group->id,
            'order' => 1
        ]);
        \App\Models\Member::factory()->create([
            'group_id' => $group->id,
            'order' => 2
        ]);

        $group->assignMemberSchedule(now());

        $this->assertEquals(2, $group->schedules()->whereNotNull('member_id')->count());
        Queue::assertPushed(\App\Jobs\SendDeadlineReminder::class);
    }

    /** @test */
    public function it_can_reorder_members()
    {
        /** @var Group */
        $group = Group::factory()->create();
        $memberOne = Member::factory()->create(['group_id' => $group->id, 'order' => 1]);
        $memberTwo = Member::factory()->create(['group_id' => $group->id, 'order' => 2]);
        $memberThree = Member::factory()->create(['group_id' => $group->id, 'order' => 3]);

        $group->resetMemberOrder();

        $this->assertEquals(2, $memberOne->fresh()->order);
        $this->assertEquals(3, $memberTwo->fresh()->order);
        $this->assertEquals(1, $memberThree->fresh()->order);
    }
}
