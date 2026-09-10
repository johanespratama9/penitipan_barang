<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Services\SaleService;
use Livewire\Component;

class PosTerminal extends Component
{
    // ── State ──────────────────────────────────────────────────
    public string  $search            = '';
    public string  $barcode           = '';
    public string  $customer_name     = '';
    public ?int    $selectedCategory  = null;
    public string  $mobileView        = 'catalog';
    public array   $cart              = [];
    public float   $discount          = 0;
    public float   $paid_amount       = 0;
    public string  $payment_method    = 'cash';
    public ?array  $last_sale         = null;
    public string  $orderNumber       = '1042';
    public bool    $isCheckoutModalOpen = false;

    public function mount(): void
    {
        // Hanya bisa diakses oleh role yang berwenang
        if (! auth()->user()?->hasAnyRole(['admin', 'kasir', 'super_admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $this->cart        = [];
        $this->orderNumber = (string) rand(1001, 9999);
    }

    // ── Checkout Modal ─────────────────────────────────────────
    public function openCheckoutModal(): void
    {
        if (empty($this->cart)) {
            $this->notify('warning', 'Keranjang transaksi masih kosong!');
            return;
        }
        $this->isCheckoutModalOpen = true;
    }

    public function closeCheckoutModal(): void
    {
        $this->isCheckoutModalOpen = false;
    }

    // ── Cart Actions ───────────────────────────────────────────
    public function addToCart(int $productId): void
    {
        $product = Product::available()->find($productId);

        if (! $product) {
            $this->notify('danger', 'Produk tidak tersedia atau stok habis.');
            return;
        }

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] >= $product->stock) {
                $this->notify('warning', "Stok maksimal tercapai ({$product->stock})");
                return;
            }
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id'       => $product->id,
                'code'     => $product->code,
                'name'     => $product->name,
                'price'    => (float) $product->selling_price,
                'stock'    => (int)   $product->stock,
                'image'    => $product->image,
                'quantity' => 1,
            ];
        }

        $this->calculateTotals();
    }

    public function scanBarcode(): void
    {
        $input = trim($this->barcode);
        if (empty($input)) return;

        $this->scanBarcodeDirect($input);
        $this->barcode = '';
    }

    public function scanBarcodeDirect(string $code): void
    {
        $code = trim($code);
        if (empty($code)) return;

        $product = Product::available()
            ->where(function ($q) use ($code) {
                $q->where('barcode', $code)->orWhere('code', $code);
            })
            ->first();

        if ($product) {
            $this->addToCart($product->id);
            $this->dispatch('play-beep');
            $this->notify('success', "{$product->name} ditambahkan ke keranjang!");
        } else {
            $this->dispatch('play-error-beep');
            $this->notify('danger', "Produk dengan barcode/kode '{$code}' tidak ditemukan.");
        }
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        if (! isset($this->cart[$productId])) return;

        $product = Product::find($productId);
        if (! $product) return;

        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        if ($quantity > $product->stock) {
            $this->notify('warning', "Stok tidak mencukupi (Tersedia: {$product->stock})");
            $this->cart[$productId]['quantity'] = $product->stock;
        } else {
            $this->cart[$productId]['quantity'] = $quantity;
        }

        $this->calculateTotals();
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
        $this->calculateTotals();
    }

    public function clearCart(): void
    {
        $this->cart         = [];
        $this->discount     = 0;
        $this->paid_amount  = 0;
        $this->customer_name = '';
        $this->last_sale    = null;
    }

    // ── Totals ─────────────────────────────────────────────────
    public function calculateTotals(): void
    {
        $subtotal = $this->getSubtotalProperty();
        $total    = max(0, $subtotal - $this->discount);

        if ($this->paid_amount < $total && $this->payment_method === 'cash') {
            $this->paid_amount = $total;
        }
    }

    public function getSubtotalProperty(): float
    {
        return array_sum(array_map(
            fn($item) => $item['price'] * $item['quantity'],
            $this->cart
        ));
    }

    public function getTotalProperty(): float
    {
        return max(0, $this->getSubtotalProperty() - $this->discount);
    }

    public function getTotalItemCountProperty(): int
    {
        return array_sum(array_column($this->cart, 'quantity'));
    }

    public function getChangeAmountProperty(): float
    {
        return max(0, $this->paid_amount - $this->getTotalProperty());
    }

    public function setPaidAmount(float $amount): void
    {
        $this->paid_amount = $amount;
    }

    public function resetFilters(): void
    {
        $this->search           = '';
        $this->selectedCategory = null;
    }

    // ── Cash Suggestions ───────────────────────────────────────
    public function getCashSuggestionsProperty(): array
    {
        $total = $this->getTotalProperty();
        if ($total <= 0) return [];

        $suggestions   = [$total];
        $denominations = [10000, 20000, 50000, 100000, 200000, 500000];

        foreach ($denominations as $d) {
            if ($d > $total && ! in_array($d, $suggestions)) {
                $suggestions[] = $d;
            }
        }

        $next10k = ceil($total / 10000) * 10000;
        if ($next10k > $total && ! in_array($next10k, $suggestions)) $suggestions[] = $next10k;

        $next50k = ceil($total / 50000) * 50000;
        if ($next50k > $total && ! in_array($next50k, $suggestions)) $suggestions[] = $next50k;

        sort($suggestions);
        return array_slice(array_unique($suggestions), 0, 4);
    }

    // ── Checkout ───────────────────────────────────────────────
    public function checkout(): void
    {
        if (empty($this->cart)) {
            $this->notify('warning', 'Keranjang transaksi masih kosong!');
            return;
        }

        $total = $this->getTotalProperty();

        if ($this->payment_method === 'cash' && $this->paid_amount < $total) {
            $this->notify('danger', 'Jumlah uang yang dibayarkan kurang!');
            return;
        }

        try {
            $cartItems = array_map(fn($item) => [
                'product_id'   => $item['id'],
                'quantity'     => $item['quantity'],
                'custom_price' => $item['price'],
            ], array_values($this->cart));

            $saleService = app(SaleService::class);
            $sale        = $saleService->createSale(
                [
                    'customer_name'  => $this->customer_name ?: 'Pelanggan Umum',
                    'discount'       => $this->discount,
                    'paid_amount'    => $this->payment_method === 'cash' ? $this->paid_amount : $total,
                    'payment_method' => $this->payment_method,
                ],
                $cartItems,
                auth()->id() ?? 1
            );

            $this->last_sale = [
                'invoice_number' => $sale->invoice_number,
                'total'          => $sale->total,
                'paid_amount'    => $sale->paid_amount,
                'change_amount'  => $sale->change_amount,
                'payment_method' => $sale->payment_method,
                'time'           => $sale->sold_at->format('d M Y, H:i'),
                'items'          => $sale->items->map(fn($i) => [
                    'name'     => $i->product_name,
                    'qty'      => $i->quantity,
                    'subtotal' => $i->subtotal,
                ])->toArray(),
            ];

            $this->dispatch('play-success-sound');

            $this->cart                = [];
            $this->discount            = 0;
            $this->paid_amount         = 0;
            $this->customer_name       = '';
            $this->isCheckoutModalOpen = false;
            $this->orderNumber         = (string) rand(1001, 9999);

            $this->notify('success', "Transaksi {$sale->invoice_number} berhasil!");

        } catch (\Exception $e) {
            $this->notify('danger', 'Transaksi Gagal: ' . $e->getMessage());
        }
    }

    // ── Data Queries ───────────────────────────────────────────
    public function getCategoriesProperty()
    {
        return Category::active()->orderBy('name')->get();
    }

    public function getAvailableProductsProperty()
    {
        $q = Product::available()->with('category');

        if ($this->selectedCategory) {
            $q->where('category_id', $this->selectedCategory);
        }

        if (! empty($this->search)) {
            $q->where(function ($query) {
                $query->where('name',    'like', "%{$this->search}%")
                      ->orWhere('code',    'like', "%{$this->search}%")
                      ->orWhere('barcode', 'like', "%{$this->search}%");
            });
        }

        return $q->take(40)->get();
    }

    // ── Notification Helper (Alpine-based, no Filament needed) ─
    protected function notify(string $type, string $message): void
    {
        $this->dispatch('pos-notify', type: $type, message: $message);
    }

    // ── Render ─────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.pos-terminal')
            ->layout('layouts.pos', [
                'title' => 'POS Kasir – Sarinah Street',
            ]);
    }
}

