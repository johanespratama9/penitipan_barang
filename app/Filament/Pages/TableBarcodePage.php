<?php

namespace App\Filament\Pages;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class TableBarcodePage extends Page
{
    protected string $view = 'filament.pages.table-barcode-page';

    protected static ?string $slug = 'cetak-barcode-meja';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-qr-code';
    }

    public static function getNavigationLabel(): string
    {
        return 'Cetak Barcode Meja';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Cetak Barcode & QR Code Meja (Menu Digital)';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Penjualan';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'kasir', 'super_admin']) ?? false;
    }

    // Livewire state untuk generator meja
    public string $mode = 'range'; // 'range' atau 'custom'
    public int $startNumber = 1;
    public int $endNumber = 8;
    public string $prefix = 'Meja ';
    public bool $padZero = true; // e.g. Meja 01 vs Meja 1
    public string $customTables = "Meja 1, Meja 2, Meja 3, Meja 4, Meja 5, Meja 6, VIP 1, VIP 2";
    public string $storeName = 'SARINAH STREET';
    public string $subtitle = 'Buku Menu Digital';
    public string $noticeText = 'Scan barcode untuk melihat menu digital. Pemesanan langsung ke kasir.';
    public string $cardTheme = 'amber'; // 'amber' (mewah) atau 'mono' (hitam putih)
    public string $cardSize = 'medium'; // 'small' (6/A4), 'medium' (4/A4), 'large' (2/A4)

    /**
     * Set preset nomor meja cepat
     */
    public function setPreset(int $end): void
    {
        $this->mode = 'range';
        $this->startNumber = 1;
        $this->endNumber = $end;
    }

    /**
     * Dapatkan daftar meja dan QR code hasil render
     */
    public function getTableListProperty(): array
    {
        $tables = [];

        if ($this->mode === 'range') {
            $start = max(1, $this->startNumber);
            $end = max($start, min(100, $this->endNumber));

            for ($i = $start; $i <= $end; $i++) {
                $numStr = $this->padZero ? sprintf('%02d', $i) : (string) $i;
                $tableName = trim($this->prefix . $numStr);
                $tables[] = $tableName;
            }
        } else {
            // Mode custom
            $rawList = preg_split('/[\r\n,]+/', $this->customTables);
            foreach ($rawList as $item) {
                $cleaned = trim($item);
                if (! empty($cleaned)) {
                    $tables[] = $cleaned;
                }
            }
        }

        if (empty($tables)) {
            $tables = ['Meja 01'];
        }

        // Generate data setiap kartu meja
        $result = [];
        $qrOptions = new QROptions();
        $qrOptions->outputType = QRCode::OUTPUT_MARKUP_SVG;
        $qrOptions->imageBase64 = true;
        $qr = new QRCode($qrOptions);

        $baseUrl = url('/menu');

        foreach ($tables as $index => $tableName) {
            $menuUrl = $baseUrl . '?meja=' . urlencode($tableName);
            $qrDataUri = '';

            try {
                $qrDataUri = $qr->render($menuUrl);
            } catch (\Throwable $e) {
                $qrDataUri = '';
            }

            $result[] = [
                'index' => $index + 1,
                'name' => $tableName,
                'url' => $menuUrl,
                'qr_code' => $qrDataUri,
                'code_text' => 'TBL-' . strtoupper(substr(md5($tableName), 0, 5)),
            ];
        }

        return $result;
    }

    public function generate(): void
    {
        Notification::make()
            ->title('Barcode Meja Siap')
            ->body(count($this->getTableListProperty()) . ' kartu meja siap dicetak.')
            ->success()
            ->send();
    }
}
