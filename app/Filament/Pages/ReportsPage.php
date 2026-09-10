<?php

namespace App\Filament\Pages;

use App\Models\Consignor;
use App\Models\ConsignorPayment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Pages\Page;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsPage extends Page
{
    protected string $view = 'filament.pages.reports-page';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document-chart-bar';
    }

    public static function getNavigationLabel(): string
    {
        return 'Laporan Bisnis';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Laporan & Analitik Konsinyasi';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Laporan';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'super_admin']) ?? false;
    }

    public string $activeTab = 'sales';
    public string $startDate = '';
    public string $endDate = '';
    public string $search = '';

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function updatedActiveTab(): void
    {
        $this->search = '';
    }

    public function getSalesReportProperty()
    {
        return Sale::with('cashier')
            ->whereDate('sold_at', '>=', $this->startDate)
            ->whereDate('sold_at', '<=', $this->endDate)
            ->where('status', 'completed')
            ->when(filled($this->search), function ($query) {
                $term = trim($this->search);
                $query->where(function ($q) use ($term) {
                    $q->where('invoice_number', 'like', "%{$term}%")
                        ->orWhere('customer_name', 'like', "%{$term}%")
                        ->orWhere('payment_method', 'like', "%{$term}%")
                        ->orWhereHas('cashier', fn ($cq) => $cq->where('name', 'like', "%{$term}%"));
                });
            })
            ->orderBy('sold_at', 'desc')
            ->get();
    }

    public function getConsignorReportProperty()
    {
        return Consignor::with(['balance', 'products'])
            ->when(filled($this->search), function ($query) {
                $term = trim($this->search);
                $query->where(function ($q) use ($term) {
                    $q->where('code', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->get()
            ->map(function ($c) {
                $totalProducts = $c->products->count();
                $soldProducts = $c->products->where('status', 'sold')->count();
                $balance = $c->balance;

                return [
                    'code' => $c->code,
                    'name' => $c->name,
                    'phone' => $c->phone,
                    'total_products' => $totalProducts,
                    'sold_products' => $soldProducts,
                    'total_sales' => (float) ($balance?->total_sales ?? 0),
                    'total_commission' => (float) ($balance?->total_commission ?? 0),
                    'total_earned' => (float) ($balance?->total_earned ?? 0),
                    'total_paid' => (float) ($balance?->total_paid ?? 0),
                    'balance' => (float) ($balance?->balance ?? 0),
                ];
            });
    }

    public function getProductReportProperty()
    {
        return Product::with(['category', 'consignor'])
            ->when(filled($this->search), function ($query) {
                $term = trim($this->search);
                $query->where(function ($q) use ($term) {
                    $q->where('code', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%")
                        ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('consignor', fn ($cq) => $cq->where('name', 'like', "%{$term}%"));
                });
            })
            ->get()
            ->map(function ($p) {
                $soldQty = SaleItem::where('product_id', $p->id)->sum('quantity');
                $omzet = SaleItem::where('product_id', $p->id)->sum('subtotal');

                return [
                    'code' => $p->code,
                    'name' => $p->name,
                    'category' => $p->category?->name ?? '-',
                    'consignor' => $p->consignor?->name ?? '-',
                    'price' => (float) $p->selling_price,
                    'stock' => $p->stock,
                    'sold_qty' => $soldQty,
                    'omzet' => (float) $omzet,
                    'status' => $p->status,
                ];
            });
    }

    public function getPaymentReportProperty()
    {
        return ConsignorPayment::with(['consignor', 'admin'])
            ->whereDate('paid_at', '>=', $this->startDate)
            ->whereDate('paid_at', '<=', $this->endDate)
            ->when(filled($this->search), function ($query) {
                $term = trim($this->search);
                $query->where(function ($q) use ($term) {
                    $q->where('payment_number', 'like', "%{$term}%")
                        ->orWhere('reference_number', 'like', "%{$term}%")
                        ->orWhere('payment_method', 'like', "%{$term}%")
                        ->orWhereHas('consignor', fn ($cq) => $cq->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('admin', fn ($aq) => $aq->where('name', 'like', "%{$term}%"));
                });
            })
            ->orderBy('paid_at', 'desc')
            ->get();
    }

    public function exportCsv(string $type): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"laporan_{$type}_" . date('Ymd_His') . ".csv\"",
        ];

        return response()->stream(function () use ($type) {
            $handle = fopen('php://output', 'w');

            if ($type === 'sales') {
                fputcsv($handle, ['No. Invoice', 'Tanggal', 'Kasir', 'Pelanggan', 'Metode Pembayaran', 'Total']);
                foreach ($this->getSalesReportProperty() as $s) {
                    fputcsv($handle, [
                        $s->invoice_number,
                        $s->sold_at->format('Y-m-d H:i'),
                        $s->cashier?->name ?? '-',
                        $s->customer_name ?? '-',
                        strtoupper($s->payment_method),
                        $s->total,
                    ]);
                }
            } elseif ($type === 'consignor') {
                fputcsv($handle, ['Kode Penitip', 'Nama', 'Telepon', 'Total Barang', 'Barang Terjual', 'Total Penjualan', 'Komisi Toko', 'Hak Penitip', 'Sudah Dibayar', 'Saldo Tersisa']);
                foreach ($this->getConsignorReportProperty() as $c) {
                    fputcsv($handle, [
                        $c['code'],
                        $c['name'],
                        $c['phone'],
                        $c['total_products'],
                        $c['sold_products'],
                        $c['total_sales'],
                        $c['total_commission'],
                        $c['total_earned'],
                        $c['total_paid'],
                        $c['balance'],
                    ]);
                }
            } elseif ($type === 'products') {
                fputcsv($handle, ['Kode Produk', 'Nama Barang', 'Kategori', 'Penitip', 'Harga Jual', 'Stok Saat Ini', 'Total Terjual', 'Total Omzet']);
                foreach ($this->getProductReportProperty() as $p) {
                    fputcsv($handle, [
                        $p['code'],
                        $p['name'],
                        $p['category'],
                        $p['consignor'],
                        $p['price'],
                        $p['stock'],
                        $p['sold_qty'],
                        $p['omzet'],
                    ]);
                }
            } elseif ($type === 'payments') {
                fputcsv($handle, ['No. Pembayaran', 'Tanggal', 'Penitip', 'Metode', 'No. Referensi', 'Nominal', 'Admin']);
                foreach ($this->getPaymentReportProperty() as $pay) {
                    fputcsv($handle, [
                        $pay->payment_number,
                        $pay->paid_at->format('Y-m-d H:i'),
                        $pay->consignor?->name ?? '-',
                        strtoupper($pay->payment_method),
                        $pay->reference_number ?? '-',
                        $pay->amount,
                        $pay->admin?->name ?? '-',
                    ]);
                }
            }

            fclose($handle);
        }, 200, $headers);
    }
}

