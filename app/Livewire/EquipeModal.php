<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Livewire\Attributes\On;
use Livewire\Component;

class EquipeModal extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    // Fica "escutando" esse comando para abrir a janela
    #[On('abrirModalEquipe')]
    public function triggerModal(): void
    {
        $this->mountAction('verEquipe');
    }

    public function verEquipeAction(): Action
    {
        return Action::make('verEquipe')
            ->modalHeading('Equipe do Consultório')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Fechar')
            ->slideOver()
            ->infolist([
                RepeatableEntry::make('team')
                    ->label('Equipe')
                    ->getStateUsing(function () {
                        return User::whereHas('clinics', function ($query) {
                            $query->where('clinics.id', filament()->getTenant()->id);
                        })->get();
                    })
                    ->schema([
                        TextEntry::make('name')->label('Colega'),
                        TextEntry::make('role')->label('Cargo')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'clinic_admin' => 'Líder',
                                'operator' => 'Auxiliar',
                                'admin' => 'Admin',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'clinic_admin' => 'primary',
                                'admin' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2)
            ]);
    }

    // Renderiza os modais invisíveis do Filament na página
    public function render()
    {
        return <<<'HTML'
            <div>
                <x-filament-actions::modals />
                
                <script>
                    document.addEventListener('click', function (e) {
                        let link = e.target.closest('a');
                        
                        if (link && link.getAttribute('href') === '#ver-equipe') {
                            e.preventDefault(); 
                            e.stopPropagation(); 
                            
                            // O truque de mestre: Esperamos 100ms para o menu do Filament 
                            // terminar de fechar antes de invocarmos a janela lateral.
                            setTimeout(() => {
                                Livewire.dispatch('abrirModalEquipe'); 
                            }, 100);
                        }
                    });
                </script>
            </div>
        HTML;
    }
}