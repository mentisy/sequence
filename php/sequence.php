<?php

require('knx.php');

class Sequence {

    /**
     * Traverse through addresses turning them ON one by one (after nextDelay)
     *
     * @param array $addresses KNX addresses to traverse and turn on
     * @param int $nextDelay
     */
    public static function traverseOn(array $addresses, int $nextDelay = 1000): void {

        foreach($addresses as $address) {

            Knx::on($address);
            sleep($nextDelay / 1000);
        }
    }

    /**
     * Traverse through addresses turning one by one ON, then wait for $offDelay, before turning one by one off again
     *
     * @param array $addresses KNX addresses to traverse
     * @param int $offDelay Time in milli seconds to wait before turning off
     */
    public static function traverseToggle(array $addresses, $offDelay = 1000): void {

        foreach($addresses as $address) {

            Knx::on($address);
            sleep($offDelay / 1000);
            Knx::off($address);
        }
    }

    /**
     * @param array $addresses Array of KNX addresses to turn ON and OFF
     * @param int $times Amount of times to repeat ON/OFF process
     * @param array $options Array of options
     *      - offDelay - Delay after turning the outputs ON,  before turning them OFF again (in ms)
     *      - onDelay  - Delay after turning the outputs OFF, before turning them ON  again (in ms)
     *      - leaveOn  - Whether to keep the outputs ON at the end of the loop.
     */
    public static function allOnOff(array $addresses, int $times = 1, array $options = []) {

        $default = [
            'offDelay' => 1000,
            'onDelay' => 1000,
            'leaveOn' => false
        ];
        $options = $options + $default;
        $count = 0;
    	while($count < $times) {

            self::manyOn($addresses);

            // Stop loop if we want to stop with the lights on and this is the last iteration
            if($options['leaveOn'] && $count+1 === $times) {
    	        break;
            }

            sleep($options['offDelay'] / 1000);

            self::manyOff($addresses);

            sleep($options['onDelay'] / 1000);
            $count++;
        }
    }

    /**
     * Traverse through KNX addresses.
     * First forward until $traverse is reached. Then it goes backwards.
     * Then backwards until $skipBack is reached. Then it goes forwards.
     *
     * @param array $addresses KNX addresses to traverse
     * @param int $traverse How many addresses to traverse forwards  before going backwards
     * @param int $skipBack How many addresses to traverse backwards before going forwards
     * @param int $offDelay Delay after turning the outputs ON,  before turning them OFF again (in ms)
     *
     * @throws Exception
     *
     * traverseSkipBack([1, 2, 3, 4, 5]) = On/Off 1, 2, 3, 2, 3, 4, 3, 4, 5, 4, 5, 6, 5, 6
     * traverseSkipBack([1, 2, 3, 4, 5], 3, 2) = On/Off 1, 2, 3, 4, 3, 2, 3, 4, 5, 4, 3, 4, 5, 6, 5, 4, 5, 6
     * traverseSkipBack([1, 2, 3, 4, 5], 3, 3) = Exception ($skipBack must be lower than $traverse)
     */
    public static function traverseSkipBack(array $addresses, $traverse = 2, $skipBack = 1, $offDelay = 1000) {

        if($skipBack >= $traverse) {
            throw new Exception('$skipBack must be lower than $traverse. Or else it will be an internal loop.');
        }
        if($skipBack < 1 || $traverse < 1) {
            throw new Exception('$skipBack and $traverse can\'t be lower than 1.');
        }
        $haveTraversed = 0;
        $haveSkippedBack = 0;
        $goingBack = false;

        $i = 0;
        while($i < count($addresses)) {

            Knx::on($addresses[$i]);
            sleep($offDelay / 1000);
            Knx::off($addresses[$i]);

            if($haveSkippedBack === $skipBack) {
                $goingBack = false;
            }

            // If we've reached the traverse forward point, start going backwards
            if($haveTraversed === $traverse || $goingBack) {

                $goingBack = true;
                $haveSkippedBack++;
                $haveTraversed = 0;
                $i--;
            }
            // Traverse forward
            else {

                $goingBack = false;
                $haveTraversed++;
                $haveSkippedBack = 0;
                $i++;
            }
        }
    }

    /**
     * Turn on an array of KNX addresses
     *
     * @param array $addresses KNX Addresses to turn on
     */
    public static function manyOn(array $addresses): void {

        foreach($addresses as $address) {

            Knx::on($address);
        }
    }

    /**
     * Turn off an array of KNX addresses
     *
     * @param array $addresses KNX Addresses to turn off
     */
    public static function manyOff(array $addresses): void {

        foreach($addresses as $address) {

            Knx::off($address);
        }
    }
}
