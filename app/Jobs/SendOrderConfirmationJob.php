<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order
    ) {
        $this->afterCommit();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Load relationships if not already loaded
        $this->order->loadMissing(['customer', 'orderItems.product']);

        Log::info("Simulated sending order confirmation email for Order #{$this->order->id}.", [
            'order_id' => $this->order->id,
            'customer_name' => $this->order->customer?->name,
            'customer_email' => $this->order->customer?->email,
            'grand_total' => $this->order->grand_total,
            'items_count' => $this->order->orderItems->count(),
        ]);
    }
}
