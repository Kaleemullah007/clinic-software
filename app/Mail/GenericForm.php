<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericForm extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $form_data;
    public $email_config;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $email_config, array $form_data)
    {
        $this->email_config = $email_config;
        $this->form_data = $form_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->email_config['from'])
                    ->subject($this->email_config['subject'])
                    ->text($this->email_config['email_template'])
                    ->with(['formData' => $this->form_data]);
    }
}
