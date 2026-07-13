<?php

namespace App\Filament\Resources\Tickets\Tables;

use App\Models\Ticket;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('guest_email')
                    ->label('Guest')
                    ->description(fn (Ticket $r) => str($r->message)->limit(60))
                    ->searchable(),
                TextColumn::make('reservation.property.name')
                    ->label('Property')
                    ->placeholder('— unmatched —')
                    ->searchable(),
                TextColumn::make('category')
                    ->badge()
                    ->searchable(),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'normal' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'resolved' => 'success',
                        'investigating', 'waiting' => 'warning',
                        'triaged' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('confidence')
                    ->suffix('%')
                    ->badge()
                    ->color(fn (int $state) => match (true) {
                        $state >= 70 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                IconColumn::make('needs_escalation')
                    ->label('Escalated')
                    ->boolean(),
                TextColumn::make('channel')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(array_combine(Ticket::STATUSES, Ticket::STATUSES)),
                SelectFilter::make('category')
                    ->options(array_combine(Ticket::CATEGORIES, Ticket::CATEGORIES)),
                SelectFilter::make('priority')
                    ->options(array_combine(Ticket::PRIORITIES, Ticket::PRIORITIES)),
                TernaryFilter::make('needs_escalation')
                    ->label('Escalated only'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
