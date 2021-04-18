<?php
/*
Plugin Name: elementor-form-costum-action
Description: send leads from elementor form to Costumapi leads
version: 1.0
@create date 2021-04-18 13:33:24
@modify date 2021-04-18 16:04:57
*/
if (!defined('ABSPATH')) exit;

add_action( 'elementor_pro/init', function() {
	// Here its safe to include our action class file
	/**
	 * Class Costumapi_lead_Action_After_Submit
	 * @see https://developers.elementor.com/custom-form-action/
	 * Custom elementor form action after submit to add a subsciber to
	 */
	class Costumapi_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {
		/**
		 * Get Name
		 *
		 * Return the action name
		 *
		 * @access public
		 * @return string
		 */
		public function get_name() {
			return 'costumapi';
		}

		/**
		 * Get Label
		 *
		 * Returns the action label
		 *
		 * @access public
		 * @return string
		 */
		public function get_label() {
			return __( 'Costumapi', 'text-domain' );
		}

		/**
		 * Run
		 *
		 * Runs the action after submit
		 *
		 * @access public
		 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
		 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
		 */
		public function run( $record, $ajax_handler ) {
			$settings = $record->get( 'form_settings' );

			//  Make sure that there is a costumapi_company_id
			if ( empty( $settings['costumapi_company_id'] ) ) {
				return;
			}

			//  Make sure that there is a costumapi_branch_id
			if ( empty( $settings['costumapi_branch_id'] ) ) {
				return;
			}

			// Get sumitetd Form data
			$raw_fields = $record->get( 'fields' );

			// Normalize the Form Data
			$fields = [];
			foreach ( $raw_fields as $id => $field ) {
				$fields[ $id ] = $field['value'];
			}

			// If we got this far we can start building our request data
			// Based on the param list
			$costumapi_data = [
				'name' => urlencode($fields[ $settings['costumapi_name_id'] ]),
				'mobile' => urlencode($fields[ $settings['costumapi_mobile_id'] ]),
				'company_id' => $settings['costumapi_company_id'],
				'branch_id' => $settings['costumapi_branch_id']
			];
			error_log('costumapi data: '.json_encode($costumapi_data));
			// Send the request, cURL func go's here

		}

		/**
		 * Register Settings Section
		 *
		 * Registers the Action controls
		 *
		 * @access public
		 * @param \Elementor\Widget_Base $widget
		 */
		public function register_settings_section( $widget ) {
			$widget->start_controls_section(
				'section_costumapi',
				[
					'label' => __( 'Costumapi', 'text-domain' ),
					'condition' => [
						'submit_actions' => $this->get_name(),
					],
				]
			);

			$widget->add_control(
				'costumapi_company_id',
				[
					'label' => __( 'Costumapi company id', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::TEXT,
				]
			);

			$widget->add_control(
				'costumapi_branch_id',
				[
					'label' => __( 'Costumapi branch id', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::TEXT,
				]
			);

			$widget->add_control(
				'costumapi_name_id',
				[
					'label' => __( 'Name Field ID', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::TEXT,
				]
			);

			$widget->add_control(
				'costumapi_mobile_id',
				[
					'label' => __( 'Mobile Field ID', 'text-domain' ),
					'type' => \Elementor\Controls_Manager::TEXT,
				]
			);

			$widget->end_controls_section();

		}

		/**
		 * On Export
		 *
		 * Clears form settings on export
		 * @access Public
		 * @param array $element
		 */
		public function on_export( $element ) {
			unset(
				$element['costumapi_company_id'],
				$element['costumapi_branch_id'],
				$element['costumapi_name_id'],
				$element['costumapi_mobile_id']
			);
		}
	}

	// Instantiate the action class
	$costumapi_action = new Costumapi_Action_After_Submit();

	// Register the action with form widget
	\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $costumapi_action->get_name(), $costumapi_action );
});
