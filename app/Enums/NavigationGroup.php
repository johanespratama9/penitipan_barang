<?php

namespace App\Enums;

enum NavigationGroup: string
{
    case MasterData = 'Master Data';
    case Consignment = 'Consignment';
    case Penjualan = 'Penjualan';
    case Keuangan = 'Keuangan';
    case Laporan = 'Laporan';
    case Pengaturan = 'Pengaturan';
}
