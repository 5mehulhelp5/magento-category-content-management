### Category Content Management (Magento 2 Module)

This module extends Magento category management in the Admin. It adds quick actions on the category edit page to:
- Copy selected attributes from one category to another
- Mass‑assign products to a category by SKU list (with simple positioning mode)
- Truncate (clear) all product assignments from a category

These tools help merchandisers manage category data faster without leaving the category screen.

### Prerequisites
- Magento Open Source/Adobe Commerce 2.4.x
- PHP 8.3 or 8.4 (per `composer.json`)
- Admin role with access to `Catalog > Categories` (`Magento_Catalog::categories`)

### Project structure
```text
Block/
Controller/
etc/
Model/
Plugin/
Service/
Ui/
view/
composer.json
registration.php
README.md
```

Notable directories:
- [Block](Block/): Admin blocks for rendering buttons on the category edit page (copy, mass assign, truncate).
- [Controller](Controller/): Admin controllers handling copy, mass assign, and truncate actions.
- [Service](Service/): Application services for attribute copy and product linking logic.
- [Plugin](Plugin/): Admin UI plugin to inject extra buttons into the category product assignment UI.
- [Ui](Ui/): UI data providers for admin modals/forms.
- [view](view/): Adminhtml layouts, templates, JS, and LESS for the UI.
- [etc](etc/): Module declaration and admin wiring (routes, DI, ACL via Magento catalog permissions).

Root files:
- [composer.json](composer.json): Module metadata and autoloading.
- [registration.php](registration.php): Module registration.

### Istruction
- Install via Composer
  ```bash
  composer require lucafuser/magento-category-content-management
  ```
- From the project root, enable the module:
  ```bash
  bin/magento module:enable LFuser_CategoryContentManagement
  ```
- Run setup upgrade:
  ```bash
  bin/magento setup:upgrade
  ```
- In production mode, also compile and deploy static content:
  ```bash
  bin/magento setup:di:compile
  bin/magento setup:static-content:deploy -f
  ```
- Clear caches:
  ```bash
  bin/magento cache:flush
  ```

### Usage
All features are available in the Admin at Catalog > Categories, on the category edit page.

- Copy category attributes
  - Opens a modal to choose source and target categories and which attributes to copy.
  - Runs via an admin controller that uses services under [Service](Service/).

- Mass‑assign products by SKU
  - Paste one SKU per line into the provided field; choose sorting mode (e.g., append/after).
  - Uses `CategoryProductLinkManagement` to link products efficiently.

- Truncate products in category
  - Removes existing product assignments for the current category.

Access control: actions reuse Magento’s `Magento_Catalog::categories` permission, so users with category management rights can use the buttons.

### Contributing
- Open an issue describing the change or problem you’re solving.
- Follow Magento coding standards and keep changes focused.
- Include clear commit messages and, when relevant, admin UI screenshots/GIFs.
- Submit a PR against this module with a concise description and testing notes.

### License
This project is licensed under the [Open Software License 3.0 (OSL-3.0)](LICENSE).