<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $request;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(strtolower($this->request["contact"]) . '@thevinylshop.com', 'The Vinyl Shop - '. $this->request["contact"])
            ->cc(strtolower($this->request["contact"]) . '@thevinylshop.com', 'The Vinyl Shop - ' . $this->request["contact"])
            ->subject('The Vinyl Shop - Contact Form')
            ->markdown('email.contact');
    }


}
