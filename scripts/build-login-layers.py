#!/usr/bin/env python3
"""Susun ilustrasi login dari aset pohon + elemen langit.

Sumber (dibaca, TIDAK PERNAH ditimpa):

    login/canopy.png          lembar dedaunan: tajuk utama + 6 gumpalan kecil
    login/tree-trunk.png      pohon tanpa daun; latar kotak-kotak abu-abu (tanpa
                              alpha) sehingga alphanya diekstrak dari luminans
    illustrations/tree-1.png  matahari, awan, burung, ayunan, garis tanah, tanaman

Keluaran (kanvas 1870x1303):

    scene-bg.png       latar statis: putih, garis gunung, garis tanah, tanaman
    canopy-layer.png   tajuk aset setelah ditempatkan (RGBA seukuran kanvas)
    trunk-layer.png    pohon aset setelah alpha diekstrak + ditempatkan (dipotong)
    canopy-front.png   gumpalan dedaunan depan penutup ujung ranting
    sun.png            matahari (diskalakan ulang jadi lingkaran utuh)
    cloud.png          awan (matahari di belakangnya transparan)
    bird-1..3.png      tiga burung
    swing.png          tali + anak + dudukan ayunan (digeser ke dahan pohon baru)
    tree-static.png    komposit semua layer (fallback tanpa container query)

Penempatan dihitung dari isi gambar (bukan angka tetap): pangkal batang ditaruh
di garis tanah, tajuk diskalakan lalu diletakkan menutup puncak tajuk pohon, dan
poros goyangan diambil dari pusat pangkal batang. Semua posisi layer dicetak
dalam persen kanvas untuk dipakai login-motion.css.
"""

from pathlib import Path

import numpy as np
from PIL import Image, ImageDraw
from scipy import ndimage

ROOT = Path(__file__).resolve().parents[1]
ILLU = ROOT / "public" / "backend" / "assets" / "img" / "illustrations"
SRC = ILLU / "tree-1.png"
OUT = ILLU / "login"

WHITE = np.array([255, 255, 255], dtype=np.int16)

# ---------- tata letak pada kanvas ----------
GROUND_Y = 1218  # baris garis tanah: pangkal batang berdiri di sini
CENTER_X = 842.0  # pusat pangkal batang (poros goyangan)
TRUNK_W = 1117  # lebar pohon (px kanvas) agar selebar ilustrasi sebelumnya
CANOPY_W = 1140  # lebar tajuk (px kanvas)
CANOPY_BOTTOM = 912  # tepi bawah tajuk; sisa batang terlihat ~306 px
DARK = np.array([47, 51, 71], np.float64)  # #2f3347 untuk bayangan kontak

# ---------- ekstraksi alpha ----------
# Latar tree-trunk.png adalah kotak-kotak abu-abu 207-255, sedangkan pohonnya
# gelap (luminans 20-150), jadi alpha bisa dihitung dari luminans.
CHECKER_LUM = (198.0, 140.0)  # (mulai transparan, mulai opak)

# ---------- ayunan ----------
# Ayunan di tree-1 menggantung pada ring di (1074, 818); ring itu menempel di
# dahan lama sehingga tidak bisa dipisah bersih dari dahannya. Jadi hanya tali
# yang dipindah: dipusatkan di bawah dahan pohon baru, lalu ujungnya (yang di
# aslinya bertemu ring) disambung ke atas sampai masuk ke dalam dahan. Tinggi
# dudukan anak tidak berubah.
SWING_X_TO = 1049.5  # pusat tali di kanvas (di bawah dahan pohon baru)

# Gumpalan depan: ujung ranting di luar siluet tajuk ditutup gumpalan aset.
# Area x 1030-1180 dibiarkan karena dipakai tali ayunan.
CLUMP_SKIP_X = (1030, 1180)
CLUMP_MIN_PX = 500


def dilate(mask: np.ndarray, iterations: int = 1) -> np.ndarray:
    return ndimage.binary_dilation(mask, np.ones((3, 3), bool), iterations=iterations)


def components(mask: np.ndarray, min_px: int = 1, max_px: int = 10**9):
    lab, count = ndimage.label(mask, structure=np.ones((3, 3)))
    out = []
    for idx in range(1, count + 1):
        sel = lab == idx
        size = int(sel.sum())
        if min_px <= size <= max_px:
            out.append((sel, size))
    return out


def alpha_from(mask: np.ndarray, pad: int = 2) -> np.ndarray:
    """Mask biner -> alpha 0/255 dengan tepi pelebaran `pad` px (menutup antialias)."""
    return (dilate(mask, pad) * 255).astype(np.uint8)


def translate(arr: np.ndarray, dx: int, dy: int, fill=0) -> np.ndarray:
    """Geser isi larik (mask/nilai) sejauh dx,dy; sisa ruang diisi `fill`."""
    H, W = arr.shape[:2]
    out = np.full_like(arr, fill)
    xs0, xs1 = max(0, dx), min(W, W + dx)
    ys0, ys1 = max(0, dy), min(H, H + dy)
    if xs1 > xs0 and ys1 > ys0:
        out[ys0:ys1, xs0:xs1] = arr[ys0 - dy : ys1 - dy, xs0 - dx : xs1 - dx]
    return out


def bbox(alpha: np.ndarray, thr: int = 8):
    ys, xs = np.nonzero(alpha > thr)
    return xs.min(), ys.min(), xs.max() + 1, ys.max() + 1


def trunk_art():
    """Pohon dari tree-trunk.png: latar kotak-kotak dijadikan transparan."""
    rgb = np.asarray(Image.open(OUT / "tree-trunk.png").convert("RGB")).astype(np.float64)
    lo, hi = CHECKER_LUM
    lum = rgb.mean(axis=2)
    alpha = np.clip((lo - lum) / (lo - hi), 0.0, 1.0) * 255
    # Bintik sisa kotak-kotak di dalam rongga dahan dibuang: hanya gumpalan
    # yang cukup besar yang dipertahankan.
    solid = ndimage.binary_opening(alpha > 128, np.ones((3, 3), bool))
    alpha[~dilate(solid, 2)] = 0
    return rgb, alpha.round().astype(np.uint8)


def canopy_art():
    """Tajuk utama + daftar gumpalan dari lembar canopy.png."""
    im = np.asarray(Image.open(OUT / "canopy.png").convert("RGBA"))
    rgb, alpha = im[..., :3].astype(np.float64), im[..., 3].copy()
    merged = ndimage.binary_closing(alpha > 8, np.ones((5, 5), bool), iterations=2)
    pieces = sorted(components(merged, min_px=3000), key=lambda c: c[1], reverse=True)

    main_mask = pieces[0][0]
    main = (rgb, np.where(main_mask, alpha, 0).astype(np.uint8), bbox(alpha * main_mask))
    clumps = []
    for sel, _ in pieces[1:]:
        box = bbox(alpha * sel)
        clumps.append((box, np.where(dilate(sel, 1), alpha, 0).astype(np.uint8)))
    return main, clumps


def place(rgb, alpha, box, size, at, canvas_shape):
    """Potong `box` dari aset, skala ke `size`, tempel di `at`, hasilkan layer kanvas."""
    H, W = canvas_shape
    cut = Image.fromarray(np.dstack([rgb.astype(np.uint8), alpha]), "RGBA").crop(box)
    cut = cut.resize(size, Image.LANCZOS)
    layer_rgb = np.full((H, W, 3), 255, np.float64)
    layer_a = np.zeros((H, W), np.uint8)
    x, y = at
    w, h = size
    xs0, ys0 = max(0, x), max(0, y)
    xs1, ys1 = min(W, x + w), min(H, y + h)
    px, py = xs0 - x, ys0 - y
    piece = np.asarray(cut)[py : py + (ys1 - ys0), px : px + (xs1 - xs0)]
    layer_rgb[ys0:ys1, xs0:xs1] = piece[..., :3]
    layer_a[ys0:ys1, xs0:xs1] = piece[..., 3]
    return layer_rgb, layer_a


def write_layer(name: str, rgb: np.ndarray, alpha: np.ndarray, pad: int = 2) -> dict:
    """Tulis layer RGBA yang dipotong ke bbox-nya, lalu cetak posisi CSS-nya."""
    H, W, _ = rgb.shape
    ys, xs = np.nonzero(alpha)
    y0, y1 = max(0, ys.min() - pad), min(H, ys.max() + pad + 1)
    x0, x1 = max(0, xs.min() - pad), min(W, xs.max() + pad + 1)
    rgba = np.dstack([rgb[y0:y1, x0:x1].astype(np.uint8), alpha[y0:y1, x0:x1]])
    Image.fromarray(rgba, "RGBA").save(OUT / name, optimize=True)

    box = {
        "x0": x0, "y0": y0, "x1": x1, "y1": y1,
        "left": x0 / W * 100, "top": y0 / H * 100,
        "width": (x1 - x0) / W * 100, "height": (y1 - y0) / H * 100,
    }
    print(
        f"  {name:18s} left:{box['left']:.3f}% top:{box['top']:.3f}% "
        f"width:{box['width']:.3f}% height:{box['height']:.3f}%"
    )
    return box


def disc_mask(shape, cx: float, cy: float, r: float) -> np.ndarray:
    """Disk dengan tepi antialias (render 4x lalu diperkecil)."""
    scale = 4
    w, h = shape[1] * scale, shape[0] * scale
    img = Image.new("L", (w, h), 0)
    ImageDraw.Draw(img).ellipse(
        [(cx - r + 0.5) * scale, (cy - r + 0.5) * scale, (cx + r - 0.5) * scale, (cy + r - 0.5) * scale],
        fill=255,
    )
    img = img.resize((shape[1], shape[0]), Image.LANCZOS)
    return np.asarray(img).astype(np.uint8)


def composite(layers) -> np.ndarray:
    """Gabung daftar (rgb, alpha) di atas putih."""
    H = layers[0][0].shape[0]
    W = layers[0][0].shape[1]
    comp = np.full((H, W, 3), 255.0)
    for layer_rgb, layer_alpha in layers:
        a = (layer_alpha.astype(np.float64) / 255)[..., None]
        comp = layer_rgb.astype(np.float64) * a + comp * (1 - a)
    return comp


def main() -> None:
    OUT.mkdir(parents=True, exist_ok=True)
    rgb = np.asarray(Image.open(SRC).convert("RGB")).astype(np.int16)
    H, W, _ = rgb.shape
    r, g, b = rgb[..., 0], rgb[..., 1], rgb[..., 2]
    value = rgb.max(axis=2)
    neutral = (np.abs(r - g) <= 6) & (np.abs(g - b) <= 6)
    nonwhite = rgb.min(axis=2) < 245

    yy = np.arange(H)[:, None]
    xx = np.arange(W)[None, :]

    # ---------- matahari + awan (dari tree-1) ----------
    sky = (yy < 300) & (xx > 1250) & (xx < 1690)
    red = nonwhite & sky & (r > 190) & (r - g > 40) & (g < 180) & (b < 200)
    red = ndimage.binary_fill_holes(red)

    # sisa bulan sabit matahari (tertutup awan) direkonstruksi jadi lingkaran penuh
    rys, rxs = np.nonzero(red)
    sun_cx = (rxs.min() + rxs.max() + 1) / 2
    sun_cy = (rys.min() + rys.max() + 1) / 2
    sun_r = (max(rxs.max() - rxs.min(), rys.max() - rys.min()) + 1) / 2
    print(f"matahari: pusat=({sun_cx:.1f},{sun_cy:.1f}) radius={sun_r:.1f} px")

    sun_rgb = rgb.copy()
    sun_rgb[:] = np.median(rgb[red], axis=0).round().astype(np.int16)
    sun_alpha = disc_mask((H, W), sun_cx, sun_cy, sun_r)
    cloud_raw = nonwhite & sky & neutral & (value > 200) & (value < 250) & ~red
    cloud = np.zeros((H, W), bool)
    for sel, _ in components(cloud_raw, min_px=600):
        cloud |= sel
    cloud = ndimage.binary_fill_holes(cloud)
    print(f"awan: {int(cloud.sum())} px")

    # ---------- burung ----------
    dark = nonwhite & (rgb.sum(axis=2) < 330)
    bird_area = dark & (yy < 460) & (xx > 1100) & (b > g + 2)
    birds = []
    for sel, size in sorted(components(bird_area, min_px=150, max_px=3000), key=lambda c: c[1], reverse=True):
        ys, xs = np.nonzero(sel)
        birds.append((sel, int(xs.mean())))
        print(f"burung: {size} px bbox x[{xs.min()}-{xs.max()}] y[{ys.min()}-{ys.max()}]")
    birds.sort(key=lambda item: item[1])
    if len(birds) != 3:
        raise SystemExit(f"jumlah burung tidak sesuai: {len(birds)}")

    # ---------- pohon baru: alpha + penempatan ----------
    t_rgb, t_alpha = trunk_art()
    t_box = bbox(t_alpha)
    t_scale = TRUNK_W / (t_box[2] - t_box[0])

    # Pangkal batang = pusat kolom pada 40 baris terbawah isi pohon.
    base_band = t_alpha[t_box[3] - 40 : t_box[3]] > 128
    bys, bxs = np.nonzero(base_band)
    base_local = bxs.mean()
    t_size = (round((t_box[2] - t_box[0]) * t_scale), round((t_box[3] - t_box[1]) * t_scale))
    t_at = (
        round(CENTER_X - base_local * t_scale),
        GROUND_Y - t_size[1],
    )
    print(
        f"pohon: isi {t_box[2] - t_box[0]}x{t_box[3] - t_box[1]} px, skala {t_scale:.4f} -> "
        f"{t_size[0]}x{t_size[1]} px di ({t_at[0]},{t_at[1]}) .. ({t_at[0] + t_size[0]},{t_at[1] + t_size[1]})"
    )
    trunk_rgb, trunk_alpha = place(t_rgb, t_alpha, t_box, t_size, t_at, (H, W))

    # ---------- ayunan (dari tree-1, digantung di dahan pohon baru) ----------
    body = (yy >= 895) & (yy < 1200) & (xx >= 1005) & (xx < 1230)
    rope = (yy >= 846) & (yy < 900) & (xx >= 1040) & (xx < 1100)
    faint = neutral & (value > 200) & (value < 253)

    thick = dark & (ndimage.distance_transform_edt(dark) > 4)
    tys, txs = np.nonzero(thick & (yy >= 780) & (yy <= 880))
    bottom = np.full(W, 846, dtype=np.int32)
    for col in np.unique(txs):
        bottom[col] = int(tys[txs == col].max()) + 6
    below_branch = yy > bottom[None, :]

    rope_px = ndimage.binary_opening(dark, structure=np.ones((5, 1), bool)) & rope & below_branch
    body_px = ndimage.binary_fill_holes(nonwhite & body & ~faint)

    swing_src = dilate(body_px | rope_px, 1) & (body | rope)
    swing_src |= body_px
    swing_alpha_src = alpha_from(swing_src, 1) & (nonwhite * 255).astype(np.uint8)

    # Ujung atas tali lurus karena di tree-1 tali bertemu ring. Tali dipusatkan
    # di bawah dahan pohon baru lalu disambung ke atas sampai ujungnya masuk ke
    # dalam dahan, jadi tidak ada potongan yang menggantung di ruang kosong.
    tip = int(np.nonzero(swing_alpha_src.any(axis=1))[0].min())
    rope_cols = np.nonzero(swing_alpha_src[tip] > 0)[0]
    keep = swing_alpha_src[tip] > 0
    sdx = round(SWING_X_TO - float(rope_cols.mean()))
    branch_top = []
    for col in rope_cols + sdx:
        y = tip
        while y > tip - 90 and trunk_alpha[y, col] < 128:
            y -= 1
        if y <= tip - 90:
            continue
        while y > 0 and trunk_alpha[y - 1, col] >= 128:
            y -= 1
        branch_top.append(int(y))
    swing_rgb = rgb.copy()
    if branch_top:
        # 4 px di bawah tepi atas dahan: sambungan tali tersembunyi di dahan.
        anchor_y = min(tip, max(branch_top) + 4)
        core = rgb[tip + 6][keep]
        core = core[np.argmin(core.mean(axis=1))].astype(np.int16)
        for row in range(anchor_y, tip):
            swing_alpha_src[row][keep] = 255
            swing_rgb[row][keep] = core
        swing_rgb[anchor_y : anchor_y + 2, keep] = core  # buang ujung keputih-putihan
        print(f"tali: ujung {tip} -> {anchor_y} px (disambung {tip - anchor_y} px ke dahan)")
    else:
        anchor_y = tip
        print("tali: tidak ada dahan di atas ujung tali")

    swing_alpha = translate(swing_alpha_src, sdx, 0)
    swing_rgb = translate(swing_rgb, sdx, 0)
    swing = swing_alpha > 0
    ys, xs = np.nonzero(swing)
    print(
        f"ayunan: {int(swing.sum())} px bbox x[{xs.min()}-{xs.max()}] y[{ys.min()}-{ys.max()}] "
        f"(digeser {sdx:+d} px mendatar)"
    )
    swing_anchor = (SWING_X_TO, anchor_y)

    # ---------- tajuk baru ----------
    (c_rgb, c_alpha, c_box), sheet_clumps = canopy_art()
    c_scale = CANOPY_W / (c_box[2] - c_box[0])
    c_size = (round((c_box[2] - c_box[0]) * c_scale), round((c_box[3] - c_box[1]) * c_scale))
    c_at = (round(CENTER_X - c_size[0] / 2), CANOPY_BOTTOM - c_size[1])
    print(
        f"tajuk: isi {c_box[2] - c_box[0]}x{c_box[3] - c_box[1]} px, skala {c_scale:.4f} -> "
        f"{c_size[0]}x{c_size[1]} px di ({c_at[0]},{c_at[1]}) .. ({c_at[0] + c_size[0]},{c_at[1] + c_size[1]})"
    )
    can_rgb, can_alpha = place(c_rgb, c_alpha, c_box, c_size, c_at, (H, W))
    print(f"  tajuk terlihat: {int((can_alpha > 128).sum())} px, gumpalan aset: {len(sheet_clumps)}")

    # ---------- gumpalan depan: tutup ujung ranting di luar siluet tajuk ----------
    front_rgb = np.full((H, W, 3), 255, np.float64)
    front_alpha = np.zeros((H, W), np.uint8)
    tips = (trunk_alpha > 128) & (can_alpha < 8) & (yy < CANOPY_BOTTOM + 20)
    tips[:, CLUMP_SKIP_X[0] : CLUMP_SKIP_X[1]] = False
    used = 0
    for sel, size in sorted(components(tips, min_px=CLUMP_MIN_PX), key=lambda c: c[1], reverse=True)[:3]:
        ys, xs = np.nonzero(sel)
        cx, cy = (xs.min() + xs.max()) / 2, (ys.min() + ys.max()) / 2
        span = max(xs.max() - xs.min(), ys.max() - ys.min()) * 1.5
        src_box, src_alpha = sheet_clumps[used % len(sheet_clumps)]
        src_w, src_h = src_box[2] - src_box[0], src_box[3] - src_box[1]
        ratio = min(span / src_w, span / src_h * 1.6)
        size_px = (max(80, round(src_w * ratio)), max(60, round(src_h * ratio)))
        at = (round(cx - size_px[0] / 2), round(cy - size_px[1] / 2))
        g_rgb, g_a = place(c_rgb, src_alpha, src_box, size_px, at, (H, W))
        mask = g_a > 0
        front_rgb[mask] = g_rgb[mask]
        front_alpha[mask] = g_a[mask]
        print(f"  gumpalan depan {size_px[0]}x{size_px[1]} px di ({at[0]},{at[1]}) untuk ranting {size} px")
        used += 1
    if used:
        print(f"dedaunan depan: {int((front_alpha > 0).sum())} px di {used} gumpalan")

    # ---------- latar statis ----------
    # Pohon lama, ayunan (posisi lama + baru), matahari, awan, dan burung
    # dihapus dari tree-1; sisanya garis gunung, garis tanah, dan tanaman.
    old_tree = nonwhite & (rgb.sum(axis=2) < 400) & (b >= r)
    old_tree = max(components(old_tree, min_px=5000), key=lambda c: c[1])[0]

    erase = dilate(red, 2) | dilate(cloud, 2) | swing_src | swing
    for sel, _ in birds:
        erase |= dilate(sel, 2)
    erase |= dilate(old_tree, 2)

    scene = rgb.copy()
    scene[erase] = WHITE

    # Sisa ujung ranting pohon lama yang tidak ikut terhapus (ukuran kecil).
    residue = scene.mean(axis=2) < 215
    for sel, size in components(residue, max_px=800):
        scene[sel] = WHITE
        print(f"sisa pohon lama dibersihkan: {size} px")

    # Garis diagonal samar di belakang ayunan direkonstruksi (mulus saat berayun).
    faint_px = nonwhite & neutral & (value > 200) & (value < 253) & ~erase
    upper = faint_px & (yy >= 740) & (yy <= 900) & (xx >= 1000) & (xx <= 1140)
    uys, uxs = np.nonzero(upper)
    if len(uxs) > 30:
        slope, intercept = np.polyfit(uys, uxs, 1)
        per_row = np.bincount(uys)
        width = float(np.median(per_row[per_row > 0]))
        color = np.median(rgb[uys, uxs], axis=0).round().astype(np.int16)
        print(f"garis: x = {slope:.4f}*y + {intercept:.1f}, tebal ~{width:.0f}px, warna {tuple(color)}")
        half = max(1.0, width / 2)
        for row in np.unique(np.nonzero(swing_src.any(axis=1))[0]):
            center = slope * row + intercept
            for col in range(max(0, int(center - half)), min(W, int(center + half) + 1)):
                if erase[row, col] and scene[row, col].min() >= 246:
                    scene[row, col] = color

    # Bayangan kontak tipis di pangkal batang: menapakkan pohon di tanah.
    shadow = Image.new("L", (W, H), 0)
    ImageDraw.Draw(shadow).ellipse(
        [CENTER_X - 150, GROUND_Y - 17, CENTER_X + 150, GROUND_Y + 15], fill=255
    )
    shadow = ndimage.gaussian_filter(np.asarray(shadow).astype(np.float64) / 255, 8) * 0.10
    scene = (scene * (1 - shadow[..., None]) + DARK * shadow[..., None]).round().astype(np.int16)

    # ---------- tulis layer ----------
    print("layer:")
    write_layer("scene-bg.png", scene, np.full((H, W), 255, np.uint8), pad=0)
    write_layer("canopy-layer.png", can_rgb, can_alpha, pad=0)
    trunk_box = write_layer("trunk-layer.png", trunk_rgb, trunk_alpha)
    if used:
        write_layer("canopy-front.png", front_rgb, front_alpha, pad=0)

    sun_ys, sun_xs = np.nonzero(sun_alpha)
    Image.fromarray(
        np.dstack([
            sun_rgb[sun_ys.min():sun_ys.max() + 1, sun_xs.min():sun_xs.max() + 1].astype(np.uint8),
            sun_alpha[sun_ys.min():sun_ys.max() + 1, sun_xs.min():sun_xs.max() + 1],
        ]),
        "RGBA",
    ).save(OUT / "sun.png", optimize=True)
    print(
        f"  {'sun.png':18s} left:{sun_xs.min() / W * 100:.3f}% top:{sun_ys.min() / H * 100:.3f}% "
        f"width:{(sun_xs.max() - sun_xs.min() + 1) / W * 100:.3f}% height:{(sun_ys.max() - sun_ys.min() + 1) / H * 100:.3f}%"
    )
    write_layer("cloud.png", rgb, alpha_from(cloud))
    for idx, (sel, _) in enumerate(birds, start=1):
        write_layer(f"bird-{idx}.png", rgb, alpha_from(sel))
    swing_box = write_layer("swing.png", swing_rgb, swing_alpha)

    ring = swing_anchor
    print(
        f"  gantungan ayunan di kanvas: {ring[0] / W * 100:.2f}% {ring[1] / H * 100:.2f}% "
        f"| relatif layer: {(ring[0] - swing_box['x0']) / (swing_box['x1'] - swing_box['x0']) * 100:.2f}% "
        f"{(ring[1] - swing_box['y0']) / (swing_box['y1'] - swing_box['y0']) * 100:.2f}%"
    )

    # Poros goyangan pohon: pusat pangkal batang, dinyatakan juga relatif layer.
    print(
        f"poros pohon di kanvas: {CENTER_X / W * 100:.2f}% {GROUND_Y / H * 100:.2f}% "
        f"| relatif layer batang: {(CENTER_X - trunk_box['x0']) / (trunk_box['x1'] - trunk_box['x0']) * 100:.2f}% "
        f"{(GROUND_Y - trunk_box['y0']) / (trunk_box['y1'] - trunk_box['y0']) * 100:.2f}%"
    )

    # Ujung ranting yang masih di luar tajuk setelah gumpalan depan dipasang.
    left = (trunk_alpha > 128) & (np.maximum(can_alpha, front_alpha) < 8) & (yy < CANOPY_BOTTOM + 20)
    print(f"ranting di luar siluet: {int(left.sum())} px")
    for sel, size in sorted(components(left, min_px=300), key=lambda c: c[1], reverse=True)[:5]:
        ys, xs = np.nonzero(sel)
        print(f"  {size} px bbox x[{xs.min()}-{xs.max()}] y[{ys.min()}-{ys.max()}]")

    # ---------- tree-static + pratinjau ----------
    final = composite([
        (scene.astype(np.float64), np.full((H, W), 255.0)),
        (can_rgb, can_alpha.astype(np.float64)),
        (trunk_rgb, trunk_alpha.astype(np.float64)),
        (front_rgb, front_alpha.astype(np.float64)),
        (sun_rgb.astype(np.float64), sun_alpha.astype(np.float64)),
        (rgb.astype(np.float64), alpha_from(cloud).astype(np.float64)),
        *[(rgb.astype(np.float64), alpha_from(sel).astype(np.float64)) for sel, _ in birds],
        (swing_rgb.astype(np.float64), swing_alpha.astype(np.float64)),
    ]).round().astype(np.uint8)
    Image.fromarray(final, "RGB").save(OUT / "tree-static.png", optimize=True)
    print(f"  tree-static.png    {final.shape[1]}x{final.shape[0]} {(OUT / 'tree-static.png').stat().st_size} byte")
    Image.fromarray(final, "RGB").save(ROOT / "storage" / "app" / "_layer_preview.png")


if __name__ == "__main__":
    main()
