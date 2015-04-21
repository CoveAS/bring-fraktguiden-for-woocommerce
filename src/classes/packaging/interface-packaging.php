<?php

interface iPackaging {
  public function pack( $products_dimensions );
  public function get_dimensions_weight_url_params();
}