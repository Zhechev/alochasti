# GiftShare — A Community Gifting Board

GiftShare is a small Laravel 12 web application where registered users can post items they are giving away for free. Other users can browse, vote, and comment on listings. The UI is built with **Bootstrap 5** and the interactive parts are built with **Livewire 3**.

## Features

- Authentication required for the main content (guests are redirected to login/register)
- Listings (items):
  - Fields: title, description, category, city
  - Tags: assign multiple tags to an item
  - Optional: weight, dimensions
  - Photos: upload one or more images (stored on the public disk)
  - Status: available / gifted (with gifted timestamp)
  - Ownership rules: only the author can edit/delete an item
  - Photo management: authors can delete existing photos while editing
- Browsing & filtering (feed):
  - Pagination
  - Filter by category, city, status (available/gifted)
  - Text search (title or tag name)
  - Filter by one or more tags
  - Sorting (newest / most upvoted)
  - My Items page (shows only the authenticated user's listings)
- Item details:
  - Full description, all photos, category, city, author
  - Gifted indicator
- Interactions (no full page reload, Livewire):
  - Upvote/downvote with toggle (one vote per user per item enforced at DB level)
  - Comments (create + paginated list)
- Demo data:
  - `php artisan migrate:fresh --seed` creates categories, users, items, votes and comments
  - Item photos in seed data are generated SVG placeholders saved to the public disk
  - Bulgarian cities are seeded into the `cities` table and items reference them via `city_id`

## Tech Stack

- PHP: **8.2+**
- Framework: **Laravel 12**
- UI: **Bootstrap 5**
- Interactivity: **Livewire 3**
- Database: **MariaDB**

## Requirements (local / non-Docker)

- PHP **8.2+** (with common extensions required by Laravel, including `pdo_mysql`)
- [Composer](https://getcomposer.org/)
- Node.js **20.19+** (or **22.12+**) + npm
- MariaDB (or MySQL-compatible server)

## Setup (local / non-Docker)

1) Install dependencies:

- `composer install`
- `npm install`

2) Configure environment:

- Copy `.env.example` to `.env`
- Set MariaDB connection values (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
- Generate app key: `php artisan key:generate`

Example MariaDB settings (adjust to your machine):

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=giftshare
DB_USERNAME=root
DB_PASSWORD=
```

Make sure the database exists (example):

- `mysql -u root -p -e "CREATE DATABASE giftshare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"`

3) Public storage symlink (required for item photos):

- `php artisan storage:link`

4) Migrate + seed:

- `php artisan migrate:fresh --seed`

5) Run the app:

- `php artisan serve`
- In another terminal:
  - dev: `npm run dev`
  - or build once: `npm run build`

Tip: you can also use `composer run dev` to start Laravel + Vite in one command (requires Node installed).

## Setup (Docker / Sail) — optional

If you prefer Docker, you can run the project with Laravel Sail:

- Start containers: `./vendor/bin/sail up -d`
- Install dependencies:
  - `./vendor/bin/sail composer install`
  - `./vendor/bin/sail npm install`
- Generate key: `./vendor/bin/sail artisan key:generate`
- Migrate + seed: `./vendor/bin/sail artisan migrate:fresh --seed`
- Storage link: `./vendor/bin/sail artisan storage:link`
- Run Vite: `./vendor/bin/sail npm run dev`

Note: if you ever see `403` when opening `/storage/...` assets in Docker, recreate the symlink inside the container/project as a relative link.

## Demo credentials

- Email: `test@example.com`
- Password: `password`

## Notes

- This repository pins Composer to a PHP 8.2 platform for consistent installs across environments.
- Vite warns if Node is older than the recommended version. For best results, use **Node 20.19+** (or **22.12+**).
