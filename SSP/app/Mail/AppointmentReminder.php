<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($appointment, $user)
    {
        $this->appointment = $appointment;
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Upcoming Appointment Reminder')
                    ->view('emails.appointment_remainder')
                    ->with([
                        'appointment' => $this->appointment,
                        'user' => $this->user,
                    ]);
    }
}
