#!/bin/bash
touch /tmp/dependancy_center_in_progress
echo 0 > /tmp/dependancy_center_in_progress
echo "########### Installation en cours ##########"
cd "$(dirname "$0")"
sudo apt-get update -y -q
echo 30 > /tmp/dependancy_center_in_progress
sudo apt-get install i2c-tools -y -q
echo 70 > /tmp/dependancy_center_in_progress
sudo apt-get install python-smbus -y -q
echo 80 > /tmp/dependancy_center_in_progress
sudo chmod +x initrelais.sh
sudo chmod +x initcolor.sh
sudo chmod +x initbattery.sh
echo 100 > /tmp/dependancy_center_in_progress
echo "########### Fin ##########"
rm /tmp/dependancy_center_in_progress
