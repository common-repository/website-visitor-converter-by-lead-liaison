<?php

if ( ! class_exists( 'WVC_Options' ) ) {
    class WVC_Options {
        /**
         * Holds the values to be used in the fields callbacks
         */
        private $options;

        /**
         * Start up
         */
        public function __construct() {
            add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
            add_action( 'admin_init', array( $this, 'page_init' ) );
        }

        /**
         * Add options page
         */
        public function add_plugin_page() {
            add_submenu_page( 
            	'edit.php?post_type=wvc-forms', 
            	__('Settings', 'wvc-forms'), 
            	__('Settings', 'wvc-forms'), 
            	'manage_options', 
            	'wvc-settings', 
            	array( $this, 'create_admin_page' ) 
            ); 
        }

        /**
         * Options page callback
         */
        public function create_admin_page()
        {
            // Set class property
            $this->options = get_option( 'wvc_plugin' );
            // automation_id
            
            ?>
            <div class="wrap">
                <form method="post" action="options.php">
                <?php
                    // This prints out all hidden setting fields
                    settings_fields( 'wvc_plugin_settings_group' );
                    do_settings_sections( 'wvc-setting-admin' );
                    submit_button();
                ?>
                </form>
            </div>
            <?php
        }

        /**
         * Register and add settings
         */
        public function page_init()
        {        
            register_setting(
                'wvc_plugin_settings_group', // Option group
                'wvc_plugin' // Option name
            );

            add_settings_section(
                'wvc_plugin_settings', // ID
                esc_html__('Website Visitor Converter Settings', 'wvc-forms'), // Title
                array( $this, 'print_section_info' ), // Callback
                'wvc-setting-admin' // Page
            );

            add_settings_field(
                'datavalidation_key', 
                esc_html__('Datavalidation.com API key', 'wvc-forms'), 
                array( $this, 'datavalidation_com_callback' ), 
                'wvc-setting-admin', 
                'wvc_plugin_settings'
            );

            add_settings_field(
                'leadliaison_key', 
                esc_html__('Lead Liaison API key', 'wvc-forms'), 
                array( $this, 'leadliaison_key_callback' ), 
                'wvc-setting-admin', 
                'wvc_plugin_settings'
            );

            add_settings_field(
                'workflow_id', 
                esc_html__('Lead Liaison Global Automation ID', 'wvc-forms'), 
                array( $this, 'workflow_id_callback' ), 
                'wvc-setting-admin', 
                'wvc_plugin_settings'
            );
        }

        /** 
         * Print the Section text
         */
        public function print_section_info()
        {
            esc_html_e('Enter your settings below:', 'wvc-forms');
        }

        /** 
         * Get the settings option array and print one of its values
         */
        public function datavalidation_com_callback()
        {
            $datavalidation_key = isset( $this->options['datavalidation_key'] ) ? trim($this->options['datavalidation_key']) : '';
            printf(
                '<input type="text" id="title" name="wvc_plugin[datavalidation_key]" value="%s" />',
                esc_attr( $datavalidation_key )
            );
            ?> 
                <p class="description"><?php esc_html_e( 'Field description.', 'wvc-forms' ); ?></p>
            <?php
        }

        /** 
         * Get the settings option array and print one of its values
         */
        public function leadliaison_key_callback()
        {
            $leadliaison_key = isset( $this->options['leadliaison_key'] ) ? trim($this->options['leadliaison_key']) : '';
            printf(
                '<input type="text" id="title" name="wvc_plugin[leadliaison_key]" value="%s" />',
                esc_attr( $leadliaison_key )
            );
            ?> 
                <p class="description"><?php esc_html_e( 'Field description.', 'wvc-forms' ); ?></p>
            <?php
        }

        /** 
         * Get the settings option array and print one of its values
         */
        public function workflow_id_callback()
        {
            $workflow_id = isset( $this->options['workflow_id'] ) ? trim($this->options['workflow_id']) : '';
            printf(
                '<input type="text" id="title" name="wvc_plugin[workflow_id]" value="%s" />',
                esc_attr( $workflow_id )
            );
            ?> 
                <p class="description"><?php esc_html_e( 'Field description.', 'wvc-forms' ); ?></p>
            <?php
        }
    }
}

if( is_admin() ) {
    new WVC_Options();
}
