<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsignorPaymentResource\Pages;
use App\Models\Consignor;
use App\Models\ConsignorPayment;
use App\Services\PaymentService;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConsignorPaymentResource extends Resource
{
    protected static ?string $model = ConsignorPayment::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-banknotes';
    }

    public static function getNavigationLabel(): string
    {
        return 'Pembayaran Penitip';
    }

    public static function getModelLabel(): string
    {
        return 'Pembayaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pembayaran Penitip';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Keuangan';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['consignor', 'admin']);

        if (auth()->check() && auth()->user()->hasRole('penitip')) {
            $query->whereHas('consignor', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pembayaran Saldo')
                    ->schema([
                        Select::make('consignor_id')
                            ->label('Penitip (Penerima Dana)')
                            ->relationship('consignor', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->helperText(function (Get $get) {
                                $consignorId = $get('consignor_id');
                                if (!$consignorId) return 'Pilih penitip untuk melihat saldo yang tersedia.';
                                $consignor = Consignor::with('balance')->find($consignorId);
                                $balance = (float) ($consignor?->balance?->balance ?? 0);
                                return 'Saldo tersedia untuk dicairkan: Rp ' . number_format($balance, 0, ',', '.');
                            }),

                        TextInput::make('payment_number')
                            ->label('Nomor Pembayaran')
                            ->default(fn () => ConsignorPayment::generatePaymentNumber())
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('amount')
                            ->label('Nominal Pembayaran (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->minValue(1),

                        Select::make('payment_method')
                            ->label('Metode Pencairan')
                            ->options([
                                'transfer' => 'Transfer Bank',
                                'cash' => 'Tunai (Cash)',
                                'other' => 'Lainnya',
                            ])
                            ->default('transfer')
                            ->required(),

                        TextInput::make('reference_number')
                            ->label('Nomor Referensi / No. Resi Bank')
                            ->nullable(),

                        DateTimePicker::make('paid_at')
                            ->label('Waktu Pembayaran')
                            ->default(now())
                            ->required(),

                        Textarea::make('notes')
                            ->label('Catatan Pembayaran')
                            ->rows(2)
                            ->columnSpanFull()
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payment_number')
                    ->label('No. Pembayaran')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('consignor.name')
                    ->label('Penitip')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),

                TextColumn::make('reference_number')
                    ->label('No. Ref')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('paid_at')
                    ->label('Waktu Bayar')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('admin.name')
                    ->label('Diproses Oleh')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('consignor_id')
                    ->label('Penitip')
                    ->relationship('consignor', 'name'),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->defaultSort('paid_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListConsignorPayments::route('/'),
            'create' => Pages\CreateConsignorPayment::route('/create'),
            'view'   => Pages\ViewConsignorPayment::route('/{record}'),
        ];
    }
}

