<?php

namespace App\Filament\Resources\ProductPurchases;

use App\Filament\Resources\ProductPurchases\Pages\CreateProductPurchase;
use App\Filament\Resources\ProductPurchases\Pages\EditProductPurchase;
use App\Filament\Resources\ProductPurchases\Pages\ListProductPurchases;
use App\Filament\Resources\ProductPurchases\Schemas\ProductPurchaseForm;
use App\Filament\Resources\ProductPurchases\Tables\ProductPurchasesTable;
use App\Models\ProductPurchase;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductPurchaseResource extends Resource
{
    protected static ?string $model = ProductPurchase::class;
    protected static ?string $modelLabel = 'Compra de Material';
    protected static ?string $pluralModelLabel = 'Compras (Entrada de Estoque)';
    protected static string|UnitEnum|null $navigationGroup = 'Estoque';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProductPurchaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductPurchasesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductPurchases::route('/'),
            'create' => CreateProductPurchase::route('/create'),
            'edit' => EditProductPurchase::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        if (filament()->getCurrentPanel()->getId() === 'admin') {
            return true;
        }

        return (bool) filament()->getTenant()?->has_financial;
    }

    public static function canViewAny(): bool
    {
        if (filament()->getCurrentPanel()->getId() === 'admin') {
            return true;
        }
        
        return (bool) filament()->getTenant()?->has_financial;
    }
}
