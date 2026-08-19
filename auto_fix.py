import os
from PIL import Image, ImageDraw, ImageFont

orig_path = "/Users/imadabda/.gemini/antigravity-ide/brain/5f7a7e29-fe06-4425-8387-7709700a2b3d/hero_import_desktop_1786970768772.jpg"
target_file = "/Users/imadabda/Documents/Projects/BBQ TOOLS/استوردلي/assets/hero_banner_import.png"

img = Image.open(orig_path).convert('RGBA')
width, height = img.size
print(f"Original image size: {width}x{height}")

# Analyze dark pixel clusters per line in original image
lines_y = {}
for y in range(400, 700):
    count = 0
    xs = []
    for x in range(60, 350):
        p = img.getpixel((x, y))
        if p[0] < 60 and p[1] < 70 and p[2] < 90:
            count += 1
            xs.append(x)
    if count > 5:
        lines_y[y] = (count, min(xs), max(xs))

# Group consecutive Ys
clusters = []
cur_cluster = []
for y in sorted(lines_y.keys()):
    if not cur_cluster or y == cur_cluster[-1] + 1:
        cur_cluster.append(y)
    else:
        clusters.append(cur_cluster)
        cur_cluster = [y]
if cur_cluster:
    clusters.append(cur_cluster)

for i, c in enumerate(clusters):
    min_x = min(lines_y[y][1] for y in c)
    max_x = max(lines_y[y][2] for y in c)
    print(f"Cluster {i+1}: Y=[{c[0]}, {c[-1]}], X=[{min_x}, {max_x}]")



# Pure Python Arabic Reshaper
def reshape_arabic(text):
    arabic_map = {
        '\u0621': ['\uFE80', '\uFE80', '\uFE80', '\uFE80'], # ء
        '\u0622': ['\uFE81', '\uFE82', '\uFE81', '\uFE82'], # آ
        '\u0623': ['\uFE83', '\uFE84', '\uFE83', '\uFE84'], # أ
        '\u0624': ['\uFE85', '\uFE86', '\uFE85', '\uFE86'], # ؤ
        '\u0625': ['\uFE87', '\uFE88', '\uFE87', '\uFE88'], # إ
        '\u0626': ['\uFE89', '\uFE8A', '\uFE8B', '\uFE8C'], # ئ
        '\u0627': ['\uFE8D', '\uFE8E', '\uFE8D', '\uFE8E'], # ا
        '\u0628': ['\uFE8F', '\uFE90', '\uFE91', '\uFE92'], # ب
        '\u0629': ['\uFE93', '\uFE94', '\uFE93', '\uFE94'], # ة
        '\u062A': ['\uFE95', '\uFE96', '\uFE97', '\uFE98'], # ت
        '\u062B': ['\uFE99', '\uFE9A', '\uFE9B', '\uFE9C'], # ث
        '\u062C': ['\uFE9D', '\uFE9E', '\uFE9F', '\uFEA0'], # ج
        '\u062D': ['\uFEA1', '\uFEA2', '\uFEA3', '\uFEA4'], # ح
        '\u062E': ['\uFEA5', '\uFEA6', '\uFEA7', '\uFEA8'], # خ
        '\u062F': ['\uFEA9', '\uFEAA', '\uFEA9', '\uFEAA'], # د
        '\u0630': ['\uFEAB', '\uFEAC', '\uFEAB', '\uFEAC'], # ذ
        '\u0631': ['\uFEAD', '\uFEAE', '\uFEAD', '\uFEAE'], # ر
        '\u0632': ['\uFEAF', '\uFEB0', '\uFEAF', '\uFEB0'], # ز
        '\u0633': ['\uFEB1', '\uFEB2', '\uFEB3', '\uFEB4'], # س
        '\u0634': ['\uFEB5', '\uFEB6', '\uFEB7', '\uFEB8'], # ش
        '\u0635': ['\uFEB9', '\uFEBA', '\uFEBB', '\uFEBC'], # ص
        '\u0636': ['\uFEBD', '\uFEBE', '\uFEBF', '\uFEC0'], # ض
        '\u0637': ['\uFEC1', '\uFEC2', '\uFEC3', '\uFEC4'], # ط
        '\u0638': ['\uFEC5', '\uFEC6', '\uFEC7', '\uFEC8'], # ظ
        '\u0639': ['\uFEC9', '\uFECA', '\uFECB', '\uFECC'], # ع
        '\u063A': ['\uFECD', '\uFECE', '\uFECF', '\uFED0'], # غ
        '\u0641': ['\uFED1', '\uFED2', '\uFED3', '\uFED4'], # ف
        '\u0642': ['\uFED5', '\uFED6', '\uFED7', '\uFED8'], # ق
        '\u0643': ['\uFED9', '\uFEDA', '\uFEDB', '\uFEDC'], # ك
        '\u0644': ['\uFEDD', '\uFEDE', '\uFEDF', '\uFEE0'], # ل
        '\u0645': ['\uFEE1', '\uFEE2', '\uFEE3', '\uFEE4'], # م
        '\u0646': ['\uFEE5', '\uFEE6', '\uFEE7', '\uFEE8'], # ن
        '\u0647': ['\uFEE9', '\uFEEA', '\uFEEB', '\uFEEC'], # ه
        '\u0648': ['\uFEED', '\uFEEE', '\uFEED', '\uFEEE'], # و
        '\u0649': ['\uFEEF', '\uFEF0', '\uFEF3', '\uFEF4'], # ى
        '\u064A': ['\uFEF1', '\uFEF2', '\uFEF3', '\uFEF4'], # ي
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

font_path = "/System/Library/Fonts/Supplemental/Arial Bold.ttf"
if not os.path.exists(font_path):
    font_path = "/System/Library/Fonts/Supplemental/Arial.ttf"

font = ImageFont.truetype(font_path, 15)
draw = ImageDraw.Draw(img)

line1_raw = "نحن حلقة الوصل بينك وبين المصانع العالمية"
line1_shaped = reshape_arabic(line1_raw)

# Color matching original navy text: #0f172a
color = (15, 23, 42, 255)

bbox = draw.textbbox((0, 0), line1_shaped, font=font)
w = bbox[2] - bbox[0]
x = int(198 - w / 2)
y = 590

draw.text((x, y), line1_shaped, fill=color, font=font)

img.save(target_file)
print(f"Flawlessly updated {target_file} at y=590!")







