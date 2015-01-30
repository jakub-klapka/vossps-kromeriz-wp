<?php
$data = Timber::get_context();
Timber::render( 'home.twig', $data );