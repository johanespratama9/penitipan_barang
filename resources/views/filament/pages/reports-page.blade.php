<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Bar & Tabs -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <!-- Tab buttons -->
            <div class="flex gap-2 border-b md:border-b-0 pb-2 md:pb-0 w-full md:w-auto overflow-x-auto">
                @foreach([
                    'sales' => 'Laporan Penjualan',
                    'consignor' => 'Laporan Penitip & Saldo',
                    'products' => 'Laporan Produk & Stok',
                    'payments' => 'Laporan Pencairan Dana',
                ] as $tab => $title)
                    <button
                        type="button"
                        wire:click="$set('activeTab', '{{ $tab }}')"
                        class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition {{ $activeTab === $tab ? 'bg-primary-600 text-white shadow' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    >
                        {{ $title }}
                    </button>
                @endforeach
            </div>

            <!-- Date Range & Export -->
            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                @if(in_array($activeTab, ['sales', 'payments']))
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <span>Dari:</span>
                        <input type="date" wire:model.live="startDate" class="py-1 px-2 border rounded-lg dark:bg-gray-700 dark:text-white text-xs" />
                        <span>Sampai:</span>
                        <input type="date" wire:model.live="endDate" class="py-1 px-2 border rounded-lg dark:bg-gray-700 dark:text-white text-xs" />
                    </div>
                @endif

                <button
                    wire:click="exportCsv('{{ $activeTab }}')"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold flex items-center gap-1.5 transition shadow-sm"
                >
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    Export CSV
                </button>
            </div>
        </div>

        <!-- Tab 1: Laporan Penjualan -->
        @if($activeTab === 'sales')
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 border-b">
                            <tr>
                                <th class="px-4 py-3">No. Invoice</th>
                                <th class="px-4 py-3">Waktu Transaksi</th>
                                <th class="px-4 py-3">Kasir</th>
                                <th class="px-4 py-3">Pelanggan</th>
                                <th class="px-4 py-3">Metode</th>
                                <th class="px-4 py-3 text-right">Total Transaksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @php $totalSalesSum = 0; @endphp
                            @forelse($this->salesReport as $sale)
                                @php $totalSalesSum += (float) $sale->total; @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-4 py-3 font-semibold">{{ $sale->invoice_number }}</td>
                                    <td class="px-4 py-3">{{ $sale->sold_at->format('d M Y, H:i') }}</td>
                                    <td class="px-4 py-3">{{ $sale->cashier?->name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $sale->customer_name ?: 'Umum' }}</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs font-mono bg-gray-100 dark:bg-gray-700">{{ strtoupper($sale->payment_method) }}</span></td>
                                    <td class="px-4 py-3 text-right font-bold text-primary-600">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada transaksi pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($this->salesReport->count() > 0)
                            <tfoot class="bg-gray-50 dark:bg-gray-700/50 font-bold">
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-right">Total Keseluruhan:</td>
                                    <td class="px-4 py-3 text-right text-base text-primary-600">Rp {{ number_format($totalSalesSum, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        <!-- Tab 2: Laporan Penitip & Saldo -->
        @elseif($activeTab === 'consignor')
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 border-b">
                            <tr>
                                <th class="px-4 py-3">Kode</th>
                                <th class="px-4 py-3">Nama Penitip</th>
                                <th class="px-4 py-3">Telepon</th>
                                <th class="px-4 py-3 text-center">Barang (Terjual/Total)</th>
                                <th class="px-4 py-3 text-right">Total Penjualan</th>
                                <th class="px-4 py-3 text-right">Komisi Toko</th>
                                <th class="px-4 py-3 text-right">Hak Penitip</th>
                                <th class="px-4 py-3 text-right">Sudah Dibayar</th>
                                <th class="px-4 py-3 text-right">Saldo Tersisa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($this->consignorReport as $c)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-4 py-3 font-mono font-semibold">{{ $c['code'] }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $c['name'] }}</td>
                                    <td class="px-4 py-3 text-xs">{{ $c['phone'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $c['sold_products'] }} / {{ $c['total_products'] }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($c['total_sales'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-gray-500">Rp {{ number_format($c['total_commission'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($c['total_earned'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-green-600">Rp {{ number_format($c['total_paid'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-bold {{ $c['balance'] > 0 ? 'text-amber-600' : 'text-gray-400' }}">
                                        Rp {{ number_format($c['balance'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-400">Belum ada data penitip.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- Tab 3: Laporan Produk & Stok -->
        @elseif($activeTab === 'products')
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 border-b">
                            <tr>
                                <th class="px-4 py-3">Kode</th>
                                <th class="px-4 py-3">Nama Barang</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Penitip</th>
                                <th class="px-4 py-3 text-right">Harga Jual</th>
                                <th class="px-4 py-3 text-center">Sisa Stok</th>
                                <th class="px-4 py-3 text-center">Terjual</th>
                                <th class="px-4 py-3 text-right">Omzet</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($this->productReport as $p)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-4 py-3 font-mono font-semibold">{{ $p['code'] }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $p['name'] }}</td>
                                    <td class="px-4 py-3 text-xs">{{ $p['category'] }}</td>
                                    <td class="px-4 py-3 text-xs">{{ $p['consignor'] }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($p['price'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $p['stock'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $p['stock'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold">{{ $p['sold_qty'] }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-primary-600">Rp {{ number_format($p['omzet'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">Belum ada data produk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- Tab 4: Laporan Pencairan Dana -->
        @elseif($activeTab === 'payments')
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 border-b">
                            <tr>
                                <th class="px-4 py-3">No. Pembayaran</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Penitip</th>
                                <th class="px-4 py-3">Metode</th>
                                <th class="px-4 py-3">No. Referensi</th>
                                <th class="px-4 py-3 text-right">Nominal Dibayar</th>
                                <th class="px-4 py-3">Diproses Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($this->paymentReport as $pay)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-4 py-3 font-mono font-semibold">{{ $pay->payment_number }}</td>
                                    <td class="px-4 py-3">{{ $pay->paid_at->format('d M Y, H:i') }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $pay->consignor?->name ?? '-' }}</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs font-mono bg-gray-100 dark:bg-gray-700">{{ strtoupper($pay->payment_method) }}</span></td>
                                    <td class="px-4 py-3 text-xs">{{ $pay->reference_number ?: '-' }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-green-600">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-xs">{{ $pay->admin?->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">Tidak ada riwayat pencairan dana pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>

