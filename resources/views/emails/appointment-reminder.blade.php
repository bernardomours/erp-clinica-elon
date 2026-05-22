<x-mail::message>
# Olá, Doutor(a)!

Este é um lembrete automático do **OdontoFlow** para ajudar no planejamento da sua agenda. Existe um atendimento agendado para daqui a **7 dias**.

### 📋 Detalhes do Agendamento:
* **Paciente:** {{ $nomePaciente }}
* **Data/Hora:** {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y \à\s H:i') }}
* **Procedimento:** {{ $nomeProcedimento }}
@if($schedule->notes)
* **Observações:** {{ $schedule->notes }}
@endif

<x-mail::button :url="config('app.url')">
Acessar Painel do OdontoFlow
</x-mail::button>

Atenciosamente,<br>
Equipe {{ config('app.name') }}
</x-mail::message>