<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/8/16
 * Time: 3:07 PM
 */

interface IWidgetService {
    public function getWidgetData($parameters, $limit, $order_by);
    public function getWidgetDataCount($parameters);
    public function implementDerivedColumns($data);
} 