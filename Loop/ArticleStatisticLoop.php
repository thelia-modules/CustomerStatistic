<?php

namespace CustomerStatistic\Loop;


use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Order;
use Thelia\Model\OrderProduct;
use Thelia\Model\OrderQuery;
use Thelia\Model\Product;
use Thelia\Model\ProductI18nQuery;
use Thelia\Model\ProductQuery;
use Thelia\Model\ProductSaleElementsQuery;


class ArticleStatisticLoop extends BaseLoop implements ArraySearchLoopInterface
{
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createAnyTypeArgument('customer_id')
        );
    }

    /**
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function buildArray()
    {
        $customerId = $this->getCustomerId();

        $search = OrderQuery::create();
        $search->findByCustomerId($customerId);

        $listArticle = array();

        $grandTotal = 0;

        /** @var Order $order */
        foreach ($search as $order) {

            if (in_array((int) $order->getStatusId(), [2, 3, 4])) {
                /** @var OrderProduct $product */
                foreach ($order->getOrderProducts()->getData() as $product) {
                    $article = ProductQuery::create()->findOneByRef($product->getProductRef());
                    $locale = $this->request->getSession()->getLang()->getLocale();
                    if ($product->getWasInPromo() == 1) {
                        $price = $product->getPromoPrice();
                    } else {
                        $price = $product->getPrice();
                    }
                    $grandTotal += $price;
                    if ($article) {
                        $listArticle[$product->getProductRef()] = [
                            "Id"            => $article->getId(),
                            "Reference"     => $product->getProductRef(),
                            "Name"          => ProductI18nQuery::create()->filterByLocale($locale)->findOneById($article->getId())->getTitle(),
                            "UnitPrice"     => $price,
                            "Quantity"      => (int) $listArticle[$product->getProductRef()]["Quantity"] + (int) $product->getQuantity(),
                        ];
                    } else {
                        $listArticle[$product->getProductRef()] = [
                            "Id"            => 0,
                            "Reference"     => $product->getProductRef(),
                            "Name"          => $product->getTitle(),
                            "UnitPrice"     => $price,
                            "Quantity"      => (int) $listArticle[$product->getProductRef()]["Quantity"] + (int) $product->getQuantity(),
                        ];
                    }
                }
            }
        }

        $listArticle[0]= [
            "Id"            => -1,
            "Reference"     => -1,
            "Name"          => "Total",
            "UnitPrice"     => $grandTotal,
            "Quantity"      => 1,
        ];
        return $listArticle;
    }

    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $article) {
            $loopResultRow = new LoopResultRow($article);
            $loopResultRow
                ->set("PRODUCT_ID", $article["Id"])
                ->set("REFERENCE", $article["Reference"])
                ->set("NAME", $article["Name"])
                ->set("UNIT_PRICE", round((float) $article["UnitPrice"], 2))
                ->set("QUANTITY", $article["Quantity"])
                ->set("TOTAL_PRICE", round((int) $article["Quantity"] * (float) $article["UnitPrice"], 2))
            ;
            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}