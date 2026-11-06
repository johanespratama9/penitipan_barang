<?php

namespace App\Filament\Pages;

use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class KitchenPage extends Page
{
    protected string $view = 'filament.pages.kitchen-page';

    protected static ?string $slug = 'dapur';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-fire';
    }

    public static function getNavigationLabel(): string
    {
        return 'Layar Dapur (KDS)';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Layar Pesanan Dapur (Kitchen Display System)';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Penjualan';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Sale::whereIn('kitchen_status', ['pending', 'cooking', 'ready'])
            ->whereDate('created_at', today())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $hasPending = Sale::where('kitchen_status', 'pending')
            ->whereDate('created_at', today())
            ->exists();

        return $hasPending ? 'warning' : 'info';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'kasir', 'super_admin', 'dapur']) ?? false;
    }

    // ── Livewire State ──────────────────────────────────────────
    public string $filter = 'active'; // 'active', 'pending', 'cooking', 'ready', 'completed'
    public string $search = '';
    public bool $soundEnabled = true;
    public ?int $lastKnownMaxSaleId = null;
    public ?array $selectedTicketForPrint = null;
    public array $checkedItems = []; // sale_item_id => bool

    public function mount(): void
    {
        $maxSale = Sale::max('id');
        $this->lastKnownMaxSaleId = $maxSale ? (int) $maxSale : 0;
    }

    /**
     * Polling method dipanggil setiap beberapa detik oleh wire:poll
     */
    public function checkNewOrders(): void
    {
        $currentMax = (int) (Sale::max('id') ?? 0);

        if ($this->lastKnownMaxSaleId !== null && $currentMax > $this->lastKnownMaxSaleId) {
            // Ada order baru masuk
            $newOrdersCount = Sale::where('id', '>', $this->lastKnownMaxSaleId)
                ->where('kitchen_status', 'pending')
                ->count();

            if ($newOrdersCount > 0 && $this->soundEnabled) {
                $this->dispatch('play-kitchen-bell');
                Notification::make()
                    ->title("Ada {$newOrdersCount} Pesanan Baru Masuk ke Dapur!")
                    ->warning()
                    ->send();
            }

            $this->lastKnownMaxSaleId = $currentMax;
        }
    }

    public function setFilter(string $status): void
    {
        $this->filter = $status;
    }

    public function toggleSound(): void
    {
        $this->soundEnabled = ! $this->soundEnabled;
    }

    /**
     * Aksi 1: Mulai Masak / Proses Orderan
     */
    public function startCooking(int $saleId): void
    {
        $sale = Sale::findOrFail($saleId);
        $sale->update([
            'kitchen_status'     => 'cooking',
            'cooking_started_at' => now(),
        ]);

        Notification::make()
            ->title("Pesanan {$sale->invoice_number} mulai dimasak")
            ->info()
            ->send();
    }

    /**
     * Aksi 2: Tandai Siap Saji
     */
    public function markReady(int $saleId): void
    {
        $sale = Sale::findOrFail($saleId);
        $sale->update([
            'kitchen_status'      => 'ready',
            'cooking_finished_at' => now(),
        ]);

        $this->dispatch('play-ready-bell');

        Notification::make()
            ->title("Pesanan {$sale->invoice_number} SIAP SAJI!")
            ->success()
            ->send();
    }

    /**
     * Aksi 3: Tandai SEMUA SELESAI (ALL DONE / FULL SELESAI)
     */
    public function markCompleted(int $saleId): void
    {
        $sale = Sale::findOrFail($saleId);
        $sale->update([
            'kitchen_status'        => 'completed',
            'kitchen_completed_at'  => now(),
        ]);

        $this->dispatch('play-success-sound');

        Notification::make()
            ->title("Pesanan {$sale->invoice_number} FULL SELESAI (ALL DONE)!")
            ->body('Seluruh menu telah selesai disajikan ke pelanggan.')
            ->success()
            ->send();
    }

    /**
     * Rollback status jika salah klik
     */
    public function rollbackStatus(int $saleId, string $targetStatus): void
    {
        $sale = Sale::findOrFail($saleId);
        $sale->update([
            'kitchen_status' => $targetStatus,
        ]);

        Notification::make()
            ->title("Status pesanan {$sale->invoice_number} dikembalikan ke: " . ucfirst($targetStatus))
            ->info()
            ->send();
    }

    /**
     * Cetak Tiket Bon Dapur (Kitchen Order Ticket / KOT)
     */
    public function printKitchenTicket(int $saleId): void
    {
        $sale = Sale::with(['items', 'cashier'])->findOrFail($saleId);

        // Tandai sudah diprint di dapur
        $sale->update(['kitchen_printed_at' => now()]);

        $this->selectedTicketForPrint = [
            'id'                 => $sale->id,
            'invoice_number'     => $sale->invoice_number,
            'table_number'       => $sale->table_number ?: 'Tanpa Meja (Bungkus/Umum)',
            'customer_name'      => $sale->customer_name ?: 'Pelanggan Umum',
            'cashier_name'       => $sale->cashier?->name ?: 'Kasir',
            'order_notes'        => $sale->order_notes,
            'time'               => $sale->sold_at ? $sale->sold_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
            'printed_at'         => now()->format('H:i'),
            'kitchen_status'     => $sale->kitchen_status,
            'items'              => $sale->items->map(fn($item) => [
                'id'       => $item->id,
                'name'     => $item->product_name,
                'qty'      => $item->quantity,
                'notes'    => $item->notes,
            ])->toArray(),
        ];

        // Trigger browser print dialog untuk struk dapur
        $this->dispatch('print-kitchen-ticket');
    }

    public function closePrintTicketModal(): void
    {
        $this->selectedTicketForPrint = null;
    }

    /**
     * Toggle checklist item di dapur
     */
    public function toggleItemCheck(int $itemId): void
    {
        $this->checkedItems[$itemId] = ! ($this->checkedItems[$itemId] ?? false);
    }

    /**
     * Ambil data pesanan sesuai filter yang aktif
     */
    public function getOrdersProperty()
    {
        $query = Sale::with(['items', 'cashier'])
            ->whereDate('created_at', today());

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                  ->orWhere('customer_name', 'like', "%{$this->search}%")
                  ->orWhere('table_number', 'like', "%{$this->search}%")
                  ->orWhere('order_notes', 'like', "%{$this->search}%");
            });
        }

        switch ($this->filter) {
            case 'pending':
                $query->where('kitchen_status', 'pending')->orderBy('id', 'asc');
                break;
            case 'cooking':
                $query->where('kitchen_status', 'cooking')->orderBy('cooking_started_at', 'asc');
                break;
            case 'ready':
                $query->where('kitchen_status', 'ready')->orderBy('cooking_finished_at', 'desc');
                break;
            case 'completed':
                $query->where('kitchen_status', 'completed')->orderBy('kitchen_completed_at', 'desc');
                break;
            case 'active':
            default:
                $query->whereIn('kitchen_status', ['pending', 'cooking', 'ready'])
                    ->orderByRaw("CASE 
                        WHEN kitchen_status = 'pending' THEN 1 
                        WHEN kitchen_status = 'cooking' THEN 2 
                        WHEN kitchen_status = 'ready' THEN 3 
                        ELSE 4 END")
                    ->orderBy('id', 'asc');
                break;
        }

        return $query->get();
    }

    /**
     * Metrics / Ringkasan Pesanan Hari Ini
     */
    public function getMetricsProperty(): array
    {
        $today = today();

        return [
            'pending'   => Sale::whereDate('created_at', $today)->where('kitchen_status', 'pending')->count(),
            'cooking'   => Sale::whereDate('created_at', $today)->where('kitchen_status', 'cooking')->count(),
            'ready'     => Sale::whereDate('created_at', $today)->where('kitchen_status', 'ready')->count(),
            'completed' => Sale::whereDate('created_at', $today)->where('kitchen_status', 'completed')->count(),
            'active'    => Sale::whereDate('created_at', $today)->whereIn('kitchen_status', ['pending', 'cooking', 'ready'])->count(),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'orders'  => $this->orders,
            'metrics' => $this->metrics,
        ];
    }
}
