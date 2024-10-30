<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 * @param $name
 * @param bool $echo
 * @return string
 */


function ms_form_options($name, $echo = false) {
    $options = array(
        'text'      => array(
                'title' => __('Text Box', 'ms-registration'),
                'desc'  => __('A simple input box', 'ms-registration')),
        'tel'       => array(
                'title' => __('Phone Number Input', 'ms-registration'),
                'desc'  => __('Input for phone number (Validated at form submission)', 'ms-registration')),
        'url'       => array(
                'title' => __('URL Input', 'ms-registration'),
                'desc'  => __('Input for URL\'s (Validated at form submission)', 'ms-registration')),
        'email'     => array(
                'title' => __('Email Input', 'ms-registration'),
                    'desc'  => __('Input for emails (Validated at form submission)', 'ms-registration')),
        'password'  => array(
                'title' => __('Password Input', 'ms-registration'),
                'desc'  => __('Wordpress by default doesnt include the password input in the registration form', 'ms-registration')),
        'number'    => array(
                'title' => __('Number Input', 'ms-registration'),
                'desc'  => __('Number type input', 'ms-registration')),
        'checkbox'  => array(
                'title' => __('Checkbox', 'ms-registration'),
                'desc'  => __('Standard checkbox', 'ms-registration'))
    );

    $return = '<select name="' . $name . '">';
	$return .= '<option selected="selected" disabled>-- Select --</option>';
    foreach($options as $k => $v)
        $return .= '<option value="' . $k . '" data-desc="' . esc_attr($v['desc']) . '">' . $v['title'] . '</option>';
    $return .= '</select>';

    if ($echo)
        echo $return;
    else
        return $return;
}

/**
 *
 */
function ms_registration_options() {
    $registration = get_option('ms_registration_form', array());
    ?>
    <div class="wrap ms-wrap">
        <h2><?php echo __('Customize WP registration form', 'ms-registration') ?></h2>
        <hr>
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <p>
                    Input Type: <?php ms_form_options('input_type',true) ?>
                    <hr>
                    <p id="ms_description"></p>
                    <div id="input_builder"></div>
                    <br>
                    <button id="add_input" class="button button-primary"><?php echo __('Add', 'ms-registration') ?></button>
                </p>
                <hr>
                <?php
				if (is_array($registration)) {
					foreach ($registration as $k => $rego) {
						?>
						<p>
							<?php echo $rego['label'] ?><input type="<?php echo $rego['type'] ?>" placeholder="<?php echo $rego['placeholder'] ?>" value="<?php echo $rego['default_value'] ?>">
							<span data-index="<?php echo $k ?>" class="dashicons dashicons-no ms-remove-input" style="cursor: pointer;"></span>
						</p>
						<?php
					}
				}
                ?>
                <div id="ms_sections"></div>
                <p>
                    <button id="ms-save-changes" class="button button-primary">Save Changes</button>
                </p>
            </div>
        </div>
        <br class="clear">
    </div>
    <script>
        jQuery(document).ready(function ($) {
            var submit_array = {};
            <?php
			if (is_array($registration)) {
				foreach($registration as $k => $rego) {
					?>
					submit_array['<?php echo $k ?>'] = { type: '<?php echo $rego['type'] ?>', label: '<?php echo $rego['label'] ?>', name: '<?php echo $rego['name'] ?>', placeholder: '<?php echo $rego['placeholder'] ?>', default_value: '<?php echo $rego['default_value'] ?>' };
					<?php
				}
			}
            ?>
            var key_count = <?php echo time() ?>;

            var text_types = ['tel', 'url', 'email'];
            var input_type = '';

            String.prototype.isValidName = function() {
                var regExp = /^[A-Za-z0-9_]+$/;
                return (this.match(regExp));
            };

            function create_inputs(inputs) {
                var output = '';
                for (var index in inputs) {
                    output += '<p>' + inputs[index] + '</p>';
                }
                return output;
            }

            $('select[name="input_type"]').on('change', function() {
                 var input_builder = $('#input_builder');
                 input_builder.html('');
                 input_type = $(this).val();

                 if (input_type == '')
                     return;

                $('#ms_description').html($(this).find(':selected').attr('data-desc'));

                 var extra_inputs = [];
                 var inputs = [
                     'Label: <input type="text" name="label">',
                     'Name: <input type="text" name="name"> (No whitespace or special characters)',
                     'Required: <input type="checkbox" name="required">'
                 ];

                 if (text_types.indexOf(input_type) != -1) {
                     extra_inputs = [
                         'Placeholder: <input type="text" name="placeholder">',
                         'Default Value: <input type="text" name="default_value">'
                     ];
                     inputs = inputs.concat(extra_inputs);
                     input_builder.append(create_inputs(inputs));
                 } else if ('text' == input_type) {
                     extra_inputs = [
                         'Placeholder: <input type="text" name="placeholder">',
                         'Default Value: <input type="text" name="default_value">',
                         'Minimum Length: <input type="number" value="0" min="0" name="min_length">',
                         'Maximum Length: <input type="number" value="0" min="0" name="max_length"> (0 is unlimited)',
                         'Capitalize first character: <input type="checkbox" value="1" name="cap_first">',
                         'Uppercase: <input type="checkbox" value="1" name="uppercase">',
                         'Lowercase: <input type="checkbox" value="1" name="lowercase">'
                     ];
                     inputs = inputs.concat(extra_inputs);
                     input_builder.append(create_inputs(inputs));
                 } else if ('password' == input_type) {
                     extra_inputs = [
                         'Placeholder: <input type="text" name="placeholder">',
                         'Minimum Length: <input type="number" value="3" min="3" name="min_length">',
                         'Must contain: Numbers <input type="checkbox" value="1" name="numbers"> Symbols <input type="checkbox" value="1" name="symbols">'
                     ];
                     inputs = inputs.concat(extra_inputs);
                     input_builder.append(create_inputs(inputs));
                     input_builder.find('input[name="required"]').attr('checked', true).attr('disabled', true);
                 } else if ('number' == input_type) {
                     extra_inputs = [
                         'Placeholder: <input type="text" name="placeholder">',
                         'Default Value: <input type="number" name="default_value">',
                         'Minimum Value: <input type="number" name="min_value">',
                         'Maximum Value: <input type="number" name="max_value">'
                     ];
                     inputs = inputs.concat(extra_inputs);
                     input_builder.append(create_inputs(inputs));
                 } else if ('checkbox' == input_type) {
                     extra_inputs = [
                         'Checked (Yes): <input type="radio" name="checked" value="1">',
                         'Checked (No): <input type="radio" name="checked" value="0" checked>'
                     ];
                     inputs = inputs.concat(extra_inputs);
                     input_builder.append(create_inputs(inputs));
                 }
            });

            $('#add_input').on('click', function (e) {
                e.preventDefault();
                if (input_type == '')
                    return;

                var input_builder = $('#input_builder');

                var label = input_builder.find('input[name="label"]').val();
                if (label.length == 0) {
                    alert('Label is required!');
                    return;
                }

                var name = input_builder.find('input[name="name"]').val();
                if (name.length == 0 || !name.isValidName()) {
                    alert('Valid name is required! Please use alpha numeric characters.');
                    return;
                }

                if (text_types.indexOf(input_type) != -1) {
                    var placeholder = input_builder.find('input[name="placeholder"]').val();
                    var default_value = input_builder.find('input[name="default_value"]').val();
                    submit_array['index' + key_count] = { type: input_type, label: label, name: name, placeholder: placeholder, default_value: default_value };
                    $('#ms_sections').before('<p>' + label + ' <input type="' + input_type + '" placeholder="' + placeholder + '" value="' + default_value + '"> <span data-index="index' + key_count + '" class="dashicons dashicons-no ms-remove-input" style="cursor: pointer;"></span></p>');
                    key_count++;
                } else if ('text' == input_type) {
                    var placeholder = input_builder.find('input[name="placeholder"]').val();
                    var default_value = input_builder.find('input[name="default_value"]').val();
                    var min_length = input_builder.find('input[name="min_length"]').val();
                    var max_length = input_builder.find('input[name="min_length"]').val();
                    var cap_first = input_builder.find('input[name="cap_first"]').is(':checked');
                    var uppercase = input_builder.find('input[name="uppercase"]').is(':checked');
                    var lowerrcase = input_builder.find('input[name="lowercase"]').is(':checked');

                    submit_array['index' + key_count] = {
                        type: input_type,
                        label: label,
                        name: name,
                        placeholder: placeholder,
                        default_value: default_value,
                        min_length: min_length,
                        max_length: max_length,
                        cap_first: cap_first,
                        uppercase: uppercase,
                        lowerrcase: lowerrcase
                    };
                    $('#ms_sections').before('<p>' + label + ' <input type="' + input_type + '" placeholder="' + placeholder + '" value="' + default_value + '"> <span data-index="index' + key_count + '" class="dashicons dashicons-no ms-remove-input" style="cursor: pointer;"></span></p>');
                    key_count++;
                } else if ('password' == input_type) {
                    var placeholder = input_builder.find('input[name="placeholder"]').val();
                    var min_length = input_builder.find('input[name="min_length"]').val();
                    var numbers = input_builder.find('input[name="numbers"]').is(':checked');
                    var symbols = input_builder.find('input[name="symbols"]').is(':checked');

                    submit_array['index' + key_count] = {
                        type: input_type,
                        label: label,
                        name: name,
                        placeholder: placeholder,
                        min_length: min_length,
                        numbers: numbers,
                        symbols: symbols
                    };
                    $('#ms_sections').before('<p>' + label + ' <input type="' + input_type + '" placeholder="' + placeholder + '""> <span data-index="index' + key_count + '" class="dashicons dashicons-no ms-remove-input" style="cursor: pointer;"></span></p>');
                    key_count++;
                }
            });

            $('#post-body-content').on('click', 'span.ms-remove-input', function() {
                var element = $(this);
                var index_position = element.data('index');
                delete submit_array[index_position];
                element.parent().remove();
            });

            $('#ms-save-changes').on('click', function(e) {
                e.preventDefault();
                $.post('admin-ajax.php', { action: 'ms_registration_submit_data', form: submit_array}, function(data) {
                    $('#ms_submit_norif').remove();
                    $('div.wrap').prepend('<div id="ms_submit_norif" class="notice ' + (data.msg === 'ok' ? 'notice-success' : 'notice-error') + ' is-dismissible"><p>' + data._msg + '</p></div>');
                });
            });
        });
    </script>
    <?php
}
