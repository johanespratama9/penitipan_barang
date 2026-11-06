<?php

namespace App\Livewire;

use App\Models\Sale;
use App\Models\SaleItem;
use Livewire\Component;

class KitchenDisplay extends Component
{
    // ── State ──────────────────────────────────────────────────
    public string $filter = 'active'; // 'active', 'pending', 'cooking', 'ready', 'completed'
    public string $search = '';
    public bool $soundEnabled = true;
    public ?int $lastKnownMaxSaleId = null;
    public ?array $selectedTicketForPrint = null;
    public array $checkedItems = []; // sale_item_id => bool

    public function mount(): void
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'kasir', 'super_admin', 'dapur'])) {
            abort(403, 'Akses ditolak.');
        }

        $maxSale = Sale::max('id');
        $this->lastKnownMaxSaleId = $maxSale ? (int) $maxSale : 0;
    }

    /**
     * Polling method dipanggil setiap 4 detik
     */
    public function checkNewOrders(): void
    {
        $currentMax = (int) (Sale::max('id') ?? 0);

        if ($this->lastKnownMaxSaleId !== null && $currentMax > $this->lastKnownMaxSaleId) {
            $newOrdersCount = Sale::where('id', '>', $this->lastKnownMaxSaleId)
                ->where('kitchen_status', 'pending')
                ->count();

            if ($newOrdersCount > 0 && $this->soundEnabled) {
                $this->dispatch('play-kitchen-bell');
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

        $this->dispatch('notify', type: 'info', message: "Pesanan {$sale->invoice_number} mulai dimasak");
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
        $this->dispatch('notify', type: 'success', message: "Pesanan {$sale->invoice_number} SIAP SAJI!");
    }

    /**
     * Aksi 3: Tandai SEMUA SELESAI (ALL DONE / FULL SELESAI)
     */
    public function markCompleted(int $saleId): void
    {
        $sale = Sale::findOrFail($saleId);
        $sale->update([
            'kitchen_status'       => 'completed',
            'kitchen_completed_at' => now(),
        ]);

        $this->dispatch('play-success-sound');
        $this->dispatch('notify', type: 'success', message: "Pesanan {$sale->invoice_number} FULL SELESAI (ALL DONE)!");
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

        $this->dispatch('notify', type: 'info', message: "Status pesanan {$sale->invoice_number} dikembalikan ke " . ucfirst($targetStatus));
    }

    /**
     * Cetak Tiket Bon Dapur (Kitchen Order Ticket / KOT)
     */
    public function printKitchenTicket(int $saleId): void
    {
        $sale = Sale::with(['items', 'cashier'])->findOrFail($saleId);
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

        $this->dispatch('print-kitchen-ticket');
    }

    public function closePrintTicketModal(): void
    {
        $this->selectedTicketForPrint = null;
    }

    public function toggleItemCheck(int $itemId): void
    {
        $this->checkedItems[$itemId] = ! ($this->checkedItems[$itemId] ?? false);
    }

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

    public function render()
    {
        return view('livewire.kitchen-display', [
            'orders'  => $this->orders,
            'metrics' => $this->metrics,
        ])->layout('layouts.pos', ['title' => 'Layar Dapur (KDS) – Sarinah Street']);
    }
}

