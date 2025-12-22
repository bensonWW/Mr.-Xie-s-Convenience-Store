<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Services\MemberLevelService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpgradeMemberLevel
{
    protected $memberService;

    /**
     * Create the event listener.
     */
    public function __construct(MemberLevelService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {
        $user = $event->order->user;
        if ($user) {
            $this->memberService->checkAndUpgrade($user);
        }
    }
}
