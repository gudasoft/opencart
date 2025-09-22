# vqmod

schema https://raw.githubusercontent.com/vqmod/vqmod/refs/heads/master/vqmod.xsd

VQMOD version  v2.6.6-opencart

## How to Install

I used the vqmod-opencart release. Then I found that the opencart support is in a new repo:
https://github.com/vqmod/opencart/releases/


Here is how to install the latest version:

First install the vQmod package
Then install the opencart script package on top of that
Then run the installer


Commands:

```
    remote:
      chown -R www-data:mongrel vqmod/

    local:
        mkdir /tmp/vqmod
        cd /tmp/vqmod

        download and unzip:
            - https://github.com/vqmod/vqmod/releases
            - https://github.com/vqmod/opencart/releases

        wget https://github.com/vqmod/vqmod/releases/download/v2.6.8-stand-alone/vqmod-2.6.8-stand-alone.zip
        wget https://github.com/vqmod/opencart/releases/download/v4.0.0/vqmod-opencart-scripts-v4.0.0.zip

        unzip vqmod-2.6.8-stand-alone.zip -d vqmod-2.6.8-stand-alone/
        unzip vqmod-opencart-scripts-v4.0.0.zip -d vqmod-opencart-scripts-v4.0.0/

        scp -r ./vqmod-2.6.8-stand-alone/vqmod sarah:/WEBSITE_ROOT/upload/
        scp -r ./vqmod-opencart-scripts-v4.0.0/* sarah:/WEBSITE_ROOT/upload/vqmod/

    remote:
      chown -R www-data:mongrel vqmod/ && group-is-ok

      sudo chmod g+w index.php adminp/index.php ./vqmod/pathReplaces.php

      set the admin folder if not set ./vqmod/pathReplaces.php:

      put it above the // END REPLACES //  and replace the exsiting one
        replace:
          if (defined('DIR_CATALOG')) { $replaces[] = array('~^admin\b~', basename(DIR_APPLICATION)); }
        with:
          if (defined('DIR_CATALOG')) { $replaces[] = array('~^admin\b~', 'adminp'); }
```

Then go to

    http://yourwebsite.example.com/vqmod/install/


## Remove cache

delete
  cd vqmod
  rm -rf mods.cache vqcache/


## Cheat

 Here are examples based on the vqmod schema:

  Basic Examples

  Simple text replacement:
  <?xml version="1.0" encoding="UTF-8"?>
  <modification xmlns="https://github.com/vqmod/vqmod">
      <id>simple-replacement</id>
      <version>1.0.0</version>
      <vqmver>2.6.0</vqmver>
      <author>Example Author</author>

      <file name="config.php">
          <operation>
              <search position="replace"><![CDATA[define('OLD_VALUE', 'old');]]></search>
              <add><![CDATA[define('NEW_VALUE', 'new');]]></add>
          </operation>
      </file>
  </modification>

  Add code after a line:
  <?xml version="1.0" encoding="UTF-8"?>
  <modification xmlns="https://github.com/vqmod/vqmod">
      <id>add-after-example</id>
      <version>1.0.0</version>
      <vqmver>2.6.0</vqmver>
      <author>Developer</author>

      <file name="index.php">
          <operation>
              <search position="after"><![CDATA[<?php]]></search>
              <add><![CDATA[
  // Added by modification
  require_once 'custom_functions.php';
  ]]></add>
          </operation>
      </file>
  </modification>

  Full Complex Examples

  Multi-operation modification with error handling:
  <?xml version="1.0" encoding="UTF-8"?>
  <modification xmlns="https://github.com/vqmod/vqmod">
      <id>complex-ecommerce-mod</id>
      <version>2.1.0</version>
      <vqmver>2.6.0</vqmver>
      <author>John Developer</author>

      <file name="catalog/controller/product/product.php" path="catalog/controller/product/" error="log">
          <operation info="Add custom product fields" error="skip">
              <search position="before" index="1"><![CDATA[$data['heading_title'] = $product_info['name'];]]></search>
              <add trim="true"><![CDATA[
              // Custom product modifications
              $data['custom_field_1'] = isset($product_info['custom_field_1']) ? $product_info['custom_field_1'] : '';
              $data['custom_field_2'] = isset($product_info['custom_field_2']) ? $product_info['custom_field_2'] : '';
              ]]></add>
              <ignoreif><![CDATA[$data['custom_field_1']]]></ignoreif>
          </operation>

          <operation info="Modify price calculation">
              <search position="replace" regex="true"><![CDATA[\$price\s*=\s*\$this->currency->format.*?;]]></search>
              <add><![CDATA[$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'],
  $this->config->get('config_tax')), $this->session->data['currency']);

              // Apply custom discount logic
              if (isset($product_info['discount_percentage']) && $product_info['discount_percentage'] > 0) {
                  $discounted_price = $product_info['price'] * (1 - $product_info['discount_percentage'] / 100);
                  $price = $this->currency->format($this->tax->calculate($discounted_price, $product_info['tax_class_id'],
  $this->config->get('config_tax')), $this->session->data['currency']);
              }]]></add>
          </operation>

          <operation info="Add to bottom of file">
              <search position="bottom"></search>
              <add><![CDATA[

  // Custom functions added by modification
  private function customProductFunction($product_id) {
      // Custom logic here
      return true;
  }]]></add>
          </operation>
      </file>

      <file name="catalog/model/catalog/product.php" error="abort">
          <operation info="Extend product model" error="log">
              <search position="after" offset="2"><![CDATA[public function getProduct($product_id) {]]></search>
              <add trim="false"><![CDATA[
          // Log product access
          $this->db->query("INSERT INTO product_access_log SET product_id = '" . (int)$product_id . "', access_time = NOW()");
              ]]></add>
          </operation>
      </file>
  </modification>

  Advanced regex-based modification:
  <?xml version="1.0" encoding="UTF-8"?>
  <modification xmlns="https://github.com/vqmod/vqmod">
      <id>advanced-regex-mod</id>
      <version>1.5.2</version>
      <vqmver>2.6.0</vqmver>
      <author>Advanced Modder</author>

      <file name="admin/controller/sale/order.php" path="admin/controller/sale/">
          <operation info="Replace all currency format calls with custom formatter">
              <search position="replace" regex="true" index="all"><![CDATA[\$this->currency->format\(([^,]+),\s*([^)]+)\)]]></search>
              <add><![CDATA[$this->custom_currency->formatAdvanced($1, $2, array('show_symbol' => true, 'decimal_places' => 2))]]></add>
              <ignoreif regex="true"><![CDATA[custom_currency->formatAdvanced]]></ignoreif>
          </operation>

          <operation info="Add custom validation before order processing">
              <search position="ibefore" trim="true"><![CDATA[if ($this->validate()) {]]></search>
              <add><![CDATA[
              // Custom pre-validation checks
              if (!$this->customOrderValidation($this->request->post)) {
                  $this->error['custom'] = 'Custom validation failed';
              }
              ]]></add>
          </operation>

          <operation info="Insert at top of file">
              <search position="top"></search>
              <add><![CDATA[// Modified by Advanced Order Extension v1.5.2
              ]]></add>
          </operation>
      </file>
  </modification>

  Key features demonstrated:
  - Required elements: id, version, vqmver, author
  - Position types: replace, after, before, top, bottom, ibefore, iafter
  - Attributes: regex, index, offset, trim, error, info
  - Error handling: skip, log, abort
  - Conditional logic: ignoreif elements
  - CDATA sections: For preserving code formatting
  - Multiple operations: Per file and across files

> what means ibefore iafter? diff with after and before??

● The difference between before/after and ibefore/iafter is about whitespace and indentation handling:

  before and after

  - Add code exactly where the search match ends/begins
  - No automatic indentation matching
  - You control exact positioning and whitespace

  ibefore and iafter (intelligent before/after)

  - Add code with automatic indentation detection
  - Matches the indentation level of the found line
  - "i" stands for "intelligent" or "indented"

  Examples

  Original code:
  class MyClass {
      public function test() {
          if ($condition) {
              $result = true;
          }
      }
  }

  Using before:
  <search position="before"><![CDATA[$result = true;]]></search>
  <add><![CDATA[        $debug = 'before action';
  ]]></add>

  Result:
  class MyClass {
      public function test() {
          if ($condition) {
          $debug = 'before action';  // No automatic indentation
              $result = true;
          }
      }
  }

  Using ibefore:
  <search position="ibefore"><![CDATA[$result = true;]]></search>
  <add><![CDATA[$debug = 'before action';]]></add>

  Result:
  class MyClass {
      public function test() {
          if ($condition) {
              $debug = 'before action';  // Automatically indented to match
              $result = true;
          }
      }
  }

  Practical Usage

  Use before/after when:
  - You need exact control over positioning
  - Adding to beginning/end of files
  - Working with specific formatting requirements

  Use ibefore/iafter when:
  - Adding code inside functions/classes/blocks
  - Want consistent indentation with existing code
  - Working with nested structures
