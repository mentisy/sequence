### Sequence Light Control

Sequence Class for turning outputs (lights) on/off in a sequential manner. 

#### Methods
* **traverseOn:**  Traverse provided addresses turning them ON one by one (delay before turning on next address)
* **traverseToggle:** Traverse provided addresses turning one by one ON, then wait for offDelay, before turning one by one off again
* **traverseSkipBack:** Traverse provided addresses. Go forward X times, then backwards Y times, before going fowards X times again. Repeat until end of addresses
* **allOnOff:** Turn all provided addresses on, then off after provided delay. Repeat X times. Output can be left on the end of the loop
* **manyOn:** Turn provided addresses on straight away *(base method that he sequences use)*
* **manyOff:** Turn provided addresses off straigth away *(base method that he sequences use)*
* **on:** Turn provided address on straight away *(base method that he sequences use)*
* **off:** Turn provided address off straigth away1 *(base method that he sequences use)*