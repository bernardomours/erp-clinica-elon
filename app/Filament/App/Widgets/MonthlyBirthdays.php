<?php

namespace App\Filament\App\Widgets;

use App\Models\Customer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class MonthlyBirthdays extends TableWidget
{
    protected static ?string $heading = '🎂 Aniversariantes do Mês';
    
    protected static ?string $pollingInterval = null;
    protected static bool $isDiscovered = false;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        $tenantId = filament()->getTenant()?->id;

        $query = Customer::query()
            ->when($tenantId, fn (Builder $q) => $q->where('clinic_id', $tenantId))
            ->whereMonth('birth_date', Carbon::now()->month);

        if ($query->getConnection()->getDriverName() === 'sqlite') {
            $query->orderByRaw("strftime('%d', birth_date) ASC"); 
        } else {
            $query->orderByRaw("DAY(birth_date) ASC");
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

