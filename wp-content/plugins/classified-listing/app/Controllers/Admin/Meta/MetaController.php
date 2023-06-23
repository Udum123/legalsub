<?php

namespace Rtcl\Controllers\Admin\Meta;

class MetaController
{
    public function __construct() {
        new AddMetaBox();
        new AddTermMetaField();
        new ListingSubmitBoxMice();
        new SavePricingMetaData();
        new SaveCFGData();
        new SaveListingMetaData();
        new RemoveMetaBox();
        new ListingMetaColumn();
        PricingMetaColumn::init();
        OrderMetaColumn::init();
    }

}