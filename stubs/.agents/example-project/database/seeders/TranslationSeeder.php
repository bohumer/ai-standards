<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Common UI
            'admin.common.all' => ['sk' => 'Všetko', 'en' => 'All'],
            'admin.common.active' => ['sk' => 'Aktívne', 'en' => 'Active'],
            'admin.common.inactive' => ['sk' => 'Neaktívne', 'en' => 'Inactive'],
            'admin.common.actions' => ['sk' => 'Akcie', 'en' => 'Actions'],
            'admin.common.edit' => ['sk' => 'Upraviť', 'en' => 'Edit'],
            'admin.common.delete' => ['sk' => 'Zmazať', 'en' => 'Delete'],
            'admin.common.cancel' => ['sk' => 'Zrušiť', 'en' => 'Cancel'],
            'admin.common.save' => ['sk' => 'Uložiť', 'en' => 'Save'],
            'admin.common.saving' => ['sk' => 'Ukladám...', 'en' => 'Saving...'],
            'admin.common.back_to_list' => ['sk' => 'Späť na zoznam', 'en' => 'Back to list'],
            'admin.common.search_placeholder' => ['sk' => 'Hľadať...', 'en' => 'Search...'],
            'admin.common.reset_filters' => ['sk' => 'Zrušiť filtre', 'en' => 'Reset filters'],
            'admin.common.no_records' => ['sk' => 'Žiadne záznamy.', 'en' => 'No records found.'],
            'admin.common.select_option' => ['sk' => '-- Vyberte možnosť --', 'en' => '-- Select option --'],
            'admin.common.draft_loaded' => ['sk' => 'Načítaný uložený koncept', 'en' => 'Saved draft loaded'],
            'admin.common.confirm_delete_title' => ['sk' => 'Potvrdenie zmazania', 'en' => 'Confirm deletion'],
            'admin.table.bulk_delete_success' => ['sk' => 'Vybrané záznamy boli zmazané.', 'en' => 'Selected records were deleted.'],
            'admin.table.bulk_update_success' => ['sk' => 'Vybrané záznamy boli upravené.', 'en' => 'Selected records were updated.'],

            // Products
            'admin.product.index.title' => ['sk' => 'Produkty', 'en' => 'Products'],
            'admin.product.index.add_new' => ['sk' => 'Pridať produkt', 'en' => 'Add product'],
            'admin.product.show.title' => ['sk' => 'Detail produktu', 'en' => 'Product detail'],
            'admin.product.create.title' => ['sk' => 'Nový produkt', 'en' => 'New product'],
            'admin.product.edit.title' => ['sk' => 'Úprava produktu', 'en' => 'Edit product'],
            'admin.product.fields.name' => ['sk' => 'Názov', 'en' => 'Name'],
            'admin.product.fields.slug' => ['sk' => 'URL slug', 'en' => 'URL slug'],
            'admin.product.fields.description' => ['sk' => 'Popis', 'en' => 'Description'],
            'admin.product.fields.price' => ['sk' => 'Cena', 'en' => 'Price'],
            'admin.product.fields.category' => ['sk' => 'Kategória', 'en' => 'Category'],
            'admin.product.fields.status' => ['sk' => 'Stav', 'en' => 'Status'],
            'admin.product.fields.author' => ['sk' => 'Autor', 'en' => 'Author'],
            'admin.product.fields.is_active' => ['sk' => 'Produkt je aktívny', 'en' => 'Product is active'],
            'admin.product.validation.name_required' => ['sk' => 'Slovenský názov produktu je povinný.', 'en' => 'Slovak product name is required.'],
            'admin.product.validation.slug_unique' => ['sk' => 'Tento URL slug sa už používa.', 'en' => 'This URL slug is already in use.'],
            'admin.product.validation.price_min' => ['sk' => 'Cena nemôže byť záporná.', 'en' => 'Price cannot be negative.'],
            'admin.product.messages.created' => ['sk' => 'Produkt bol vytvorený.', 'en' => 'Product has been created.'],
            'admin.product.messages.updated' => ['sk' => 'Produkt bol aktualizovaný.', 'en' => 'Product has been updated.'],
            'admin.product.messages.deleted' => ['sk' => 'Produkt bol zmazaný.', 'en' => 'Product has been deleted.'],
            'admin.product.delete.confirm_text' => ['sk' => 'Naozaj chcete zmazať produkt :name?', 'en' => 'Are you sure you want to delete :name?'],

            // Settings
            'admin.setting.index.title' => ['sk' => 'Nastavenia a číselníky', 'en' => 'Settings & dictionaries'],
            'admin.setting.index.add_new' => ['sk' => 'Pridať možnosť', 'en' => 'Add option'],
            'admin.setting.show.title' => ['sk' => 'Detail nastavenia', 'en' => 'Setting detail'],
            'admin.setting.create.title' => ['sk' => 'Nová možnosť', 'en' => 'New option'],
            'admin.setting.edit.title' => ['sk' => 'Úprava možnosti', 'en' => 'Edit option'],
            'admin.setting.fields.group' => ['sk' => 'Skupina', 'en' => 'Group'],
            'admin.setting.fields.value' => ['sk' => 'Kód (value)', 'en' => 'Code (value)'],
            'admin.setting.fields.label' => ['sk' => 'Zobrazovaný názov', 'en' => 'Display label'],
            'admin.setting.validation.exists' => ['sk' => 'Táto možnosť v danej skupine už existuje.', 'en' => 'This option already exists in the group.'],
            'admin.setting.validation.label_required' => ['sk' => 'Slovenský názov možnosti je povinný.', 'en' => 'Slovak option label is required.'],
            'admin.setting.messages.created' => ['sk' => 'Možnosť bola vytvorená.', 'en' => 'Option has been created.'],
            'admin.setting.messages.updated' => ['sk' => 'Možnosť bola aktualizovaná.', 'en' => 'Option has been updated.'],
            'admin.setting.messages.deleted' => ['sk' => 'Možnosť bola zmazaná.', 'en' => 'Option has been deleted.'],
            'admin.setting.delete.confirm_text' => ['sk' => 'Naozaj chcete zmazať možnosť :label?', 'en' => 'Are you sure you want to delete option :label?'],

            // Translations
            'admin.translation.index.title' => ['sk' => 'Preklady rozhrania', 'en' => 'UI translations'],
            'admin.translation.index.add_new' => ['sk' => 'Pridať preklad', 'en' => 'Add translation'],
            'admin.translation.show.title' => ['sk' => 'Detail prekladu', 'en' => 'Translation detail'],
            'admin.translation.create.title' => ['sk' => 'Nový preklad', 'en' => 'New translation'],
            'admin.translation.edit.title' => ['sk' => 'Úprava prekladu', 'en' => 'Edit translation'],
            'admin.translation.validation.key_required' => ['sk' => 'Kľúč prekladu je povinný.', 'en' => 'Translation key is required.'],
            'admin.translation.validation.key_exists' => ['sk' => 'Tento kľúč prekladu už existuje.', 'en' => 'This translation key already exists.'],
            'admin.translation.validation.value_required' => ['sk' => 'Slovenský text prekladu je povinný.', 'en' => 'Slovak translation text is required.'],
            'admin.translation.messages.created' => ['sk' => 'Preklad bol vytvorený.', 'en' => 'Translation has been created.'],
            'admin.translation.messages.updated' => ['sk' => 'Preklad bol aktualizovaný.', 'en' => 'Translation has been updated.'],
            'admin.translation.messages.deleted' => ['sk' => 'Preklad bol zmazaný.', 'en' => 'Translation has been deleted.'],
            'admin.translation.delete.confirm_text' => ['sk' => 'Naozaj chcete zmazať prekladový kľúč :key?', 'en' => 'Are you sure you want to delete key :key?'],
        ];

        foreach ($items as $key => $locales) {
            foreach ($locales as $lang => $value) {
                Translation::query()->updateOrCreate(
                    ['lang' => $lang, 'key' => $key],
                    ['value' => $value]
                );
            }
        }
    }
}
