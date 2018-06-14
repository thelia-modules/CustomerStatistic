<?php

namespace CustomerStatistic\Loop;


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
        $search->filterByCustomerId($customerId);

        $listArticle = array();

        /** @var Order $order */
        foreach ($search as $order) {

            if (in_array((int) $order->getStatusId(), [2, 3, 4])) {
                /** @var OrderProduct $product */
                foreach ($order->getOrderProducts()->getData() as $product) {
                    $listArticle[$product->getProductRef()] = [
                        "Id"            => ProductSaleElementsQuery::create()->findOneById($product->getProductSaleElementsId())->getProductId(),
                        "Reference"     => $product->getProductRef(),
                        "Name"          => $product->getTitle(),
                        "UnitPrice"     => $product->getPrice(),
                        "Quantity"      => (int) $listArticle[$product->getProductRef()]["Quantity"] + (int) $product->getQuantity(),
                    ];
                }
            }
        }

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
                ->set("UNIT_PRICE", (float) $article["UnitPrice"])
                ->set("QUANTITY", $article["Quantity"])
                ->set("TOTAL_PRICE", (int) $article["Quantity"] * (float) $article["UnitPrice"])
            ;
            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}