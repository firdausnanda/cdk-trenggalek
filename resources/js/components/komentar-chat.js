// Chat-style news comments: send without reloading, reply with a quote, delete your own message,
// and report someone else's. Every action is a normal form too, so it still works without JavaScript.

const reduceMotion = () =>
    window.matchMedia("(prefers-reduced-motion: reduce)").matches ||
    document.documentElement.classList.contains("a11y-tanpa-animasi");

export default (opts) => ({
    jumlah: opts.jumlah,
    kosong: true,
    isi: "",
    mengirim: false,
    galat: "",
    pengumuman: "",
    pesanTerlihat: false,
    balasan: null,
    laporan: { buka: false, id: null, alasan: "spam", mengirim: false, galat: "" },
    maks: 1000,

    init() {
        this.kosong = !this.$refs.thread.querySelector("[data-komentar]");
        if (this.$refs.isi) {
            this.isi = this.$refs.isi.value;
            this.$nextTick(() => this.tumbuh(this.$refs.isi));
        }
    },

    get sisa() {
        return this.maks - this.isi.length;
    },

    tumbuh(el) {
        el.style.height = "auto";
        el.style.height = `${Math.min(el.scrollHeight, 192)}px`;
    },

    // Enter sends on devices with a keyboard and mouse; Shift+Enter (and Enter on phones) adds a line.
    tombolEnter(event) {
        if (event.shiftKey || event.isComposing || !window.matchMedia("(pointer: fine)").matches) return;
        event.preventDefault();
        this.kirim();
    },

    balas(id, nama, cuplikan) {
        this.balasan = { id, nama, cuplikan };
        this.$nextTick(() => {
            this.$refs.isi?.focus();
            this.$refs.form?.scrollIntoView({ block: "nearest", behavior: reduceMotion() ? "auto" : "smooth" });
        });
    },

    umumkan(teks) {
        this.pengumuman = teks;
        this.pesanTerlihat = true;
        clearTimeout(this._timer);
        this._timer = setTimeout(() => (this.pesanTerlihat = false), 3500);
    },

    async kirimForm(url, body) {
        const res = await fetch(url, {
            method: "POST",
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
            body,
            credentials: "same-origin",
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok) return data;

        let pesan = data.message || opts.t.gagal;
        if (data.errors) pesan = Object.values(data.errors).flat()[0] || pesan;
        if (res.status === 419) pesan = opts.t.sesiHabis;
        throw new Error(pesan);
    },

    async kirim() {
        if (this.mengirim || !this.isi.trim()) return;
        this.mengirim = true;
        this.galat = "";

        try {
            const data = await this.kirimForm(this.$refs.form.action, new FormData(this.$refs.form));
            this.tambah(data);
            this.isi = "";
            this.balasan = null;
            this.$nextTick(() => this.tumbuh(this.$refs.isi));
            this.umumkan(data.message);
        } catch (e) {
            this.galat = e.message;
        } finally {
            this.mengirim = false;
        }
    },

    tambah(data) {
        const thread = this.$refs.thread;
        const terakhir = [...thread.querySelectorAll("[data-tanggal]")].pop();

        if (!terakhir || terakhir.dataset.tanggal !== data.date) {
            const pemisah = this.$refs.pemisah.content.firstElementChild.cloneNode(true);
            pemisah.querySelector("span").textContent = data.date_label;
            thread.append(pemisah);
        }

        thread.insertAdjacentHTML("beforeend", data.html);
        const baru = thread.lastElementChild;
        if (data.status === "published") this.jumlah++;
        this.kosong = false;

        baru.classList.add("komentar-baru");
        baru.scrollIntoView({ block: "nearest", behavior: reduceMotion() ? "auto" : "smooth" });
    },

    async hapus(form) {
        if (!window.confirm(opts.t.konfirmasiHapus)) return;
        const item = form.closest("[data-komentar]");

        try {
            const data = await this.kirimForm(form.action, new FormData(form));
            this.buang(item);
            this.umumkan(data.message);
        } catch (e) {
            window.alert(e.message);
        }
    },

    // Remove a message, and its day divider when it was the last message of that day.
    buang(item) {
        if (!item) return;
        if (item.dataset.status === "published") this.jumlah = Math.max(0, this.jumlah - 1);

        const sebelum = item.previousElementSibling;
        const sesudah = item.nextElementSibling;
        item.remove();
        if (sebelum?.classList.contains("komentar-pemisah") && (!sesudah || sesudah.classList.contains("komentar-pemisah"))) {
            sebelum.remove();
        }
        this.kosong = !this.$refs.thread.querySelector("[data-komentar]");
    },

    bukaLaporan(id) {
        this.laporan = { buka: true, id, alasan: "spam", mengirim: false, galat: "" };
    },

    async kirimLaporan() {
        if (this.laporan.mengirim) return;
        this.laporan.mengirim = true;
        this.laporan.galat = "";

        const body = new FormData();
        body.append("_token", opts.token);
        body.append("reason", this.laporan.alasan);

        try {
            const data = await this.kirimForm(opts.urlLapor.replace("__ID__", this.laporan.id), body);
            if (data.hidden) this.buang(this.$refs.thread.querySelector(`[data-komentar="${this.laporan.id}"]`));
            this.laporan.buka = false;
            this.umumkan(data.message);
        } catch (e) {
            this.laporan.galat = e.message;
        } finally {
            this.laporan.mengirim = false;
        }
    },
});
