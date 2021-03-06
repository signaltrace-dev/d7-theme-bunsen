<?php

function bunsen_preprocess_node(&$vars){

}


function bunsen_field_attach_view_alter(&$output, $context){
  // We proceed only on nodes.
  if ($context['entity_type'] != 'node' || $context['view_mode'] != 'full') {
    return;
  }
  $node = $context['entity'];

  // Load all instances of the fields for the node.
  $instances = _field_invoke_get_instances('node', $node->type, array('default' => TRUE, 'deleted' => FALSE));

  foreach ($instances as $field_name => $instance) {
    // Only work with fields that we display or that have empty values
    $access = !empty($output[$field_name]['#access']) ? $output[$field_name]['#access'] : FALSE;
    if($access || empty($node->{$field_name})){
      // Set content for fields if they are empty.
      if (empty($node->{$field_name})) {
        $display = field_get_display($instance, 'full', $node);

        // Do not add field that is hidden in current display.
        if ($display['type'] == 'hidden') {
          continue;
        }

        // Load field settings.
        $field = field_info_field($field_name);
        // Set output for field.
        $output[$field_name] = array(
          '#theme' => 'field',
          '#title' => $instance['label'],
          '#label_display' => 'above',
          '#field_type' => $field['type'],
          '#field_name' => $field_name,
          '#bundle' => $node->type,
          '#object' => $node,
          '#items' => array(array()),
          '#entity_type' => 'node',
          '#weight' => $display['weight'],
          0 => array(
            '#markup' => '',
          ),
        );
      }
    }
  }
}
