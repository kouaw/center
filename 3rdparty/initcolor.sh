#!/bin/bash
echo 70 > /sys/class/gpio/export
if [ $? -eq 0 ]
then
	echo out > /sys/class/gpio/gpio70/direction
fi
echo 71 > /sys/class/gpio/export
if [ $? -eq 0 ]
then
	echo out > /sys/class/gpio/gpio71/direction
fi
echo 73 > /sys/class/gpio/export
if [ $? -eq 0 ]
then
	echo out > /sys/class/gpio/gpio73/direction
fi
case "$1" in
	"redcolor")
		echo "Red"
		echo 1 > /sys/class/gpio/gpio70/value && echo 0 > /sys/class/gpio/gpio71/value && echo 0 > /sys/class/gpio/gpio73/value
	;;
	"greencolor")
		echo "Green"
		echo 0 > /sys/class/gpio/gpio70/value && echo 1 > /sys/class/gpio/gpio71/value && echo 0 > /sys/class/gpio/gpio73/value
	;;
	"bluecolor")
		echo "Blue"
		echo 0 > /sys/class/gpio/gpio70/value && echo 0 > /sys/class/gpio/gpio71/value && echo 1 > /sys/class/gpio/gpio73/value
	;;
	"cyancolor")
		echo "Cyan"
		echo 0 > /sys/class/gpio/gpio70/value && echo 1 > /sys/class/gpio/gpio71/value && echo 1 > /sys/class/gpio/gpio73/value
	;;
	"yellowcolor")
		echo "Yellow"
		echo 1 > /sys/class/gpio/gpio70/value && echo 1 > /sys/class/gpio/gpio71/value && echo 0 > /sys/class/gpio/gpio73/value
	;;
	"pinkcolor")
		echo "Pink"
		echo 1 > /sys/class/gpio/gpio70/value && echo 0 > /sys/class/gpio/gpio71/value && echo 1 > /sys/class/gpio/gpio73/value
	;;
	"whitecolor")
		echo "White"
		echo 1 > /sys/class/gpio/gpio70/value && echo 1 > /sys/class/gpio/gpio71/value && echo 1 > /sys/class/gpio/gpio73/value
	;;
	"offcolor")
		echo "Off"
		echo 0 > /sys/class/gpio/gpio70/value && echo 0 > /sys/class/gpio/gpio71/value && echo 0 > /sys/class/gpio/gpio73/value
	;;
esac
