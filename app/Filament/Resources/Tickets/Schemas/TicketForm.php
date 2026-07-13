<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('reservation_id')
                    ->relationship('reservation', 'id'),
                Select::make('help_article_id')
                    ->relationship('helpArticle', 'title'),
                TextInput::make('guest_email')
                    ->email()
                    ->required(),
                TextInput::make('channel')
                    ->required(),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('category')
                    ->required()
                    ->default('other'),
                TextInput::make('priority')
                    ->required()
                    ->default('normal'),
                TextInput::make('status')
                    ->required()
                    ->default('new'),
                TextInput::make('confidence')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('needs_escalation')
                    ->required(),
                Textarea::make('draft_reply')
                    ->columnSpanFull(),
            ]);
    }
}
