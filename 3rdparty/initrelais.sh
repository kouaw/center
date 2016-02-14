#!/bin/bash
echo 67 > /sys/class/gpio/export
if [ $? -eq 0 ]
then
	echo out > /sys/class/gpio/gpio67/direction
fi
echo 72 > /sys/class/gpio/export
if [ $? -eq 0 ]
then
	echo out > /sys/class/gpio/gpio72/direction
fi
case "$1" in
	"onrelais1")
		echo "On 1"
		echo 1 > /sys/class/gpio/gpio67/value
	;;
	"offrelais1")
		echo "Off 1"
		echo 0 > /sys/class/gpio/gpio67/value
	;;
	"onrelais2")
		echo "On 2"
		echo 1 > /sys/class/gpio/gpio72/value
	;;
	"offrelais2")
		echo "Off 2"
		echo 0 > /sys/class/gpio/gpio72/value
	;;
esac
