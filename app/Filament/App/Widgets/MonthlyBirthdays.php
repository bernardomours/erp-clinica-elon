<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class MonthlyBirthdays extends TableWidget
{
    protected static ?string $heading = '🎂 Aniversariantes do Mês';
    
    // Evita consultas excessivas, mas mantém o dado atualizado
    protected static ?string $pollingInterval = null;

    // Ocupa metade da tela (ou 1 coluna em grids pequenos)
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        $tenantId = filament()->getTenant()?->id;

        $query = Customer::query()
            ->when($tenantId, fn (Builder $q) => $q->where('clinic_id', $tenantId))
            ->whereMonth('birth_date', Carbon::now()->month);

        // 👇 A mágica multiplataforma: Deteta o banco de dados e aplica a linguagem correta
        if ($query->getConnection()->getDriverName() === 'sqlite') {
            $query->orderByRaw("strftime('%d', birth_date) ASC"); // Para rodar no seu PC (Herd)
        } else {
            $query->orderByRaw("DAY(birth_date) ASC"); // Para rodar no Servidor (MySQL/Postgres)
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('name')
                    ->label('Paciente')
                    ->weight('bold')
                    ->icon('heroicon-m-cake')
                    ->iconColor('primary')
                    ->searchable(),

                TextColumn::make('birth_date')
                    ->label('Dia')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d'))
                    ->alignCenter()
                    ->color('info')
                    ->weight('bold'),
            ])
            ->emptyStateHeading('Ninguém soprando velinhas este mês')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->paginated(true)
            ->defaultPaginationPageOption(5);
    }
}

