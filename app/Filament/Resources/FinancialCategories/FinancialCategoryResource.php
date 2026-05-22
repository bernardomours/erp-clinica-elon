<?php

namespace App\Filament\Resources\FinancialCategories;

use App\Filament\Resources\FinancialCategories\Pages\CreateFinancialCategory;
use App\Filament\Resources\FinancialCategories\Pages\EditFinancialCategory;
use App\Filament\Resources\FinancialCategories\Pages\ListFinancialCategories;
use App\Filament\Resources\FinancialCategories\Schemas\FinancialCategoryForm;
use App\Filament\Resources\FinancialCategories\Tables\FinancialCategoriesTable;
use App\Models\FinancialCategory;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinancialCategoryResource extends Resource
{
    protected static ?string $model = FinancialCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static ?string $modelLabel = 'Classificação Financeira';
    protected static ?string $pluralModelLabel = 'Classificação Financeira';
    protected static string|UnitEnum|null $navigationGroup = 'Financeiro';
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FinancialCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinancialCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        if (filament()->getCurrentPanel()->getId() === 'admin') return true;
        return (bool) filament()->getTenant()?->has_financial;
    }

    public static function canViewAny(): bool
    {
        if (filament()->getCurrentPanel()->getId() === 'admin') return true;
        return (bool) filament()->getTenant()?->has_financial;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinancialCategories::route('/'),
            'create' => CreateFinancialCategory::route('/create'),
            'edit' => EditFinancialCategory::route('/{record}/edit'),
        ];
    }
}
