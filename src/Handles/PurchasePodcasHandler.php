<?php namespace App\Handlers\Commands;

use App\User;
use App\Commands\PurchasePodcastCommand;
use Illuminate\Contracts\Mail\Mailer;

class PurchasePodcastHandler {

    /**
     * The mailer implementation.
     */
    protected $mailer;

    /**
     * Create a new instance.
     *
     * @param  Mailer  $mailer
     * @return void
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Purchase a podcast.
     *
     * @param  PurchasePodcastCommand  $command
     * @return void
     */
    public function handle(PurchasePodcastCommand $command)
    {
        //
    }

}