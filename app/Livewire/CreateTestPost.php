<?php

namespace App\Livewire;

use App\Models\Survey;
use Livewire\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;

class CreateTestPost extends Component implements HasForms
{
    // todo: Problem 1 when this boots form is generating first then mount is happening. So form does not have the form data.
    // Now

    use InteractsWithForms;

    public ?array $data = [];
    public ?string $uri;
    public Survey $surveyModel;
    public $form_structure;

    public function mount(Survey $survey): void
    {
        $this->surveyModel = $survey;
        ray($this->surveyModel);
        // $this->uri = $survey->uri;
        $this->form->fill();
        // $this->form_structure = Survey::where('url',$uri)->firstOrFail();

    }

    // public function form(Form $form): Form
    // {
    //     // $this->form_structure = Survey::where('url','survey-3')->firstOrFail();
    //     // dd($this->form_structure);
    //     return $form
    //         ->schema([
    //             TextInput::make('title')
    //             ->label($this->uri)
    //                 ->required(),
    //             MarkdownEditor::make('content'),
    //             static::getFormSchema()
    //             // ...
    //         ])
    //         ->statePath('data');
    // }

    public function create(): void
    {
        dd($this->form->getState());
        // dd(static::getFormSchema());
        // dd($this->form->getState());
        // dd($this->form_structure['content'][0]['data']['validation'] ?? 'Nothing found');
    }
    protected function getFormStatePath(): ?string
    {
        // All of the form data needs to be saved in the `data` property,
        // as the form is dynamic and we can't add a public property for
        // every field.
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return array_map(function (array $field) {
            $config = $field['data'];
            // dd($config);

            $fieldType = match ($field['type']) {
                'text' => TextInput::class,
                'textarea' => Textarea::class,
                'select' => Select::class,
                'checkbox' => Checkbox::class,
                'file' => FileUpload::class,
            };

            // make
            // $fieldInstance = $fieldType::make($config['name'])
            //     ->label($config['label']);

            ray($config['question']);
            $fieldInstance = new $fieldType($config['question']);
            $fieldInstance->label($config['label']?? $config['question'] );

            if (!empty($config['is_required'])) {
                $fieldInstance->required();
            }

            // Add additional validation rules conditionally
            // if ($config['name'] == 'Email') {
            //     $fieldInstance->email();
            // }
            if ($field['type'] == 'text') {
                $fieldInstance->minLength(5)->maxLength(100);
            }

            // Add other configuration options conditionally
            // if ($config['someProp']) {
            //     $fieldInstance->someProp($config['someProp']);
            // }

            return $fieldInstance;
        },$this->surveyModel->content);
    }

    public function render()
    {
        return view('livewire.create-test-post');
    }
}
