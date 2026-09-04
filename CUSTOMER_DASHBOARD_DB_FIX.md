# Customer Dashboard database repair

The customer dashboard requires three tables: `customer_addresses`, `wishlists`, and `product_reviews`.

A repair migration has been added:

`database/migrations/2026_08_31_000002_repair_customer_dashboard_tables.php`

It is safe to run when the original dashboard migration already created the tables, and it also repairs installations where the original migration is marked as complete but a dashboard table is missing.

## After replacing the project

From the project root, run:

```bash
php artisan migrate
php artisan storage:link
php artisan optimize:clear
```

Then open `/account` again.

Do **not** delete or manually recreate the `wishlists` table. Let Laravel run the migration so the foreign keys and indexes are created consistently.
