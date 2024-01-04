<?php

namespace App\Filament\Resources\SurveyResource\Pages;

use App\Filament\Resources\SurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSurvey extends CreateRecord
{
    protected static string $resource = SurveyResource::class;

    protected function handleRecordCreation(array $data): Model    {
        // dd($data);
        // todo: Create a shortcode for
        // Str::random(16, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
        return static::getModel()::create($data);
    }
}
