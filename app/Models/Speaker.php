<?php

namespace App\Models;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speaker extends Model
{
    use HasFactory;

    const Qualifications = [
            'business-leader' => 'Business Leader',
            'charisma' => 'Charisma',
            'first-time' => 'First Time Speaker',
            'youtube-influencer' => 'Youtube Influencer',
            'business-leader2' => 'Business Leader',
            'charisma2' => 'Charisma',
            'first-time2' => 'First Time Speaker',
            'youtube-influencer2' => 'Youtube Influencer'
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'bio',
        'twitter_handle',
        'qualifications',
        'avatar',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'qualifications' => 'array',
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function talks(): HasMany
    {
        return $this->hasMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [

            TextInput::make('name')
                ->required()
                ->maxLength(255),
            FileUpload::make('avatar')
            ->avatar()
            ->imageEditor()
            ->image()
            ->maxSize(1024 * 12)
            ,
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            RichEditor::make('bio')
                ->columnSpanFull(),
            TextInput::make('twitter_handle')
                ->maxLength(255),
            CheckboxList::make('qualifications')
                ->searchable()
                ->bulkToggleable()
                ->columnSpanFull()
                ->columns(3)
                ->options(self::Qualifications)
                ->descriptions([
                    'business-leader' => 'Business Leader is off more importance.',
                        'charisma' => 'Charisma is the best',
                ]
                )
                ,
        ];
    }
}
