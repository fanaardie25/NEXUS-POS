<?php

namespace App\Filament\Resources\Users\Tables;

use App\Mail\GenericEmail;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('roles.name')->label('Roles')->searchable()->sortable()->badge(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                 ForceDeleteAction::make(),
                  RestoreAction::make(),
                DeleteAction::make(),
                Action::make('sendEmail')
                ->icon('heroicon-m-envelope') // Tambahin icon biar cakep
                ->color('info')
                ->schema([
                    TextInput::make('subject')->required(),
                    RichEditor::make('body')->required(),
                ])
                // Panggil $record di sini
                ->action(function (array $data, $record) { 
                    // Sesuaikan 'email' dengan nama kolom email di tabel lu (misal: $record->client_email)
                    Mail::to($record->email) 
                        ->send(new GenericEmail(
                            mailSubject: $data['subject'],
                            body: $data['body'],
                        ));

                    // Tambahin notifikasi sukses biar user gak bingung
                    \Filament\Notifications\Notification::make()
                        ->title('Email Sent Successfully')
                        ->success()
                        ->send();
                })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
