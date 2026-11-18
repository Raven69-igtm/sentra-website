#!/usr/bin/env python3
"""Bangun layer tajuk (canopy) pohon dari gambar referensi login.

Referensi (`scripts/assets/login-reference.png`) memakai ilustrasi yang sama
dengan tree-1.png tetapi pohonnya rimbun. Script ini:

  1. mengambil piksel hijau terbesar (= tajuk) dari area ilustrasi referensi,
  2. merapikan gigitan/bolong akibat cabang, awan, dan burung,
  3. menyederhanakan warnanya jadi beberapa tone (k-means) lalu memperhalus tepi,
  4. memperbesar ke skala ilustrasi asli (1870x1303) dan menaruhnya pada
     posisi yang pas dengan batang pohon di tree-1.png,
  5. menulis PNG RGBA kanvas penuh `login/canopy.png` untuk digabung oleh
     scripts/build-login-layers.py.

Jalankan sekali: python scripts/build-login-canopy.py

PERHATIAN: login/canopy.png sekarang dipakai sebagai sumber oleh
scripts/build-login-layers.py. Script ini menolak menimpa berkas itu kecuali
dijalankan dengan --force.
"""

import sys
from pathlib import Path

import numpy as np
from PIL import Image
from scipy import ndimage

ROOT = Path(__file__).resolve().parents[1]
REF = ROOT / "scripts" / "assets" / "login-reference.png"
SRC = ROOT / "public" / "backend" / "assets" / "img" / "illustrations" / "tree-1.png"
OUT = ROOT / "public" / "backend" / "assets" / "img" / "illustrations" / "login"

PANEL_X = 860  # batas kanan area ilustrasi pada referensi (sebelumnya form)
TARGET_W = 1140  # lebar tajuk pada skala ilustrasi asli
BOTTOM = 920  # tepi bawah tajuk: menutup percabangan, sisa batang ~303 px
CENTER_X = 850  # pusat tajuk mengikuti pusat tajuk pada referensi
TONES = 6


def kmeans(pix: np.ndarray, k: int, iters: int = 12, seed: int = 7) -> np.ndarray:
    rng = np.random.default_rng(seed)
    cent = pix[rng.choice(len(pix), k, replace=False)].astype(np.float64)
    for _ in range(iters):
        d = ((pix[:, None, :].astype(np.float64) - cent[None]) ** 2).sum(axis=2)
        lab = d.argmin(axis=1)
        for i in range(k):
            sel = lab == i
            if sel.any():
                cent[i] = pix[sel].mean(axis=0)
    return cent.round().astype(np.uint8)


def main() -> None:
    target = OUT / "canopy.png"
    if target.exists() and "--force" not in sys.argv:
        raise SystemExit(
            f"{target.relative_to(ROOT)} sudah ada dan dipakai sebagai sumber oleh "
            "scripts/build-login-layers.py; tambahkan --force untuk menimpanya."
        )

    ref = np.asarray(Image.open(REF).convert("RGB")).astype(np.int16)

    r, g, b = ref[..., 0], ref[..., 1], ref[..., 2]
    green = (g > r + 6) & (g > b + 6) & (g > 90)
    green[:, PANEL_X:] = False

    lab, n = ndimage.label(green, structure=np.ones((3, 3)))
    sizes = ndimage.sum(green, lab, range(1, n + 1))
    canopy = lab == int(np.argmax(sizes) + 1)
    ys, xs = np.nonzero(canopy)
    x0, x1, y0, y1 = xs.min(), xs.max() + 1, ys.min(), ys.max() + 1
    print(f"tajuk referensi: {int(canopy.sum())} px bbox x[{x0}-{x1 - 1}] y[{y0}-{y1 - 1}]")

    # Rapatkan celah tipis (garis cabang yang menimpa tajuk) lalu tutup lubang
    # interior, supaya tajuk tidak berlubang saat digabung dengan pohon asli.
    fill = ndimage.binary_fill_holes(
        ndimage.binary_closing(canopy, ndimage.generate_binary_structure(2, 1), iterations=6)
    )
    # Kuantisasi warna dari piksel tajuk. Median 3x3 lebih dulu supaya garis
    # antialias bekas cabang tidak ikut jadi tone pucat, lalu area bolong diisi
    # tone terdekat agar tidak ada lubang putih saat tajuk digabung.
    crop = ndimage.median_filter(ref[y0:y1, x0:x1].astype(np.uint8), size=(3, 3, 1))
    cmask = canopy[y0:y1, x0:x1]
    # Piksel di sekitar cabang referensi (termasuk antialias hijau-kelabunya)
    # tidak dipakai sebagai sumber warna: meniru kesan garis pucat bekas cabang.
    navy = (crop[..., 2] > crop[..., 1] + 2) & (crop.sum(axis=2) < 420)
    near_navy = ndimage.binary_dilation(navy, np.ones((3, 3), bool), iterations=4)
    cmask = cmask & ~near_navy
    print(f"piksel warna dipakai: {int(cmask.sum())} (dibuang dekat cabang: {int(near_navy.sum())})")
    pix = crop[cmask]
    cent = kmeans(pix, TONES)
    dist = ((pix[:, None, :].astype(np.float64) - cent[None]) ** 2).sum(axis=2)
    label = np.full(crop.shape[:2], -1, dtype=np.int16)
    label[cmask] = dist.argmin(axis=1)

    idx = ndimage.distance_transform_edt(label < 0, return_distances=False, return_indices=True)
    label = label[idx[0], idx[1]]

    # Filter mayoritas 5x5: garis tipis sisa antialias cabang hilang, sedangkan
    # bentuk gumpalan daun tetap utuh.
    votes = np.stack(
        [ndimage.uniform_filter((label == t).astype(np.float32), size=5) for t in range(TONES)]
    )
    label = votes.argmax(axis=0).astype(np.int16)

    palette = cent.astype(np.uint8)
    order = np.argsort(palette.sum(axis=1))  # gelap -> terang
    print("palet tajuk (gelap->terang):", [f"#{c[0]:02x}{c[1]:02x}{c[2]:02x}" for c in palette[order]])

    crop_rgb = palette[label]
    crop_a = (fill[y0:y1, x0:x1] * 255).astype(np.uint8)

    w = TARGET_W
    h = round((y1 - y0) * w / (x1 - x0))
    # Warna: nearest agar tetap flat (6 tone utuh), alpha: halus untuk tepi tajuk.
    rgb_up = np.asarray(
        Image.fromarray(crop_rgb, "RGB").resize((w, h), Image.NEAREST)
    )
    a_up = np.asarray(Image.fromarray(crop_a, "L").resize((w, h), Image.LANCZOS))

    left = CENTER_X - w // 2
    top = BOTTOM - h
    print(f"tajuk target: {w}x{h} px di ({left},{top}) -> kanan-bawah ({left + w},{top + h})")

    layer = np.zeros((1303, 1870, 4), np.uint8)
    layer[top : top + h, left : left + w, :3] = rgb_up
    layer[top : top + h, left : left + w, 3] = a_up

    # Referensi digambar dengan cabang menimpa tajuk, jadi celah bekas cabang
    # berlubang di masker. Celah yang tertutup (tidak menyentuh tepi gambar)
    # dan berada di dalam kotak tajuk diisi tone terdekat supaya tidak muncul
    # putih saat tajuk digabung dengan pohon asli.
    src = np.asarray(Image.open(SRC).convert("RGB")).astype(np.int16)
    fg = src.sum(axis=2) < 560
    a_flat = (a_up.astype(np.float64) / 255)[..., None]
    canvas = np.full((1303, 1870, 3), 255.0)
    canvas[top : top + h, left : left + w] = rgb_up * a_flat + 255.0 * (1 - a_flat)
    comp = np.where(fg[..., None], src, canvas.round())

    white = comp.min(axis=2) >= 250
    lab_w, n_w = ndimage.label(white, structure=np.ones((3, 3)))
    border = np.unique(
        np.concatenate([lab_w[0], lab_w[-1], lab_w[:, 0], lab_w[:, -1]])
    )
    gaps = np.zeros_like(white)
    for i in range(1, n_w + 1):
        if i in border:
            continue
        sel = lab_w == i
        ys, xs = np.nonzero(sel)
        if ys.min() >= top and ys.max() < top + h and xs.min() >= left and xs.max() < left + w:
            gaps |= sel
    filled_px = int(gaps.sum())

    # Isi celah dengan tone asli (bukan hasil campur alpha) dari piksel solid
    # terdekat, lalu jadikan opak.
    solid_rgb = np.full((1303, 1870, 3), 255, np.uint8)
    solid_rgb[top : top + h, left : left + w] = rgb_up
    alpha_full = np.zeros((1303, 1870), np.float64)
    alpha_full[top : top + h, left : left + w] = a_up / 255

    def inpaint(region: np.ndarray) -> None:
        """Warnai `region` dengan tone dari piksel solid terdekat."""
        known = alpha_full >= 0.99
        idx = ndimage.distance_transform_edt(
            ~known, return_distances=False, return_indices=True
        )
        solid_rgb[region] = solid_rgb[idx[0][region], idx[1][region]]

    inpaint(gaps)
    alpha_full[gaps] = 1.0
    print(f"celah lebar diisi: {int(gaps.sum())} px")

    # Celah tipis bekas cabang beserta halo antialiasnya ditutup hanya di bagian
    # dalam siluet (minimal 10 px dari tepi luar) agar tepi daun tetap utuh.
    struct = np.ones((3, 3), bool)
    target = ndimage.binary_fill_holes(
        ndimage.binary_closing(alpha_full >= 0.5, struct, iterations=10)
    )
    inside = ndimage.binary_erosion(target, struct, iterations=10)
    add = target & inside & (alpha_full < 0.99)
    inpaint(add)
    alpha_full[target & inside] = 1.0
    print(f"celah tipis diisi: {int(add.sum())} px")

    # Saluran bekas batang/cabang pada referensi: di referensi area itu digambar
    # ranting navy, jadi tidak pernah ikut masker hijau dan salurannya menyambung
    # ke tepi bawah tajuk sehingga tidak tertangkap fill_holes. Diisi dengan
    # dedaunan cermin dari sekitar (bukan tone rata) supaya bertekstur gumpalan.
    chan_up = np.asarray(
        Image.fromarray(
            (ndimage.binary_closing(navy, np.ones((3, 3), bool), iterations=5) * 255).astype(
                np.uint8
            ),
            "L",
        ).resize((w, h), Image.LANCZOS)
    ).astype(np.float64) / 255
    channel_full = np.zeros((1303, 1870), np.float64)
    channel_full[top : top + h, left : left + w] = chan_up

    # Isian tidak boleh menjuntai melewati tepi bawah dedaunan; batasnya diambil
    # dari siluet yang sudah ditutup celahnya (closing 25 px) sehingga bentuknya
    # mengikuti kontur gumpalan, bukan mengikuti saluran cabang yang menjorok ke
    # bawah. Median 101 px menghindari potongan garis tegak tiba-tiba.
    struct = np.ones((3, 3), bool)
    sil = ndimage.binary_fill_holes(
        ndimage.binary_closing(alpha_full >= 0.5, struct, iterations=25)
    )
    bottom_env = np.where(
        sil.any(axis=0), 1302 - np.argmax(sil[::-1], axis=0), 2**31 - 1
    )
    bottom_smooth = ndimage.median_filter(bottom_env.astype(np.float64), size=101) + 8
    allowed = np.arange(1303)[:, None] <= bottom_smooth[None, :]
    channel_full = np.where(allowed, channel_full, 0.0)

    known = alpha_full >= 0.99
    iy, ix = ndimage.distance_transform_edt(
        ~known, return_distances=False, return_indices=True
    )
    ys, xs = np.nonzero(channel_full > alpha_full)
    sy = np.clip(2 * iy[ys, xs] - ys, 0, 1302)
    sx = np.clip(2 * ix[ys, xs] - xs, 0, 1869)
    ok = known[sy, sx]
    solid_rgb[ys[ok], xs[ok]] = solid_rgb[sy[ok], sx[ok]]
    print(f"saluran bekas cabang diisi (cermin): {int(ok.sum())} px")

    # Sisa saluran (titik yang pantulan cerminnya jatuh di luar dedaunan) diisi
    # tone solid terdekat; alpha akhirnya diambil dari tepi halus LANCZOS.
    add_channel = (channel_full > alpha_full) & (alpha_full < 0.99)
    inpaint(add_channel)
    alpha_full = np.maximum(alpha_full, channel_full)
    print(f"saluran bekas cabang diisi (tone): {int(add_channel.sum())} px")

    # Lubang jarum dan pita tipis antialias ranting referensi ditutup: piksel
    # non-opak yang dikelilingi dedaunan (densitas 15x15 tinggi) dibuat opak,
    # sedangkan tepi luar siluet (densitas ~0.5) dibiarkan halus.
    solid = alpha_full >= 0.99
    dens = ndimage.uniform_filter(solid.astype(np.float32), size=15)
    pinhole = (~solid) & (dens > 0.58)
    inpaint(pinhole)
    alpha_full[pinhole] = 1.0
    print(f"lubang jarum ditutup: {int(pinhole.sum())} px")

    # Penisian warna terdekat menghasilkan bidang bersudut; labelnya dihaluskan
    # (mayoritas 5x5) supaya pecahan warna mengikuti gumpalan daun.
    region = solid_rgb[top : top + h, left : left + w]
    d = (
        (region[:, :, None, :].astype(np.int32) - palette[None, None].astype(np.int32)) ** 2
    ).sum(-1)
    lab_full = d.argmin(axis=2)
    votes = np.stack(
        [
            ndimage.uniform_filter((lab_full == t).astype(np.float32), size=7)
            for t in range(TONES)
        ]
    )
    lab_full = votes.argmax(axis=0)
    area = alpha_full[top : top + h, left : left + w] >= 0.99
    region[area] = palette[lab_full][area]
    solid_rgb[top : top + h, left : left + w] = region

    layer[..., :3] = solid_rgb
    layer[..., 3] = (alpha_full * 255).round().astype(np.uint8)
    OUT.mkdir(parents=True, exist_ok=True)
    Image.fromarray(layer, "RGBA").save(OUT / "canopy.png", optimize=True)
    print("tulis", (OUT / "canopy.png").relative_to(ROOT), (OUT / "canopy.png").stat().st_size, "byte")

    # Pratinjau: tajuk di belakang bagian gelap ilustrasi asli (alpha dihormati).
    shown = solid_rgb.astype(np.float64) * alpha_full[..., None] + 255.0 * (
        1 - alpha_full[..., None]
    )
    comp = np.where(fg[..., None], src, shown.round())
    Image.fromarray(comp.astype(np.uint8), "RGB").save(ROOT / "storage" / "app" / "_canopy_preview.png")

    # Ranting yang masih keluar dari siluet tajuk: dipakai untuk menaruh
    # gumpalan dedaunan depan (canopy-front.png) pada build-login-layers.py.
    navy = src.sum(axis=2) < 560
    outside = navy & ~(alpha_full >= 0.5) & (np.arange(1303)[:, None] < 1100)
    lab_o, n_o = ndimage.label(outside, np.ones((3, 3)))
    print(f"ranting di luar siluet tajuk: {int(outside.sum())} px")
    sizes = ndimage.sum(outside, lab_o, range(1, n_o + 1))
    for k in np.argsort(sizes)[::-1][:5]:
        if sizes[k] < 150:
            continue
        sel = lab_o == int(k + 1)
        oy, ox = np.nonzero(sel)
        print(
            f"  ranting {int(sizes[k]):6d} px bbox x[{ox.min()}-{ox.max()}] "
            f"y[{oy.min()}-{oy.max()}] pusat ({int(ox.mean())},{int(oy.mean())})"
        )


if __name__ == "__main__":
    main()
