# Integrasi Data SMART-HUT

Angka kehutanan di beranda (statistik hero, peta wilayah kerja, dan Capaian tahun berjalan) diambil dari
**SMART-HUT** (Sistem Informasi Data Kehutanan, repo `kda`), bukan dari CSV di `public/data`.

## Alur data

```
SMART-HUT /api/v1  ──▶  php artisan smart-hut:sync (terjadwal tiap 10 menit)  ──▶  cache  ──▶  beranda
                         ├ gagal? ringkasan tersimpan sebelumnya tetap dipakai
                         └ belum pernah berhasil? beranda pakai snapshot bawaan
```

SMART-HUT bisa lambat, jadi halaman **tidak pernah** memanggilnya langsung. Perintah `smart-hut:sync`
(dijadwalkan di `routes/console.php`, dijalankan container `scheduler`) memanggil
`App\Services\SmartHutService::sync()`, yang membaca tiga endpoint rekap. Semua angka dihitung di SMART-HUT dari
data **final**, dibatasi ke CDK pemilik token. Tahunnya adalah tahun terakhir yang punya data final, bukan tahun
kalender. Kabupaten/kota dikelompokkan menjadi tiga wilayah situs: Trenggalek (`3503`), Tulungagung (`3504`), dan
Kediri (`3506` Kabupaten + `3571` Kota).

| Angka di beranda | Endpoint | Field |
|---|---|---|
| KTH, anggota KTH (satu catatan terbaru per nomor register) | `pemberdayaan/rekap` | `kth.jumlah`, `kth.jumlah_anggota`, `per_kabupaten[].kth` |
| Kelompok PS, KK | `pemberdayaan/rekap` | `perhutanan_sosial.jumlah_kelompok`, `.jumlah_kk`, `per_kabupaten[].perhutanan_sosial` |
| Nilai transaksi ekonomi + periode | `pemberdayaan/rekap` | `nilai_transaksi_ekonomi.total_rp`, `.periode`, `per_kabupaten[].nilai_transaksi_ekonomi_rp` |
| Rehabilitasi lahan (% dari target) | `rhl/rekap` | `rehab_lahan.realisasi_ha`, `.target_tahunan_ha`, `.persentase_realisasi`, `.periode` |
| Penghijauan | `rhl/rekap` | `penghijauan_lingkungan.realisasi_ha`, `.periode` |
| Wisata alam + pengelola yang melapor | `perlindungan/rekap` | `pengunjung_wisata.jumlah_pengunjung`, `.pendapatan_bruto_rp`, `.periode`, `.per_pengelola[]` |
| Kebakaran hutan | `perlindungan/rekap` | `kebakaran_hutan.jumlah_kejadian`, `.luas_terbakar_ha`, `.periode` |
| Tanggal pembaruan | ketiganya | `diperbarui_pada` terbaru |

Setiap angka diberi label periode bulan dari field `periode`, mis. "Jan–Sep 2026". Pengunjung wisata juga
menyebut pengelola yang melapor, karena tidak semua objek wisata melapor setiap tahun.

Gangguan koneksi, 5xx, dan 429 dicoba ulang hingga 3 kali. Jika satu endpoint tetap gagal, seluruh sinkronisasi
dibatalkan dan ringkasan sebelumnya tetap dipakai. Sebelum sinkronisasi pertama berhasil, beranda memakai snapshot
`resources/data/smart-hut/rekap.json` (respons ketiga endpoint rekap).

Token wajib punya ability `pemberdayaan.read`, `rhl.read`, dan `perlindungan.read`.

## Konfigurasi (`.env`)

| Variabel | Isi |
|---|---|
| `SMART_HUT_URL` | URL dasar SMART-HUT tanpa `/api/v1`: `https://smarthut.dishut.jatimprov.go.id`. Kosong = pakai snapshot. |
| `SMART_HUT_TOKEN` | Token Bearer klien integrasi. Simpan hanya di `.env`, jangan di repositori. |
| `SMART_HUT_TIMEOUT` | Batas waktu tiap permintaan dalam detik (default `20`). |

Setelah mengubah `.env`, jalankan `php artisan config:clear` lalu `php artisan smart-hut:sync` untuk langsung
memperbarui data tanpa menunggu jadwal.

## Memperbarui snapshot

Simpan bagian `data` dari respons `pemberdayaan/rekap`, `rhl/rekap`, dan `perlindungan/rekap` ke
`resources/data/smart-hut/rekap.json` dengan kunci `pemberdayaan`, `rhl`, dan `perlindungan`.
