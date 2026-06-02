<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Table;
use App\Models\Procedure;
use Filament\Forms\Components\FileUpload;

class PatientSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'patientSchedules';
    protected static ?string $title = 'Histórico Clínico';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->heading('Histórico Clínico e Evolução')
        ->recordTitleAttribute('id')
        ->columns([
            TextColumn::make('created_at')
                ->label('Data')
                ->date('d/m/Y')
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

            TextColumn::make('tooth_number')
                ->label('Dente(s)')
                ->placeholder('Geral'),

            TextColumn::make('clinical_evolution')
                ->label('Evolução / Anotações')
                ->limit(50)
                ->wrap(),
                
            TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Scheduled' => 'warning',
                    'Completed' => 'success',
                    'Cancelled' => 'danger',
                    default => 'gray',
                })
                // Traduz visualmente a palavra na tela
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'Scheduled' => 'Agendado',
                    'Completed' => 'Realizada',
                    'Cancelled' => 'Cancelado',
                    default => $state,
                }),
        ])
            ->filters([
                //
            ])
            ->headerActions([
                //CreateAction::make(),
                //AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                ->modalHeading('Registrar Evolução Clínica')
                ->form([
                    TextInput::make('tooth_number')
                        ->label('Dente(s) Tratado(s)')
                        ->placeholder('Ex: 15, 16 ou Arcada Superior'),
                    Textarea::make('clinical_evolution')
                        ->label('Descrição da Evolução')
                        ->rows(4),

                    FileUpload::make('clinical_photos')
                        ->label('Registo Fotográfico / Raio-X')
                        ->image()
                        ->multiple()
                        ->directory('clinical-records') // Cria uma pastinha organizada no servidor
                        ->reorderable() // Permite arrastar para mudar a ordem das fotos
                        ->openable() // Permite clicar para ampliar a foto salva
                        ->panelLayout('grid'),
                ]),
                //DissociateAction::make(),
                //DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
