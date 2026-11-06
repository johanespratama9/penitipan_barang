<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Sale;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-receipt-percent';
    }

    public static function getNavigationLabel(): string
    {
        return 'Riwayat Penjualan';
    }

    public static function getModelLabel(): string
    {
        return 'Penjualan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penjualan';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Penjualan';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Invoice')
                    ->schema([
                        TextInput::make('invoice_number')->label('No. Invoice')->disabled(),
                        TextInput::make('customer_name')->label('Nama Pelanggan')->disabled(),
                        TextInput::make('table_number')->label('No. Meja')->placeholder('Bawa Pulang / -')->disabled(),
                        TextInput::make('cashier.name')->label('Kasir')->disabled(),
                        TextInput::make('payment_method')->label('Metode Pembayaran')->disabled(),
                        TextInput::make('kitchen_status')->label('Status Dapur')->formatStateUsing(fn (?string $state): string => match ($state) { 'pending' => 'Menunggu Dapur', 'cooking' => 'Sedang Dimasak', 'ready' => 'Siap Saji', 'completed' => 'All Selesai', default => '-' })->disabled(),
                        TextInput::make('subtotal')->label('Subtotal')->numeric()->prefix('Rp')->disabled(),
                        TextInput::make('discount')->label('Diskon')->numeric()->prefix('Rp')->disabled(),
                        TextInput::make('total')->label('Total Akhir')->numeric()->prefix('Rp')->disabled(),
                        TextInput::make('paid_amount')->label('Jumlah Dibayar')->numeric()->prefix('Rp')->disabled(),
                        TextInput::make('change_amount')->label('Kembalian')->numeric()->prefix('Rp')->disabled(),
                        TextInput::make('status')->label('Status Transaksi')->disabled(),
                        TextInput::make('order_notes')->label('Catatan Pesanan Dapur')->placeholder('Tidak ada catatan')->columnSpanFull()->disabled(),
                    ])
                    ->columns(3),

                Section::make('Rincian Produk Terjual')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                TextInput::make('product_name')->label('Barang')->disabled()->columnSpan(2),
                                TextInput::make('quantity')->label('Qty')->disabled(),
                                TextInput::make('selling_price')->label('Harga Satuan')->numeric()->prefix('Rp')->disabled(),
                                TextInput::make('subtotal')->label('Subtotal')->numeric()->prefix('Rp')->disabled(),
                                TextInput::make('consignor_amount')->label('Hak Penitip')->numeric()->prefix('Rp')->disabled(),
                                TextInput::make('commission_amount')->label('Komisi Toko')->numeric()->prefix('Rp')->disabled(),
                            ])
                            ->columns(7)
                            ->addable(false)
                            ->deletable(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('sold_at')
                    ->label('Waktu Transaksi')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('cashier.name')
                    ->label('Kasir')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer_name')
                    ->label('Pelanggan')
                    ->placeholder('Umum')
                    ->searchable(),

                TextColumn::make('table_number')
                    ->label('Meja')
                    ->placeholder('-')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kitchen_status')
                    ->label('Dapur')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'cooking'   => 'info',
                        'ready'     => 'success',
                        'completed' => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending'   => '⏳ Menunggu',
                        'cooking'   => '🔥 Dimasak',
                        'ready'     => '🔔 Siap Saji',
                        'completed' => '✓ All Selesai',
                        default     => '-',
                    })
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total Transaksi')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'qris' => 'info',
                        'transfer' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ]),

                SelectFilter::make('kitchen_status')
                    ->label('Status Dapur')
                    ->options([
                        'pending'   => 'Menunggu Dapur',
                        'cooking'   => 'Sedang Dimasak',
                        'ready'     => 'Siap Disajikan',
                        'completed' => 'All Selesai',
                    ]),

                SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'qris' => 'QRIS',
                        'transfer' => 'Transfer',
                        'other' => 'Lainnya',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->defaultSort('sold_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'view'  => Pages\ViewSale::route('/{record}'),
        ];
    }
}

