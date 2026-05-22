<?php

namespace App\Filament\Resources\PatientSchedules\Tables;

use App\Models\Revenue;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Actions\EditAction;
use App\Models\ProductConsumption;
use App\Models\Procedure;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\PatientSchedule;
use Filament\Forms\Components\DateTimePicker;

class PatientSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clinic.name')
                    ->label('Clínica')
                    ->badge()
                    ->color('info')
                    ->visible(fn () => filament()->getCurrentPanel()->getId() === 'admin'),
                
                TextColumn::make('customer.name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('schedule_date')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                TextColumn::make('procedure_id')
                    ->label('Procedimento')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'Não definido';
                        
                        $procedimento = Procedure::find($state);
                        return $procedimento ? $procedimento->name : 'Procedimento apagado';
                    })
                    ->badge()
                    ->color('info')
                    ->sortable(),
                
                TextColumn::make('status')
                    ->label('Situação')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Scheduled' => 'Agendada',
                        'Confirmed' => 'Confirmada',
                        'Completed' => 'Realizada',
                        'Cancelled' => 'Cancelada',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Scheduled' => 'warning',
                        'Confirmed' => 'info',
                        'Completed' => 'success',
                        'Cancelled' => 'danger',
                        default => 'gray',
                    }),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([

                Action::make('agendar_retorno')
                    ->label('Retorno')
                    ->icon('heroicon-o-calendar-days')
                    ->color('success')
                    ->visible(fn (PatientSchedule $record) => $record->status === 'Completed' || $record->status === 'Realizada') 
                    ->form([
                        DateTimePicker::make('new_date')
                            ->label('Data do Retorno')
                            ->required()
                            ->default(now()->addMonths(6)), 
                    ])
                    ->modalHeading('Agendar Retorno do Paciente')
                    ->modalDescription('Escolha a data e horário para a nova consulta de retorno. Os dados do paciente e procedimento serão copiados.')
                    ->modalSubmitActionLabel('Agendar')
                    ->action(function (PatientSchedule $record, array $data) {
                        $novoRetorno = $record->replicate(); 
                        
                        $novoRetorno->schedule_date = $data['new_date'];
                        
                        $novoRetorno->status = 'Scheduled'; 
                        
                        $novoRetorno->save();

                        Notification::make()
                            ->title('Retorno agendado com sucesso!')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),

                Action::make('faturar')
                    ->label('Faturar e Finalizar')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Finalizar Consulta')
                    ->modalDescription('Isto irá gerar as cobranças no financeiro e dar baixa automática nos materiais utilizados.')
                    ->visible(fn ($record) => $record->procedure_id !== null && !in_array($record->status, ['Completed', 'Cancelled']))
                    ->form([
                        Select::make('payment_plan')
                            ->label('Plano de Pagamento')
                            ->options([
                                'Boleto' => 'Boleto Bancário',
                                'Cartão de Crédito' => 'Cartão de Crédito',
                                'Carnê da Clínica' => 'Carnê da Clínica',
                                'PIX' => 'PIX (À vista)',
                                'Dinheiro' => 'Dinheiro (À vista)',
                            ])
                            ->required(),
                            
                        TextInput::make('installments')
                            ->label('Quantidade de Parcelas')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->step(1)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $procedimento = $record->procedure()->first();

                        if (!$procedimento) {
                            Notification::make()
                                ->title('Erro ao Finalizar')
                                ->body('O procedimento vinculado a esta consulta não foi encontrado.')
                                ->danger()
                                ->send();
                            return; 
                        }

                        $valorTotal = floatval($procedimento->base_price);
                        $parcelas = intval($data['installments']);
                        $valorParcela = $parcelas > 0 ? round($valorTotal / $parcelas, 2) : 0;

                        Revenue::create([
                            'clinic_id' => $record->clinic_id,
                            'customer_id' => $record->customer_id,
                            'total_amount' => $valorTotal,
                            'installments' => $parcelas,
                            'installment_amount' => $valorParcela,
                            'payment_plan' => $data['payment_plan'],
                            'status' => 'paid',
                            'description' => "Procedimento: {$procedimento->name}",
                        ]);

                        $materiaisConsumidos = $procedimento->procedureProducts;

                        $produtosZerados = []; 

                        foreach ($materiaisConsumidos as $itemPivo) {
                            $produto = $itemPivo->product;
                            
                            if ($produto) {
                                ProductConsumption::create([
                                    'clinic_id' => $record->clinic_id,
                                    'product_id' => $produto->id,
                                    'user_id' => auth()->id(),
                                    'patient_schedule_id' => $record->id,
                                    'quantity' => $itemPivo->quantity,
                                    'consumption_date' => now(),
                                    'notes' => "Baixa automática via procedimento: {$procedimento->name}",
                                ]);

                                $novoEstoque = $produto->current_stock - $itemPivo->quantity;
                                
                                $produto->decrement('current_stock', $itemPivo->quantity);
                                
                                if ($novoEstoque <= 0) {
                                    $produtosZerados[] = $produto->name;
                                }
                            }
                        }

                        $record->update(['status' => 'Completed']);

                        Notification::make()
                            ->title('Sucesso Absoluto!')
                            ->body('Consulta faturada e estoque atualizado.')
                            ->success()
                            ->send();
                            
                        if (!empty($produtosZerados)) {
                            $nomesDosProdutos = implode(', ', $produtosZerados);
                            
                            Notification::make()
                                ->title('Atenção: Estoque Zerado/Negativo!')
                                ->body("O(s) produto(s) a seguir esgotaram após este atendimento: **{$nomesDosProdutos}**. Por favor, solicite a compra!")
                                ->warning()
                                ->persistent()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}