<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PatientSchedule;
use App\Mail\AppointmentReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'odontys:send-reminders';
    protected $description = 'Envia e-mail de lembrete para os líderes (clinic_admin) sobre consultas daqui a 7 dias';

    public function handle()
    {
        $dataAlvo = Carbon::now()->addDays(7)->format('Y-m-d');

        $agendamentos = PatientSchedule::with([
            'customer' => fn($q) => $q->withoutGlobalScopes(), 
            'procedure' => fn($q) => $q->withoutGlobalScopes(),
            'clinic'
        ]) 
            ->whereDate('schedule_date', $dataAlvo)
            ->where('status', '!=', 'canceled')
            ->get();

        if ($agendamentos->isEmpty()) {
            $this->info('Nenhum agendamento encontrado para daqui a 7 dias.');
            return Command::SUCCESS;
        }

        foreach ($agendamentos as $schedule) {
            
            if ($schedule->clinic) {
                $lideresDaClinica = $schedule->clinic->users()->where('role', 'clinic_admin')->get();

                foreach ($lideresDaClinica as $dentistaLider) {
                    if ($dentistaLider->email) {
                        Mail::to($dentistaLider->email)->send(new AppointmentReminderMail($schedule));
                    }
                }
            }
        }

        $this->info($agendamentos->count() . ' agendamentos processados de forma segura!');
        return Command::SUCCESS;
    }
}