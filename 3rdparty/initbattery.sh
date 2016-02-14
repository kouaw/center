#!/bin/bash
echo "Programmation registres"
i2cset -y 2 0x6b 5 0x8c b
i2cset -y 2 0x6b 4 0xb0 b
i2cset -y 2 0x6b 3 0x10 b
i2cset -y 2 0x6b 2 0x00 b
i2cset -y 2 0x6b 0 0x5f b
echo "Activation CEn (gpio195)"
echo 195 > /sys/class/gpio/export
if [ $? -eq 0 ]
then
	echo out > /sys/class/gpio/gpio195/direction
fi
echo 0 > /sys/class/gpio/gpio195/value
