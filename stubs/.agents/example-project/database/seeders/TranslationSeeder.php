<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            // Admin Global
            ['key' => 'admin.save', 'lang' => 'sk', 'value' => 'Uložiť'],
            ['key' => 'admin.save', 'lang' => 'en', 'value' => 'Save'],
            ['key' => 'admin.edit', 'lang' => 'sk', 'value' => 'Upraviť'],
            ['key' => 'admin.edit', 'lang' => 'en', 'value' => 'Edit'],
            ['key' => 'admin.delete', 'lang' => 'sk', 'value' => 'Zmazať'],
            ['key' => 'admin.delete', 'lang' => 'en', 'value' => 'Delete'],
            ['key' => 'admin.back', 'lang' => 'sk', 'value' => 'Späť'],
            ['key' => 'admin.back', 'lang' => 'en', 'value' => 'Back'],
            ['key' => 'admin.create', 'lang' => 'sk', 'value' => 'Vytvorić'],
            ['key' => 'admin.create', 'lang' => 'en', 'value' => 'Create'],
            
            // Product Specific
            ['key' => 'product.index.title', 'lang' => 'sk', 'value' => 'Produkty'],
            ['key' => 'product.index.title', 'lang' => 'en', 'value' => 'Products'],
            ['key' => 'product.create.title', 'lang' => 'sk', 'value' => 'Nový produkt'],
            ['key' => 'product.create.title', 'lang' => 'en', 'value' => 'New Product'],
            ['key' => 'product.edit.title', 'lang' => 'sk', 'value' => 'Úprava produktu'],
            ['key' => 'product.edit.title', 'lang' => 'en', 'value' => 'Edit Product'],
            ['key' => 'product.show.title', 'lang' => 'sk', 'value' => 'Detail produktu'],
            ['key' => 'product.show.title', 'lang' => 'en', 'value' => 'Product Details'],

            ['key' => 'product.field.name', 'lang' => 'sk', 'value' => 'Názov produktu'],
            ['key' => 'product.field.name', 'lang' => 'en', 'value' => 'Product Name'],
            ['key' => 'product.field.description', 'lang' => 'sk', 'value' => 'Popis'],
            ['key' => 'product.field.description', 'lang' => 'en', 'value' => 'Description'],
            ['key' => 'product.field.price', 'lang' => 'sk', 'value' => 'Cena'],
            ['key' => 'product.field.price', 'lang' => 'en', 'value' => 'Price'],
            ['key' => 'product.field.is_active', 'lang' => 'sk', 'value' => 'Aktívny'],
            ['key' => 'product.field.is_active', 'lang' => 'en', 'value' => 'Active'],
            ['key' => 'product.field.category', 'lang' => 'sk', 'value' => 'Kategória'],
            ['key' => 'product.field.category', 'lang' => 'en', 'value' => 'Category'],
            
            // Setting Specific
            ['key' => 'setting.index.title', 'lang' => 'sk', 'value' => 'Nastavenia'],
            ['key' => 'setting.index.title', 'lang' => 'en', 'value' => 'Settings'],
        ];

        foreach ($translations as $trans) {
            DB::table('translations')->updateOrInsert(
                ['key' => $trans['key'], 'lang' => $trans['lang']],
                ['value' => $trans['value']]
            );
        }
    }
}
