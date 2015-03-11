<?php

interface iPackaging {
  public function pack( $products_dimensions );
  public function create_weight_dimensions_param( $standard_params );
}