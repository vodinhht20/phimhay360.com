<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyContactEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $companyNameInp;
    public $emailInp;
    public $phoneInp;
    public $requestInp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        $companyNameInp,
        $emailInp,
        $phoneInp,
        $requestInp
    ) {
        $this->companyNameInp = $companyNameInp;
        $this->emailInp = $emailInp;
        $this->phoneInp = $phoneInp;
        $this->requestInp = $requestInp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email', [
            'companyNameInp' => $this->companyNameInp,
            'emailInp' => $this->emailInp,
            'phoneInp' => $this->phoneInp,
            'requestInp' => $this->requestInp
        ]);
    }
}
