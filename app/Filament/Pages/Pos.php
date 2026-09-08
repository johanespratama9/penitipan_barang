<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Product;
use App\Services\SaleService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Pos extends Page
{
    protected string $view = 'filament.pages.pos';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-computer-desktop';
    }

    public static function getNavigationLabel(): string
    {
        return 'POS / Kasir';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Point of Sale (POS Kasir)';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Penjualan';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'kasir', 'super_admin']) ?? false;
    }

    // Livewire state
    public string $search = '';
    public string $barcode = '';
    public string $customer_name = '';
    public ?int $selectedCategory = null;
    public string $mobileView = 'catalog'; // 'catalog' atau 'cart'
    public array $cart = [];
    public float $discount = 0;
    public float $paid_amount = 0;
    public string $payment_method = 'cash';
    public ?array $last_sale = null;

    public function mount(): void
    {
        $this->cart = [];
    }

    public function addToCart(int $productId): void
    {
        $product = Product::available()->find($productId);

        if (!$product) {
            Notification::make()->title('Produk tidak tersedia atau stok habis')->danger()->send();
            return;
        }

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] >= $product->stock) {
                Notification::make()->title("Stok maksimal tercapai ({$product->stock})")->warning()->send();
                return;
            }
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'price' => (float) $product->selling_price,
                'stock' => (int) $product->stock,
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
            Notification::make()->title("{$product->name} ditambahkan ke keranjang!")->success()->send();
        } else {
            $this->dispatch('play-error-beep');
            Notification::make()->title("Produk dengan barcode/kode '{$code}' tidak ditemukan")->danger()->send();
        }
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        if (!isset($this->cart[$productId])) return;

        $product = Product::find($productId);
        if (!$product) return;

        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        if ($quantity > $product->stock) {
            Notification::make()->title("Stok tidak mencukupi (Tersedia: {$product->stock})")->warning()->send();
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
        $this->cart = [];
        $this->discount = 0;
        $this->paid_amount = 0;
        $this->customer_name = '';
        $this->last_sale = null;
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->getSubtotalProperty();
        $total = max(0, $subtotal - $this->discount);
        if ($this->paid_amount < $total && $this->payment_method === 'cash') {
            $this->paid_amount = $total;
        }
    }

    public function getSubtotalProperty(): float
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
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

    public function checkout(): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('Keranjang transaksi masih kosong!')->warning()->send();
            return;
        }

        $total = $this->getTotalProperty();
        if ($this->payment_method === 'cash' && $this->paid_amount < $total) {
            Notification::make()->title('Jumlah uang yang dibayarkan kurang!')->danger()->send();
            return;
        }

        try {
            $cartItems = [];
            foreach ($this->cart as $item) {
                $cartItems[] = [
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'custom_price' => $item['price'],
                ];
            }

            $saleService = app(SaleService::class);
            $sale = $saleService->createSale(
                [
                    'customer_name' => $this->customer_name ?: 'Pelanggan Umum',
                    'discount' => $this->discount,
                    'paid_amount' => $this->payment_method === 'cash' ? $this->paid_amount : $total,
                    'payment_method' => $this->payment_method,
                ],
                $cartItems,
                auth()->id() ?? 1
            );

            $this->last_sale = [
                'invoice_number' => $sale->invoice_number,
                'total' => $sale->total,
                'paid_amount' => $sale->paid_amount,
                'change_amount' => $sale->change_amount,
                'payment_method' => $sale->payment_method,
                'time' => $sale->sold_at->format('d M Y, H:i'),
                'items' => $sale->items->map(fn ($i) => [
                    'name' => $i->product_name,
                    'qty' => $i->quantity,
                    'subtotal' => $i->subtotal,
                ])->toArray(),
            ];

            $this->dispatch('play-success-sound');

            Notification::make()
                ->title('Transaksi Berhasil!')
                ->body("Invoice {$sale->invoice_number} berhasil diterbitkan.")
                ->success()
                ->send();

            // Reset cart
            $this->cart = [];
            $this->discount = 0;
            $this->paid_amount = 0;
            $this->customer_name = '';

        } catch (\Exception $e) {
            Notification::make()
                ->title('Transaksi Gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

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

        if (!empty($this->search)) {
            $q->where(function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%")
                    ->orWhere('barcode', 'like', "%{$this->search}%");
            });
        }

        return $q->take(30)->get();
    }
}
