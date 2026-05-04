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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                
                TextColumn::make('procedure.name')
                    ->label('Procedimento')
                    ->searchable()
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
                EditAction::make(),

                Action::make('faturar')
                    ->label('Faturar')
                    ->icon('heroicon-m-currency-dollar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Faturar Consulta')
                    ->modalDescription('Confirme a forma de pagamento e parcelamento deste procedimento.')
                    ->visible(fn ($record) => $record->procedure_id !== null)
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
                                ->title('Erro ao Faturar')
                                ->body('O procedimento vinculado a esta consulta não foi encontrado ou foi apagado.')
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
                        ]);

                        $record->update(['status' => 'Completed']);
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}