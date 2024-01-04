<?php

namespace App\Livewire;

use App\Models\Survey;
use Livewire\Component;

class SurveyForm extends Component
{
    public $survey;
    public $form;
    public $rules;
    public $name;
    public function mount(Survey $survey) {
        $this->survey = $survey;
    }

    public function render()
    {
        // dd($this->survey);
        return view('livewire.survey-form');
    }
}
