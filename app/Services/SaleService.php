<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class SaleService
{
    protected StockService $stockService;
    protected CommissionService $commissionService;
    protected ConsignorBalanceService $balanceService;

    public function __construct(
        StockService $stockService,
        CommissionService $commissionService,
        ConsignorBalanceService $balanceService
    ) {
        $this->stockService = $stockService;
        $this->commissionService = $commissionService;
        $this->balanceService = $balanceService;
    }

    /**
     * Memproses transaksi penjualan POS kasir secara aman (DB Transaction).
     *
     * @param array $saleData Data penjualan: customer_name, discount, tax, paid_amount, payment_method
     * @param array $cartItems Array item: [['product_id' => int, 'quantity' => int, 'custom_price' => ?float]]
     * @param int $cashierId ID User kasir
     */
    public function createSale(array $saleData, array $cartItems, int $cashierId): Sale
    {
        if (empty($cartItems)) {
            throw new \InvalidArgumentException('Keranjang transaksi kasir tidak boleh kosong.');
        }

        return DB::transaction(function () use ($saleData, $cartItems, $cashierId) {
            // 1. Hitung total subtotal
            $subtotal = 0;
            $itemsToProcess = [];

            foreach ($cartItems as $item) {
                $product = Product::with('consignor')->findOrFail($item['product_id']);
                $qty = (int) ($item['quantity'] ?? 1);

                if ($qty <= 0) {
                    throw new \InvalidArgumentException("Jumlah produk {$product->name} harus lebih dari 0.");
                }

                if ($product->stock < $qty) {
                    throw new \Exception("Stok tidak mencukupi untuk {$product->name}. Sisa stok: {$product->stock}");
                }

                $sellingPrice = isset($item['custom_price']) ? (float) $item['custom_price'] : (float) $product->selling_price;
                $lineSubtotal = $sellingPrice * $qty;
                $subtotal += $lineSubtotal;

                // Hitung komisi dan hak penitip
                $commResult = $this->commissionService->calculate(
                    $sellingPrice,
                    $product->commission_type,
                    (float) $product->commission_value
                );

                $itemsToProcess[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'purchase_price' => (float) $product->purchase_price,
                    'selling_price' => $sellingPrice,
                    'subtotal' => $lineSubtotal,
                    'consignor_unit_amount' => $commResult['consignor_amount'],
                    'commission_unit_amount' => $commResult['store_commission'],
                    'consignor_total_amount' => $commResult['consignor_amount'] * $qty,
                    'commission_total_amount' => $commResult['store_commission'] * $qty,
                ];
            }

            $discount = (float) ($saleData['discount'] ?? 0);
            $tax = (float) ($saleData['tax'] ?? 0);
            $total = max(0, $subtotal - $discount + $tax);
            $paidAmount = (float) ($saleData['paid_amount'] ?? $total);
            $changeAmount = max(0, $paidAmount - $total);

            // 2. Buat header Sale
            $sale = Sale::create([
                'invoice_number' => Sale::generateInvoiceNumber(),
                'cashier_id' => $cashierId,
                'customer_name' => $saleData['customer_name'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $saleData['payment_method'] ?? 'cash',
                'status' => 'completed',
                'sold_at' => now(),
            ]);

            // 3. Simpan detail items, kurangi stok, dan catat hak saldo penitip
            foreach ($itemsToProcess as $processed) {
                /** @var Product $product */
                $product = $processed['product'];
                $qty = $processed['quantity'];

                // Buat item transaksi (snapshot harga & komisi)
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'purchase_price' => $processed['purchase_price'],
                    'selling_price' => $processed['selling_price'],
                    'subtotal' => $processed['subtotal'],
                    'consignor_amount' => $processed['consignor_total_amount'],
                    'commission_amount' => $processed['commission_total_amount'],
                ]);

                // Kurangi stok dan catat stock movement
                $this->stockService->recordStockMovement(
                    $product,
                    $qty,
                    'out',
                    'sale',
                    $sale->id,
                    "Penjualan Kasir No. {$sale->invoice_number}",
                    $cashierId
                );

                // Tambahkan pendapatan ke saldo penitip pemilik barang
                if ($product->consignor) {
                    $this->balanceService->recordSale(
                        $product->consignor,
                        $processed['subtotal'],
                        $processed['commission_total_amount'],
                        $processed['consignor_total_amount']
                    );
                }
            }

            return $sale->load(['items', 'cashier']);
        });
    }
}

