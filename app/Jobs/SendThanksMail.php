<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\mail;
use App\Mail\TestMail;
use App\Mail\ThanksMail;

class SendThanksMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $products;
    public $user;

    public function __construct($products,$user)
    {
        //
        $this->products=$products;
        $this->user=$user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Mail::to($this->user)//勝手にメールを探してくれる
            ->send(new ThanksMail($this->products,$this->user));
    }
}
