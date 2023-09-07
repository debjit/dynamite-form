<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SurveyResource\Pages;
use App\Filament\Resources\SurveyResource\RelationManagers;
use App\Models\Survey;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'sm' => 3,
                ])
                    ->schema([
                        Tabs::make('form-details')
                            ->tabs([
                                Tabs\Tab::make('Form Name')->schema([
                                    TextInput::make('name')
                                        ->helperText('This is what your form will be named.')
                                        ->lazy()
                                        ->afterStateUpdated(function ($set, $state) {
                                            $slug = Str::slug($state);
                                            $set('url', $slug);
                                        })
                                        ->required(),
                                ]),
                                Tabs\Tab::make('URL')->schema([
                                    TextInput::make('url')
                                        ->label('URL')
                                        ->prefix(env('APP_URL') . '/' . env('APP_FORM_SEGMENT') . '/')
                                        ->helperText('Leave this blank if you wanted to generate it automatically. If you wanted to make modifications to this url manually, here\'s where you can do it:')
                                        ->suffixIcon('heroicon-m-globe-alt')
                                        ->unique(ignorable: fn ($record) => $record),
                                ]),
                                Tabs\Tab::make('Details')->schema([
                                    Textarea::make('details')
                                        ->helperText('Add more information about your form. It will be shown below your form name.')
                                ]),
                                Tabs\Tab::make('Share')->schema([
                                    Toggle::make('is_member_only')
                                        ->label('Member Only')
                                        ->helperText('Before posting a comment, you had to sign up as a member.'),
                                    Toggle::make('is_public')
                                        ->default(true)
                                        ->helperText('When this link is live, anyone with it can send a response.'),
                                ]),
                                Tabs\Tab::make('Security')->schema([
                                    TextInput::make('pass')
                                        ->helperText('Set up a password to keep your form safe.')
                                ])->columns(2)
                            ])->columnSpan('full'),
                        Section::make('Form Input')
                            ->description('Make your form\'s input field. The asterisk (*) denotes a compulsory field in your application\'s form.')
                            ->schema([
                                \Filament\Forms\Components\Builder::make('content')
                                    ->blocks([
                                        Block::make('text')
                                            ->schema([
                                                Tabs::make('Label')
                                                    ->tabs([
                                                        Tabs\Tab::make('Question')
                                                            ->schema([
                                                                Checkbox::make('is_required'),
                                                                // static::getFieldNameInput(),
                                                                Textinput::make('question')
                                                                    ->label('Your Question?')
                                                                    ->required()
                                                            ]),
                                                        Tabs\Tab::make('Details')
                                                            ->schema([
                                                                Textarea::make('details'),
                                                            ]),
                                                        Tabs\Tab::make('Validation')
                                                            ->schema([
                                                                Checkbox::make('is_number'),
                                                                Repeater::make('validation')
                                                                    ->schema([
                                                                        Select::make('type')
                                                                            ->options([
                                                                                'regx' => 'Add Regex Validation',
                                                                                'minLength' => 'Minimum Length(in numbers)',
                                                                                'maxLength' => 'Max Length(in numbers)',
                                                                                'length' => 'Exact Length(in numbers)',
                                                                            ]),
                                                                        TextInput::make('validation_value'),
                                                                    ])
                                                                    ->defaultItems(0)
                                                                    ->addActionLabel("Add another Validation")
                                                                    ->label(' ')
                                                                    ->columnSpan('full')
                                                                    ->columns(2),
                                                            ]),
                                                        Tabs\Tab::make('Tutorial')
                                                            ->schema([
                                                                // Textarea::make('details'),

                                                            ]),
                                                    ]),
                                            ])
                                            ->icon('heroicon-o-pencil-square')
                                            ->lazy()
                                            ->label(function (?array $state): string {
                                                if ($state === null) {
                                                    return 'Heading';
                                                }
                                                // Todo: If required then add * in the names end
                                                $res = $state['question'] ?? "";
                                                if (!empty($state['is_required']) & !empty($state['question'])) {
                                                    $res ="* ". $state['question'];
                                                }
                                                // return $state['question'] ?? 'Text Form Input';
                                                return !empty($state['question']) ? $res : 'Text Form Input';
                                            }),

                                        Block::make('textarea')
                                            // ->label('Text Area Input')
                                            ->icon('heroicon-o-chat-bubble-left-right')
                                            ->schema([
                                                Textinput::make('question')
                                                    ->label('Your Question?')
                                                    ->required(),
                                                Checkbox::make('is_required'),
                                            ])
                                            ->label(function (?array $state): string {
                                                if ($state === null) {
                                                    return 'Heading';
                                                }
                                                return $state['question'] ?? 'Textarea Form Input';
                                            }),
                                        // Block::make('textarea')
                                        //     ->label('Text Area Input')
                                        //     ->icon('heroicon-o-chat-bubble-left-right')
                                        //     ->schema([
                                        //         static::getFieldNameInput(),
                                        //         Checkbox::make('is_required'),
                                        //     ]),

                                        Block::make('select')
                                            ->icon('heroicon-o-chevron-up-down')
                                            ->schema([
                                                static::getFieldNameInput(),
                                                KeyValue::make('options')
                                                    ->label('Add option')
                                                    ->addActionLabel("Add options")
                                                    ->keyLabel('Value')
                                                    ->required()
                                                    ->valueLabel('Label'),
                                                Checkbox::make('is_required')
                                            ]),

                                        Block::make('checkbox')
                                            ->icon('heroicon-o-pencil-square')
                                            ->schema([
                                                static::getFieldNameInput(),
                                                Checkbox::make('is_required'),
                                            ]),
                                        Block::make('file')
                                            ->icon('heroicon-o-pencil-square')
                                            ->schema([
                                                static::getFieldNameInput(),
                                                Grid::make()
                                                    ->schema([
                                                        Checkbox::make('is_multiple'),
                                                        Checkbox::make('is_required'),
                                                    ]),
                                            ]),
                                    ])
                                    // ->columnSpan('full')
                                    ->blockNumbers(false)
                                    ->columns(2)
                                    ->minItems(1)
                                    ->cloneable()
                                    ->collapsible()
                                    ->reorderableWithButtons()
                                    ->label(' ')
                                    ->addActionLabel("Add Form Element"),
                            ])
                            ->compact()
                            // ->columns(2)
                            ->columnSpan('full')
                            ->collapsible(),
                    ])
            ]);
    }

    public function save(): void
    {
        CreateAction::make()->before(function (CreateAction $action) {
            // Runs before the form fields are saved to the database.\
            $action->halt();
        });
        dd($this->all());
        // $form = \App\Models\Form::create($this->form->getState());

        // redirect()->route('form', ['form' => $form]);
    }

    static function getFieldNameInput(): Grid
    {
        // This is not a Filament-specific method, simply saves on repetition
        // between our builder blocks.

        return Grid::make()
            ->schema([
                TextInput::make('name')
                    ->lazy()
                    ->afterStateUpdated(function ($set, $state) {
                        $label = Str::of($state)
                            ->kebab()
                            ->replace(['-', '_'], ' ')
                            ->ucfirst();

                        $set('label', $label);
                    })
                    ->required(),
                TextInput::make('label')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
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
            'index' => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'view' => Pages\ViewSurvey::route('/{record}'),
            'edit' => Pages\EditSurvey::route('/{record}/edit'),
        ];
    }
}
