<?php

namespace App\Console\Commands;

use App\Models\Consignment;
use Illuminate\Console\Command;

class CheckExpiredConsignments extends Command
{
    protected $signature = 'consignments:check-expired';
    protected $description = 'Periksa dan tandai dokumen penitipan barang yang telah melewati batas tanggal kedaluwarsa';

    public function handle(): int
    {
        $expiredConsignments = Consignment::where('expiry_date', '<', today())
            ->whereIn('status', ['received', 'approved'])
            ->get();

        $count = 0;
        foreach ($expiredConsignments as $consignment) {
            $consignment->update([
                'status' => 'expired',
                'notes' => ($consignment->notes ? $consignment->notes . "\n" : '') . 'Otomatis ditandai kedaluwarsa oleh sistem pada ' . now()->format('Y-m-d H:i'),
            ]);
            $count++;
        }

        $this->info("Pemeriksaan selesai. {$count} dokumen penitipan ditandai kedaluwarsa.");

        return Command::SUCCESS;
    }
}

