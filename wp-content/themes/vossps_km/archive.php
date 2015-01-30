<?php
$data = Timber::get_context();
Timber::render( 'archive.twig', $data );