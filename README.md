# Optimum Biomedical — Laravel 13 + Livewire 4 + Blade + Tailwind CSS 4

এই project আপনার দেওয়া `admin.zip` এবং `optimum.sql`-এর existing e-commerce flow ধরে Laravel-এ rebuild করার foundation। Laravel 13-এর জন্য PHP 8.3+, Livewire 4-এর জন্য Laravel 10+/PHP 8.1+, এবং Tailwind 4 + Vite ব্যবহার করা হয়েছে।

## কী আছে
- Customer registration/login/logout
- Product catalogue, search, pagination
- Category/Subcategory/Brand data model
- Product CRUD (Admin)
- Cart / quantity update / remove
- Checkout
- COD / Bkash / Nagad / Card selection
- Order creation + order items
- Customer account + order history
- Admin dashboard with live database counts/revenue
- Admin order status/payment status update
- Company model
- Livewire product search component
- Tailwind CSS 4 + Vite
- Original legacy SQL backup: `database/legacy-optimum.sql`
- Original ZIP backup: `database/legacy-admin.zip`

## Windows / XAMPP setup
1. PHP 8.3+, Composer এবং Node.js/NPM install করুন।
2. এই project folder `C:\xampp\htdocs\optimum-biomedical`-এ রাখুন।
3. MySQL-এ `optimum` নামে database বানান (অথবা `.env`-এ নাম পরিবর্তন করুন)।
4. PowerShell থেকে project folder-এ গিয়ে `.setup.ps1` নয়, **`.setup.ps1`-এর বদলে `.setup.ps1` টাইপ করবেন না**—সঠিক command হলো:

```powershell
.\setup.ps1
```

5. তারপর:

```powershell
php artisan serve
```

6. খুলুন: http://127.0.0.1:8000

## Demo admin
- Phone: `01700000000`
- Password: `password`

Production-এ অবশ্যই password পরিবর্তন করবেন।

## Existing SQL data import
`database/legacy-optimum.sql` পুরোনো database-এর raw backup। নতুন Laravel schema চালানোর পর পুরোনো data আনতে সরাসরি raw SQL import না করে mapping/clean migration ব্যবহার করা নিরাপদ, কারণ নতুন schema-তে `users.is_admin`, `orders.user_id`, indexes এবং Laravel-friendly timestamps যোগ করা হয়েছে।

## Production
- `.env`-এ `APP_DEBUG=false`
- Strong admin password
- `php artisan migrate --force`
- `php artisan storage:link`
- `npm run build`
- Web server document root অবশ্যই `public/` হবে
