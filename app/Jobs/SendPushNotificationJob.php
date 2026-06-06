<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $tokens;
    protected $title;
    protected $body;

    public function __construct($tokens, $title, $body)
    {
        $this->tokens = $tokens;
        $this->title = $title;
        $this->body = $body;
    }

    public function handle()
    {
        $messaging = app('firebase.messaging');

        $successcount = 0;
        $failcount = 0;

        foreach (array_chunk($this->tokens, 5) as $tokens) {
            try {
                $message = CloudMessage::new()->withNotification([
                    'title' => $this->title,
                    'body' => $this->body
                ]);

                // ⚠️ TEMP FIX for your /batch error → use single send
                foreach ($tokens as $token) {
                    try {
                        $messaging->send(
                            CloudMessage::withTarget('token', $token)
                                ->withNotification([
                                    'title' => $this->title,
                                    'body' => $this->body
                                ])
                        );
                        $successcount++;
                    } catch (\Throwable $e) {
                        $failcount++;
                        \Log::error('Token failed: '.$token.' '.$e->getMessage());
                    }
                }

            } catch (\Throwable $e) {
                $failcount += count($tokens);
                \Log::error('Push error: '.$e->getMessage());
            }
        }

        \Log::info("Push Done: Success=$successcount Failed=$failcount");
    }
}
