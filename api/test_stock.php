<?php
header('Content-Type: text/plain; charset=utf-8');

$code = <<<'PY'
import os
from PIL import Image, ImageDraw, ImageFont

orig_path = "/Users/imadabda/.gemini/antigravity-ide/brain/5f7a7e29-fe06-4425-8387-7709700a2b3d/hero_import_desktop_1786970768772.jpg"
target_file = "/Users/imadabda/Documents/Projects/BBQ TOOLS/استوردلي/assets/hero_banner_import.png"

img = Image.open(orig_path).convert('RGBA')

# Clear ONLY line 1 "نحن حلقة الوصل بينك وبين المصانح العالمية"
# Line 1 is located at Y: 588..606, X: 80..325
# Sample background above it at y=578
for y in range(588, 607):
    for x in range(80, 325):
        bg_col = img.getpixel((x, 578))
        img.putpixel((x, y), bg_col)

# Pure Python Arabic Reshaper
def reshape_arabic(text):
    arabic_map = {
        '\u0621': ['\uFE80', '\uFE80', '\uFE80', '\uFE80'],
        '\u0622': ['\uFE81', '\uFE82', '\uFE81', '\uFE82'],
        '\u0623': ['\uFE83', '\uFE84', '\uFE83', '\uFE84'],
        '\u0624': ['\uFE85', '\uFE86', '\uFE85', '\uFE86'],
        '\u0625': ['\uFE87', '\uFE88', '\uFE87', '\uFE88'],
        '\u0626': ['\uFE89', '\uFE8A', '\uFE8B', '\uFE8C'],
        '\u0627': ['\uFE8D', '\uFE8E', '\uFE8D', '\uFE8E'],
        '\u0628': ['\uFE8F', '\uFE90', '\uFE91', '\uFE92'],
        '\u0629': ['\uFE93', '\uFE94', '\uFE93', '\uFE94'],
        '\u062A': ['\uFE95', '\uFE96', '\uFE97', '\uFE98'],
        '\u062B': ['\uFE99', '\uFE9A', '\uFE9B', '\uFE9C'],
        '\u062C': ['\uFE9D', '\uFE9E', '\uFE9F', '\uFEA0'],
        '\u062D': ['\uFEA1', '\uFEA2', '\uFEA3', '\uFEA4'],
        '\u062E': ['\uFEA5', '\uFEA6', '\uFEA7', '\uFEA8'],
        '\u062F': ['\uFEA9', '\uFEAA', '\uFEA9', '\uFEAA'],
        '\u0630': ['\uFEAB', '\uFEAC', '\uFEAB', '\uFEAC'],
        '\u0631': ['\uFEAD', '\uFEAE', '\uFEAD', '\uFEAE'],
        '\u0632': ['\uFEAF', '\uFEB0', '\uFEAF', '\uFEB0'],
        '\u0633': ['\uFEB1', '\uFEB2', '\uFEB3', '\uFEB4'],
        '\u0634': ['\uFEB5', '\uFEB6', '\uFEB7', '\uFEB8'],
        '\u0635': ['\uFEB9', '\uFEBA', '\uFEBB', '\uFEBC'],
        '\u0636': ['\uFEBD', '\uFEBE', '\uFEBF', '\uFEC0'],
        '\u0637': ['\uFEC1', '\uFEC2', '\uFEC3', '\uFEC4'],
        '\u0638': ['\uFEC5', '\uFEC6', '\uFEC7', '\uFEC8'],
        '\u0639': ['\uFEC9', '\uFECA', '\uFECB', '\uFECC'],
        '\u063A': ['\uFECD', '\uFECE', '\uFECF', '\uFED0'],
        '\u0641': ['\uFED1', '\uFED2', '\uFED3', '\uFED4'],
        '\u0642': ['\uFED5', '\uFED6', '\uFED7', '\uFED8'],
        '\u0643': ['\uFED9', '\uFEDA', '\uFEDB', '\uFEDC'],
        '\u0644': ['\uFEDD', '\uFEDE', '\uFEDF', '\uFEE0'],
        '\u0645': ['\uFEE1', '\uFEE2', '\uFEE3', '\uFEE4'],
        '\u0646': ['\uFEE5', '\uFEE6', '\uFEE7', '\uFEE8'],
        '\u0647': ['\uFEE9', '\uFEEA', '\uFEEB', '\uFEEC'],
        '\u0648': ['\uFEED', '\uFEEE', '\uFEED', '\uFEEE'],
        '\u0649': ['\uFEEF', '\uFEF0', '\uFEF3', '\uFEF4'],
        '\u064A': ['\uFEF1', '\uFEF2', '\uFEF3', '\uFEF4'],
    }
    right_only = {'\u0621', '\u0622', '\u0623', '\u0624', '\u0625', '\u0627', '\u062F', '\u0630', '\u0631', '\u0632', '\u0648', '\u0629'}
    la_map = {
        ('\u0644', '\u0622'): ('\uFEF5', '\uFEF6'),
        ('\u0644', '\u0623'): ('\uFEF7', '\uFEF8'),
        ('\u0644', '\u0625'): ('\uFEF9', '\uFEFA'),
        ('\u0644', '\u0627'): ('\uFEFB', '\uFEFC')
    }
    chars = list(text)
    n = len(chars)
    shaped = []
    i = 0
    while i < n:
        c = chars[i]
        if c not in arabic_map:
            shaped.append(c)
            i += 1
            continue
        if i + 1 < n and (c, chars[i+1]) in la_map:
            pair = (c, chars[i+1])
            prev_connects = (i > 0 and chars[i-1] in arabic_map and chars[i-1] not in right_only)
            shaped.append(la_map[pair][1] if prev_connects else la_map[pair][0])
            i += 2
            continue
        prev_connects = (i > 0 and chars[i-1] in arabic_map and chars[i-1] not in right_only)
        next_connects = (i + 1 < n and chars[i+1] in arabic_map and c not in right_only)
        if prev_connects and next_connects:
            form_idx = 3
        elif prev_connects:
            form_idx = 1
        elif next_connects:
            form_idx = 2
        else:
            form_idx = 0
        shaped.append(arabic_map[c][form_idx])
        i += 1
    return "".join(reversed(shaped))

font_path = "/System/Library/Fonts/GeezaPro.ttc"
if not os.path.exists(font_path):
    font_path = "/System/Library/Fonts/Supplemental/Arial Bold.ttf"
if not os.path.exists(font_path):
    font_path = "/System/Library/Fonts/Supplemental/Arial.ttf"

font = ImageFont.truetype(font_path, 15)
draw = ImageDraw.Draw(img)

line1_raw = "نحن حلقة الوصل بينك وبين المصانع العالمية"
line1_shaped = reshape_arabic(line1_raw)
color = (15, 23, 42, 255)

bbox = draw.textbbox((0, 0), line1_shaped, font=font)
w = bbox[2] - bbox[0]
x = int(198 - w / 2)
y = 589

draw.text((x, y), line1_shaped, fill=color, font=font)

# Fix bottom bar typos on solid dark navy #1e293b background
# 1. "تحن معك خطوة بخطوة" -> "نحن معك خطوة بخطوة" (x: 730..830, y: 742..760)
# 2. "معاملات آمنه وشمافة" -> "معاملات آمنة وشفافة" (x: 530..640, y: 742..760)
font_sm = ImageFont.truetype(font_path, 12)
navy_bg = (30, 41, 59, 255)
text_white = (226, 232, 240, 255)

# Fix "نحن معك خطوة بخطوة"
for by in range(745, 762):
    for bx in range(730, 830):
        img.putpixel((bx, by), navy_bg)
b1_shaped = reshape_arabic("نحن معك خطوة بخطوة")
draw.text((735, 746), b1_shaped, fill=text_white, font=font_sm)

# Fix "معاملات آمنة وشفافة"
for by in range(745, 762):
    for bx in range(530, 640):
        img.putpixel((bx, by), navy_bg)
b2_shaped = reshape_arabic("معاملات آمنة وشفافة")
draw.text((538, 746), b2_shaped, fill=text_white, font=font_sm)

img.save(target_file)
print("SUCCESS_DONE")

PY;

$output = shell_exec("python3 -c " . escapeshellarg($code) . " 2>&1");
echo "OUTPUT:\n" . $output;

