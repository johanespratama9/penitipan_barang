<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsignorResource\Pages;
use App\Models\Consignor;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConsignorResource extends Resource
{
    protected static ?string $model = Consignor::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationLabel(): string
    {
        return 'Penitip';
    }

    public static function getModelLabel(): string
    {
        return 'Penitip';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penitip';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Master Data';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    /**
     * Role Security: Batasi query data agar penitip hanya dapat melihat data miliknya sendiri
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->check() && auth()->user()->hasRole('penitip')) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Penitip')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Penitip')
                            ->default(fn () => Consignor::generateUniqueCode())
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText('Kode unik penitip (otomatis digenerate).'),

                        Select::make('user_id')
                            ->label('Akun User (Login)')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->role('penitip')
                            )
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Hubungkan ke akun user login dengan role penitip (opsional).'),

                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Nomor Telepon / WhatsApp')
                            ->tel()
                            ->required()
                            ->maxLength(50),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->nullable()
                            ->maxLength(255),

                        TextInput::make('identity_number')
                            ->label('No. Identitas (NIK/KTP)')
                            ->nullable()
                            ->maxLength(50),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                                'suspended' => 'Ditangguhkan',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Alamat & Catatan')
                    ->schema([
                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama Penitip')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Telepon / WA')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('identity_number')
                    ->label('NIK / KTP')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.name')
                    ->label('Akun Login')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'suspended' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListConsignors::route('/'),
            'create' => Pages\CreateConsignor::route('/create'),
            'view'   => Pages\ViewConsignor::route('/{record}'),
            'edit'   => Pages\EditConsignor::route('/{record}/edit'),
        ];
    }
}

