import Alpine from "alpinejs";
import focus from "@alpinejs/focus";

// Motion is off when the OS asks for it or the visitor turns it off in the accessibility panel.
const reduceMotion = () =>
    window.matchMedia("(prefers-reduced-motion: reduce)").matches ||
    document.documentElement.classList.contains("a11y-tanpa-animasi");

// Numbers follow the page language (<html lang>, set from the visitor's language choice), matching
// the angka() and rupiah_singkat() PHP helpers.
const bahasa = document.documentElement.lang === "en" ? "en" : "id";
const NUMBER_LOCALE = { id: "id-ID", en: "en-US" }[bahasa];
const RUPIAH_UNITS = {
    id: [[1e12, "T"], [1e9, "M"], [1e6, "Jt"]],
    en: [[1e12, "T"], [1e9, "B"], [1e6, "M"]],
}[bahasa];

const formatNumber = (value, decimals) =>
    new Intl.NumberFormat(NUMBER_LOCALE, {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    }).format(value);

// Compact rupiah: 167704808505 → "Rp167,7 M" (id) or "Rp167.7 B" (en).
const formatRupiah = (value) => {
    for (const [unit, suffix] of RUPIAH_UNITS) {
        if (Math.abs(value) >= unit) return `Rp${formatNumber(value / unit, 1)} ${suffix}`;
    }
    return `Rp${formatNumber(value, 0)}`;
};

Alpine.magic("angka", () => (value, decimals = 0) => formatNumber(value, decimals));
Alpine.magic("rupiah", () => formatRupiah);

// Reveal [data-reveal] elements once they scroll into view.
const revealObserver = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("is-visible");
                revealObserver.unobserve(entry.target);
            }
        });
    },
    { rootMargin: "0px 0px -8% 0px" },
);
document.querySelectorAll("[data-reveal]").forEach((el) => revealObserver.observe(el));

// x-count="expression": counts up to the value the first time the element is visible,
// then tweens to the new value whenever the expression changes. data-decimals sets precision;
// data-format="rupiah" shows a compact rupiah amount instead.
Alpine.directive("count", (el, { expression }, { effect, evaluateLater, cleanup }) => {
    const getValue = evaluateLater(expression);
    const decimals = Number(el.dataset.decimals ?? 0);
    const rupiah = el.dataset.format === "rupiah";
    let current = 0;
    let target = 0;
    let frame;
    let seen = false;

    const render = (value) => (el.textContent = rupiah ? formatRupiah(value) : formatNumber(value, decimals));

    const run = () => {
        cancelAnimationFrame(frame);
        if (reduceMotion()) {
            current = target;
            render(target);
            return;
        }
        const from = current;
        const start = performance.now();
        const step = (now) => {
            const t = Math.min((now - start) / 1400, 1);
            current = from + (target - from) * (1 - Math.pow(1 - t, 4));
            render(current);
            if (t < 1) frame = requestAnimationFrame(step);
        };
        frame = requestAnimationFrame(step);
    };

    if (!reduceMotion()) render(0);

    const observer = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting) {
            seen = true;
            observer.disconnect();
            run();
        }
    });
    observer.observe(el);

    effect(() =>
        getValue((value) => {
            target = Number(value) || 0;
            if (seen) run();
        }),
    );

    cleanup(() => {
        observer.disconnect();
        cancelAnimationFrame(frame);
    });
});

// Subtle pointer parallax for hero backgrounds.
Alpine.data("parallax", (strength = 18) => ({
    style: "",
    move(event) {
        if (reduceMotion() || event.pointerType !== "mouse") return;
        const { innerWidth: w, innerHeight: h } = window;
        const x = (event.clientX / w - 0.5) * -strength;
        const y = (event.clientY / h - 0.5) * -strength;
        this.style = `transform: translate3d(${x}px, ${y}px, 0) scale(1.06)`;
    },
}));

// Accessibility panel (partials/aksesibilitas). Preferences live in this browser only and are
// applied to <html> by window.terapkanAksesibilitas, defined inline in the layout <head>.
const PREFERENSI_KEY = "cdk-aksesibilitas";
const PREFERENSI_AWAL = {
    teks: 100,
    kontras: false,
    spasi: false,
    garisTautan: false,
    tanpaAnimasi: false,
    fontBaca: false,
};

const bacaPreferensi = () => {
    try {
        return JSON.parse(localStorage.getItem(PREFERENSI_KEY)) || {};
    } catch {
        return {};
    }
};

Alpine.data("aksesibilitas", () => ({
    open: false,
    pref: { ...PREFERENSI_AWAL, ...bacaPreferensi() },
    init() {
        this.$watch("pref", (pref) => {
            window.terapkanAksesibilitas?.(pref);
            try {
                localStorage.setItem(PREFERENSI_KEY, JSON.stringify(pref));
            } catch {
                // Storage can be unavailable (private mode); the setting still applies to this page.
            }
            if (pref.tanpaAnimasi) document.querySelectorAll("[data-reveal]").forEach((el) => el.classList.add("is-visible"));
        });
    },
    get aktif() {
        return Object.keys(PREFERENSI_AWAL).filter((key) => this.pref[key] !== PREFERENSI_AWAL[key]).length;
    },
    aturUlang() {
        this.pref = { ...PREFERENSI_AWAL };
    },
}));

Alpine.plugin(focus);
window.Alpine = Alpine;
Alpine.start();
