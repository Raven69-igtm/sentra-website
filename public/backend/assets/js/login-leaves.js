/* Daun jatuh pada ilustrasi login SENTRA.
   --------------------------------------------------------------------------
   Menyiapkan elemen daun dan variabel CSS-nya: titik lahir, ukuran, warna
   hijau, opacity, tempo turun, arah angin, ayunan, putaran, dan delay.
   Gerakannya sendiri dijalankan CSS keyframes di login-motion.css, jadi
   skrip ini hanya bekerja sekali saat halaman dimuat (dan saat breakpoint /
   preferensi gerak berubah).

   Di layar lebar daun lahir dari tajuk pohon: daftar titik [x%, y%] pada
   kanvas ilustrasi dibaca dari atribut data-spawns, yang dihasilkan oleh
   scripts/build-login-tree-sentra.py dari tepi bawah tajuk.

   Dua panggung daun:
   - [data-login-leaves="hero"] : di dalam panggung ilustrasi, 8-12 daun.
   - [data-login-leaves="page"] : di belakang konten halaman untuk layar
                                  kecil, 4-6 daun.

   Keduanya pointer-events: none, jadi tidak pernah menghalangi form login.
   Tidak ada library eksternal: hanya JavaScript vanilla.
*/
(function () {
    "use strict";

    var heroHost = document.querySelector('[data-login-leaves="hero"]');
    var pageHost = document.querySelector('[data-login-leaves="page"]');

    if (!heroHost && !pageHost) {
        return;
    }

    var wide = window.matchMedia("(min-width: 992px)");
    var calm = window.matchMedia("(prefers-reduced-motion: reduce)");

    /* Bentuk daun: pangkal membulat, ujung runcing, dengan satu tulang daun
       utama dan tiga pasang tulang samping. Digambar sebagai SVG supaya tetap
       tajam di segala ukuran dan bisa diwarnai lewat currentColor. */
    var LEAF_SVG =
        '<svg class="login-leaf__shape" viewBox="0 0 32 32" aria-hidden="true" focusable="false">' +
        '<path fill="currentColor" d="M16 2.5C18.8 8.4 23.8 13.2 23.8 19.8 23.8 26.4 20.4 29.5 16 29.5 11.6 29.5 8.2 26.4 8.2 19.8 8.2 13.2 13.2 8.4 16 2.5Z"/>' +
        '<g class="login-leaf__veins" fill="none" stroke-linecap="round">' +
        '<path stroke-width=".9" d="M16 27.4 16 5.4"/>' +
        '<path stroke-width=".7" d="M16 22.5 13.6 19.4M16 17.5 13.3 14.1M16 12.5 13.8 9.6"/>' +
        '<path stroke-width=".7" d="M16 22.5 18.4 19.4M16 17.5 18.7 14.1M16 12.5 18.2 9.6"/>' +
        "</g></svg>";

    /* Hijau daun mengikuti palet tajuk pada tree-sentra.png. */
    var TONES = ["#8ee16f", "#61d263", "#2fc06f", "#1aa564", "#149769"];

    var template = document.createElement("template");
    template.innerHTML = LEAF_SVG;

    var DESKTOP = {
        count: [8, 12],
        size: [12, 26],
        duration: [9, 16],
        opacity: [0.6, 0.95],
        spread: 64,
        sway: [8, 22],
        drift: [30, 150],
        driftBack: [-30, 10],
        jitter: 4,
    };

    var MOBILE = {
        count: [4, 6],
        size: [10, 18],
        duration: [16, 26],
        opacity: [0.3, 0.55],
        spread: 82,
        sway: [8, 20],
        drift: [12, 64],
        driftBack: [-18, 8],
        jitter: 6,
    };

    function rnd(min, max) {
        return min + Math.random() * (max - min);
    }

    function pick(list) {
        return list[Math.floor(Math.random() * list.length)];
    }

    /* Titik lahir daun di tajuk, dalam persen kanvas ilustrasi. */
    function readSpawns(host) {
        var raw = host.getAttribute("data-spawns");

        if (!raw) {
            return [];
        }

        try {
            var points = JSON.parse(raw);
            return Array.isArray(points) ? points : [];
        } catch (err) {
            return [];
        }
    }

    function shuffle(list) {
        for (var i = list.length - 1; i > 0; i -= 1) {
            var j = Math.floor(Math.random() * (i + 1));
            var tmp = list[i];
            list[i] = list[j];
            list[j] = tmp;
        }

        return list;
    }

    function build(host, opts) {
        host.textContent = "";

        var count = Math.round(rnd(opts.count[0], opts.count[1]));
        var spawns = shuffle(readSpawns(host));

        for (var i = 0; i < count; i += 1) {
            var leaf = document.createElement("span");
            var flip = document.createElement("span");
            var duration = rnd(opts.duration[0], opts.duration[1]);
            var spin = rnd(5, 12);
            var spawn = spawns.length ? spawns[i % spawns.length] : null;

            /* Dengan titik lahir: daun lepas dari tajuk lalu jatuh sampai
               keluar dari tepi bawah panggung. Tanpa titik lahir (lapisan
               halaman di layar kecil): daun turun dari atas layar. */
            var x = spawn
                ? spawn[0] + rnd(-1.1, 1.1)
                : ((i + 0.5) / count) * opts.spread +
                  rnd(-opts.jitter, opts.jitter);
            var y = spawn ? spawn[1] + rnd(-0.8, 0.8) : -12;
            var fall = spawn ? 100 - y + rnd(10, 20) : 126;

            /* Arah angin: sebagian besar condong ke kanan, sebagian kecil
               berbalik supaya gerakannya tidak seragam. */
            var drift =
                Math.random() < 0.75
                    ? rnd(opts.drift[0], opts.drift[1])
                    : rnd(opts.driftBack[0], opts.driftBack[1]);

            leaf.className = "login-leaf";
            leaf.style.setProperty(
                "--leaf-x",
                Math.max(1, Math.min(96, x)).toFixed(2) + "%",
            );
            leaf.style.setProperty("--leaf-y", y.toFixed(2) + "%");
            leaf.style.setProperty("--leaf-fall", fall.toFixed(2) + "%");
            leaf.style.setProperty(
                "--leaf-size",
                rnd(opts.size[0], opts.size[1]).toFixed(1) + "px",
            );
            leaf.style.setProperty(
                "--leaf-opacity",
                rnd(opts.opacity[0], opts.opacity[1]).toFixed(2),
            );
            leaf.style.setProperty(
                "--leaf-duration",
                duration.toFixed(2) + "s",
            );
            leaf.style.setProperty(
                "--leaf-delay",
                (-rnd(0, duration)).toFixed(2) + "s",
            );
            leaf.style.setProperty("--leaf-drift", drift.toFixed(0) + "px");
            leaf.style.setProperty(
                "--leaf-sway",
                rnd(opts.sway[0], opts.sway[1]).toFixed(0) + "px",
            );
            leaf.style.setProperty(
                "--leaf-sway-duration",
                rnd(2.6, 4.6).toFixed(2) + "s",
            );
            leaf.style.setProperty("--leaf-spin", spin.toFixed(2) + "s");
            leaf.style.setProperty(
                "--leaf-spin-delay",
                (-rnd(0, spin)).toFixed(2) + "s",
            );
            leaf.style.setProperty(
                "--leaf-spin-direction",
                Math.random() < 0.4 ? "reverse" : "normal",
            );
            leaf.style.setProperty("--leaf-color", pick(TONES));

            flip.className = "login-leaf__flip";
            flip.appendChild(template.content.firstChild.cloneNode(true));

            leaf.appendChild(flip);
            host.appendChild(leaf);
        }
    }

    function render() {
        /* Hormati preferensi "kurangi gerak": tanpa daun sama sekali. */
        if (calm.matches) {
            if (heroHost) {
                heroHost.textContent = "";
            }

            if (pageHost) {
                pageHost.textContent = "";
            }

            return;
        }

        if (wide.matches) {
            if (pageHost) {
                pageHost.textContent = "";
            }

            if (heroHost) {
                build(heroHost, DESKTOP);
            }

            return;
        }

        if (heroHost) {
            heroHost.textContent = "";
        }

        if (pageHost) {
            build(pageHost, MOBILE);
        }
    }

    /* Jeda animasi saat tab tidak terlihat: hemat CPU tanpa mengubah posisi. */
    document.addEventListener("visibilitychange", function () {
        [heroHost, pageHost].forEach(function (host) {
            if (host) {
                host.classList.toggle("is-paused", document.hidden);
            }
        });
    });

    /* Bentuk ulang hanya ketika breakpoint atau preferensi gerak berubah. */
    if (typeof wide.addEventListener === "function") {
        wide.addEventListener("change", render);
        calm.addEventListener("change", render);
    } else {
        wide.addListener(render);
        calm.addListener(render);
    }

    render();
})();
