<?php

namespace App\Console\Commands;

use App\Models\OutboxMessage;
use App\Services\Outbox\OutboxDispatcher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProcessOutbox extends Command
{
    protected $signature = 'outbox:process';
    protected $description = 'Process pending outbox messages';

    public function handle(OutboxDispatcher $dispatcher): int
    {
        $ids = OutboxMessage::where('status', 'pending')
            ->where('available_at', '<=', now())
            ->orderBy('id')
            ->limit(50)
            ->pluck('id');

        foreach ($ids as $id) {
            $message = DB::transaction(function () use ($id) {
                $m = OutboxMessage::where('id', $id)
                    ->where('status', 'pending')
                    ->lockForUpdate()
                    ->first();

                $m?->increment('attempts');

                return $m;
            });

            if (! $message) {
                continue;
            }

            try {
                $dispatcher->handle($message);

                $message->update([
                    'status'       => 'processed',
                    'processed_at' => now(),
                ]);
            } catch (Throwable $e) {
                $message->update([
                    'status'       => $message->attempts >= 5 ? 'failed' : 'pending',
                    'last_error'   => $e->getMessage(),
                    'available_at' => now()->addMinutes(2 ** $message->attempts),
                ]);
            }
        }

        return self::SUCCESS;
    }
}
