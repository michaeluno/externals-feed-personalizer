<?php
/**
 * Externals
 * 
 * http://en.michaeluno.jp/externals/
 * Copyright (c) 2015 Michael Uno
 * 
 */

/**
 * Loads the plugin.
 * 
 * @action      do      externals_action_after_loading_plugin
 * @since       1
 */
final class ExternalsFeedPersonalizer_Bootstrap {
    
    /**
     * User constructor.
     */
    public function __construct( $sFilePath, $sHookSlug )  {

        add_action( 'plugins_loaded', array( $this, 'replyToLoadPlugin' ) );

    }

    public function replyToLoadPlugin() {

        if ( ! class_exists( 'Externals_Registry' ) ) {
            return;
        }

        add_action( 'externals_filter_template_directories', array( $this, 'replyToRegisterTemplate' ) );

    }

    public function replyToRegisterTemplate( $aTemplateDirPath ) {
        $aTemplateDirPath[] = ExternalsFeedPersonalizer_Registry::$sDirPath . '/template/feed-personalizer';
        return $aTemplateDirPath;
    }

}