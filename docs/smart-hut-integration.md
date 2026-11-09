# Integrasi Data SMART-HUT

Angka kehutanan di beranda (statistik hero, peta wilayah kerja, dan Capaian tahun berjalan) diambil dari
**SMART-HUT** (Sistem Informasi Data Kehutanan, repo `kda`), bukan dari CSV di `public/data`.

## Alur data

```
SMART-HUT  ──GET /api/public/cdk/{cdk}/ringkasan──▶  SmartHutService  ──▶  beranda
                                                     │ cache 10 menit
                                                     ├ gagal? pakai respons terakhir yang berhasil
                                                     └ belum pernah berhasil? pakai snapshot bawaan
```

- `App\Services\SmartHutService` mengambil ringkasan, menyimpannya di cache (`SMART_HUT_CACHE_TTL`, default
  600 detik), lalu mengelompokkan kabupaten/kota menjadi tiga wilayah di situs:
  Trenggalek (`3503`), Tulungagung (`3504`), dan Kediri (`3506` Kabupaten + `3571` Kota).
- Jika SMART-HUT tidak dapat diakses, situs tetap tampil dengan respons terakhir yang berhasil. Jika belum
  pernah berhasil, situs memakai `resources/data/smart-hut/ringkasan.json`.
- Halaman menampilkan sumber dan tanggal pembaruan; label "salinan" muncul jika data berasal dari snapshot.

## Konfigurasi (`.env`)

| Variabel | Isi |
|---|---|
| `SMART_HUT_URL` | URL dasar SMART-HUT, mis. `https://smarthut.example.go.id`. Kosong = pakai snapshot. |
| `SMART_HUT_TOKEN` | Token Bearer jika endpoint dilindungi (opsional). |
| `SMART_HUT_CDK_ID` | ID CDK di tabel `m_cdk` SMART-HUT. CDK Wilayah Trenggalek = `3`. |
| `SMART_HUT_CACHE_TTL` | Lama cache dalam detik (default `600`). |

## Kontrak endpoint (perlu dibuat di SMART-HUT)

`GET /api/public/cdk/{cdk_id}/ringkasan` → `200 application/json`

Semua angka hanya dari data berstatus `final` dan belum dihapus (`deleted_at IS NULL`), difilter `cdk_id`.

```json
{
  "cdk": { "id": 3, "nama": "CDK Wilayah Trenggalek" },
  "tahun": 2026,
  "diperbarui": "2026-09-10T01:34:22+07:00",
  "wilayah": [
    {
      "regency_id": "3503",
      "nama": "KABUPATEN TRENGGALEK",
      "kth": 90,
      "anggota_kth": 3908,
      "ps": 16,
      "ps_kk": 14926,
      "nilai_transaksi_ekonomi": 96521266978
    }
  ],
  "capaian": {
    "rehab_lahan": { "realisasi": 10, "target": 49 },
    "penghijauan": { "realisasi": 35, "target": 0 },
    "wisata": { "pengunjung": 331229, "pendapatan": 5251533000 },
    "kebakaran": { "kejadian": 23, "luas": 40.4 }
  }
}
```

`wilayah` berisi satu baris per kabupaten/kota di `cdk_regency` untuk CDK tersebut.

| Field | Sumber di SMART-HUT |
|---|---|
| `kth`, `anggota_kth` | `perkembangan_kth`: `count(*)`, `sum(jumlah_anggota)` |
| `ps`, `ps_kk` | `skps`: `count(*)`, `sum(number_of_kk)` |
| `nilai_transaksi_ekonomi` | `nilai_transaksi_ekonomi`: `sum(total_nilai_transaksi)` untuk `tahun` |
| `capaian.rehab_lahan` | `rehab_lahan`: `sum(realization)`, `sum(target_annual)` untuk `tahun` (ha) |
| `capaian.penghijauan` | `penghijauan_lingkungan`: `sum(realization)`, `sum(target_annual)` untuk `tahun` (ha) |
| `capaian.wisata` | `pengunjung_wisata`: `sum(number_of_visitors)`, `sum(gross_income)` untuk `tahun` |
| `capaian.kebakaran` | `kebakaran_hutan`: `sum(number_of_fires)`, `sum(fire_area)` untuk `tahun` (ha) |
| `diperbarui` | `max(updated_at)` dari tabel-tabel di atas |

Saran implementasi di SMART-HUT: rute di `routes/api.php` tanpa login (atau dengan token Sanctum
khusus), `Cache::remember` 5–10 menit seperti dashboard publik, dan `throttle` untuk membatasi permintaan.

## Memperbarui snapshot

Snapshot `resources/data/smart-hut/ringkasan.json` dibuat dari database SMART-HUT lokal (data final
CDK 3, tahun 2026, diperbarui 10 September 2026). Setelah endpoint tersedia, perbarui snapshot dengan
menyimpan respons endpoint ke file tersebut.
