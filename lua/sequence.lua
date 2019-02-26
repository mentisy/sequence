--
-- Created by IntelliJ IDEA.
-- User: Alexander Volle
-- Date: 05.02.2019
-- Time: 21.58
--
-- Sequence Class
--
-- A class to enable outputs to be turned on and off in a sequence
--

Sequence = {}

-- Create the sequence class
function Sequence:new()
    local newObj = {}
    self.__index = self
    return setmetatable(newObj, self)
end

--[[
 *
 * Traverse through addresses turning them ON one by one (after nextDelay)
 *
 * @param array $addresses KNX addresses to traverse and turn on
 * @param int $nextDelay
 *
]]
function Sequence:traverseOn(addresses, nextDelay)

    local nextDelay = nextDelay or 1000

    for _, address in ipairs(addresses) do

        self:on(address)
        sleep(nextDelay / 1000)
    end
end

--[[
 * Traverse through addresses turning one by one ON, then wait for $offDelay, before turning one by one off again
 *
 * @param array $addresses KNX addresses to traverse
 * @param int $offDelay Time in milli seconds to wait before turning off]
 ]]
function Sequence:traverseToggle(addresses, offDelay)

    local offDelay = offDelay or 1000

    for _, address in ipairs(addresses) do
        self:on(address)
        sleep(offDelay / 1000)
        self:off(address)
    end
end

--[[
 * @param array $addresses Array of KNX addresses to turn ON and OFF
 * @param int $times Amount of times to repeat ON/OFF process
 * @param array $options Array of options
 *      - offDelay - Delay after turning the outputs ON,  before turning them OFF again (in ms)
 *      - onDelay  - Delay after turning the outputs OFF, before turning them ON  again (in ms)
 *      - leaveOn  - Whether to keep the outputs ON at the end of the loop.
]]
function Sequence:allOnOff(addresses, times, options)

    local times = times or 1
    local options = options or {}

    local default = {
        offDelay = 1000,
        onDelay  = 1000,
        leaveOn  = false
    }
    options = merge(options, default)

    local count = 0
    while(count < times) do

        self:manyOn(addresses)

        -- Stop loop if we want to stop with the lights on and this is the last iteration
        if(options['leaveOn'] and count+1 == times) then
            break
        end

        sleep(options['offDelay'] / 1000)

        self:manyOff(addresses)

        sleep(options['onDelay'] / 1000)
        count = count + 1
    end
end

--[[
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
 * traverseSkipBack({1, 2, 3, 4, 5}) = On/Off 1, 2, 3, 2, 3, 4, 3, 4, 5, 4, 5, 6, 5, 6
 * traverseSkipBack({1, 2, 3, 4, 5}, 3, 2) = On/Off 1, 2, 3, 4, 3, 2, 3, 4, 5, 4, 3, 4, 5, 6, 5, 4, 5, 6
 * traverseSkipBack({1, 2, 3, 4, 5}, 3, 3) = Exception ($skipBack must be lower than $traverse)
]]
function Sequence:traverseSkipBack(addresses, traverse, skipBack, offDelay)

    local traverse = traverse or 2
    local skipBack = skipBack or 1
    local offDelay = offDelay or 1000

    if(skipBack >= traverse) then
        error('var "skipBack" must be lower than var "traverse". Or else it will be an internal loop.')
        return
    end

    if(skipBack < 1 or traverse < 1) then
        error('var "skipBack" and var "traverse" cannot be lower than 1.')
        return
    end

    local haveTraversed = 0
    local haveSkippedBack = 0
    local goingBack = false

    local i = 0

    while(i < table.getn(addresses)) do

        self:on(addresses[i + 1])
        sleep(offDelay / 1000)
        self:off(addresses[i + 1])

        if(haveSkippedBack == skipBack) then
            goingBack = false
        end

        -- If we've reached the traverse forward point, start going backwards
        if(haveTraversed == traverse or goingBack) then

            goingBack = true
            haveSkippedBack = haveSkippedBack + 1
            haveTraversed = 0
            i = i - 1
        else
            -- Traverse forward
            goingBack = false
            haveTraversed = haveTraversed + 1
            haveSkippedBack = 0
            i = i + 1
        end
    end
end

--[[
 * Turn on an array of KNX addresses
 *
 * @param array $addresses KNX Addresses to turn on
 */
]]
function Sequence:manyOn(addresses)

    for _, address in ipairs(addresses) do

        self:on(address)
    end
end

--[[
 * Turn off an array of KNX addresses
 *
 * @param array $addresses KNX Addresses to turn off
 */
]]
function Sequence:manyOff(addresses)

    for _, address in ipairs(addresses) do

        self:off(address)
    end
end

--[[
 * Turn off KNX address
 *
 * @param int|string $address KNX Address to turn off
]]
function Sequence:off(address)
    grp.write(address, false)
    --log("Lamp " .. address .. " OFF")
end

--[[
 * Turn on KNX address
 *
 * @param int|string $address KNX Address to turn on
]]
function Sequence:on(address)
    grp.write(address, true)
    --log("Lamp " .. address .. " ON")
end

