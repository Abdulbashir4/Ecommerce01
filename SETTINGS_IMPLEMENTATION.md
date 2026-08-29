# Optimum Biomedical — Settings Implementation

This source package includes expanded, persistent admin controls for:

## General Settings
- Website name
- Tagline
- Currency code and symbol
- Timezone
- Date/time formats
- Default country
- Contact email, phone, WhatsApp and address
- Order notification email
- Tax enable/rate
- Shipping enable/default shipping cost
- Customer registration
- Guest checkout
- Maintenance mode and maintenance message

## Layout Settings
- Product grid/list
- Mobile/tablet/desktop columns
- Grid gap
- Product text alignment
- Content container width
- Section spacing
- Shop sidebar left/right/hidden
- Shop header
- Breadcrumbs
- Pagination

## Product Display
- Responsive columns and grid/list
- Card padding/style/radius/shadow/hover
- Border and image zoom
- Image ratio/fit/background
- Category, brand, SKU, description
- Price and regular price
- Rating
- Stock status and optional stock quantity
- Discount/NEW/Featured badges
- Wishlist
- View Product / Add to Cart
- Button style, width and layout
- Name/description line limits
- Price size, decimals and currency position
- Home/Shop placement and home product limits

## Important
The project intentionally does not include `vendor/` or `node_modules/`. Run Composer/NPM installation in the local project before serving the application.

Recommended:
1. `composer install`
2. `copy .env.example .env` (Windows, if `.env` does not exist)
3. `php artisan key:generate`
4. Configure `.env` for MySQL database `optimum`
5. `php artisan migrate --seed`
6. `php artisan optimize:clear`
7. `npm install`
8. `npm run build`
