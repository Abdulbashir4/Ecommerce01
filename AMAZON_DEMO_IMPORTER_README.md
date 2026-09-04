# Amazon Demo Product Importer

This integration is designed for demonstration products only.

## Workflow

1. Admin Panel → Products → Amazon Demo Importer
2. Paste a public Amazon product URL.
3. Click **Preview Product**.
4. Edit product name, SKU, demo price, descriptions, specifications, category, subcategory, brand and SEO fields.
5. Select the images you want to keep.
6. Click **Import Demo Product**.

The imported record is stored in the existing `products` table as a normal product record with its Amazon source information.

## No Amazon API credentials required

This workflow does not use Amazon Creators API credentials. It only attempts to read publicly returned product-page HTML/metadata. It does not log in, bypass CAPTCHA, or bypass access controls.

Amazon pages can still refuse automated requests or return incomplete data. In that case the preview will show an error rather than fabricating product data.

## Installation

From the Laravel project root:

```bash
php artisan migrate
php artisan storage:link
php artisan optimize:clear
```

If your server is missing the PHP DOM extension, install/enable the `dom` PHP extension before using Artisan commands. The application itself also needs the normal Laravel PHP extensions listed by your existing project.

## Existing project safety

The importer uses the existing `Product` model/table and existing `products.create` permission. It does not replace the existing Product CRUD, checkout, cart, order or public product page.



## Drop-in installation
Extract this ZIP directly into the existing Laravel project folder and overwrite the included files. The importer does not require an `is_demo` column and does not require running a migration to import products. If the optional source-field migration is already applied, source tracking and duplicate-by-source-URL protection are used automatically.
