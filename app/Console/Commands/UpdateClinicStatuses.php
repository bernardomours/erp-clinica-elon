<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Clinic;

class UpdateClinicStatuses extends Command
{
    // 1. AQUI ESTÁ A CORREÇÃO: É este nome que o terminal vai reconhecer
    protected $signature = 'update:clinic-statuses';

    // 2. Uma descrição para você saber o que faz quando listar os comandos
    protected $description = 'Atualiza automaticamente o status das clínicas baseado na data de vencimento';

    public function handle()
    {
        $this->info('Iniciando varredura de assinaturas...');
        
        $today = now()->startOfDay();
        // Pega todas as clínicas, exceto as que já estão bloqueadas definitivamente
        $clinics = Clinic::where('signature_status', '!=', 'blocked')->get();
        
        $atualizadas = 0;

        foreach ($clinics as $clinic) {
            // Se não tem data de vencimento, pula para a próxima
            if (!$clinic->expires_at) continue;

            // Calcula a diferença de dias. O 'false' no final permite retornar números negativos (se estiver no futuro)
            $daysPast = $clinic->expires_at->diffInDays($today, false);

            if ($daysPast > 0) {
                // Passou da data de vencimento
                if ($daysPast >= 30) {
                    $clinic->update(['signature_status' => 'blocked']);
                    $atualizadas++;
                } elseif ($clinic->signature_status !== 'expired') {
                    // Só atualiza se já não estiver como expired para economizar query no banco
                    $clinic->update(['signature_status' => 'expired']);
                    $atualizadas++;
                }
            } else {
                // Está em dia (daysPast é negativo ou zero)
                if ($clinic->signature_status === 'expired') {
                    // Pagou a conta, a data foi pra frente, então volta pra ativo
                    $clinic->update(['signature_status' => 'active']);
                    $atualizadas++;
                }
            }
        }

        // Mensagem de sucesso verde no seu terminal
        $this->info("Varredura finalizada! {$atualizadas} clínicas tiveram o status atualizado.");
    }
}