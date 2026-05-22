<?php

namespace App\Filament\Resources\ProductPurchases\Pages;

use App\Filament\Resources\ProductPurchases\ProductPurchaseResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProductPurchase extends EditRecord
{
    protected static string $resource = ProductPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
    
    protected function getRedirectUrl():string
    {
        return $this->getResource()::getUrl('index');
    }
}
