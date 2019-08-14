<?php
/**
 * @package elemenntor-lightx-widgets
 */

namespace ElementorLightXWidgets;

final class Init
{
    /**
     * Loop through the classes, initialize them 
     * and call the register() method if it exists.
     * @return
     */
    public static function get_widgets($widgets) 
    {
        $services = [];

        foreach ( $widgets as $class ) {
            if ( class_exists( $class ) ) {
                $service = self::instantiate( $class );
                // $service->register();
                $services[] = $service;
            }
        }

        return $services;
    }

    /**
     * Loop through the classes, initialize them 
     * and call the register() method if it exists.
     * @return
     */
    public static function register_handlers($handlers) 
    {
        foreach ( $handlers as $class ) {
            $service = self::instantiate( $class );
            if ( method_exists( $service, 'register' ) ) {
                $service->register();
            }
        }
    }

    /**
     * Simply initialize the class
     * @param class $class class from the services array
     * @return class instance new instance of the class
     */
    private static function instantiate( $class ) 
    {
        return new $class();
    }
}