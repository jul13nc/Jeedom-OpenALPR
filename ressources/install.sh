#!/bin/bash
touch /tmp/compilation_openAlpr_in_progress
echo 0 > /tmp/compilation_openAlpr_in_progress
echo "*****************************************************************************************************"
echo "*                                Installing additional libraries                                    *"
echo "*****************************************************************************************************"
#sudo apt-get -y -allow update
#sudo apt-get -y -allow upgrade
echo 10 > /tmp/compilation_openAlpr_in_progress
sudo apt-get install -y autoconf automake libtool
sudo apt-get install -y pkg-config
sudo apt-get install -y libpng12-dev
sudo apt-get install -y libjpeg62-turbo-dev
sudo apt-get install -y libtiff4-dev
sudo apt-get install -y zlib1g-dev
sudo apt-get install -y git
sudo apt-get install -y git-core
sudo apt-get install -y cmake
sudo apt-get install -y liblog4cplus-dev 
sudo apt-get install -y libcurl3-dev 
sudo apt-get install -y uuid-dev
sudo apt-get install -y build-essential
sudo apt-get install -y libjpeg8-dev libjasper-dev
sudo apt-get install -y libgtk2.0-dev
sudo apt-get install -y libavcodec-dev libavformat-dev libswscale-dev libv4l-dev
sudo apt-get install -y libatlas-base-dev gfortran
sudo apt-get install -y python2.7-dev
sudo apt-get install -y libopencv-dev
sudo apt-get install -y libtesseract-dev
sudo apt-get install -y libleptonica-dev
sudo apt-get install -y beanstalkd
echo 50 > /tmp/compilation_openAlpr_in_progress
echo "*****************************************************************************************************"
echo "*                                            Compile openalpr:                                      *"
echo "*****************************************************************************************************"
if [ -d "/usr/local/src/openalpr" ]; then
	sudo rm -R "/usr/local/src/openalpr"
fi
if [ -d "/etc/openalpr/" ]; then
	sudo rm -R "/etc/openalpr/"
fi
sudo mkdir /usr/local/src/openalpr/
sudo mkdir /etc/openalpr/
cd /usr/local/src/openalpr/
git clone https://github.com/openalpr/openalpr.git
cd openalpr/src
mkdir build
cd build
cmake -DCMAKE_INSTALL_PREFIX:PATH=/usr -DCMAKE_INSTALL_SYSCONFDIR:PATH=/etc ..
echo 60 > /tmp/compilation_openAlpr_in_progress
# compile the library
make
echo 75 > /tmp/compilation_openAlpr_in_progress
# Install the binaries/libraries to your local system (prefix is /usr)
sudo make install
echo 85 > /tmp/compilation_openAlpr_in_progress
echo "v2.1.0" > /etc/openalpr/openalpr_VERSION
sudo chmod 777 -R /etc/openalpr
echo 100 > /tmp/compilation_openAlpr_in_progress
echo "*****************************************************************************************************"
echo "*                                            Fin de l'installation                                      *"
echo "*****************************************************************************************************"
rm /tmp/compilation_openAlpr_in_progress
