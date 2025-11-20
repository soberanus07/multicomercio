public function aroundProcess($subject, \Closure $proceed, $jsLayout)
{
    var_dump($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']);
            die();
    $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['sortOrder'] = 41;
 
    $customJsLayout = $proceed($jsLayout);

    return $customJsLayout;
}