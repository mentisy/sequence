<?php

class Knx {

    const debug = true;
    /**
     * Turn on KNX address
     *
     * @param int|string $address KNX Address to turn on
     */
    public static function on($address): void {

        if(self::debug) {

            echo sprintf("%s: %s, ", $address, "on");
        }
    }

    /**
     * Turn off KNX address
     *
     * @param int|string $address KNX Address to turn off
     */
    public static function off($address): void {

        if(self::debug) {

            echo sprintf("%s: %s\n", $address, "off");
        }
    }
}