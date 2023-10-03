<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Currency;
use App\Models\Unit;
use App\Models\Product;
use App\Models\product_warehouse;
use App\Models\Client;
use App\Models\Provider;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //warehouse
        $warehouse = Warehouse::create([
            'name'    => 'warehouse',
            'mobile'  => '0123456789',
            'city'    => 'city',
            'country' => 'country',
        ]);
        $warehouse_2 = Warehouse::create([
            'name'    => 'warehouse_2',
            'mobile'  => '0123456789',
            'city'    => 'city',
            'country' => 'country',
        ]);

        //category
        $category = Category::create([
            'name'  => 'category',
            'photo' => 'category.jpg',
        ]);
        $category_2 = Category::create([
            'name'  => 'category_2',
            'photo' => 'category_2.jpg',
        ]);

        //brand
        $brand = Brand::create([
            'name'  => 'brand',
            'image' => 'brand.jpg',
        ]);
        $brand_2 = Brand::create([
            'name'  => 'brand_2',
            'image' => 'brand_2.jpg',
        ]);

        //currency
        $currency = Currency::create([
            'code'   => 1,
            'name'   => 'currency',
            'symbol' => 'currency',
        ]);
        $currency_2 = Currency::create([
            'code'   => 2,
            'name'   => 'currency_2',
            'symbol' => 'currency_2',
        ]);

        //unit
        $unit = Unit::create([
            'name'           => 'unit',
            'ShortName'      => 'unit',
            'operator'       => '*',
            'operator_value' => 1,
        ]);
        $unit_2 = Unit::create([
            'name'           => 'unit_2',
            'ShortName'      => 'unit_2',
            'operator'       => '/',
            'operator_value' => 10,
            'base_unit'      => 1,
        ]);

        //product
        $product = Product::create([
            'code'             => 1,
            'name'             => 'product',
            'image'            => 'product.jpg',
            'note'             => 'note',
            'cost'             => 1,
            'price'            => 1,
            'taxNet'           => 0,
            'tax_method'       => 1,
            'stock_alert'      => 1,
            'is_variant'       => 0,
            'is_active'        => 1,
            'category_id'      => 1,
            'brand_id'         => 1,
            'unit_id'          => 1,
            'unit_sale_id'     => 1,
            'unit_purchase_id' => 1,
        ]);
        $product_2 = Product::create([
            'code'             => 2,
            'name'             => 'product_2',
            'image'            => 'product_2.jpg',
            'note'             => 'note',
            'cost'             => 2,
            'price'            => 2,
            'taxNet'           => 0,
            'tax_method'       => 1,
            'stock_alert'      => 2,
            'is_variant'       => 0,
            'is_active'        => 1,
            'category_id'      => 2,
            'brand_id'         => 2,
            'unit_id'          => 2,
            'unit_sale_id'     => 2,
            'unit_purchase_id' => 2,
        ]);


        //product_warehouse
        $product_warehouse = product_warehouse::create([
            'product_id'   => 1,
            'warehouse_id' => 1,
        ]);
        $product_warehouse_2 = product_warehouse::create([
            'product_id'   => 1,
            'warehouse_id' => 2,
        ]);
        $product_warehouse_3 = product_warehouse::create([
            'product_id'   => 2,
            'warehouse_id' => 1,
        ]);
        $product_warehouse_4 = product_warehouse::create([
            'product_id'   => 2,
            'warehouse_id' => 2,
        ]);

        

        //client
        $client = Client::create([
            'code'    => 1,
            'name'    => 'client',
            'phone'   => '0123456789',
            'country' => 'country',
            'city'    => 'city',
            'adresse' => 'address',
        ]);
        $client_2 = Client::create([
            'code'    => 2,
            'name'    => 'client_2',
            'phone'   => '0123456789',
            'country' => 'country',
            'city'    => 'city',
            'adresse' => 'address',
        ]);

        //provider
        $provider = Provider::create([
            'code'    => 1,
            'name'    => 'provider',
            'phone'   => '0123456789',
            'country' => 'country',
            'city'    => 'city',
            'adresse' => 'address',
        ]);
        $provider_2 = Provider::create([
            'code'    => 2,
            'name'    => 'provider_2',
            'phone'   => '0123456789',
            'country' => 'country',
            'city'    => 'city',
            'adresse' => 'address',
        ]);



        

        //expenseCategory
        $expenseCategory = ExpenseCategory::create([
            'name'        => 'expenseCategory',
            'description' => 'description',
            'user_id'     => 1,
        ]);
        $expenseCategory_2 = ExpenseCategory::create([
            'name'        => 'expenseCategory_2',
            'description' => 'description_2',
            'user_id'     => 1,
        ]);

        //expense
        $expense = Expense::create([
            'date'                => now(),
            'Ref'                 => 'EXP_111',
            'details'             => 'details',
            'amount'              => 1,
            'expense_category_id' => 1,
            'warehouse_id'        => 1,
            'user_id'             => 1,
        ]);
        $expense_2 = Expense::create([
            'date'                => now(),
            'Ref'                 => 'EXP_222',
            'details'             => 'details_2',
            'amount'              => 2,
            'expense_category_id' => 2,
            'warehouse_id'        => 2,
            'user_id'             => 1,
        ]);
    }
}
