<?php

namespace App\Filament\Resources;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use App\Filament\Resources\TalkResource\Pages;
use App\Filament\Resources\TalkResource\RelationManagers;
use App\Models\Talk;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Talk::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->filtersTriggerAction(function($action){
                return $action->button()->label('Filter');
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable()
                    ->description(function (Talk $record) {
                        return Str::of($record->abstract)->limit(40);
                    }),
                Tables\Columns\ImageColumn::make('speaker.avatar')
                    ->label('Speaker Avatar')
                    ->circular()
                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->speaker->name) . '&size=128&background=random'),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('new_talk'),
                TextColumn::make('status')
                    ->badge()
                    ->searchable()
                    ->color(function ($state) {
                        return $state->getColor();
                    }),

                Tables\Columns\IconColumn::make('lenght')
                    ->icon(function ($state) {
                        return match ($state) {
                            TalkLength::NORMAL => 'heroicon-o-megaphone',
                            TalkLength::LIGHTNING => 'heroicon-o-bolt',
                            TalkLength::KEYNOTE => 'heroicon-o-key',
                            default => null,
                        };
                    })


            ])
            ->filters([
                TernaryFilter::make('new_talk'),
                SelectFilter::make('speaker')
                    ->relationship('speaker' , 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Filter::make('has_avatar')
                    ->label('Show only speakers with avatar')
                    ->toggle()
                    ->query(function($query){
                        return $query->whereHas('speaker' , function(Builder $query){
                            $query->whereNotNull('avatar');
                        });
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->slideOver(),

               ActionsActionGroup::make([
                    Action::make('approved')
                    ->visible(function($record){
                        return $record->status === TalkStatus::SUBMITTED || $record->status === TalkStatus::REJECTED ;
                    })
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function(Talk $record){
                        $record->approved();
                    })->after(function(){
                        Notification::make()->success()->title('The Talk Approved!')
                            ->duration(1000)
                            ->body('And the user was notified.')
                            ->send();
                    })
                    ,
                    Action::make('reject')
                    ->visible(function($record){
                        return $record->status === TalkStatus::SUBMITTED || $record->status === TalkStatus::APPROVED ;
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->action(function(Talk $record){
                        $record->reject();
                    })->after(function(){
                        Notification::make()->danger()->title('The Talk Rejected!')
                            ->duration(1000)
                            ->body('And the user was notified.')
                            ->send();
                    })

                ])



            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function(Collection $record){
                        $record->each->approved();
                    })->after(function(){
                        Notification::make()->success()->title('The Talk Approved!')
                            ->duration(1000)
                            ->body('And the user was notified.')
                            ->send();
                    })
                    ,
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
            // 'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}
