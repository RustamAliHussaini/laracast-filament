<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;

class Conference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'region',
        'venue_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'region' => Region::class,
        'venue_id' => 'integer',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakerTalks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public static function getForm(): array
    {
        return [
            Section::make('Conference Details')
                ->collapsible()
                ->description('All you need to know')
                ->icon('heroicon-o-information-circle')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->columnSpanFull()
                        ->label('Conference')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('description')
                        ->columnSpanFull()
                        ->required()
                        ->maxLength(255),
                    DateTimePicker::make('start_date')
                        ->required(),
                    DateTimePicker::make('end_date')
                        ->required(),
                    Fieldset::make('Status')
                        ->columns(1)
                        ->schema([

                            Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'archived' => 'Archived'
                                ])
                                ->required(),
                            Checkbox::make('is_published')
                                ->default(true),
                            CheckboxList::make('speakers')
                                ->relationship('speakers', 'name')
                                ->options(
                                    Speaker::all()->pluck('name', 'id')
                                ),
                        ]),
                    ]),


            Section::make('Location Info')
                ->columns(2)
                ->schema([
                    Select::make('region')
                        ->live()
                        ->enum(Region::class)
                        ->options(Region::class),
                    Select::make('venue_id')
                        ->searchable()
                        ->preload()
                        ->createOptionForm(Venue::getForm())
                        ->editOptionForm(Venue::getForm())
                        ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                            ray($get('region'));
                            return $query->where('region', $get('region'));
                        }),
                ]),



                Actions::make([
                    Action::make('star')
                        ->label('Fill with Factory data')
                        ->icon('heroicon-m-star')
                        ->action(function ($livewire) {
                            $data = Conference::factory()->make()->toArray();
                            $livewire->form->fill($data);
                        }),

                ]),


        ];


    }
}
