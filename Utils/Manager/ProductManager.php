<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ASF\ProductBundle\Utils\Manager;

/**
 * Product Entity Manager.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class ProductManager extends DefaultManager implements ProductManagerInterface
{
    /**
     * Populate a new product.
     *
     * @param ProductInterface $product
     */
    public function populateProduct(ProductInterface $product)
    {
        $terms = explode('/', $product->getName());
        $brand_name = $terms[1];
        $weight = isset($terms[2]) && $terms[2] != 0 ? $terms[2] : null;
        $capacity = isset($terms[3]) && $terms[3] != 0 ? $terms[3] : null;
        $product->setName($terms[0])->setState(ProductModel::STATE_PUBLISHED)->setWeight($weight)->setCapacity($capacity);
    }

    /**
     * Return a product based on keywords.
     *
     * @param string $keywords
     */
    public function getProductWithFormattedKeywords($keywords)
    {
        $product_name = $this->findProductNameInString($keywords, true);
        $brand_names = $this->findBrandNameInString($keywords, true);
        $weight = $this->findWeightPropertyInString($keywords);
        $capacity = $this->findCapacityPropertyInString($keywords);

        $terms = explode(' ', $keywords);
        $brand_name = null;
        foreach ($terms as $term) {
            if ($term == in_array($term, $brand_names)) {
                $brand_name = $term;
            }
        }

        return $this->getRepository()->findProductByNameBrandWeightAndCapacity($product_name, $brand_name, $weight, $capacity);
    }

    /**
     * Return a list of products form list of keywords.
     *
     * @param string $keywords
     *
     * @return array
     */
    public function getProductsByKeywords($keywords)
    {
        $result = array();

        // Detect products names in keywords
        $products_by_name = $this->findProductNameInString($keywords);

        // Detect brand names in keywords
        $brand_by_name = $this->findBrandNameInString($keywords);

        // Detect weight property in keywords
        $weight = $this->findWeightPropertyInString($keywords);

        // Detect capacity property in keywords
        $capacity = $this->findCapacityPropertyInString($keywords);

        foreach ($products_by_name as $product_name => $weighting) {

            // If in $keywords typed in search field, we found strings matched with a brand name,
            // we search products ON product name AND brand name
            if (count($brand_by_name) > 0) {
                foreach ($brand_by_name as $brand_names) {
                    foreach ($brand_names as $brand_name) {
                        $products = $this->getRepository()->findProductsByNameAndBrandContains($product_name, $brand_name);
                        foreach ($products as $product) {
                            $result[$product->getId()] = $product;
                        }
                    }
                }

                // Else if just product name, we search products by name
            } else {
                $products = $this->getRepository()->findProductsByNameContains($product_name);
                foreach ($products as $product) {
                    $result[$product->getId()] = $product;
                }
            }

            if (count($result) == 0) {
                continue;
            }

            if (!is_null($weight) && !is_null($capacity)) {
                $wc_products = array();
                foreach ($result as $product) {
                    if ($product->getWeight() == $weight && $product->getCapacity() == $capacity) {
                        $wc_products[] = $product;
                    }
                }
                $result = $wc_products;
            } elseif (!is_null($weight) && is_null($capacity)) {
                $weight_products = array();
                foreach ($result as $product) {
                    if ($product->getWeight() == $weight) {
                        $weight_products[] = $product;
                    }
                }
                $result = $weight_products;
            } elseif (is_null($weight) && !is_null($capacity)) {
                $capacity_products = array();
                foreach ($result as $product) {
                    if ($product->getCapacity() == $capacity) {
                        $capacity_products[] = $product;
                    }
                }
                $result = $capacity_products;
            }
        }

        return $result;
    }

    /**
     * Clean keywords for search in repository.
     *
     * @param string $keywords
     *
     * @return string
     */
    public function cleanKeywords($keywords)
    {
        $return = '';
        $keywords = mb_strtolower(trim($keywords), 'UTF-8');
        $keywords = str_replace(array('-', '_', '\'', '"'), ' ', $keywords);
        $keywords = mb_ereg_replace('/[^A-Za-z0-9 ]/', '', $keywords);
        $terms = explode(' ', $keywords);

        foreach ($terms as $term) {
            if (strlen($term) >= 3) {
                $return .= ' '.$term;
            }
        }

        return trim($return);
    }

    /**
     * Find a product name from a list of keywords.
     *
     * @param string $string
     * @param bool   $is_flat
     *
     * @return array
     */
    public function findProductNameInString($string, $is_flat = false)
    {
        $string = $this->cleanKeywords($string);
        $terms = explode(' ', $string);
        $result = array();

        foreach ($terms as $term) {
            if (!isset($result[$term])) {
                $result[$term] = array();
            }
            $products = $this->getRepository()->findProductsByNameContains($term);
            foreach ($products as $product) {
                if (!in_array(strtolower($product->getName()), $result[$term])) {
                    $result[$term][] = strtolower($product->getName());
                }
            }
        }

        if ($is_flat == true) {
            $return = '';
            foreach ($result as $searched_term => $values) {
                if (count($values) > 0) {
                    $return .= ' '.$searched_term;
                }
            }

            return empty(trim($return)) ? null : trim($return);
        }

        return $this->joinKeywords($result);
    }

    /**
     * Join keywords.
     *
     * @param array $keywords
     */
    public function joinKeywords($keywords)
    {
        $joined_terms = implode(' ', array_keys($keywords));
        $result = array($joined_terms => array());
        $products = $this->getRepository()->findProductsByNameContains($joined_terms);
        foreach ($products as $product) {
            if (!in_array(strtolower($product->getName()), $result[$joined_terms])) {
                $result[$joined_terms][] = strtolower($product->getName());
            }
        }
        $keywords = array_merge($keywords, $result);

        $weighting = array();
        foreach ($keywords as $searched_term => $product_names) {
            foreach ($product_names as $product_name) {
                if (!isset($weighting[$product_name])) {
                    $weighting[$product_name] = 0;
                }
                ++$weighting[$product_name];
            }
        }
        arsort($weighting);

        $heavy_weighting = array();
        $old_value = null;
        foreach ($weighting as $key => $value) {
            if (is_null($old_value)) {
                $old_value = $value;
                $heavy_weighting[$key] = $value;
            } elseif ($value > $old_value) {
                $old_value = $value;
                $heavy_weighting = array($key => $value);
            } elseif ($value == $old_value) {
                if (!isset($heavy_weighting[$key])) {
                    $heavy_weighting[$key] = $value;
                }
            }
        }

        return $heavy_weighting;
    }

    /**
     * Find a brand name from a list of keywords.
     *
     * @param string $string
     * @param bool   $is_flat
     *
     * @return array
     */
    public function findBrandNameInString($string, $is_flat = false)
    {
        $string = $this->cleanKeywords($string);
        $terms = explode(' ', $string);
        $result = array();

        foreach ($terms as $term) {
            $products = $this->getRepository()->findProductsByBrandNameContains($term);
            foreach ($products as $product) {
                $result[$term][$product->getBrand()->getId()] = $product->getBrand()->getName();
            }
        }

        if ($is_flat == true) {
            $flat_result = array();
            foreach ($result as $brand_names) {
                foreach ($brand_names as $name) {
                    $flat_result[] = $name;
                }
            }

            return $flat_result;
        }

        return $result;
    }

    /**
     * Find weight property in string.
     *
     * @param string $string
     */
    public function findWeightPropertyInString($string)
    {
        $terms = explode(' ', strtolower($string));
        $weight = null;

        foreach ($terms as $key => $term) {
            $term = str_replace(',', '.', $term);
            // If the term is a numeric value (exemple : 0.5 or 1, etc.)
            if (1 === preg_match('/^(?:[1-9]\d*|0)?(?:\.\d+)?$/', $term)) {
                $value = $term;
                // We check first if the next term is a kilogramm unit
                if (isset($terms[$key + 1]) &&  (1 === preg_match('/^t$/', $terms[$key + 1]) || 1 === preg_match('/^kg$/', $terms[$key + 1]) || 1 === preg_match('/^g$/', $terms[$key + 1]))) {
                    $value .= $terms[$key + 1];
                } else {
                    continue;
                }
                $transformer = new StringToWeightTransformer();
                $weight = $transformer->reverseTransform($value);
                break;

                // If the term is a numeric value followed by a unit
            } elseif (1 === preg_match('/^(?:[1-9]\d*|0)?(?:\.\d+)?(t)$/', $term) || 1 === preg_match('/^(?:[1-9]\d*|0)?(?:\.\d+)?(kg)$/', $term) || 1 === preg_match('/^(?:[1-9]\d*|0)?(?:\.\d+)?(g)$/', $term)) {
                $transformer = new StringToWeightTransformer();
                $weight = $transformer->reverseTransform($term);
                break;
            }
        }

        return $weight;
    }

    /**
     * Find capacity property in string.
     *
     * @param string $string
     */
    public function findCapacityPropertyInString($string)
    {
        $terms = explode(' ', strtolower($string));
        $capacity = null;

        foreach ($terms as $key => $term) {
            $term = str_replace(',', '.', $term);
            // If the term is a numeric value (exemple : 0.5 or 1, etc.)
            if (1 === preg_match('/^(?:[1-9]\d*|0)?(?:\.\d+)?$/', $term)) {
                $value = $term;
                // We check first if the next term is a kilogramm unit
                if (isset($terms[$key + 1]) &&  (1 === preg_match('/^ml$/', $terms[$key + 1]) || 1 === preg_match('/^dl$/', $terms[$key + 1]) || 1 === preg_match('/^cl$/', $terms[$key + 1]) || 1 === preg_match('/^l$/', $terms[$key + 1]))) {
                    $value .= $terms[$key + 1];
                } else {
                    continue;
                }
                $transformer = new StringToLiterTransformer();
                $capacity = $transformer->reverseTransform($value);
                break;

                // If the term is a numeric value followed by a unit
            } elseif (1 === preg_match('/^(?:[1-9]\d*|0)?(?:\.\d+)?(ml)$/', $term) || 1 === preg_match('/^(?:[1-9]\d*|0)?(?:\.\d+)?(dl)$/', $term) || 1 === preg_match('/^(?:[1-9]\d*|0)?(?:\.\d+)?(cl)$/', $term) || 1 === preg_match('/^(?:[1-9]\d*|0)?(?:\.\d+)?(l)$/', $term)) {
                $transformer = new StringToLiterTransformer();
                $capacity = $transformer->reverseTransform($term);
                break;
            }
        }

        return $capacity;
    }

    /**
     * Return formatted product name.
     *
     * @param ProductInterface $product
     *
     * @return string
     */
    public function getFormattedProductName(ProductInterface $product)
    {
        return $product->getName().
        (!is_null($product->getBrand()) ? ' '.$product->getBrand()->getName() : '').
        (!is_null($product->getWeight()) ? ' '.$product->getWeight().'Kg' : '').
        (!is_null($product->getCapacity()) ? ' '.$product->getCapacity().'L' : '');
    }

    /**
     * Find a product brand name from a list of keywords.
     *
     * @param string $string
     *
     * @return array
     */
    public function findProductBrandNameInString($string)
    {
        $string = strtolower(trim($string));
        $terms = explode(' ', $string);
        $result = array();

        foreach ($terms as $term) {
            $term = preg_replace('/[^A-Za-z0-9 ]/', '', $term);
            $products = $this->getRepository()->findProductsByBrand($term);
            $result[$term] = $products;
        }

        return $result;
    }
}
