"""Memecah ilustrasi login menjadi lapisan yang bisa dianimasikan.

Sumber satu-satunya: public/backend/assets/img/illustrations/tree-sentra.png
(aset milik pengguna, tidak pernah diubah). Hasilnya tiga lapisan RGBA
berukuran kanvas penuh sehingga posisi CSS cukup inset: 0:

  sentra-scene.png  langit, bukit, matahari, burung, tanaman & batu di tanah
  sentra-tree.png   batang + dahan + tajuk (berayun sebagai satu pohon)
  sentra-swing.png  tali + anak + papan ayunan (berayun pada gantungannya)

Skrip juga mencetak titik poros (persen kanvas) untuk CSS dan titik lahir
daun di tepi bawah tajuk untuk animasi daun jatuh.

Jalankan:  python scripts/build-login-tree-sentra.py [--preview]
"""

import json
import sys
from pathlib import Path

import numpy as np
from PIL import Image
from scipy import ndimage as ndi

ROOT = Path(__file__).resolve().parents[1]
SRC = ROOT / "public/backend/assets/img/illustrations/tree-sentra.png"
OUT = ROOT / "public/backend/assets/img/illustrations/login"

# Daerah kerja dalam piksel kanvas 1672x941.
TREE_REGION = (0, 960, 100, 845)  # x0, x1, y0, y1 : pohon saja
SWING_REGION = (530, 705, 480, 803)  # tali, anak, papan
GROUND_Y = 800  # di bawah ini rumput/batu, bukan bagian ayunan


def runs(col):
    """Daftar (awal, akhir) piksel True pada satu kolom."""
    ys = np.nonzero(col)[0]
    if not len(ys):
        return []
    out = []
    start = prev = int(ys[0])
    for y in ys[1:]:
        y = int(y)
        if y > prev + 1:
            out.append((start, prev))
            start = y
        prev = y
    out.append((start, prev))
    return out


def main() -> None:
    OUT.mkdir(parents=True, exist_ok=True)
    img = Image.open(SRC).convert("RGB")
    a = np.asarray(img).astype(np.float64)
    H, W, _ = a.shape
    r, g, b = a[..., 0], a[..., 1], a[..., 2]
    lum = a.mean(axis=2)
    sat = a.max(axis=2) - a.min(axis=2)

    green = (g > r + 14) & (g > b + 14) & (sat > 45)
    dark = (lum < 115) & (sat < 90)

    # ---------------------------------------------------------------- ayunan
    # Tali menyatu dengan dahan, jadi ujung atas tali ditentukan per kolom:
    # selama masih dahan, lebar piksel depan di baris itu besar; begitu hanya
    # tersisa tali, lebarnya tinggal beberapa piksel.
    x0, x1, y0, y1 = SWING_REGION
    fg_soft = ((255 - lum) > 8) | (sat > 25)  # termasuk baju anak yang terang
    cand = np.zeros((H, W), bool)
    for x in range(x0, x1):
        col = fg_soft[y0:y1, x]
        row_runs = runs(col)
        if not row_runs:
            continue
        for k, (ya, yb) in enumerate(row_runs):
            ya += y0
            yb += y0
            if k == 0 and ya < 520:
                # Cari ujung bawah dahan pada kolom ini.
                cut = yb
                for y in range(ya, min(yb, 640)):
                    line = fg_soft[y, x0:x1]
                    # lebar gumpalan yang memuat kolom x
                    left = x - x0
                    while left > 0 and line[left - 1]:
                        left -= 1
                    right = x - x0
                    while right < len(line) - 1 and line[right + 1]:
                        right += 1
                    if right - left + 1 <= 9:
                        cut = y - 1
                        break
                if cut >= ya:
                    cand[cut + 1 : yb + 1, x] = True
            else:
                cand[ya : yb + 1, x] = True

    lab, n = ndi.label(cand, structure=np.ones((3, 3)))
    swing = np.zeros((H, W), bool)
    # Bibit pada bagian yang pasti milik ayunan: kedua tali, kepala anak, dan
    # papan duduk. Tanaman di tanah atau gumpalan daun di dekatnya tidak
    # menyentuh bibit ini, jadi otomatis tersisih.
    seeds = np.zeros((H, W), bool)
    seeds[545:615, 570:606] = True  # tali
    seeds[600:650, 556:616] = True  # rambut / kepala anak
    seeds[722:748, 540:640] = True  # papan ayunan
    keep_ids = set(np.unique(lab[seeds & (lab > 0)]).tolist())
    for idx in range(1, n + 1):
        if idx in keep_ids:
            swing |= lab == idx

    ys, xs = np.nonzero(swing)
    print("ayunan: %d px, bbox x[%d-%d] y[%d-%d]" % (len(ys), xs.min(), xs.max(), ys.min(), ys.max()))

    # Titik gantung: ujung atas kedua tali.
    rope_tops = []
    for x in range(560, 615):
        col = np.nonzero(swing[500:640, x])[0]
        if len(col) > 60:  # kolom tali: panjang dan hampir tak putus
            rope_tops.append((x, int(col.min()) + 500))
    if rope_tops:
        groups = []
        for x, y in rope_tops:
            if groups and x - groups[-1][-1][0] <= 2:
                groups[-1].append((x, y))
            else:
                groups.append([(x, y)])
        tops = [groups[0][0], groups[-1][-1]] if len(groups) > 1 else [groups[0][0]]
        anchor_x = float(np.mean([p[0] for p in tops]))
        anchor_y = float(np.mean([p[1] for p in tops]))
    else:
        anchor_x, anchor_y = 588.0, 528.0
    print("gantungan ayunan: (%.1f, %.1f)" % (anchor_x, anchor_y))

    # ----------------------------------------------------------------- pohon
    glab, gn = ndi.label(green)
    gsizes = ndi.sum(green, glab, range(1, gn + 1))
    crown_id = int(np.argmax(gsizes)) + 1
    crown = glab == crown_id
    cys, cxs = np.nonzero(crown)
    print("tajuk: %d px, bbox x[%d-%d] y[%d-%d]" % (len(cys), cxs.min(), cxs.max(), cys.min(), cys.max()))

    dlab, dn = ndi.label(dark)
    dsizes = ndi.sum(dark, dlab, range(1, dn + 1))
    trunk_id = int(np.argmax(dsizes)) + 1
    tree = crown.copy()
    for idx in range(1, dn + 1):
        if dsizes[idx - 1] < 40:
            continue
        sel = dlab == idx
        ys2, xs2 = np.nonzero(sel)
        cx, cy = xs2.mean(), ys2.mean()
        if idx == trunk_id or (cy < 600 and 0 < cx < 960):
            tree |= sel

    tx0, tx1, ty0, ty1 = TREE_REGION
    region = np.zeros((H, W), bool)
    region[ty0:ty1, tx0:tx1] = True
    tree &= region
    tree &= ~swing
    tree = ndi.binary_closing(tree, structure=np.ones((3, 3)))

    tys, txs = np.nonzero(tree)
    print("pohon: %d px, bbox x[%d-%d] y[%d-%d]" % (len(tys), txs.min(), txs.max(), tys.min(), tys.max()))

    # Poros pohon: titik tengah pangkal batang (kontak dengan tanah).
    base_rows = tree[tys.max() - 5 : tys.max() + 1, :]
    bxs = np.nonzero(base_rows.any(axis=0))[0]
    pivot_x = float(bxs.mean())
    pivot_y = float(tys.max())
    print("poros pohon: (%.1f, %.1f)" % (pivot_x, pivot_y))

    # --------------------------------------------------------------- latar
    removed = ndi.binary_dilation(tree | swing, iterations=3)
    scene = a.copy()
    # Isi lubang dengan interpolasi mendatar antara piksel utuh terdekat di
    # kiri dan kanan pada baris yang sama: langit dan bukit tetap nyambung.
    for y in range(H):
        hole = removed[y]
        if not hole.any():
            continue
        solid = np.nonzero(~hole)[0]
        if not len(solid):
            continue
        xs_hole = np.nonzero(hole)[0]
        left = np.searchsorted(solid, xs_hole, side="right") - 1
        right = np.clip(left + 1, 0, len(solid) - 1)
        left = np.clip(left, 0, len(solid) - 1)
        xl = solid[left]
        xr = solid[right]
        denom = np.maximum(xr - xl, 1)
        t = ((xs_hole - xl) / denom)[:, None]
        scene[y, xs_hole] = a[y, xl] * (1 - t) + a[y, xr] * t
    scene = ndi.gaussian_filter(scene, sigma=(1.2, 1.2, 0))

    # ------------------------------------------------------------ simpan
    def save(mask, name, feather, erode=False):
        m = ndi.binary_erosion(mask, iterations=1) if erode else mask
        alpha = ndi.gaussian_filter(m.astype(np.float64), feather)
        alpha = np.clip(alpha * 1.15, 0, 1)
        rgb = a.copy()
        rgb[alpha < 0.02] = 0  # pangkas data warna di area transparan
        rgba = np.dstack([rgb, alpha * 255]).astype(np.uint8)
        Image.fromarray(rgba, "RGBA").save(OUT / name, optimize=True)
        print("%s %d byte" % (name, (OUT / name).stat().st_size))

    save(tree, "sentra-tree.png", 0.7, erode=False)
    save(swing, "sentra-swing.png", 0.6, erode=False)
    Image.fromarray(scene.astype(np.uint8), "RGB").save(OUT / "sentra-scene.png", optimize=True)
    print("sentra-scene.png %d byte" % (OUT / "sentra-scene.png").stat().st_size)

    # ---------------------------------------------- titik lahir daun di tajuk
    spawns = []
    xs_range = np.linspace(np.percentile(cxs, 6), np.percentile(cxs, 94), 15)
    for x in xs_range:
        col = np.nonzero(crown[:, int(x)])[0]
        if not len(col):
            continue
        y = float(col.max()) - 6
        spawns.append([round(float(x) / W * 100, 2), round(y / H * 100, 2)])

    print("\n--- untuk CSS ---")
    print("poros pohon : %.3f%% %.3f%%" % (pivot_x / W * 100, pivot_y / H * 100))
    print("poros ayunan: %.3f%% %.3f%%" % (anchor_x / W * 100, anchor_y / H * 100))
    print("\n--- titik lahir daun (persen kanvas) ---")
    print(json.dumps(spawns))

    if "--preview" in sys.argv:
        tree_rgba = np.asarray(Image.open(OUT / "sentra-tree.png").convert("RGBA")).astype(np.float64)
        swing_rgba = np.asarray(Image.open(OUT / "sentra-swing.png").convert("RGBA")).astype(np.float64)

        def over(bg, layer):
            al = layer[..., 3:4] / 255.0
            return bg * (1 - al) + layer[..., :3] * al

        base = over(over(scene, tree_rgba), swing_rgba)
        Image.fromarray(base.astype(np.uint8)).save(ROOT / "storage/app/_sentra_rest.png")
        print("gabungan diam vs asli: selisih rata-rata %.3f" % np.abs(base - a).mean())

        # Lapisan di atas warna kontras untuk memeriksa piksel yang salah ikut.
        for layer, name in ((tree_rgba, "tree"), (swing_rgba, "swing")):
            flat = np.full_like(a, [255, 0, 255])
            Image.fromarray(over(flat, layer).astype(np.uint8)).save(
                ROOT / ("storage/app/_sentra_mask_%s.png" % name)
            )

        def rot(layer, deg, cx, cy):
            im = Image.fromarray(layer.astype(np.uint8), "RGBA")
            return np.asarray(
                im.rotate(deg, resample=Image.BICUBIC, center=(cx, cy)), dtype=np.float64
            )

        for deg_tree, deg_swing, name in ((-1.0, -6.0, "a"), (1.0, 6.0, "b")):
            t = rot(tree_rgba, deg_tree, pivot_x, pivot_y)
            s = rot(swing_rgba, deg_swing, anchor_x, anchor_y)
            s = rot(s, deg_tree, pivot_x, pivot_y)
            comp = over(over(scene, t), s)
            Image.fromarray(comp.astype(np.uint8)).save(ROOT / ("storage/app/_sentra_pose_%s.png" % name))
        print("pratinjau tersimpan di storage/app/_sentra_*.png")


if __name__ == "__main__":
    main()
