<?php
return array(
    'coffee' => array(
        'src_dir_base' => APPPATH . 'coffee',
        'output_dir_base' => DOCROOT . 'assets/js_compiled',
        'link_path_base' => '/assets/js_compiled',
        'minimize' => true,
    ),
    'less' => array(
        'src_dir_base' => APPPATH . 'less',
        'output_dir_base' => DOCROOT . 'assets/css_compiled',
        'link_path_base' => '/assets/css_compiled',
        'minimize' => true,
    ),
);