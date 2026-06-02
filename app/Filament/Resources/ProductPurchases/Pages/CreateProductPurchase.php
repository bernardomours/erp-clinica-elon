<?php

namespace App\Filament\Resources\ProductPurchases\Pages;

use App\Filament\Resources\ProductPurchases\ProductPurchaseResource;
use App\Models\Expense;
use App\Models\FinancialCategory;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateProductPurchase extends CreateRecord
{
    protected static string $resource = ProductPurchaseResource::class;

    // Esta função roda automaticamente assim que a compra é guardada no banco de dados
    protected function afterCreate(): void
    {
        $purchase = $this->record;
        $formData = $this->form->getRawState();

        // 1. MAGIA DE ESTOQUE: Soma a quantidade comprada ao estoque atual do produto
        $product = Product::find($purchase->product_id);
        if ($product) {
            $product->update([
                'current_stock' => $product->current_stock + $purchase->quantity,
                'unit_cost' => $formData['unit_cost'] ?? $product->unit_cost,
                'batch' => $formData['batch'] ?? $product->batch,
                'expiration_date' => $formData['expiration_date'] ?? $product->expiration_date,
            ]);
        }

        // 2. MAGIA FINANCEIRA: Cria a Despesa
        $category = FinancialCategory::firstOrCreate(
            [
                'clinic_id' => $purchase->clinic_id,
                'name' => 'Compra de Materiais (Estoque)',
                'type' => 'expense',
            ]
        );

        // AQUI ESTÁ A MUDANÇA: Agora lemos o $purchase->total_cost
        $parcelas = intval($formData['installments'] ?? 1);
        $valorParcela = $parcelas > 0 ? round($purchase->total_cost / $parcelas, 2) : $purchase->total_cost;

        // Gera a conta a pagar
        Expense::create([
            'clinic_id' => $purchase->clinic_id,
            'supplier_id' => $purchase->supplier_id,
            'financial_category_id' => $category->id,
            'description' => "Nf Estoque: {$purchase->quantity}x {$product->name}",
            'due_date' => $formData['due_date'] ?? now(),
            
            // Na tabela Expense usamos total_amount, mas puxamos o valor do total_cost da compra!
            'total_amount' => $purchase->total_cost, 
            
            'installments' => $parcelas,
            'installment_amount' => $valorParcela,
            'payment_plan' => $formData['payment_plan'] ?? 'PIX',
            'status' => $formData['status'] ?? 'paid',
        ]);

        // Mostra um aviso bonito de sucesso na tela
        Notification::make()
            ->success()
            ->title('Operação Completa!')
            ->body('Estoque atualizado e conta gerada no financeiro com sucesso.')
            ->send();
    }

    // Força o preenchimento do ID da clínica automaticamente
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['clinic_id'] = filament()->getTenant()->id ?? 1;
        $data['user_id'] = auth()->id();
        
        return $data;
    }

    protected function getRedirectUrl():string
    {
        return $this->getResource()::getUrl('index');
    }
}