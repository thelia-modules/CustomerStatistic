<?php

namespace CustomerStatistic\Hook;


use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

class CustomerEditHook extends BaseHook
{
    public function onCustomerEdit(HookRenderEvent $event)
    {
        $customer_id = intval($event->getArgument("customer_id", null));

        if (0 !== $customer_id) {
            $html = $this->render("article-statistic.html", array("customer_id" => $customer_id));

            if ("" !== $html) {
                $event->add($html);
            }
        }
    }
}