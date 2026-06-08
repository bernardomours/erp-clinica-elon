<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use App\Models\PatientSchedule;
use App\Models\Procedure;
use App\Models\Customer;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $schedule;
    public $nomeProcedimento;
    public $nomePaciente;

    public function __construct(PatientSchedule $schedule)
    {
        $this->schedule = $schedule;

        $procedimento = Procedure::withoutGlobalScopes()->find($schedule->procedure_id);
        $this->nomeProcedimento = $procedimento ? $procedimento->name : 'Não informado';

        $paciente = Customer::withoutGlobalScopes()->find($schedule->customer_id);
        $this->nomePaciente = $paciente ? $paciente->name : 'Não informado';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 Lembrete: Consultas marcadas nos próximos 7 dias',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointment-reminder',
        );
    }
}