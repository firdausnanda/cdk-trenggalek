<script>
    document.documentElement.classList.add('js');

    // Accessibility panel preferences (partials/aksesibilitas), applied before first paint.
    window.terapkanAksesibilitas = function (pref) {
        var classes = document.documentElement.classList;
        var toggles = { kontras: 'a11y-kontras', spasi: 'a11y-spasi', garisTautan: 'a11y-garis-tautan', tanpaAnimasi: 'a11y-tanpa-animasi', fontBaca: 'a11y-font-baca' };
        classes.toggle('a11y-teks-115', pref.teks === 115);
        classes.toggle('a11y-teks-130', pref.teks === 130);
        Object.keys(toggles).forEach(function (key) { classes.toggle(toggles[key], !!pref[key]); });
    };
    try { window.terapkanAksesibilitas(JSON.parse(localStorage.getItem('cdk-aksesibilitas')) || {}); } catch (e) {}
</script>
