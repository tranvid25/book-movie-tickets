<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendNewPassword extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $password;
    public function __construct($password)
    {
        $this->password=$password;
    }

    public function build()
   {
    return $this->subject('TVHG - Mật khẩu mới')
                ->markdown('mail.sendPassword')
                ->with(['pass' => $this->password]);
    }

}
