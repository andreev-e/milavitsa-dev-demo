<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mailing extends Mailable
{
    use Queueable, SerializesModels;

    protected $text, $template, $subj;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($text, $subj, $template)
    {
        $this->text = $text;
        $this->subj = $subj;
        $this->template = $template;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subj)
            ->markdown(
                'emails.mailing.' . $this->template,
                ['text' => $this->text, 'subj' => $this->subj]
            );
    }
}
