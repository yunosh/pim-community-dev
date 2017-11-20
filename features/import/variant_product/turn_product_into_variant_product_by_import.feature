@javascript
Feature: Import variant products that were previously products
  In order to import my variant products
  As a catalog manager
  I need to be able to turn a product into a variant product

  Background:
    Given the "catalog_modeling" catalog configuration
    And the following root product models:
      | code      | family_variant      | description-ecommerce-en_US      |
      | model-col | clothing_color_size | Magnificent Cult of Luna t-shirt |
      | model-nin | clothing_size       |                                  |
    And the following sub product models:
      | code            | parent    | family_variant      | color | composition             |
      | model-col-white | model-col | clothing_color_size | white | cotton 90%, viscose 10% |
    And the following products:
      | sku         | family   | color | size | description-ecommerce-en_US | composition | weight   |
      | col-white-m | clothing | white | m    | Cult of Luna tee            | 100% cotton | 478 GRAM |
      | nin-s       | clothing |       | s    | Nine Inch Nails tee         | 100% cotton |          |
    And I am logged in as "Julia"

  Scenario: Turn a product into a variant product inside a family variant with 2 levels of hierarchy
    Given the following CSV file to import:
      """
      sku;parent
      col-white-m;model-col
      """
    And the following job "csv_catalog_modeling_product_import" configuration:
      | filePath | %file to import% |
    When I am on the "csv_catalog_modeling_product_import" import job page
    And I launch the import job
    And I wait for the "csv_catalog_modeling_product_import" job to finish
    Then the parent of "col-white-m" should be "model-col"

  Scenario: Turn a product into a variant product inside a family variant with 1 levels of hierarchy
    Given the following CSV file to import:
      """
      sku;parent
      nin-s;model-nin
      """
    And the following job "csv_catalog_modeling_product_import" configuration:
      | filePath | %file to import% |
    When I am on the "csv_catalog_modeling_product_import" import job page
    And I launch the import job
    And I wait for the "csv_catalog_modeling_product_import" job to finish
    Then the parent of "nin-s" should be "model-nin"

  Scenario: Turning a product into a variant product overwrites the values already defined in its ancestry
    Given the following CSV file to import:
      """
      sku;parent
      col-white-m;model-col
      """
    And the following job "csv_catalog_modeling_product_import" configuration:
      | filePath | %file to import% |
    When I am on the "csv_catalog_modeling_product_import" import job page
    And I launch the import job
    And I wait for the "csv_catalog_modeling_product_import" job to finish
    Then the product "col-white-m" should have the following values:
      | description-ecommerce-en_US | Magnificent Cult of Luna t-shirt |
      | composition                 | cotton 90%, viscose 10%          |

  Scenario: Turning a product into a variant product overwrites the empty or non defined values in its ancestry
    Given the following CSV file to import:
      """
      sku;parent
      nin-s;model-nin
      """
    And the following job "csv_catalog_modeling_product_import" configuration:
      | filePath | %file to import% |
    When I am on the "csv_catalog_modeling_product_import" import job page
    And I launch the import job
    And I wait for the "csv_catalog_modeling_product_import" job to finish
    Then the product "nin-s" should have the following values:
      | description-ecommerce-en_US |  |
      | composition                 |  |

  Scenario: Update the values defined in the last level of the variant of the family
    Given the following CSV file to import:
      """
      sku;parent;weight
      col-white-m;model-col;150 GRAM
      """
    And the following job "csv_catalog_modeling_product_import" configuration:
      | filePath | %file to import% |
    When I am on the "csv_catalog_modeling_product_import" import job page
    And I launch the import job
    And I wait for the "csv_catalog_modeling_product_import" job to finish
    Then the product "col-white-m" should have the following values:
      | weight | 150 GRAM |

  Scenario: Skip the product in case the parent does not exist

  Scenario: Skip the product in case a non eligible parent is set to the product

  Scenario: Skip the product in case its family is not that the same than the parent's

  Scenario: Skip the product in case the variant axes are empty

  Scenario: Skip the product in case there is already a sibling with the same variant axes

  Scenario: Skip the product in case we try to change its parent


