# راهنمای قدم‌به‌قدم انتشار روی GitHub (کاملاً مبتدی)

این راهنما مخصوص پروژهٔ **Cloner Smart Image Optimizer & Auto WebP** نوشته شده.
اگر تا حالا هیچ‌وقت با Git کار نکردی، همین فایل کافیه. فقط از بالا به پایین انجام بده.

---

## فهرست

1. [Git و GitHub یعنی چی؟](#۱-git-و-github-یعنی-چی)
2. [نصب Git](#۲-نصب-git)
3. [تنظیم اولیه Git](#۳-تنظیم-اولیه-git)
4. [ساخت ریپازیتوری در GitHub](#۴-ساخت-ریپازیتوری-در-github)
5. [آماده‌سازی پوشه پروژه](#۵-آمادهسازی-پوشه-پروژه)
6. [اولین Commit](#۶-اولین-commit)
7. [اتصال به GitHub و Push](#۷-اتصال-به-github-و-push)
8. [مرتب کردن صفحه ریپو](#۸-مرتب-کردن-صفحه-ریپو)
9. [ساخت اولین Release](#۹-ساخت-اولین-release)
10. [کار روزمره بعد از این](#۱۰-کار-روزمره-بعد-از-این)
11. [خطاهای رایج و راه‌حل](#۱۱-خطاهای-رایج-و-راهحل)
12. [چیت‌شیت دستورات](#۱۲-چیتشیت-دستورات)

---

## ۱. Git و GitHub یعنی چی؟

قبل از هر دستوری، این دو تا مفهوم رو بفهم — بقیه راه آسون می‌شه.

### Git چیست؟

تصور کن داری یه پروژه می‌نویسی. معمولاً چیکار می‌کنی؟

```
project.php
project-final.php
project-final2.php
project-final-REAL.php
```

این روش افتضاحه. **Git** یه برنامه‌ست که روی کامپیوتر خودت نصب می‌شه و این کار رو حرفه‌ای انجام می‌ده:
هر بار که کارت به یه نقطهٔ خوب رسید، بهش می‌گی «الان یه عکس از وضعیت پروژه بگیر». به اون عکس می‌گن **Commit**.

بعداً هر وقت خواستی، می‌تونی برگردی به هر کدوم از اون عکس‌ها.

### GitHub چیست؟

Git روی کامپیوتر **خودت** کار می‌کنه. اگه لپ‌تاپت بسوزه، همه چی رفته.

**GitHub** یه سایت هست که نسخهٔ پروژه‌ت رو **آنلاین** نگه می‌داره. یعنی:
- پشتیبان‌گیری امن
- بقیه می‌تونن پروژه‌ت رو ببینن و استفاده کنن
- رزومهٔ برنامه‌نویسی تو

### چند اصطلاح که مدام می‌بینی

| اصطلاح | یعنی چی |
|---|---|
| **Repository (ریپو)** | پوشهٔ پروژه که Git داره ردیابیش می‌کنه |
| **Commit** | یک «عکس» ذخیره‌شده از وضعیت پروژه |
| **Stage** | انتخاب اینکه کدوم فایل‌ها توی عکس بعدی باشن |
| **Branch (شاخه)** | یه مسیر جداگانه برای کار روی یه قابلیت جدید |
| **main** | شاخهٔ اصلی و پایدار پروژه |
| **Remote** | آدرس نسخهٔ آنلاین پروژه روی GitHub |
| **Push** | فرستادن commitها از کامپیوتر به GitHub |
| **Pull** | گرفتن تغییرات از GitHub به کامپیوتر |
| **Clone** | دانلود کامل یه ریپو روی کامپیوتر |

### چرخهٔ کار (این رو حفظ کن)

```
فایل‌ها رو ویرایش می‌کنی
        ↓
   git add        →  کدوم فایل‌ها توی عکس بعدی باشن؟
        ↓
   git commit     →  عکس رو بگیر و اسم‌گذاری کن
        ↓
   git push       →  عکس رو بفرست روی GitHub
```

---

## ۲. نصب Git

### ویندوز

1. برو به [git-scm.com/download/win](https://git-scm.com/download/win)
2. فایل دانلود شده رو اجرا کن.
3. توی نصب همه‌جا **Next** بزن. فقط این دو تا رو دقت کن:
   - در مرحلهٔ *Adjusting your PATH environment* گزینهٔ وسط یعنی **Git from the command line and also from 3rd-party software** انتخاب باشه.
   - در مرحلهٔ *Default branch name* گزینهٔ **Override... `main`** رو انتخاب کن.
4. بعد از نصب، منوی Start رو باز کن و **Git Bash** رو اجرا کن.

> از این به بعد همهٔ دستورات این راهنما رو توی **Git Bash** می‌زنی، نه CMD.

### مک

```bash
brew install git
```

### لینوکس (اوبونتو/دبیان)

```bash
sudo apt update && sudo apt install git -y
```

### تست نصب

```bash
git --version
```

باید چیزی شبیه این ببینی:

```
git version 2.45.1
```

اگه دیدی، یعنی نصب موفق بوده. ✅

---

## ۳. تنظیم اولیه Git

Git باید بدونه تو کی هستی، چون اسمت روی هر commit ثبت می‌شه. این کار **فقط یک بار** در عمر سیستمت لازمه.

```bash
git config --global user.name "Shayan"
git config --global user.email "you@example.com"
```

> ⚠️ ایمیلی که اینجا می‌زنی باید **همون ایمیل حساب GitHub** باشه، وگرنه commitهات به پروفایلت وصل نمی‌شن.

چند تنظیم مفید دیگه:

```bash
# اسم شاخه پیش‌فرض را main کن
git config --global init.defaultBranch main

# جلوگیری از خراب شدن خط‌ها بین ویندوز و لینوکس
git config --global core.autocrlf true      # ویندوز
# git config --global core.autocrlf input   # مک و لینوکس

# پشتیبانی از اسم فایل‌های فارسی/یونیکد
git config --global core.quotepath false
```

بررسی تنظیمات:

```bash
git config --global --list
```

---

## ۴. ساخت ریپازیتوری در GitHub

1. وارد [github.com](https://github.com) شو (اگه حساب نداری، **Sign up** کن).
2. بالا سمت راست روی **+** کلیک کن → **New repository**.
3. فرم رو این‌طوری پر کن:

| فیلد | مقدار پیشنهادی |
|---|---|
| **Repository name** | `smart-image-optimizer` |
| **Description** | `Automatically resize, compress and convert WordPress uploads to WebP. Persian/English admin UI.` |
| **Public / Private** | **Public** (برای رزومه و دیده شدن) |
| **Add a README file** | ❌ تیک نزن |
| **Add .gitignore** | ❌ None |
| **Choose a license** | ❌ None |

> ❗ **خیلی مهم:** هیچ‌کدوم از سه گزینهٔ آخر رو تیک نزن. چون ما خودمون `README.md` و `.gitignore` و `LICENSE` رو ساختیم. اگه تیک بزنی، موقع push خطای `rejected` می‌گیری.

4. روی **Create repository** بزن.
5. صفحه‌ای که باز می‌شه یه آدرس بهت می‌ده، شبیه این — کپیش کن:

```
https://github.com/Shayan-alinezhad/smart-image-optimizer.git
```

---

## ۵. آماده‌سازی پوشه پروژه

1. فایل ZIP آماده‌شده رو دانلود و **اکسترکت** کن.
2. پوشهٔ اکسترکت‌شده رو یه جای مناسب بذار، مثلاً:

```
D:\Projects\smart-image-optimizer
```

3. توی اون پوشه راست‌کلیک کن → **Open Git Bash here**.
   (اگه این گزینه رو نداری، Git Bash رو باز کن و با `cd` برو توی پوشه:)

```bash
cd /d/Projects/smart-image-optimizer
```

4. مطمئن شو توی پوشهٔ درستی: 

```bash
ls -a
```

باید این‌ها رو ببینی:

```
.editorconfig  .github  .gitignore  CHANGELOG.md  CODE_OF_CONDUCT.md
CONTRIBUTING.md  LICENSE  README.md  SECURITY.md  assets  composer.json
includes  languages  phpcs.xml  readme.txt  smart-image-optimizer.php
templates  uninstall.php
```

### جایگزینی Shayan-alinezhad

توی چند فایل عبارت `Shayan-alinezhad` نوشته شده که باید با نام کاربری GitHub خودت عوض بشه:

```bash
grep -rl "Shayan-alinezhad" . --exclude-dir=.git
```

اون‌ها رو باز کن و `Shayan-alinezhad` رو با نام کاربریت جایگزین کن. یا با یه دستور:

```bash
grep -rl "Shayan-alinezhad" . --exclude-dir=.git | xargs sed -i 's/Shayan-alinezhad/your-github-username/g'
```

---

## ۶. اولین Commit

### گام ۱ — تبدیل پوشه به ریپازیتوری Git

```bash
git init
```

خروجی:

```
Initialized empty Git repository in D:/Projects/smart-image-optimizer/.git/
```

**چی شد؟** یه پوشهٔ مخفی به اسم `.git` ساخته شد. کل تاریخچهٔ پروژه از این به بعد اونجا ذخیره می‌شه. هیچ‌وقت دستی پاکش نکن.

### گام ۲ — دیدن وضعیت

```bash
git status
```

همهٔ فایل‌ها رو با رنگ قرمز و عنوان `Untracked files` می‌بینی. یعنی: «Git این فایل‌ها رو می‌بینه ولی هنوز ردیابیشون نمی‌کنه.»

### گام ۳ — Stage کردن فایل‌ها

```bash
git add .
```

**چی شد؟** اون نقطه یعنی «همهٔ فایل‌های این پوشه». حالا Git این فایل‌ها رو گذاشته توی «سبد خرید» تا توی commit بعدی ثبتشون کنه.

> فایل‌هایی که توی `.gitignore` نوشتیم (مثل `node_modules` و `*.zip`) خودکار نادیده گرفته می‌شن.

حالا دوباره چک کن:

```bash
git status
```

این بار فایل‌ها سبز شدن و زیر عنوان `Changes to be committed` هستن. ✅

### گام ۴ — گرفتن Commit

```bash
git commit -m "feat: initial release of Cloner Smart Image Optimizer v1.1.0"
```

**چی شد؟** عکس گرفته شد و ذخیره شد. اون متن بعد از `-m` توضیح commit هست.

> 💡 **قانون پیام commit:** به انگلیسی، فعل امری، کوتاه. مثل:
> `feat: add AVIF support` یا `fix: prevent bulk timeout`

دیدن تاریخچه:

```bash
git log --oneline
```

---

## ۷. اتصال به GitHub و Push

### گام ۱ — نام‌گذاری شاخه اصلی

```bash
git branch -M main
```

**چی شد؟** اسم شاخهٔ فعلی رو گذاشتیم `main` (استاندارد امروزی GitHub).

### گام ۲ — اضافه کردن آدرس GitHub

```bash
git remote add origin https://github.com/Shayan-alinezhad/smart-image-optimizer.git
```

**چی شد؟** به Git گفتیم «نسخهٔ آنلاین پروژه اینجاست». اسم `origin` یه اسم مستعار قراردادیه.

بررسی:

```bash
git remote -v
```

### گام ۳ — Push کردن

```bash
git push -u origin main
```

**چی شد؟** همهٔ commitها رفتن روی GitHub.

- `-u` یعنی «این ارتباط رو یادت بمونه». دفعه‌های بعد فقط کافیه `git push` بزنی.

### احراز هویت

موقع اولین push ازت یوزرنیم و پسورد می‌خواد. **پسورد حساب GitHub کار نمی‌کنه!** باید Token بسازی:

1. GitHub → عکس پروفایل → **Settings**
2. پایین صفحه → **Developer settings**
3. **Personal access tokens** → **Tokens (classic)**
4. **Generate new token (classic)**
5. تنظیمات:
   - **Note:** `laptop-git`
   - **Expiration:** `90 days` یا `No expiration`
   - **Scopes:** تیک `repo` و `workflow` رو بزن
6. **Generate token** → توکن رو **کپی کن** (فقط یک بار نشون داده می‌شه!)
7. موقع push:
   - `Username:` نام کاربری GitHub
   - `Password:` **توکن رو پیست کن** (موقع تایپ چیزی نشون داده نمی‌شه — طبیعیه)

### حالا برو صفحهٔ ریپو رو رفرش کن

همهٔ فایل‌ها اونجان و README حرفه‌ای با بج‌ها نمایش داده می‌شه. 🎉

---

## ۸. مرتب کردن صفحه ریپو

این بخش همون چیزیه که ریپو رو از «معمولی» به «فوق‌حرفه‌ای» تبدیل می‌کنه.

### ۸.۱ — About (سمت راست بالا، آیکون چرخ‌دنده)

**Description:**
```
Automatically resize, compress and convert WordPress Media Library uploads to WebP. Bulk optimization, backups, statistics, logging and a built-in Persian/English admin UI.
```

**Website:**
```
https://clonerr.ir
```

**Topics** (این‌ها رو یکی‌یکی اضافه کن — باعث پیدا شدن ریپو توی جستجو می‌شن):

```
wordpress  wordpress-plugin  php  webp  image-optimization
image-compression  performance  media-library  imagick  gd
rtl  persian  farsi  open-source
```

تیک‌های پایین: ✅ Releases ✅ Packages

### ۸.۲ — فعال کردن قابلیت‌ها

**Settings → General → Features:**
- ✅ Issues
- ✅ Discussions
- ✅ Projects (اختیاری)

**Settings → Code security:**
- ✅ Dependency graph
- ✅ Dependabot alerts
- ✅ Private vulnerability reporting

### ۸.۳ — محافظت از شاخه main

**Settings → Branches → Add branch protection rule**

- Branch name pattern: `main`
- ✅ Require a pull request before merging
- ✅ Require status checks to pass before merging

### ۸.۴ — ساخت برچسب‌های Issue

**Issues → Labels** — این‌ها رو اضافه کن:

| برچسب | رنگ |
|---|---|
| `bug` | `#d73a4a` |
| `enhancement` | `#a2eeef` |
| `documentation` | `#0075ca` |
| `good first issue` | `#7057ff` |
| `help wanted` | `#008672` |
| `needs-triage` | `#fbca04` |
| `dependencies` | `#0366d6` |
| `rtl` / `i18n` | `#c5def5` |

### ۸.۵ — اضافه کردن اسکرین‌شات (مهم!)

ریپویی که عکس داره ۱۰ برابر حرفه‌ای‌تر دیده می‌شه.

```bash
mkdir -p .github/screenshots
# عکس‌ها را داخل این پوشه کپی کن:
# settings.png, bulk.png, media-column.png, dashboard.png
```

بعد توی `README.md` این بخش رو بعد از Overview اضافه کن:

```markdown
## 📸 Screenshots

| Settings (Persian RTL) | Bulk Optimization |
|---|---|
| ![Settings](.github/screenshots/settings.png) | ![Bulk](.github/screenshots/bulk.png) |

| Media Library Column | Dashboard Widget |
|---|---|
| ![Column](.github/screenshots/media-column.png) | ![Dashboard](.github/screenshots/dashboard.png) |
```

بعد push کن:

```bash
git add .
git commit -m "docs: add admin screenshots to README"
git push
```

---

## ۹. ساخت اولین Release

Release یعنی یه نسخهٔ رسمی و قابل دانلود. کاربر می‌تونه ZIP رو مستقیم بگیره و توی وردپرس نصب کنه.

### روش خودکار (توصیه‌شده — از قبل برات آماده‌ست)

ما یه GitHub Action ساختیم که با ساختن Tag، خودش ZIP می‌سازه و Release منتشر می‌کنه:

```bash
git tag -a v1.1.0 -m "Release version 1.1.0"
git push origin v1.1.0
```

**چی شد؟**
- `git tag` یه برچسب دائمی روی commit فعلی زد.
- `git push origin v1.1.0` اون برچسب رو فرستاد GitHub.
- GitHub Action خودکار اجرا شد، پوشهٔ تمیز ساخت، ZIP کرد و Release منتشر کرد.

برو تب **Actions** و ببین در حال اجراست. بعد از تموم شدن، برو تب **Releases**.

### روش دستی

1. صفحهٔ ریپو → سمت راست → **Releases** → **Create a new release**
2. **Choose a tag** → بنویس `v1.1.0` → **Create new tag**
3. **Release title:** `v1.1.0 — Persian/English UI + Row Actions`
4. **Description:** محتوای بخش `[1.1.0]` از `CHANGELOG.md` رو پیست کن
5. فایل ZIP افزونه رو Drag & Drop کن
6. **Publish release**

---

## ۱۰. کار روزمره بعد از این

### چرخهٔ ساده (تغییرات کوچک)

```bash
# ۱. ببین چی عوض شده
git status

# ۲. دقیقاً ببین چه خط‌هایی عوض شدند
git diff

# ۳. فایل‌ها را stage کن
git add .

# ۴. commit بگیر
git commit -m "fix: correct WebP quality validation range"

# ۵. بفرست روی GitHub
git push
```

### چرخهٔ حرفه‌ای (قابلیت جدید با Branch)

```bash
# ۱. یک شاخه جدید بساز و برو داخلش
git checkout -b feature/avif-support

# ۲. کدت را بنویس، بعد commit کن
git add .
git commit -m "feat(optimizer): add AVIF output support"

# ۳. شاخه را push کن
git push -u origin feature/avif-support

# ۴. برو GitHub — دکمه سبز "Compare & pull request" را می‌بینی
# ۵. Pull Request بساز، توضیح بده، Merge کن

# ۶. برگرد روی main و به‌روزرسانی کن
git checkout main
git pull

# ۷. شاخه تمام‌شده را پاک کن
git branch -d feature/avif-support
```

### انتشار نسخه جدید

هر بار که نسخه بالا می‌بری، **این سه جا باید یکی باشن** (وگرنه CI خطا می‌ده):

1. `smart-image-optimizer.php` → خط `* Version: 1.2.0`
2. `smart-image-optimizer.php` → `define( 'SIO_VERSION', '1.2.0' );`
3. `readme.txt` → `Stable tag: 1.2.0`

بعد:

```bash
# CHANGELOG.md را هم به‌روز کن، سپس:
git add .
git commit -m "chore(release): bump version to 1.2.0"
git push

git tag -a v1.2.0 -m "Release version 1.2.0"
git push origin v1.2.0
```

---

## ۱۱. خطاهای رایج و راه‌حل

<details>
<summary><strong>❌ fatal: not a git repository</strong></summary>

توی پوشهٔ اشتباهی هستی یا `git init` نزدی.

```bash
pwd        # ببین کجایی
ls -a      # ببین پوشه .git هست یا نه
cd /مسیر/درست
```
</details>

<details>
<summary><strong>❌ Updates were rejected because the remote contains work...</strong></summary>

موقع ساخت ریپو در GitHub، README یا LICENSE اضافه کردی.

```bash
git pull origin main --allow-unrelated-histories
# اگر تداخل بود، فایل‌ها را دستی درست کن، سپس:
git add .
git commit -m "chore: merge remote history"
git push
```
</details>

<details>
<summary><strong>❌ Support for password authentication was removed</strong></summary>

پسورد حساب کار نمی‌کنه. باید Personal Access Token بسازی — [بخش ۷](#۷-اتصال-به-github-و-push) رو ببین.
</details>

<details>
<summary><strong>❌ فایلی را اشتباهی commit کردم</strong></summary>

```bash
# فقط از stage درش بیار (فایل سالم می‌ماند)
git restore --staged path/to/file

# اگر قبلاً commit شده و می‌خواهی از ردیابی خارج شود
git rm --cached path/to/file
echo "path/to/file" >> .gitignore
git commit -m "chore: stop tracking local file"
```
</details>

<details>
<summary><strong>❌ می‌خواهم آخرین commit را برگردانم</strong></summary>

```bash
# فقط پیام commit را اصلاح کن
git commit --amend -m "پیام درست"

# commit را لغو کن ولی تغییرات فایل‌ها بماند
git reset --soft HEAD~1

# ⚠️ commit و تغییرات را کاملاً پاک کن (برگشت‌ناپذیر)
git reset --hard HEAD~1
```
</details>

<details>
<summary><strong>❌ اسم فایل‌های فارسی به شکل عدد نمایش داده می‌شود</strong></summary>

```bash
git config --global core.quotepath false
```
</details>

<details>
<summary><strong>❌ GitHub Action قرمز شد (CI failed)</strong></summary>

برو تب **Actions**، روی اجرای قرمز کلیک کن، لاگ رو بخون.
معمول‌ترین دلیل: **عدم تطابق شمارهٔ نسخه** بین سه جای گفته‌شده در [بخش ۱۰](#۱۰-کار-روزمره-بعد-از-این).
</details>

---

## ۱۲. چیت‌شیت دستورات

### شروع

| دستور | کار |
|---|---|
| `git init` | تبدیل پوشه به ریپازیتوری |
| `git clone <url>` | دانلود یک ریپو |
| `git remote add origin <url>` | اتصال به GitHub |
| `git remote -v` | نمایش آدرس‌های متصل |

### کار روزمره

| دستور | کار |
|---|---|
| `git status` | وضعیت فعلی |
| `git diff` | تغییرات stage نشده |
| `git diff --staged` | تغییرات stage شده |
| `git add .` | stage کردن همه |
| `git add file.php` | stage کردن یک فایل |
| `git restore --staged file.php` | خارج کردن از stage |
| `git commit -m "msg"` | ثبت commit |
| `git commit --amend` | اصلاح آخرین commit |
| `git push` | ارسال به GitHub |
| `git pull` | دریافت از GitHub |

### شاخه‌ها

| دستور | کار |
|---|---|
| `git branch` | لیست شاخه‌ها |
| `git checkout -b name` | ساخت و رفتن به شاخه جدید |
| `git checkout main` | رفتن به شاخه main |
| `git merge name` | ادغام شاخه |
| `git branch -d name` | حذف شاخه |

### تاریخچه

| دستور | کار |
|---|---|
| `git log --oneline` | تاریخچه خلاصه |
| `git log --oneline --graph --all` | تاریخچه گرافیکی |
| `git show <hash>` | جزئیات یک commit |

### تگ و انتشار

| دستور | کار |
|---|---|
| `git tag` | لیست تگ‌ها |
| `git tag -a v1.2.0 -m "msg"` | ساخت تگ |
| `git push origin v1.2.0` | ارسال تگ |
| `git push --tags` | ارسال همهٔ تگ‌ها |

---

## ✅ چک‌لیست نهایی

- [ ] Git نصب و تنظیم شد
- [ ] ریپو در GitHub ساخته شد (بدون README/LICENSE)
- [ ] `Shayan-alinezhad` در فایل‌ها جایگزین شد
- [ ] `git init` → `git add .` → `git commit` انجام شد
- [ ] `git push -u origin main` موفق بود
- [ ] Description و Topics در About پر شد
- [ ] Issues و Discussions فعال شد
- [ ] برچسب‌های Issue ساخته شد
- [ ] اسکرین‌شات‌ها به README اضافه شد
- [ ] Branch protection روی `main` فعال شد
- [ ] اولین Release با تگ `v1.1.0` منتشر شد
- [ ] تب Actions سبز است ✅

---

<div align="center">

**موفق باشی شایان! 🚀**

ساخته‌شده برای [Cloner](https://clonerr.ir)

</div>
