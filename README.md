# Useful extension for open-cart for OpenCart 4.1.x.x

This repo works/tested with OpenCart: 4.1.0.3

Content:

- patch - enables OCMOD in Opencart
- vqmod - extensions
- ocmod - extensions



## OCMOD Patch

Copy the files from version of opencart to your hosting root.

OCMOD will now work.

Read more [here](patch/fix-ocmod.md)


## OCMOD Extensions

location: ocmod/dual-prices.ocmod

Display the prices in BGN and EUR.

Inspired from:
- [tsvetelintsachev](https://tsvetelintsachev.com/%D0%BC%D0%BE%D0%B4%D1%83%D0%BB-bgn-%D0%B8-eur-%D0%B2-opencart-4/) extension
- [tankoo](https://tankoo.eu/opencart/bezplaten-modul-za-dve-valuti-bgn-eur/) extension


## VQMod Extensions

This directory contains VQMod extensions

VQMod: 2.6.6+ with the opencart scripts from their new repository

### Auto Model No For New Product

location: vqmod/auto-product-model.xml

Automatically generates a random model number for new products in the admin panel.

- **Target**: `admin/controller/catalog/product.php`
- **Functionality**: Replaces empty model field with a random 13-digit number (1000000000000-9999999999999)

### Hide Model No For Product

location vqmod/hide-product-model.xml

Hides the product model number from the product display page.

- **Target**: `catalog/view/template/product/product.twig`
- **Functionality**: Comments out the model display line in product templates



## Author

[Gudasoft](https://www.gudasoft.com)
