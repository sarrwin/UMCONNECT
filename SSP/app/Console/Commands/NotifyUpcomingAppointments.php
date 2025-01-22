<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminder as AppointmentReminderMailable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AppointmentNotification;
use Illuminate\Console\Command;

class NotifyUpcomingAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-upcoming-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for upcoming appointments';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $appointments = Appointment::where('date', '=', Carbon::now()->addDay()->toDateString())
                                    ->where('status', 'accepted')
                                    ->get();

        foreach ($appointments as $appointment) {
            // Notify the student
            $student = $appointment->student;
            if ($student) {
                // Send email notification
                Mail::to($student->email)->send(new AppointmentReminderMailable($appointment, $student));
                // Send system notification
                Notification::send($student, new AppointmentNotification($appointment, 'You have an upcoming appointment tomorrow.'));
            }

            // Notify the supervisor
            $supervisor = $appointment->supervisor;
            if ($supervisor) {
                // Send email notification
                Mail::to($supervisor->email)->send(new AppointmentReminderMailable($appointment, $supervisor));
                // Send system notification
                Notification::send($supervisor, new AppointmentNotification($appointment, 'You have an upcoming appointment tomorrow with a student.'));
            }
        }

        $this->info('Notifications for upcoming appointments have been sent successfully.');
    }
}
