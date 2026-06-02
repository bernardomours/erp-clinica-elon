<?php

use Livewire\Component;
use App\Models\Customer;
use App\Models\Procedure;

new class extends Component
{
    public Customer $customer;
    public array $odontogramState = [];
    
    public ?int $selectedTooth = null;
    public ?int $selectedProcedure = null;
    public bool $isModalOpen = false;

    public function mount(Customer $customer)
    {
        $this->customer = $customer;
        $this->odontogramState = $customer->odontogram_state ?? []; 
    }

    public function getIsChildProperty(): bool
    {
        return $this->customer->birth_date ? $this->customer->birth_date->age <= 12 : false;
    }

    public function getProceduresProperty()
    {
        return Procedure::where('clinic_id', $this->customer->clinic_id)
            ->orderBy('name')
            ->get();
    }

    public function openToothModal($toothNumber)
    {
        $this->selectedTooth = $toothNumber;
        $this->selectedProcedure = $this->odontogramState[$toothNumber]['procedure_id'] ?? null; 
        $this->resetErrorBag();         
        $this->dispatch('open-modal', id: 'tooth-modal');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->selectedTooth = null;
        $this->selectedProcedure = null;
        $this->dispatch('close-modal', id: 'tooth-modal');
    }

    public function saveToothStatus($status)
    {
        if ($status !== 'intacto' && !$this->selectedProcedure) {
            $this->addError('selectedProcedure', 'Selecione um procedimento para registrar o tratamento.');
            return;
        }

        $this->odontogramState[$this->selectedTooth] = [
            'status' => $status,
            'procedure_id' => $status !== 'intacto' ? $this->selectedProcedure : null
        ];
        
        $this->customer->update(['odontogram_state' => $this->odontogramState]);

        if ($status !== 'intacto') {
            $procedure = Procedure::find($this->selectedProcedure);
            $statusText = $status === 'tratado' ? 'Tratamento realizado' : 'Avaliação: Necessita de tratamento';
            
            $this->customer->patientSchedules()->create([
                'clinic_id' => $this->customer->clinic_id,
                'tooth_number' => (string) $this->selectedTooth,
                'procedure_id' => $this->selectedProcedure,
                'schedule_date' => now(),
                'status' => 'Completed',
                'clinical_evolution' => "{$statusText} : {$procedure->name} no dente {$this->selectedTooth}.",
            ]);
        }

        $this->closeModal();
    }

    public function getToothStatusClass($toothNumber): string
    {
        $status = $this->odontogramState[$toothNumber]['status'] ?? 'intacto';
        return match ($status) {
            'tratado' => 'status-tratado',
            'a_tratar' => 'status-atratar',
            default => 'status-intacto',
        };
    }
};
?>

@php
    function getToothType($tooth) {
        $lastDigit = $tooth % 10;
        if (in_array($lastDigit, [1, 2])) return 'incisor';
        if ($lastDigit == 3) return 'canine';
        if (in_array($lastDigit, [4, 5])) return $tooth > 50 ? 'molar' : 'premolar';
        return 'molar'; 
    }
@endphp

<div>
    {{-- A "FÁBRICA" DE DENTES --}}
    <svg class="hidden" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <g id="incisor"><path d="M 6,2 C 10,-1 14,-1 18,2 C 20,6 20,14 18,18 C 15,22 9,22 6,18 C 4,14 4,6 6,2 Z" stroke-width="1.5" stroke-linejoin="round" /><path d="M 8,14 C 12,18 16,14 16,14" fill="none" stroke-width="1" opacity="0.3" /></g>
            <g id="canine"><path d="M 12,1 C 16,3 18,8 18,14 C 18,18 15,22 12,23 C 9,22 6,18 6,14 C 6,8 8,3 12,1 Z" stroke-width="1.5" stroke-linejoin="round" /><path d="M 12,4 L 12,18" fill="none" stroke-width="1" opacity="0.3" /></g>
            <g id="premolar"><path d="M 5,3 C 12,1 12,1 19,3 C 21,7 21,15 19,19 C 15,22 9,22 5,19 C 3,15 3,7 5,3 Z" stroke-width="1.5" stroke-linejoin="round" /><path d="M 8,11 L 16,11" fill="none" stroke-width="1" opacity="0.3" /></g>
            <g id="molar"><path d="M 4,4 C 10,1 14,1 20,4 C 23,8 23,16 20,20 C 16,23 8,23 4,20 C 1,16 1,8 4,4 Z" stroke-width="1.5" stroke-linejoin="round" /><path d="M 12,4 L 12,20 M 4,12 L 20,12" fill="none" stroke-width="1" opacity="0.3" /></g>
        </defs>
    </svg>

    <style>
        .odonto-wrapper { padding: 2rem; background: #ffffff; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; }
        .odonto-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 2px solid #f3f4f6; padding-bottom: 1rem; }
        .odonto-title { font-size: 1.5rem; font-weight: 800; color: #111827; margin: 0; }
        .odonto-badge { padding: 0.4rem 1rem; background: #e0f2fe; color: #0369a1; border-radius: 9999px; font-size: 0.875rem; font-weight: 700; text-transform: uppercase;}
        
        .odonto-legend { display: flex; justify-content: center; gap: 2rem; margin-bottom: 2.5rem; font-size: 0.9375rem; font-weight: 600; color: #4b5563;}
        .legend-item { display: flex; align-items: center; gap: 0.6rem; }
        .legend-circle { width: 1.25rem; height: 1.25rem; border-radius: 9999px; border: 2px solid; }

        .odonto-container { display: flex; flex-direction: column; align-items: center; gap: 2.5rem; overflow-x: auto; padding: 1rem 0;}
        .odonto-grid-10 { display: grid; grid-template-columns: repeat(10, 1fr); gap: 1rem; }
        .odonto-grid-16 { display: grid; grid-template-columns: repeat(16, 1fr); gap: 0.5rem; }
        
        .tooth-container { display: flex; flex-direction: column; align-items: center; cursor: pointer; padding: 0.25rem; border-radius: 0.5rem; }
        .container-adult { width: 3.5rem; height: 5rem; }
        .container-child { width: 4.5rem; height: 6rem; }
        
        .tooth-number { font-size: 0.875rem; font-weight: 800; color: #6b7280; margin-top: 0.5rem; }
        
        .tooth-icon { width: 100%; height: 100%; transition: all 0.3s ease; filter: drop-shadow(0 2px 3px rgba(0,0,0,0.1)); }
        .tooth-container:hover .tooth-icon { transform: scale(1.1); }
        
        .status-intacto .tooth-icon { fill: #f8fafc; stroke: #94a3b8; }
        .status-atratar .tooth-icon { fill: #fee2e2; stroke: #ef4444; }
        .status-tratado .tooth-icon { fill: #dcfce3; stroke: #22c55e; }
        
        .status-tratado .tooth-number { color: #16a34a; }
        .status-atratar .tooth-number { color: #dc2626; }
    </style>

    <div class="odonto-wrapper">
        <div class="odonto-header">
            <h2 class="odonto-title">Odontograma Clínico</h2>
            <span class="odonto-badge">{{ $this->isChild ? 'Odontopediatria' : 'Permanentes' }}</span>
        </div>

        <div class="odonto-legend">
            <div class="legend-item"><div class="legend-circle" style="border-color: #94a3b8; background: #f8fafc;"></div> Intacto</div>
            <div class="legend-item"><div class="legend-circle" style="border-color: #ef4444; background: #fee2e2;"></div> A Tratar</div>
            <div class="legend-item"><div class="legend-circle" style="border-color: #22c55e; background: #dcfce3;"></div> Tratado</div>
        </div>

        <div class="odonto-container">
            @if($this->isChild)
                <div class="odonto-grid-10">
                    @foreach([55,54,53,52,51, 61,62,63,64,65] as $tooth)
                        <div wire:click="openToothModal({{ $tooth }})" class="tooth-container container-child {{ $this->getToothStatusClass($tooth) }}">
                            <svg class="tooth-icon" viewBox="0 0 24 24"><use href="#{{ getToothType($tooth) }}" /></svg>
                            <span class="tooth-number">{{ $tooth }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="odonto-grid-10">
                    @foreach([85,84,83,82,81, 71,72,73,74,75] as $tooth)
                        <div wire:click="openToothModal({{ $tooth }})" class="tooth-container container-child {{ $this->getToothStatusClass($tooth) }}">
                            <svg class="tooth-icon" viewBox="0 0 24 24"><use href="#{{ getToothType($tooth) }}" /></svg>
                            <span class="tooth-number">{{ $tooth }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="odonto-grid-16">
                    @foreach([18,17,16,15,14,13,12,11, 21,22,23,24,25,26,27,28] as $tooth)
                        <div wire:click="openToothModal({{ $tooth }})" class="tooth-container container-adult {{ $this->getToothStatusClass($tooth) }}">
                            <svg class="tooth-icon" viewBox="0 0 24 24"><use href="#{{ getToothType($tooth) }}" /></svg>
                            <span class="tooth-number">{{ $tooth }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="odonto-grid-16">
                    @foreach([48,47,46,45,44,43,42,41, 31,32,33,34,35,36,37,38] as $tooth)
                        <div wire:click="openToothModal({{ $tooth }})" class="tooth-container container-adult {{ $this->getToothStatusClass($tooth) }}">
                            <svg class="tooth-icon" viewBox="0 0 24 24"><use href="#{{ getToothType($tooth) }}" /></svg>
                            <span class="tooth-number">{{ $tooth }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <x-filament::modal id="tooth-modal" width="md">
        <x-slot name="heading">
            Dente Selecionado: <span style="color: #0ea5e9;">{{ $selectedTooth }}</span>
        </x-slot>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                Selecione o Procedimento:
            </label>
            <select wire:model="selectedProcedure" style="width: 100%; border-radius: 0.5rem; border: 1px solid #d1d5db; padding: 0.5rem; font-size: 0.875rem; color: #1f2937; background-color: #ffffff; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                <option value="">-- Escolha um procedimento --</option>
                @foreach($this->procedures as $procedure)
                    <option value="{{ $procedure->id }}">{{ $procedure->name }}</option>
                @endforeach
            </select>
            
            {{-- Mensagem de erro caso tente salvar sem procedimento --}}
            @error('selectedProcedure')
                <span style="color: #ef4444; font-size: 0.75rem; font-weight: 600; display: block; margin-top: 0.5rem;">{{ $message }}</span>
            @enderror
        </div>

        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1.5rem; border-top: 1px solid #f3f4f6; padding-top: 1rem;">
            Agora, selecione o estado clínico para salvar:
        </p>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <x-filament::button color="gray" wire:click="saveToothStatus('intacto')" style="justify-content: center; width: 100%;">
                Dente Intacto / Saudável (Limpa tratamento)
            </x-filament::button>

            <x-filament::button color="danger" wire:click="saveToothStatus('a_tratar')" style="justify-content: center; width: 100%;">
                Marcar como "A Tratar"
            </x-filament::button>

            <x-filament::button color="success" wire:click="saveToothStatus('tratado')" style="justify-content: center; width: 100%;">
                Marcar como "Tratado"
            </x-filament::button>
        </div>
    </x-filament::modal>
</div>