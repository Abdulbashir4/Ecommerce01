<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id(); $t->string('name',150); $t->string('phone',50)->nullable()->unique(); $t->string('password');
            $t->timestamp('created_at')->nullable(); $t->timestamp('updated_at')->nullable(); $t->string('profile_image')->nullable(); $t->boolean('is_admin')->default(false); $t->rememberToken();
        });
        Schema::create('categories', function(Blueprint $t){$t->increments('category_id');$t->string('category_name');$t->string('category_image')->nullable();$t->timestamps();});
        Schema::create('subcategories', function(Blueprint $t){$t->increments('subcategory_id');$t->unsignedInteger('category_id');$t->string('subcategory_name');$t->timestamps();$t->index('category_id');});
        Schema::create('brands', function(Blueprint $t){$t->increments('brand_id');$t->unsignedInteger('subcategory_id');$t->string('brand_name');$t->timestamps();$t->index('subcategory_id');});
        Schema::create('products', function(Blueprint $t){
            $t->increments('product_id');$t->string('product_name')->nullable();$t->string('slug')->nullable()->unique();$t->string('sku',100)->nullable()->unique();
            $t->unsignedInteger('category_id')->nullable();$t->unsignedInteger('subcategory_id')->nullable();$t->unsignedInteger('brand_id')->nullable();
            $t->text('short_description')->nullable();$t->longText('long_description')->nullable();$t->longText('specifications')->nullable();
            $t->decimal('price',10,2)->nullable();$t->decimal('discount_price',10,2)->nullable();$t->integer('discount_percent')->nullable();$t->decimal('purchase_price',10,2)->nullable();
            $t->integer('stock_qty')->nullable();$t->enum('stock_status',['In Stock','Out of Stock'])->default('In Stock');$t->integer('min_order_qty')->nullable();$t->integer('max_order_qty')->nullable();
            $t->string('thumbnail')->nullable();$t->string('featured_image')->nullable();$t->decimal('weight',10,2)->nullable();$t->string('dimensions')->nullable();$t->decimal('shipping_cost',10,2)->nullable();
            $t->boolean('is_featured')->default(false);$t->boolean('is_trending')->default(false);$t->boolean('is_new')->default(false);$t->boolean('flash_sale')->default(false);$t->boolean('status')->default(true);
            $t->string('meta_title')->nullable();$t->string('meta_keywords')->nullable();$t->text('meta_description')->nullable();$t->timestamps();$t->json('gallery_images')->nullable();
            $t->index(['category_id','subcategory_id','brand_id']);$t->index(['status','stock_status']);
        });
        Schema::create('orders', function(Blueprint $t){$t->increments('order_id');$t->string('customer_name',200)->nullable();$t->string('email',200)->nullable();$t->string('phone',100)->nullable();$t->string('address')->nullable();$t->string('city',100)->nullable();$t->string('postal_code',50)->nullable();$t->string('country',100)->nullable();$t->string('payment_method',50)->nullable();$t->decimal('total_amount',10,2)->default(0);$t->dateTime('created_at')->nullable();$t->enum('order_status',['Pending','Processing','Shipped','Completed','Cancelled'])->default('Pending');$t->enum('payment_status',['Unpaid','Paid','Refunded'])->default('Unpaid');$t->unsignedBigInteger('user_id')->nullable();$t->index('user_id');});
        Schema::create('order_items', function(Blueprint $t){$t->increments('item_id');$t->unsignedInteger('order_id')->nullable();$t->unsignedInteger('product_id')->nullable();$t->string('product_name')->nullable();$t->decimal('price',10,2)->nullable();$t->integer('qty')->nullable();$t->decimal('total',10,2)->nullable();$t->foreign('order_id')->references('order_id')->on('orders')->cascadeOnDelete();});
        Schema::create('companies', function(Blueprint $t){$t->increments('company_id');$t->string('company_name');$t->string('logo')->nullable();$t->text('about_us')->nullable();$t->string('mobile_number',20)->nullable();$t->string('hotline_number',20)->nullable();$t->string('whatsapp_number',20)->nullable();$t->string('email')->nullable();$t->string('facebook_page')->nullable();$t->string('youtube_channel')->nullable();$t->text('office_address')->nullable();$t->string('google_map_location')->nullable();$t->string('support_hours',100)->nullable();$t->text('privacy_policy')->nullable();$t->text('terms_conditions')->nullable();$t->text('refund_policy')->nullable();$t->text('shipping_policy')->nullable();$t->decimal('average_rating',2,1)->default(0);$t->integer('total_reviews')->default(0);$t->boolean('status')->default(true);$t->timestamps();});
        Schema::create('company_gallery', function(Blueprint $t){$t->id();$t->string('image_name')->nullable();$t->timestamps();});
        Schema::create('company_info', function(Blueprint $t){$t->id();$t->string('company_name')->nullable();$t->text('slider_image')->nullable();$t->text('right_slider')->nullable();$t->string('phone')->nullable();$t->string('email')->nullable();$t->text('address')->nullable();$t->string('logo')->nullable();$t->string('banner')->nullable();$t->string('facebook')->nullable();$t->string('youtube')->nullable();$t->string('gallery')->nullable();$t->text('about_us')->nullable();$t->string('map_location')->nullable();$t->timestamps();});
    }
    public function down(): void { foreach(['order_items','orders','products','brands','subcategories','categories','company_gallery','company_info','companies','users'] as $table) Schema::dropIfExists($table); }
};
