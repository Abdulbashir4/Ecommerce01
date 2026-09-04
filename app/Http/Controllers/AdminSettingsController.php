<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminSettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    /*
    |--------------------------------------------------------------------------
    | Product Display Settings
    |--------------------------------------------------------------------------
    */

    private function productDisplayDefaults(): array
    {
        return [
            'layout' => 'grid',
            'mobile_columns' => 1,
            'tablet_columns' => 2,
            'desktop_columns' => 4,
            'gap' => 5,
            'card_padding' => 4,
            'text_align' => 'left',
            'card_style' => 'clean',
            'card_radius' => '2xl',
            'card_shadow' => 'sm',
            'hover_effect' => 'lift-shadow',
            'card_border' => true,
            'show_image' => true,
            'image_ratio' => '1/1',
            'image_fit' => 'cover',
            'image_zoom' => true,
            'image_background' => 'slate',
            'show_category' => true,
            'show_sku' => false,
            'show_brand' => true,
            'show_description' => false,
            'description_lines' => 2,
            'show_price' => true,
            'show_regular_price' => true,
            'show_rating' => false,
            'show_stock' => true,
            'show_stock_quantity' => false,
            'show_discount_badge' => true,
            'show_new_badge' => true,
            'show_featured_badge' => false,
            'show_wishlist' => true,
            'show_view_button' => true,
            'show_add_to_cart' => true,
            'button_style' => 'solid',
            'button_full_width' => true,
            'button_layout' => 'row',
            'name_lines' => 2,
            'price_size' => 'xl',
            'price_decimals' => 2,
            'currency_position' => 'before',
            'show_home_products' => true,
            'show_shop_products' => true,
            'home_title' => 'Best Seller Products',
            'home_limit' => 8,
            'home_desktop_columns' => 4,
        ];
    }

    private function productDisplaySettings(): array
    {
        $defaults = $this->productDisplayDefaults();
        $stored = Setting::get('product.display', []);
        return array_replace($defaults, is_array($stored) ? $stored : []);
    }

    public function productDisplay()
    {
        return view('admin.settings.product-display', [
            'settings' => $this->productDisplaySettings(),
        ]);
    }

    public function productDisplayUpdate(Request $request)
    {
        $data = $request->validate([
            'layout' => ['required', Rule::in(['grid', 'list'])],
            'mobile_columns' => ['required', 'integer', Rule::in([1, 2])],
            'tablet_columns' => ['required', 'integer', Rule::in([2, 3, 4])],
            'desktop_columns' => ['required', 'integer', Rule::in([3, 4, 5, 6])],
            'gap' => ['required', 'integer', Rule::in([3, 4, 5, 6, 8])],
            'card_padding' => ['required', 'integer', Rule::in([3, 4, 5, 6])],
            'text_align' => ['required', Rule::in(['left', 'center'])],
            'card_style' => ['required', Rule::in(['clean', 'bordered', 'soft', 'glass'])],
            'card_radius' => ['required', Rule::in(['none', 'lg', 'xl', '2xl', '3xl'])],
            'card_shadow' => ['required', Rule::in(['none', 'sm', 'md', 'lg', 'xl'])],
            'hover_effect' => ['required', Rule::in(['none', 'lift', 'zoom', 'lift-shadow'])],
            'image_ratio' => ['required', Rule::in(['1/1', '4/3', '3/4', '16/9', 'auto'])],
            'image_fit' => ['required', Rule::in(['cover', 'contain'])],
            'image_background' => ['required', Rule::in(['slate', 'white', 'transparent'])],
            'description_lines' => ['required', 'integer', Rule::in([1, 2, 3])],
            'button_style' => ['required', Rule::in(['solid', 'outline', 'soft'])],
            'button_layout' => ['required', Rule::in(['row', 'stack'])],
            'name_lines' => ['required', 'integer', Rule::in([1, 2, 3])],
            'price_size' => ['required', Rule::in(['sm', 'lg', 'xl', '2xl'])],
            'price_decimals' => ['required', 'integer', Rule::in([0, 1, 2])],
            'currency_position' => ['required', Rule::in(['before', 'after'])],
            'home_title' => ['required', 'string', 'max:100'],
            'home_limit' => ['required', 'integer', 'min:1', 'max:24'],
            'home_desktop_columns' => ['required', 'integer', Rule::in([2, 3, 4, 5, 6])],
        ]);

        foreach ([
            'card_border','show_image','image_zoom','show_category','show_sku','show_brand',
            'show_description','show_price','show_regular_price','show_rating','show_stock',
            'show_stock_quantity','show_discount_badge','show_new_badge','show_featured_badge',
            'show_wishlist','show_view_button','show_add_to_cart','button_full_width',
            'show_home_products','show_shop_products'
        ] as $key) {
            $data[$key] = $request->boolean($key);
        }

        $data = array_replace($this->productDisplayDefaults(), $data);
        Setting::set('product.display', $data, 'product', 'json');

        foreach (['show_image','show_sku','show_brand','show_category','show_stock','show_price','show_rating','show_add_to_cart'] as $key) {
            Setting::set('product.' . $key, $data[$key], 'product', 'boolean');
        }
        Setting::set('product.layout', $data['layout'], 'layout', 'string');
        Setting::set('product.columns', $data['desktop_columns'], 'layout', 'integer');

        return back()->with('success', 'Product display settings saved successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Layout Settings
    |--------------------------------------------------------------------------
    */

    private function layoutDefaults(): array
    {
        return [
            'product_layout' => $this->productDisplaySettings()['layout'],
            'mobile_columns' => $this->productDisplaySettings()['mobile_columns'],
            'tablet_columns' => $this->productDisplaySettings()['tablet_columns'],
            'desktop_columns' => $this->productDisplaySettings()['desktop_columns'],
            'gap' => $this->productDisplaySettings()['gap'],
            'text_align' => $this->productDisplaySettings()['text_align'],
            'sidebar' => Setting::get('layout.sidebar', 'right'),
            'container' => Setting::get('layout.container', '7xl'),
            'section_spacing' => Setting::get('layout.section_spacing', 'normal'),
            'shop_header' => Setting::get('layout.shop_header', true),
            'breadcrumbs' => Setting::get('layout.breadcrumbs', true),
            'pagination' => Setting::get('layout.pagination', true),
        ];
    }

    public function layout()
    {
        return view('admin.settings.layout', ['settings' => $this->layoutDefaults()]);
    }

    public function layoutUpdate(Request $request)
    {
        $data = $request->validate([
            'product_layout' => ['required', Rule::in(['grid', 'list'])],
            'mobile_columns' => ['required', 'integer', Rule::in([1, 2])],
            'tablet_columns' => ['required', 'integer', Rule::in([2, 3, 4])],
            'desktop_columns' => ['required', 'integer', Rule::in([3, 4, 5, 6])],
            'gap' => ['required', 'integer', Rule::in([3, 4, 5, 6, 8])],
            'text_align' => ['required', Rule::in(['left', 'center'])],
            'sidebar' => ['required', Rule::in(['left', 'right', 'none'])],
            'container' => ['required', Rule::in(['5xl', '6xl', '7xl', 'full'])],
            'section_spacing' => ['required', Rule::in(['compact', 'normal', 'large'])],
            'shop_header' => ['nullable', 'boolean'],
            'breadcrumbs' => ['nullable', 'boolean'],
            'pagination' => ['nullable', 'boolean'],
        ]);

        $product = $this->productDisplaySettings();
        $product['layout'] = $data['product_layout'];
        $product['mobile_columns'] = (int) $data['mobile_columns'];
        $product['tablet_columns'] = (int) $data['tablet_columns'];
        $product['desktop_columns'] = (int) $data['desktop_columns'];
        $product['gap'] = (int) $data['gap'];
        $product['text_align'] = $data['text_align'];
        Setting::set('product.display', $product, 'product', 'json');
        Setting::set('product.layout', $product['layout'], 'layout', 'string');
        Setting::set('product.columns', $product['desktop_columns'], 'layout', 'integer');

        Setting::set('layout.sidebar', $data['sidebar'], 'layout', 'string');
        Setting::set('layout.container', $data['container'], 'layout', 'string');
        Setting::set('layout.section_spacing', $data['section_spacing'], 'layout', 'string');
        Setting::set('layout.shop_header', $request->boolean('shop_header'), 'layout', 'boolean');
        Setting::set('layout.breadcrumbs', $request->boolean('breadcrumbs'), 'layout', 'boolean');
        Setting::set('layout.pagination', $request->boolean('pagination'), 'layout', 'boolean');

        return back()->with('success', 'Layout settings saved successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | General Settings
    |--------------------------------------------------------------------------
    */

    private function generalDefaults(): array
    {
        return [
            'site_name' => 'Optimum Biomedical',
            'tagline' => 'Medical equipment and biomedical products',
            'currency' => 'BDT',
            'currency_symbol' => '৳',
            'timezone' => 'Asia/Dhaka',
            'date_format' => 'd M Y',
            'time_format' => 'h:i A',
            'contact_email' => 'optimumbiomedical.50@gmail.com',
            'contact_phone' => '01862252456',
            'whatsapp' => '',
            'address' => 'Khilkhet, Dhaka',
            'default_country' => 'Bangladesh',
            'tax_enabled' => false,
            'tax_rate' => 0,
            'shipping_enabled' => true,
            'default_shipping_cost' => 0,
            'registration_enabled' => true,
            'guest_checkout' => false,
            'order_notification_email' => '',
            'maintenance' => false,
            'maintenance_message' => 'We are currently performing scheduled maintenance. Please check back soon.',
        ];
    }

    private function generalSettings(): array
    {
        return array_replace($this->generalDefaults(), [
            'site_name' => Setting::get('general.site_name', 'Optimum Biomedical'),
            'tagline' => Setting::get('general.tagline', 'Medical equipment and biomedical products'),
            'currency' => Setting::get('general.currency', 'BDT'),
            'currency_symbol' => Setting::get('general.currency_symbol', '৳'),
            'timezone' => Setting::get('general.timezone', 'Asia/Dhaka'),
            'date_format' => Setting::get('general.date_format', 'd M Y'),
            'time_format' => Setting::get('general.time_format', 'h:i A'),
            'contact_email' => Setting::get('general.contact_email', ''),
            'contact_phone' => Setting::get('general.contact_phone', ''),
            'whatsapp' => Setting::get('general.whatsapp', ''),
            'address' => Setting::get('general.address', ''),
            'default_country' => Setting::get('general.default_country', 'Bangladesh'),
            'tax_enabled' => Setting::get('general.tax_enabled', false),
            'tax_rate' => Setting::get('general.tax_rate', 0),
            'shipping_enabled' => Setting::get('general.shipping_enabled', true),
            'default_shipping_cost' => Setting::get('general.default_shipping_cost', 0),
            'registration_enabled' => Setting::get('general.registration_enabled', true),
            'guest_checkout' => Setting::get('general.guest_checkout', false),
            'order_notification_email' => Setting::get('general.order_notification_email', ''),
            'maintenance' => Setting::get('general.maintenance', false),
            'maintenance_message' => Setting::get('general.maintenance_message', ''),
        ]);
    }

    public function general()
    {
        return view('admin.settings.general', ['settings' => $this->generalSettings()]);
    }

    public function generalUpdate(Request $request)
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:20'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'timezone' => ['required', 'timezone'],
            'date_format' => ['required', 'string', 'max:50'],
            'time_format' => ['required', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'default_country' => ['required', 'string', 'max:100'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'default_shipping_cost' => ['required', 'numeric', 'min:0'],
            'order_notification_email' => ['nullable', 'email', 'max:255'],
            'maintenance_message' => ['required', 'string', 'max:500'],
        ]);

        $booleans = [
            'tax_enabled' => $request->boolean('tax_enabled'),
            'shipping_enabled' => $request->boolean('shipping_enabled'),
            'registration_enabled' => $request->boolean('registration_enabled'),
            'guest_checkout' => $request->boolean('guest_checkout'),
            'maintenance' => $request->boolean('maintenance'),
        ];

        foreach ($data as $key => $value) {
            if (!in_array($key, array_keys($booleans), true)) {
                Setting::set('general.' . $key, $value, 'general', is_numeric($value) && in_array($key, ['tax_rate', 'default_shipping_cost'], true) ? 'string' : 'string');
            }
        }
        foreach ($booleans as $key => $value) {
            Setting::set('general.' . $key, $value, 'general', 'boolean');
        }
        Setting::set('general.tax_rate', $data['tax_rate'], 'general', 'string');
        Setting::set('general.default_shipping_cost', $data['default_shipping_cost'], 'general', 'string');

        return back()->with('success', 'General settings saved successfully.');
    }
}
