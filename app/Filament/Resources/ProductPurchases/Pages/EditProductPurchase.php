<?php

namespace App\Filament\Resources\ProductPurchases\Pages;

use App\Filament\Resources\ProductPurchases\ProductPurchaseResource;
use App\Models\Expense;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditProductPurchase extends EditRecord
{
    protected static string $resource = ProductPurchaseResource::class;

    public int $oldQuantity = 0;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $purchase = $this->record;

        $expense = Expense::where('clinic_id', $purchase->clinic_id)
            ->where('supplier_id', $purchase->supplier_id)
            ->where('description', 'like', "Nf Estoque:%")
            ->latest()
            ->first();

        if ($expense) {
            $data['payment_plan'] = $expense->payment_plan;
            $data['installments'] = $expense->installments;
            $data['due_date'] = $expense->due_date;
            $data['status'] = $expense->status;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->oldQuantity = $this->record->quantity;
        return $data;
    }

    protected function afterSave(): void
    {
        $purchase = $this->record;
        $formData = $this->form->getRawState();

        $diferenca = $purchase->quantity - $this->oldQuantity;
        $product = $purchase->product;
        
        if ($product) {
            $product->update([
                'current_stock' => $product->current_stock + $diferenca,
                'unit_cost' => $formData['unit_cost'] ?? $product->unit_cost,
                'batch' => $formData['batch'] ?? $product->batch,
                'expiration_date' => $formData['expiration_date'] ?? $product->expiration_date,
            ]);
        }

        $expense = Expense::where('clinic_id', $purchase->clinic_id)
            ->where('supplier_id', $purchase->supplier_id)
            ->where('description', 'like', "Nf Estoque:%")
            ->latest()
            ->first();

        if ($expense) {
            $parcelas = intval($formData['installments'] ?? 1);
            $valorParcela = $parcelas > 0 ? round($purchase->total_cost / $parcelas, 2) : $purchase->total_cost;

            $expense->update([
                'description' => "Nf Estoque: {$purchase->quantity}x {$purchase->product->name}",
                'total_amount' => $purchase->total_cost,
                'installments' => $parcelas,
                'installment_amount' => $valorParcela,
                'due_date' => $formData['due_date'] ?? $expense->due_date,
                'payment_plan' => $formData['payment_plan'] ?? $expense->payment_plan,
                'status' => $formData['status'] ?? $expense->status,
                'payment_date' => ($formData['status'] ?? $expense->status) === 'paid' ? now() : null,
            ]);
        }

        Notification::make()
            ->success()
            ->title('Compra Atualizada!')
            ->body('Estoque e Financeiro sincronizados com sucesso.')
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}