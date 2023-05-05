<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\mail;
use App\Mail\OrderedMail;

class SendOrderedMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $product;
    public $user;

    public function __construct($product,$user)
    {
        //
        $this->product=$product;
        $this->user=$user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Mail::to($this->product['email'])
        ->send(new OrderedMail($this->product,$this->user));
    }
}
