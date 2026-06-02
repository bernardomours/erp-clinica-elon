<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

class OdontogramRelationManager extends RelationManager
{
    protected static string $relationship = 'patientSchedules'; // Usa a relação que já existe

    protected static ?string $title = 'Odontograma'; // Nome que aparecerá na Aba

    //protected static ?string $icon = 'heroicon-o-sparkles';

    // A MÁGICA: Em vez de uma tabela, renderizamos o seu componente Livewire
    public function render(): View
    {
        return view('filament.resources.customer-resource.pages.odontogram-tab');
    }

    public function table(Table $table): Table
    {
        return $table; // Deixamos vazio pois não usaremos a tabela padrão
    }
}