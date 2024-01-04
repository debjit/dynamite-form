<?php

namespace App\Filament\Resources\SurveyResource\Pages;

use App\Filament\Resources\SurveyResource;
use App\Models\Survey;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewSurvey extends ViewRecord
{
    protected static string $resource = SurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Action::make('Publish')
                ->icon('heroicon-o-document-check')
                ->requiresConfirmation()
                ->modalIcon('heroicon-o-document-check')
                // ->color('success')
                ->modalDescription('You cannot change the questions after they have been published. Before you publish, update and double-check your work.')
                ->modalSubmitActionLabel('Yes, Continue to Publish')
                ->action(fn () => dd(static::publishSurvey($this->data['id'])))
        ];
    }

    static function publishSurvey($id)
    {
        $data = [];

        $getSurvey = Survey::where('id',$id)->first();
        foreach ($getSurvey->content as $value) {
            $questionDetails = [
                'type' => $value['type'],
                'question' => $value['data']['question'],
                'validation' => $value['data']['validation'] ?? [],
                // 'details' => $value['data']['details'] ?? [],
                'documentation' => $value['data']['documentation'] ?? [],
            ];

            $questionDetails['validation'][] = ['type' => 'required', 'value' => $value['data']['is_required'] ? true : false];
            $questionDetails['validation'][] = ['type' => 'numeric', 'value' => empty($value['data']['is_number']) ? false : true];

            $data[] = $questionDetails;
        }

        return $data;
    }
}
