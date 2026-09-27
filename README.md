# Velina Beauty — Laravel Catalog + Admin Panel

This package contains the **application code** for the Velina Beauty storefront:
public catalog (logo, categories, barcode cards, WhatsApp checkout) and a
full admin panel (login, products CRUD with manual barcode entry, categories).

It is delivered as *application files*, not a full Laravel skeleton (the
vendor/ folder with the framework itself is installed by Composer on your
machine — that part can't be shipped as plain files). Setup takes about
5 minutes.

## 1. Requirements
- PHP 8.1+
- Composer
- A local server (`php artisan serve` is enough — works on phones over your
  Wi-Fi, and on any browser, old or new, since the frontend is plain
  HTML/CSS/JS with no build step)

## 2. Create a fresh Laravel project

```bash
composer create-project laravel/laravel velina-beauty "^10.10"
cd velina-beauty
```

## 3. Copy these files into it

Copy every folder from this package **on top of** the fresh project,
overwriting these specific files:

```
app/Models/User.php                        → replace
app/Http/Kernel.php                         → replace
composer.json                               → merge (or just copy, it matches this project)
.env.example                                → replace
```

And **add** these new files/folders (they don't exist in a fresh install):

```
app/Models/Category.php
app/Models/Product.php
app/Http/Controllers/CatalogController.php
app/Http/Controllers/Auth/LoginController.php
app/Http/Controllers/Admin/*.php
app/Http/Middleware/IsAdmin.php
database/migrations/2024_01_01_*.php
database/seeders/*.php
resources/views/*  (all folders: layouts, catalog, auth, admin)
routes/web.php                              → replace
public/images/logo.png
public/images/placeholder.png
```

Then open `config/services.php` and paste in the block from
`config/services.snippet.php` (add the `whatsapp` array next to the
existing `mailgun`, `postmark`, etc. entries). This is the only config file
you need to hand-edit.

## 4. Configure

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set:
- `WHATSAPP_NUMBER` — the store owner's WhatsApp number, digits only,
  international format, no `+` (example: `970599123456`)
- `ADMIN_EMAIL` / `ADMIN_PASSWORD` — the admin login the seeder will create

For SQLite (simplest — no database server needed):
```bash
touch database/database.sqlite
```
`DB_CONNECTION=sqlite` is already set in `.env.example`. If you'd rather
use MySQL, switch the `DB_*` lines in `.env` instead.

## 5. Install, migrate, seed, link storage

```bash
composer install
php artisan migrate --seed
php artisan storage:link
```

The seeder creates:
- An admin account (`ADMIN_EMAIL` / `ADMIN_PASSWORD` from `.env`)
- Two categories: **Makeup Tools** and **Accessories**
- 8 sample products with real barcode numbers (00101–00108), matching the
  approved mockup (8 cards, 2 per row)

## 6. Run it

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

- Storefront: `http://localhost:8000/` (or `http://YOUR-COMPUTER-IP:8000`
  from a phone on the same Wi-Fi)
- Admin login: `http://localhost:8000/login`
- Admin panel: `http://localhost:8000/admin` (redirects to login if needed)

## How it matches what you asked for

- **English only**, LTR layout.
- Category tab says **"Makeup Tools"**, not "All" — the "All" tab is
  removed; only the two real categories show.
- **8 product cards**, 2 per row, exactly like the reference screenshot.
- Each card shows a **real scannable barcode** (drawn client-side by
  JsBarcode from the number you type in the admin — no image files, no
  server load, works instantly on any phone).
- **No total item count** shown — only the total price, in the sticky
  bottom bar, as requested earlier.
- **"Confirm Order via WhatsApp"** button builds a message with every
  added product's name, barcode, quantity, price and image link, then
  opens `wa.me/<your number>` with that message pre-filled.
- **Admin panel**: login-protected (`/admin/*` requires an admin account),
  add/edit/delete products, type the barcode number by hand (or scan it
  with a barcode gun into the same text field — it's just a text input),
  leading zeros like `00107` are preserved because the barcode column is
  stored as text, not a number. Categories are manageable too.
- Built with **Laravel + Blade + Tailwind (via CDN, no build step) +
  vanilla JS** — loads fast and works on old phones, since there's no
  heavy JavaScript framework.

## Notes / things to customize before going live
- Replace `public/images/logo.png` with a higher-resolution export of the
  logo if you have one — the current one is cropped from your reference
  photo.
- Product photos: upload them from the admin "Add product" page — they're
  stored in `storage/app/public/products` and served through the symlink
  created by `storage:link`.
- Change the seeded admin password after first login (there's no
  "change password" screen yet — update it via `php artisan tinker` or
  add one if you'd like; happy to add that screen on request).
- For production, set `APP_ENV=production`, `APP_DEBUG=false`, and put
  the app behind HTTPS (WhatsApp's `wa.me` links work over HTTP too, but
  a real domain with HTTPS looks more trustworthy to buyers).
