# Useful extension for open-cart for OpenCart 4.1.x.x

Those are tested with OpenCart: 4.1.x.x

Folders content:

- patch - enable OCMOD
- vqmod - extensions
- ocmod - extensions


## Patch

Copy the files from version of opencart to your hosting. OCMOD will now work.


## OCMOD Extensions

location: ocmod/dual-prices.ocmod

Display the prices in BGN and EUR.

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
