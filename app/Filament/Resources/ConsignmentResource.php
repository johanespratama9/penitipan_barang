<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsignmentResource\Pages;
use App\Models\Consignment;
use App\Services\CommissionService;
use App\Services\ConsignmentService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConsignmentResource extends Resource
{
    protected static ?string $model = Consignment::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function getNavigationLabel(): string
    {
        return 'Penitipan Barang';
    }

    public static function getModelLabel(): string
    {
        return 'Penitipan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penitipan Barang';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Consignment';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    /**
     * Role Security: Penitip hanya dapat melihat dokumen miliknya sendiri
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['consignor', 'items']);

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
                Section::make('Informasi Dokumen Penitipan')
                    ->schema([
                        Select::make('consignor_id')
                            ->label('Penitip (Pemilik Barang)')
                            ->relationship('consignor', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('code')
                            ->label('Nomor Dokumen')
                            ->default(fn () => Consignment::generateUniqueCode())
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        DatePicker::make('received_date')
                            ->label('Tanggal Diterima')
                            ->default(now())
                            ->required(),

                        DatePicker::make('expiry_date')
                            ->label('Batas Waktu Penitipan (Expiry)')
                            ->nullable()
                            ->helperText('Jika melewati tanggal ini, status dapat ditandai kedaluwarsa.'),

                        Select::make('status')
                            ->label('Status Penitipan')
                            ->options([
                                'draft' => 'Draft',
                                'submitted' => 'Diajukan Penitip',
                                'received' => 'Diterima Toko (Menunggu Review)',
                                'approved' => 'Disetujui (Barang Siap Dijual)',
                                'rejected' => 'Ditolak',
                                'completed' => 'Selesai (Semua Terjual/Lunas)',
                                'expired' => 'Kedaluwarsa',
                            ])
                            ->default('draft')
                            ->required(),

                        Textarea::make('notes')
                            ->label('Catatan / Syarat Khusus')
                            ->rows(2)
                            ->columnSpanFull()
                            ->nullable(),
                    ])
                    ->columns(2),

                Section::make('Rincian Barang yang Dititipkan')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                TextInput::make('product_name')
                                    ->label('Nama Barang')
                                    ->required()
                                    ->columnSpan(2),

                                Select::make('category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->columnSpan(1),

                                TextInput::make('quantity')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('selling_price')
                                    ->label('Harga Jual (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        $type = $get('commission_type') ?? 'percentage';
                                        $val = (float) ($get('commission_value') ?? 0);
                                        $selling = (float) ($state ?? 0);
                                        $service = app(CommissionService::class);
                                        $set('consignor_amount', $service->calculateConsignorAmount($selling, $type, $val));
                                    })
                                    ->columnSpan(1),

                                Select::make('commission_type')
                                    ->label('Tipe Komisi')
                                    ->options([
                                        'percentage' => 'Persentase (%)',
                                        'fixed' => 'Nominal (Rp)',
                                    ])
                                    ->default('percentage')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        $selling = (float) ($get('selling_price') ?? 0);
                                        $val = (float) ($get('commission_value') ?? 0);
                                        $service = app(CommissionService::class);
                                        $set('consignor_amount', $service->calculateConsignorAmount($selling, $state ?? 'percentage', $val));
                                    })
                                    ->columnSpan(1),

                                TextInput::make('commission_value')
                                    ->label('Nilai Komisi')
                                    ->numeric()
                                    ->default(20)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        $selling = (float) ($get('selling_price') ?? 0);
                                        $type = $get('commission_type') ?? 'percentage';
                                        $service = app(CommissionService::class);
                                        $set('consignor_amount', $service->calculateConsignorAmount($selling, $type, (float) ($state ?? 0)));
                                    })
                                    ->columnSpan(1),

                                TextInput::make('consignor_amount')
                                    ->label('Hak Penitip (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->columnSpan(1),

                                TextInput::make('purchase_price')
                                    ->label('Harga Acuan Penitip (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['product_name'] ?? null)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('No. Dokumen')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('consignor.name')
                    ->label('Penitip')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('received_date')
                    ->label('Tgl Terima')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('expiry_date')
                    ->label('Tgl Kedaluwarsa')
                    ->date('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_items')
                    ->label('Total Item')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'received' => 'info',
                        'submitted' => 'warning',
                        'draft' => 'gray',
                        'rejected' => 'danger',
                        'expired' => 'danger',
                        'completed' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'submitted' => 'Diajukan',
                        'received' => 'Diterima Toko',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'completed' => 'Selesai',
                        'expired' => 'Kedaluwarsa',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Diajukan',
                        'received' => 'Diterima Toko',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'completed' => 'Selesai',
                        'expired' => 'Kedaluwarsa',
                    ]),

                SelectFilter::make('consignor_id')
                    ->label('Penitip')
                    ->relationship('consignor', 'name'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Dokumen Penitipan')
                    ->modalDescription('Barang dalam dokumen ini akan otomatis ditambahkan ke katalog Produk dan siap dijual di POS.')
                    ->visible(fn ($record) => in_array($record->status, ['draft', 'submitted', 'received']) && (auth()->user()?->hasRole('admin') || auth()->user()?->hasRole('super_admin')))
                    ->action(function (Consignment $record) {
                        app(ConsignmentService::class)->approveConsignment($record);
                        Notification::make()
                            ->title('Penitipan Berhasil Disetujui')
                            ->body("Dokumen {$record->code} telah disetujui. Seluruh barang telah masuk ke katalog produk.")
                            ->success()
                            ->send();
                    }),

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
            'index'  => Pages\ListConsignments::route('/'),
            'create' => Pages\CreateConsignment::route('/create'),
            'view'   => Pages\ViewConsignment::route('/{record}'),
            'edit'   => Pages\EditConsignment::route('/{record}/edit'),
        ];
    }
}
