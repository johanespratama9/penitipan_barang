<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Services\CommissionService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-shopping-bag';
    }

    public static function getNavigationLabel(): string
    {
        return 'Produk / Barang';
    }

    public static function getModelLabel(): string
    {
        return 'Produk';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Produk';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Master Data';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    /**
     * Role Security: Penitip hanya dapat melihat produk miliknya sendiri
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

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
                Section::make('Informasi Dasar Barang')
                    ->schema([
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('consignor_id')
                            ->label('Penitip (Pemilik Barang)')
                            ->relationship('consignor', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->label('Nama Barang')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label('Kode Produk')
                            ->default(fn () => Product::generateUniqueCode())
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->helperText('Kode unik internal toko.'),

                        TextInput::make('barcode')
                            ->label('Barcode')
                            ->unique(ignoreRecord: true)
                            ->nullable()
                            ->maxLength(100)
                            ->helperText('Dapat discan dengan barcode scanner di POS.'),

                        Select::make('condition')
                            ->label('Kondisi Barang')
                            ->options([
                                'new' => 'Baru (BNIB/Segel)',
                                'like_new' => 'Bekas Seperti Baru (Like New)',
                                'used' => 'Bekas Baik (Used)',
                                'fair' => 'Cukup (Fair Condition)',
                            ])
                            ->default('like_new')
                            ->nullable(),

                        Select::make('status')
                            ->label('Status Barang')
                            ->options([
                                'pending' => 'Pending (Menunggu Verifikasi)',
                                'available' => 'Tersedia (Siap Dijual di POS)',
                                'sold' => 'Terjual (Sold Out)',
                                'returned' => 'Dikembalikan ke Penitip',
                                'rejected' => 'Ditolak',
                                'expired' => 'Kedaluwarsa',
                            ])
                            ->default('available')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Harga & Pembagian Komisi')
                    ->schema([
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
                                $set('consignor_price', $service->calculateConsignorAmount($selling, $type, $val));
                            }),

                        Select::make('commission_type')
                            ->label('Tipe Komisi Toko')
                            ->options([
                                'percentage' => 'Persentase (%)',
                                'fixed' => 'Nominal Tetap (Rp)',
                            ])
                            ->default('percentage')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                $selling = (float) ($get('selling_price') ?? 0);
                                $val = (float) ($get('commission_value') ?? 0);
                                $service = app(CommissionService::class);
                                $set('consignor_price', $service->calculateConsignorAmount($selling, $state ?? 'percentage', $val));
                            }),

                        TextInput::make('commission_value')
                            ->label('Nilai Komisi Toko')
                            ->numeric()
                            ->default(20)
                            ->required()
                            ->live(onBlur: true)
                            ->helperText('Jika persentase, masukkan angka misal: 20 untuk 20%.')
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                $selling = (float) ($get('selling_price') ?? 0);
                                $type = $get('commission_type') ?? 'percentage';
                                $service = app(CommissionService::class);
                                $set('consignor_price', $service->calculateConsignorAmount($selling, $type, (float) ($state ?? 0)));
                            }),

                        TextInput::make('consignor_price')
                            ->label('Pendapatan Penitip (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->helperText('Otomatis dihitung dari harga jual dikurangi komisi toko.'),

                        TextInput::make('purchase_price')
                            ->label('Harga Acuan Penitip / Modal')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->helperText('Harga yang diminta penitip saat awal menitipkan.'),
                    ])
                    ->columns(2),

                Section::make('Stok, Foto & Catatan')
                    ->schema([
                        TextInput::make('stock')
                            ->label('Stok Saat Ini')
                            ->numeric()
                            ->default(1)
                            ->minValue(0)
                            ->required(),

                        DateTimePicker::make('received_at')
                            ->label('Tanggal Terima')
                            ->default(now()),

                        FileUpload::make('image')
                            ->label('Foto Barang')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->maxSize(4096)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->columnSpanFull()
                            ->helperText('Format JPG, PNG, WEBP, GIF max 4MB. Otomatis dikonversi ke WebP.')
                            ->saveUploadedFileUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string {
                                return app(\App\Services\ImageService::class)
                                    ->storeAsWebp($file, directory: 'products', quality: 82);
                            }),

                        Textarea::make('description')
                            ->label('Deskripsi / Catatan Barang')
                            ->rows(3)
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
                ImageColumn::make('image')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=Barang&background=f3f4f6&color=6b7280'),

                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('barcode')
                    ->label('Barcode')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),

                TextColumn::make('consignor.name')
                    ->label('Penitip')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('selling_price')
                    ->label('Harga Jual')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('consignor_price')
                    ->label('Hak Penitip')
                    ->money('IDR', locale: 'id_ID')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('commission_value')
                    ->label('Komisi')
                    ->formatStateUsing(fn ($record) => $record->commission_type === 'percentage'
                        ? $record->commission_value . '%'
                        : 'Rp ' . number_format((float) $record->commission_value, 0, ',', '.'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'pending' => 'warning',
                        'sold' => 'info',
                        'returned' => 'gray',
                        'rejected' => 'danger',
                        'expired' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Tersedia',
                        'pending' => 'Pending',
                        'sold' => 'Terjual',
                        'returned' => 'Dikembalikan',
                        'rejected' => 'Ditolak',
                        'expired' => 'Kedaluwarsa',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'pending' => 'Pending',
                        'sold' => 'Terjual',
                        'returned' => 'Dikembalikan',
                        'rejected' => 'Ditolak',
                        'expired' => 'Kedaluwarsa',
                    ]),

                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),

                SelectFilter::make('consignor_id')
                    ->label('Penitip')
                    ->relationship('consignor', 'name'),
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
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view'   => Pages\ViewProduct::route('/{record}'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

