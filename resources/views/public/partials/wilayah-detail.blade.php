<aside data-reveal style="--reveal-delay: 120ms"
    class="min-w-0 border-t border-white/15 pt-7 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-10"
    aria-label="Data wilayah terpilih">
    <p class="sr-only" aria-live="polite"
        x-text="`${region.label}: ${$angka(region.kth)} kelompok tani hutan, ${$angka(region.anggota_kth)} anggota, ${$angka(region.ps)} kelompok perhutanan sosial, nilai transaksi ekonomi ${$rupiah(region.nilai_transaksi_ekonomi)}.`"></p>

    <div>
        <p class="text-xs font-semibold tracking-[0.12em] text-white/65 uppercase">Data wilayah terpilih</p>
        <h3 class="mt-2 font-display text-3xl leading-tight" x-text="region.label">{{ $regions['trenggalek']['label'] }}</h3>

        <dl class="mt-6 divide-y divide-white/15 border-t border-white/15">
            @foreach ([
                ['key' => 'kth', 'label' => 'Kelompok Tani Hutan'],
                ['key' => 'anggota_kth', 'label' => 'Anggota KTH'],
                ['key' => 'ps', 'label' => 'Kelompok Perhutanan Sosial'],
                ['key' => 'ps_kk', 'label' => 'KK Perhutanan Sosial'],
            ] as $item)
                <div class="flex items-baseline justify-between gap-5 py-3">
                    <dt class="text-sm text-white/70">{{ $item['label'] }}</dt>
                    <dd class="font-display text-xl tabular-nums" x-text="$angka(region.{{ $item['key'] }})"
                        :class="metric === '{{ $item['key'] }}' ? 'text-leaf' : 'text-white'">
                        {{ number_format($regions['trenggalek'][$item['key']], 0, ',', '.') }}
                    </dd>
                </div>
            @endforeach
            <div class="flex items-baseline justify-between gap-5 py-3">
                <dt class="text-sm text-white/70">Nilai transaksi ekonomi KTH {{ $tahun }}</dt>
                <dd class="shrink-0 font-display text-xl whitespace-nowrap tabular-nums" x-text="$rupiah(region.nilai_transaksi_ekonomi)"
                    :class="metric === 'nilai_transaksi_ekonomi' ? 'text-leaf' : 'text-white'">
                    {{ rupiah_singkat($regions['trenggalek']['nilai_transaksi_ekonomi']) }}
                </dd>
            </div>
        </dl>
    </div>

    <div id="capaian" class="mt-8 scroll-mt-24 border-t border-white/20 pt-7">
        <h3 class="font-display text-2xl">Capaian kegiatan <span class="text-sm font-sans text-white/65">{{ $tahun }} · seluruh wilayah</span></h3>
        <dl class="mt-4 divide-y divide-white/15 border-t border-white/15">
            @foreach ($capaianItems as $item)
                <div class="flex items-baseline justify-between gap-4 py-3">
                    <dt class="text-sm text-white/70">
                        {{ $item['label'] }}
                        @if ($item['note'])
                            <span class="mt-1 block text-xs text-white/60">{{ $item['note'] }}</span>
                        @endif
                    </dt>
                    <dd class="shrink-0 text-right font-display text-xl tabular-nums">
                        {{ number_format($item['value'], $item['decimals'], ',', '.') }}
                        <span class="text-xs text-white/65">{{ $item['unit'] }}</span>
                    </dd>
                </div>
            @endforeach
        </dl>
    </div>
</aside>
