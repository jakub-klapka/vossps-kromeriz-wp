<?php
$data = Timber::get_context();
Timber::render( 'master.twig', $data );